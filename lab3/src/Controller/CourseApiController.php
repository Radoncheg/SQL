<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\Request\CourseApiRequestParser;
use App\Controller\Request\RequestValidationException;
use App\Controller\Response\CourseApiResponseFormatter;
use App\Model\Exception\CourseNotFoundException;
use App\Model\Exception\EnrollmentNotFoundException;
use App\Model\Service\ServiceProvider;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CourseApiController
{
    private const HTTP_STATUS_OK = 200;
    private const HTTP_STATUS_BAD_REQUEST = 400;

    public function getCourseStatus(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try
        {
            $enrollmentId = CourseApiRequestParser::parseString($request->getQueryParams(), 'enrollmentId');
            $courseStatusData = ServiceProvider::getInstance()->getCourseService()->getCourseStatusData($enrollmentId);
        }
        catch (RequestValidationException $exception)
        {
            return $this->badRequest($response, $exception->getFieldErrors());
        }
        catch (EnrollmentNotFoundException $exception)
        {
            return $this->badRequest($response, ['enrollmentId' => $exception->getMessage()]);
        }
        return $this->success($response, CourseApiResponseFormatter::formatCourseStatusData($courseStatusData));
    }

    public function saveCourse(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try
        {
            $params = CourseApiRequestParser::parseSaveCourseParams((array)$request->getParsedBody());
        }
        catch (RequestValidationException $exception)
        {
            return $this->badRequest($response, $exception->getFieldErrors());
        }

        $courseId = ServiceProvider::getInstance()->getCourseService()->saveCourse($params);

        return $this->success($response, ['courseId' => $courseId]);
    }

    public function saveCourseEnrollment(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try
        {
            $params = CourseApiRequestParser::parseSaveEnrollmentParams((array)$request->getParsedBody());
        }
        catch (RequestValidationException $exception)
        {
            return $this->badRequest($response, $exception->getFieldErrors());
        }

        $enrollmentId = ServiceProvider::getInstance()->getCourseService()->saveEnrollment($params);

        return $this->success($response, ['enrollmentId' => $enrollmentId]);
    }

    public function saveMaterialStatus(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try
        {
            $params = CourseApiRequestParser::parseSaveMaterialStatusParams((array)$request->getParsedBody());
        }
        catch (RequestValidationException $exception)
        {
            return $this->badRequest($response, $exception->getFieldErrors());
        }

        $moduleId = ServiceProvider::getInstance()->getCourseService()->saveMaterialStatus($params);

        return $this->success($response, ['moduleId' => $moduleId]);
    }

    public function deleteCourse(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try
        {
            $id = CourseApiRequestParser::parseString($request->getQueryParams(), 'courseId');
        }
        catch (RequestValidationException $exception)
        {
            return $this->badRequest($response, $exception->getFieldErrors());
        }

        ServiceProvider::getInstance()->getCourseService()->deleteCourse($id);
        return $this->success($response, []);
    }

    private function success(ResponseInterface $response, array $responseData): ResponseInterface
    {
        return $this->withJson($response, $responseData)->withStatus(self::HTTP_STATUS_OK);
    }

    private function badRequest(ResponseInterface $response, array $errors): ResponseInterface
    {
        $responseData = ['errors' => $errors];
        return $this->withJson($response, $responseData)->withStatus(self::HTTP_STATUS_BAD_REQUEST);
    }

    private function withJson(ResponseInterface $response, array $responseData): ResponseInterface
    {
        try
        {
            $responseBytes = json_encode($responseData, JSON_THROW_ON_ERROR);
            $response->getBody()->write($responseBytes);
            return $response->withHeader('Content-Type', 'application/json');
        }
        catch (\JsonException $e)
        {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }
}

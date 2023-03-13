<?php
declare(strict_types=1);

use App\Controller\CourseApiController;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$isProduction = getenv('APP_ENV') === 'prod';

$app = AppFactory::create();

// Регистрация middlewares фреймворка Slim.
$app->addRoutingMiddleware();
$errorMiddleware = $app->addErrorMiddleware(!$isProduction, true, true);

$app->post('/course/save', CourseApiController::class . ':saveCourse');
$app->post('/course/enrollment/save', CourseApiController::class . ':saveCourseEnrollment');
$app->delete('/course/delete', CourseApiController::class . ':deleteCourse');
$app->post('/course/material/status/save', CourseApiController::class . ':saveMaterialStatus');
$app->get('/course/status', CourseApiController::class . ':getCourseStatus');
$app->post('/course/material/delete', CourseApiController::class . ':deleteCourseMaterial');

$app->run();

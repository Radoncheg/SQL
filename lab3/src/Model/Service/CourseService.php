<?php
declare(strict_types=1);

namespace App\Model\Service;

use App\Common\Database\Synchronization;
use App\Model\Course;
use App\Model\CourseMaterial;
use App\Model\Data\CourseStatusData;
use App\Model\Data\SaveCourseParams;
use App\Database\CourseQueryService;
use App\Model\Data\SaveEnrollmentParams;
use App\Model\Data\SaveMaterialStatusParams;

class CourseService
{
    public function __construct(private Synchronization $synchronization, private CourseQueryService $courseQueryService)
    {}

    public function saveEnrollment(SaveEnrollmentParams $params): string
    {
        return $this->courseQueryService->saveEnrollment($params->getEnrollmentId(), $params->getCourseId());
    }

    public function saveMaterialStatus(SaveMaterialStatusParams $params): string
    {
        return $this->courseQueryService->saveMaterialStatus(
            $params->getEnrollmentId(),
            $params->getModuleId(),
            $params->getProgress(),
            $params->getSessionDuration()
        );
    }

    public function saveCourse(SaveCourseParams $params): void
    {
        $this->synchronization->doWithTransaction(function () use ($params) {
            $courseId = $params->getCourseId();
            $moduleIds = $params->getModuleIds();
            $requiredModuleIds = $params->getRequiredModuleIds();
            $course = new Course(
                $courseId,
                1,
                $moduleIds,
                $requiredModuleIds,
                new \DateTimeImmutable(),
                new \DateTimeImmutable()
            );
            $this->courseQueryService->saveCourse($course);
            foreach ($moduleIds as $moduleId)
            {
                $isRequired = 0;
                if (in_array($moduleId, $requiredModuleIds))
                {
                    $isRequired = 1;
                }
                $module = new CourseMaterial(
                    $moduleId,
                    $isRequired,
                    new \DateTimeImmutable(),
                    new \DateTimeImmutable(),
                    null
                );
                $this->courseQueryService->saveModule($module, $courseId);
            }
        });
    }

    /**
     * @param string $enrollmentId
     * @return CourseStatusData
     */
    public function getCourseStatusData(string $enrollmentId): CourseStatusData
    {
        return $this->courseQueryService->getCourseStatusData($enrollmentId);
    }
    public function deleteCourse(string $id): void
    {
        $this->courseQueryService->deleteCourse($id);
    }
}

<?php
declare(strict_types=1);

namespace App\Database;

use App\Common\Database\Connection;
use App\Common\Database\DatabaseDateFormat;
use App\Controller\Request\RequestValidationException;
use App\Model\Course;
use App\Model\CourseMaterial;
use App\Model\Data\CourseStatusData;
use App\Model\Data\ModuleStatusData;

class CourseQueryService
{
   public function __construct(private Connection $connection)
    {}

    public function saveCourse(Course $course): void
    {
        $query = <<<SQL
            INSERT INTO course
              (course_id, version, created_at, updated_at)
            VALUES
              (:courseId, :version, :createdAt, :updatedAt)
            SQL;
        $params = [
            ':courseId' => $course->getCourseId(),
            ':version' => $course->getVersion(),
            ':createdAt' => $this->formatDateTimeOrNull($course->getCreatedAt()),
            ':updatedAt' => $this->formatDateTimeOrNull($course->getUpdatedAt()),
        ];

            $this->connection->execute($query, $params);
    }

    public function saveModule(CourseMaterial $module, string $courseId): void
    {
        $query = <<<SQL
            INSERT INTO course_material
              (module_id, course_id, is_required, created_at, updated_at)
            VALUES
              (:moduleId, :courseId, :isRequired, :createdAt, :updatedAt)
            SQL;
        $params = [
            ':moduleId' => $module->getModuleId(),
            ':courseId' => $courseId,
            ':isRequired' => $module->getIsRequired(),
            ':createdAt' => $this->formatDateTimeOrNull($module->getCreatedAt()),
            ':updatedAt' => $this->formatDateTimeOrNull($module->getUpdatedAt()),
        ];

        $this->connection->execute($query, $params);
    }

    public function saveEnrollment(string $enrollmentId, string $courseId): void
    {
        $query = <<<SQL
            INSERT INTO course_enrollment
              (enrollment_id, course_id)
            VALUES
              (:enrollmentId, :courseId)
            SQL;
        $params = [
            ':enrollmentId' => $enrollmentId,
            ':courseId' => $courseId,
        ];

        $this->connection->execute($query, $params);
        $this->saveCourseStatus($enrollmentId);
    }

    public function saveMaterialStatus(
        string $enrollmentId,
        string $moduleId,
        int $progress,
        int $duration
    ): void
    {
        $query = <<<SQL
            INSERT INTO course_module_status
              (enrollment_id, module_id, progress, duration)
            VALUES
              (:enrollmentId, :moduleId, :progress, :duration)
            SQL;
        $params = [
            ':enrollmentId' => $enrollmentId,
            ':moduleId' => $moduleId,
            ':progress' => $progress,
            ':duration' => $duration
        ];

        $this->connection->execute($query, $params);
    }


    /**
     * @param string $enrollmentId
     * @return CourseStatusData
     */
    public function getCourseStatusData(string $enrollmentId): CourseStatusData
    {
        $query = <<<SQL
            SELECT
              cs.enrollment_id,
              JSON_ARRAYAGG(JSON_OBJECT('moduleId', cms.module_id, 'progress', cms.progress)) AS modules,
              cs.progress,
              cs.duration
            FROM course_status cs
            LEFT JOIN course_module_status cms ON cs.enrollment_id = cms.enrollment_id
            WHERE cs.enrollment_id = :enrollmentId AND cms.deleted_at IS NULL
            GROUP BY cms.module_id
            SQL;
        $params = [
            ':enrollmentId' => $enrollmentId,
        ];

        $stmt = $this->connection->execute($query, $params);

        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if (empty($result))
        {
            throw new RequestValidationException([$name => 'All modules are deleted']);
        }

        return $this->hydrateCourseStatusData($result);
    }

    public function deleteCourse(string $courseId): void
    {
        $params = [
            ':courseId' => $courseId
        ];
        $this->connection->execute(
            <<<SQL
            DELETE c, ce, cm 
            FROM course c
            LEFT JOIN course_enrollment ce ON c.course_id = ce.course_id
            LEFT JOIN course_material cm ON c.course_id = cm.course_id  
            WHERE c.course_id = :courseId
            SQL,
        $params);

    }

    public function deleteCourseMaterial(string $materialId): void
    {
        $query = <<<SQL
            UPDATE course_module_status
            SET deleted_at = NOW()
            WHERE module_id = :materialId
            SQL;
        $params = [
            ':materialId' => $materialId,
        ];

        $this->connection->execute($query, $params);
    }

    private function saveCourseStatus(string $enrollmentId): void
    {
        $query = <<<SQL
            INSERT INTO course_status
              (enrollment_id, progress, duration)
            VALUES
              (:enrollmentId, :progress, :duration)
            SQL;
        $params = [
            ':enrollmentId' => $enrollmentId,
            ':progress' => 0,
            ':duration' => 100
        ];

        $this->connection->execute($query, $params);
    }

    private function hydrateCourseStatusData(array $row): CourseStatusData
    {
        $modules = [];
        foreach ($row as $data)
        {
            $modules[] = json_decode($data['modules'], false, 512, 0);
        }
        try
        {
            return new CourseStatusData(
                (string)$row['enrollment_id'],
                $modules,
                (int)$row['progress'],
                (int)$row['duration']
            );
        }
        catch (\Exception $e)
        {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    private function formatDateTimeOrNull(?\DateTimeImmutable $dateTime): ?string
    {
        return $dateTime?->format(DatabaseDateFormat::MYSQL_DATETIME_FORMAT);
    }
}

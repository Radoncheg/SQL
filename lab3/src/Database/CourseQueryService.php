<?php
declare(strict_types=1);

namespace App\Database;

use App\Common\Database\Connection;
use App\Common\Database\DatabaseDateFormat;
use App\Model\Course;
use App\Model\CourseMaterial;
use App\Model\Data\CourseStatusData;

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

    public function saveEnrollment(string $enrollmentId, string $courseId): string
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

        return $this->connection->getLastInsertId();
    }

    public function saveMaterialStatus(
        string $enrollmentId,
        string $moduleId,
        int $progress,
        int $duration
    ): string
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

        return $this->connection->getLastInsertId();
    }


    /**
     * @return CourseStatusData
     */
    public function getCourseStatusData(string $enrollmentId): CourseStatusData
    {
        $query = <<<SQL
            SELECT
              cms.enrollment_id,
              JSON_ARRAYAGG(cms.module_id) AS modules,
              cms.progress,
              cms.duration
            FROM course_module_status cms
            WHERE enrollment_id = {$enrollmentId}
            GROUP BY cms.enrollment_id
            SQL;
        $stmt = $this->connection->execute($query);

        return array_map(
            fn($row) => $this->hydrateCourseStatusData($row),
            $stmt->fetchAll(\PDO::FETCH_ASSOC)
        );
    }

    public function deleteCourse(string $courseId): int
    {
        $this->connection->execute(
            <<<SQL
            DELETE FROM course WHERE course_id = {$courseId}
            SQL,
        );
    }

    private function hydrateCourseStatusData(array $row): CourseStatusData
    {
        try
        {
            return new CourseStatusData(
                (string)$row['enrollment_id'],
                json_decode($row['modules'], true, 512, JSON_THROW_ON_ERROR),
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

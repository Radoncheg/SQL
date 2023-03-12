<?php
declare(strict_types=1);

namespace App\Model\Data;

class SaveEnrollmentParams
{
    public function __construct(
        private string $enrollmentId,
        private string $courseId
    ) {}

    public function getEnrollmentId(): string
    {
        return $this->enrollmentId;
    }

    public function getCourseId(): string
    {
        return $this->courseId;
    }
}

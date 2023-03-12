<?php
declare(strict_types=1);

namespace App\Model\Data;

class SaveMaterialStatusParams
{
    public function __construct(
        private string $enrollmentId,
        private string $moduleId,
        private int $progress,
        private int $sessionDuration
    ) {}

    public function getEnrollmentId(): string
    {
        return $this->enrollmentId;
    }

    public function getModuleId(): string
    {
        return $this->moduleId;
    }

    public function getProgress(): int
    {
        return $this->progress;
    }

    public function getSessionDuration(): int
    {
        return $this->sessionDuration;
    }
}

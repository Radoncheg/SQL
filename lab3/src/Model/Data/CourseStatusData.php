<?php
declare(strict_types=1);

namespace App\Model\Data;

class CourseStatusData
{
    /**
     * @param ModuleStatusData[] $modules
     */
    public function __construct(
        private string $enrollmentId,
        private array $modules,
        private int $progress,
        private int $duration
    ) {}

    public function getEnrollmentId(): string
    {
        return $this->enrollmentId;
    }

    /**
     * @return ModuleStatusData[]
     */
    public function getModules(): array
    {
        return $this->modules;
    }

    public function getProgress(): int
    {
        return $this->progress;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }
}

<?php
declare(strict_types=1);

namespace App\Model\Data;

class ModuleStatusData
{
    public function __construct(
        private string $moduleId,
        private int $progress
    ) {}

    public function getModuleId(): string
    {
        return $this->moduleId;
    }

    public function getProgress(): int
    {
        return $this->progress;
    }
}

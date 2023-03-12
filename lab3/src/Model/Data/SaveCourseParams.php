<?php
declare(strict_types=1);

namespace App\Model\Data;

class SaveCourseParams
{
    /**
     * @param string[] $moduleIds
     * @param string[] $requiredModuleIds
     */
    public function __construct(
        private string $courseId,
        private array $moduleIds,
        private array $requiredModuleIds
    ) {}

    public function getCourseId(): string
    {
        return $this->courseId;
    }

    /**
     * @return string[]
     */
    public function getModuleIds(): array
    {
        return $this->moduleIds;
    }

    /**
     * @return string[]
     */
    public function getRequiredModuleIds(): array
    {
        return $this->requiredModuleIds;
    }
}

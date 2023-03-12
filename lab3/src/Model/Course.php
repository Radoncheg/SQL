<?php
declare(strict_types=1);

namespace App\Model;

class Course
{
    /**
     * @param string[] $moduleIds
     * @param string[] $requredModuleIds
     */
    public function __construct(
        private string $courseId,
        private int $version,
        private array $moduleIds,
        private array $requredModuleIds,
        private \DateTimeImmutable $createdAt,
        private ?\DateTimeImmutable $updatedAt
    ) {}

    public function getCourseId(): string
    {
        return $this->courseId;
    }

    public function getVersion(): int
    {
        return $this->version;
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
        return $this->requredModuleIds;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }
}

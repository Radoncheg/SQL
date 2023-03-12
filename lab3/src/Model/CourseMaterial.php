<?php
declare(strict_types=1);

namespace App\Model;

class CourseMaterial
{
    public function __construct(
        private string $moduleId,
        private int $isRequired,
        private \DateTimeImmutable $createdAt,
        private ?\DateTimeImmutable $updatedAt,
        private ?\DateTimeImmutable $deletedAt,
    ) {}

    /**
     * @return void
     */
    public function delete(): void
    {
        $this->deletedAt = new \DateTimeImmutable();
    }

    public function getModuleId(): string
    {
        return $this->moduleId;
    }

    public function getIsRequired(): int
    {
        return $this->isRequired;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }
}

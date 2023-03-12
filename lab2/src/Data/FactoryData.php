<?php
declare(strict_types=1);

namespace App\Data;

class FactoryData
{
    /**
     * @param string[] $products
     */
    public function __construct(
        private string $fullName,
        private string $shortName,
        private string $legalAddress,
        private string $actualAddress,
        private string $processingDepth,
        private string $registerInfo,
        private string $statusText,
        private array $products
    ) {}

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function getShortName(): string
    {
        return $this->shortName;
    }

    public function getLegalAddress(): string
    {
        return $this->legalAddress;
    }

    public function getActualAddress(): string
    {
        return $this->actualAddress;
    }

    public function getProcessingDepth(): string
    {
        return $this->processingDepth;
    }

    public function getRegisterInfo(): string
    {
        return $this->registerInfo;
    }
    public function getStatusText(): string
    {
        return $this->statusText;
    }

    /**
     * @return string[]
     */
    public function getProducts(): array
    {
        return $this->products;
    }
}

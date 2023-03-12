<?php
declare(strict_types=1);

namespace App\Controller\Request;

class RequestValidationException extends \InvalidArgumentException
{
    /**
     * @var array<string,string>
     */
    private array $fieldErrors;

    /**
     * @param array<string,string> $fieldErrors - maps field name to error message
     */
    public function __construct(array $fieldErrors)
    {
        $this->fieldErrors = $fieldErrors;
        parent::__construct(self::buildMessage($fieldErrors));
    }

    /**
     * @return array<string,string> - maps field name to error message
     */
    public function getFieldErrors(): array
    {
        return $this->fieldErrors;
    }

    private static function buildMessage(array $fieldErrors): string
    {
        $errors = [];
        foreach ($fieldErrors as $fieldName => $error)
        {
            $errors[] = "$fieldName: $error";
        }
        return implode('. ', $errors);
    }
}

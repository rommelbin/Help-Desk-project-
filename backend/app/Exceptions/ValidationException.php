<?php
declare(strict_types=1);

namespace App\Exceptions;

use Throwable;

class ValidationException extends \Exception
{
    protected array $validation_error;
    public function __construct($message = "Ошибка валидации", $code = 200, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return array
     */
    public function getValidationError(): array
    {
        return $this->validation_error;
    }

    /**
     * @param array $validation_error
     */
    public function setValidationError(array $validation_error): void
    {
        $this->validation_error = $validation_error;
    }

}

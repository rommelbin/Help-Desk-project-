<?php

namespace App\Exceptions;

use Exception;

class CodeException extends Exception
{
    /**
     * @param mixed $message
     */
    /**
     * @return mixed
     */
    public function setMessage($message): void
    {
        $this->message = $message;
    }
    public function getCodeMessage(): string
    {
        return $this->message;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code): void
    {
        $this->code = $code;
    }
    public function getCodeCode(): string
    {
        return $this->code;
    }
}

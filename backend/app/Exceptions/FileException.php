<?php

namespace App\Exceptions;

use Exception;

class FileException extends Exception
{
    /**
     * @param mixed $message
     */
    /**
     * @return mixed
     */
    protected $message = "Больше 5 файлов";
    public function setMessage($message): void
    {
        $this->message = $message;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code): void
    {
        $this->code = $code;
    }
}

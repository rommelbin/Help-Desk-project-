<?php

namespace App\Validators;
use App\Exceptions\ValidationException;
use Exception;
use Illuminate\Support\Facades\Validator;

class CustomValidator
{
    protected array $attributes;
    protected \Illuminate\Contracts\Validation\Validator $validator;

    /**
     * @throws Exception
     */
    public function __construct(array $attributes, array $rules)
    {
        $this->validator = Validator::make($attributes, $rules);
        $this->showErrors()
             ->validateAttributes();
    }


    /**
     * @throws Exception
     */
    public function showErrors(): CustomValidator
    {
        if ($this->validator->fails()) {
            $validation_exception = new ValidationException();
            $validation_exception->setValidationError($this->validator->errors()->toArray());
            throw $validation_exception;
        }
        return $this;
    }

    public function validateAttributes(): CustomValidator
    {
        $this->attributes = $this->validator->validated();
        return $this;
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }
}

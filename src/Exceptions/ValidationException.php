<?php

class ValidationException extends Exception
{
    private $errors;

    public function __construct($message, array $errors = array(), $code = 422)
    {
        parent::__construct($message, $code);
        $this->errors = $errors;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
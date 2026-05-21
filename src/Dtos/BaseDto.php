<?php

abstract class BaseDto
{
    protected $attributes;

    public function __construct(array $attributes = array())
    {
        $this->attributes = $attributes;
    }

    public function toArray()
    {
        return $this->attributes;
    }
}
<?php

/**
 * Represents a context parameter's value
 */
class ContextParameterValue
{
    private mixed $value;
    public function __construct(mixed $value) {
        $this->value = $value;
    }

    public function set(mixed $value): void
    {
        $this->value = $value;
    }

    public function isArray(): bool {
        return is_array($this->value);
    }

    public function get(): mixed {
        return $this->value;
    }
}
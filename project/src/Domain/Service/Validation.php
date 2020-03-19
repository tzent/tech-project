<?php

declare(strict_types=1);

namespace PS\Domain\Service;

class Validation
{
    /**
     * @var array
     */
    private array $fields;

    /**
     * @var array
     */
    private array $validators;

    /**
     * @var array
     */
    private array $errors;

    /**
     * Validation constructor.
     */
    public function __construct(array $fields, array $validators)
    {
        $this->fields = $fields;
        $this->validators = $validators;
        $this->errors = [];
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function validate(): bool
    {
        foreach ($this->validators as $field => $validators) {
            $this->validateField($field, explode(';', $validators));
        }

        return empty($this->errors);
    }

    private function validateField(string $field, array $validators): void
    {
        foreach ($validators as $validator) {
            if (false !== strpos($validator, ':')) {
                list($method, $params) = explode(':', $validator);
                $params = json_decode($params);
                $this->{$method}($field, $params);
            } else {
                $this->{$validator}($field);
            }
        }
    }

    private function required(string $field): void
    {
        if (!isset($this->fields[$field]) || '' === $this->fields[$field]) {
            $this->errors[] = sprintf('Field %s is required', $field);
        }
    }

    /**
     * @throws \Exception
     */
    private function date(string $field): void
    {
        if (isset($this->fields[$field])
            && (
                !preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $this->fields[$field])
                || strtotime($this->fields[$field]) > time()
            )
        ) {
            $this->errors[] = sprintf('Field %s is invalid date', $field);
        }
    }

    private function integer(string $field): void
    {
        if (isset($this->fields[$field]) && !ctype_digit(strval($this->fields[$field]))) {
            $this->errors[] = sprintf('Field %s is invalid integer', $field);
        }
    }

    private function positive(string $field): void
    {
        if (isset($this->fields[$field]) && $this->fields[$field] <= 0) {
            $this->errors[] = sprintf('Field %s has to be positive', $field);
        }
    }

    private function numeric(string $field): void
    {
        if (isset($this->fields[$field]) && !is_numeric($this->fields[$field])) {
            $this->errors[] = sprintf('Field %s has to be numeric', $field);
        }
    }

    private function in(string $field, array $choice): void
    {
        if (isset($this->fields[$field]) && !in_array($this->fields[$field], $choice, true)) {
            $this->errors[] = sprintf('Field %s has to be one of (%s)', $field, implode(',', $choice));
        }
    }
}

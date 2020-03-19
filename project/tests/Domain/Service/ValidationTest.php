<?php

declare(strict_types=1);

namespace PS\Tests\Domain\Service;

use PHPUnit\Framework\TestCase;
use PS\Domain\Service\Validation;

class ValidationTest extends TestCase
{
    /**
     * @param array $fields
     * @param array $validators
     *
     * @testWith    [{"testField": 1}, {"testField": "required"}]
     *              [{"testField": 0}, {"testField": "required"}]
     *              [{"testField": "2020-03-14"}, {"testField": "date"}]
     *              [{"testField": "123"}, {"testField": "integer"}]
     *              [{"testField": 11}, {"testField": "integer"}]
     *              [{"testField": 0}, {"testField": "integer"}]
     *              [{"testField": 11}, {"testField": "positive"}]
     *              [{"testField": "123"}, {"testField": "positive"}]
     *              [{"testField": 11.00}, {"testField": "numeric"}]
     *              [{"testField": "123"}, {"testField": "numeric"}]
     *              [{"testField": "123.12"}, {"testField": "numeric"}]
     *              [{"testField": 1}, {"testField": "in:[1,2,3]"}]
     */
    public function testValidator(array $fields, array $validators): void
    {
        $validation = new Validation($fields, $validators);
        $isValid = $validation->validate();

        $this->assertIsBool($isValid);
        $this->assertTrue($isValid);
        $this->assertEmpty($validation->getErrors());
    }

    /**
     * @param array $fields
     * @param array $validators
     *
     * @testWith    [{"testField": ""}, {"testField": "required"}]
     *              [{"testField": null}, {"testField": "required"}]
     *              [{"testField": "2222-03-14"}, {"testField": "date"}]
     *              [{"testField": "14-03-2019"}, {"testField": "date"}]
     *              [{"testField": "14 Jan 2019"}, {"testField": "date"}]
     *              [{"testField": ""}, {"testField": "date"}]
     *              [{"testField": "0123 "}, {"testField": "integer"}]
     *              [{"testField": ""}, {"testField": "integer"}]
     *              [{"testField": "0"}, {"testField": "positive"}]
     *              [{"testField": 0}, {"testField": "positive"}]
     *              [{"testField": -123}, {"testField": "positive"}]
     *              [{"testField": "-123"}, {"testField": "positive"}]
     *              [{"testField": ""}, {"testField": "positive"}]
     *              [{"testField": "test"}, {"testField": "numeric"}]
     *              [{"testField": "0x123"}, {"testField": "numeric"}]
     *              [{"testField": 0}, {"testField": "in:[1,2,3]"}]
     *              [{"testField": "2"}, {"testField": "in:[1,2,3]"}]
     */
    public function testValidationFails(array $fields, array $validators): void
    {
        $validation = new Validation($fields, $validators);
        $isValid = $validation->validate();
        $errors = $validation->getErrors();

        $this->assertIsBool($isValid);
        $this->assertFalse($isValid);
        $this->assertNotEmpty($errors);
        $this->assertCount(1, $errors);
    }
}

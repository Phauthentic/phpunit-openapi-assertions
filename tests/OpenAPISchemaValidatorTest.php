<?php

declare(strict_types=1);

namespace Phauthentic\PHPUnit\OpenAPIValidator\Tests;

use JsonException;
use Nyholm\Psr7\Request;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Phauthentic\PHPUnit\OpenAPIValidator\OpenAPISchemaValidator;
use Phauthentic\PHPUnit\OpenAPIValidator\OpenAPISchemaValidationFailedException;

class OpenAPISchemaValidatorTest extends TestCase
{
    private OpenAPISchemaValidator $validator;
    private string $schemaPath = __DIR__ . '/OpenAPI.yaml';

    protected function setUp(): void
    {
        $this->validator = new OpenAPISchemaValidator($this->schemaPath);
    }

    /**
     * @throws OpenAPISchemaValidationFailedException
     */
    public function testValidateRequestPasses(): void
    {
        $request = new Request(
            method:'GET',
            uri: '/items',
        );

        $this->validator = new OpenAPISchemaValidator($this->schemaPath);
        $this->validator->validateRequest($request);

        $this->assertTrue(true); // If no exception is thrown, the test passes.
    }

    #[Test]
    public function testValidateRequestFails(): void
    {
        $request = new Request(
            method:'GET',
            uri: '/does-not-exist',
        );

        $this->expectException(OpenAPISchemaValidationFailedException::class);
        $this->expectExceptionMessage('OpenAPI spec contains no such operation [/does-not-exist]');

        $this->validator->validateRequest($request); // Use an invalid path
    }

    /**
     * @throws OpenAPISchemaValidationFailedException
     * @throws JsonException
     */
    #[Test]
    public function testValidateResponsePasses(): void
    {
        $response = new Response(
            status: 200,
            headers: [
                'content-type' => 'application/json',
            ],
            body: json_encode([[
                'id' => 1,
                'name' => 'Item 1',
            ]], JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT),
        );

        $this->validator->validateResponse($response, '/items', 'get'); // Use valid path and method

        $this->assertTrue(true); // If no exception is thrown, the test passes.
    }

    #[Test]
    public function testValidateResponseFails(): void
    {
        $response = new Response(
            status: 200,
            headers: [
                'Content-Type' => 'application/json',
            ]
        );

        $this->expectException(OpenAPISchemaValidationFailedException::class);
        $this->expectExceptionMessage('OpenAPI spec contains no such operation [/does-not-exist]');

        $this->validator->validateResponse($response, '/does-not-exist', 'GET'); // Use a valid path but simulate failure
    }
}

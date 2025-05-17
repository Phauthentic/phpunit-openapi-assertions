<?php

declare(strict_types=1);

namespace Phauthentic\PHPUnit\OpenAPIValidator\Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Phauthentic\PHPUnit\OpenAPIValidator\OpenAPISchemaValidationFailedException;
use Phauthentic\PHPUnit\OpenAPIValidator\OpenAPISymfonySchemaValidator;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class OpenAPISymfonySchemaValidatorTest extends TestCase
{
    private OpenAPISymfonySchemaValidator $validator;
    private string $schemaPath = __DIR__ . '/OpenAPI.yaml';

    protected function setUp(): void
    {
        $this->validator = new OpenAPISymfonySchemaValidator($this->schemaPath);
    }

    #[Test]
    public function testValidateSymfonyRequestPasses(): void
    {
        $request = SymfonyRequest::create('/items', 'GET');

        $this->validator->validateRequest($request);

        $this->assertTrue(true); // If no exception is thrown, the test passes.
    }

    #[Test]
    public function testValidateSymfonyRequestFails(): void
    {
        $request = SymfonyRequest::create('/does-not-exist', 'GET');

        $this->expectException(OpenAPISchemaValidationFailedException::class);
        $this->expectExceptionMessage('OpenAPI spec contains no such operation [/does-not-exist]');

        $this->validator->validateRequest($request);
    }

    #[Test]
    public function testValidateSymfonyResponsePasses(): void
    {
        $response = new SymfonyResponse(
            json_encode([[
                'id' => 1,
                'name' => 'Item 1',
            ]], JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT),
            200,
            ['Content-Type' => 'application/json']
        );

        $this->validator->validateResponse($response, '/items', 'get');

        $this->assertTrue(true); // If no exception is thrown, the test passes.
    }

    #[Test]
    public function testValidateSymfonyResponseFails(): void
    {
        $response = new SymfonyResponse('', 200, ['Content-Type' => 'application/json']);

        $this->expectException(OpenAPISchemaValidationFailedException::class);
        $this->expectExceptionMessage('OpenAPI spec contains no such operation [/does-not-exist]');

        $this->validator->validateResponse($response, '/does-not-exist', 'GET');
    }
}

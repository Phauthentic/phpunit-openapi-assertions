<?php

declare(strict_types=1);

namespace Phauthentic\PHPUnit\OpenAPIValidator;

use PHPUnit\Framework\Exception;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

trait OpenAPIValidatorTrait
{
    protected static ?OpenAPISchemaValidatorInterface $openAPISchemaValidator;

    protected static function getOpenAPISchemaValidator(): OpenAPISchemaValidatorInterface
    {
        if (null === self::$openAPISchemaValidator) {
            throw new Exception(
                'OpenAPISchemaValidator is not set. '
                . 'Call self::setOpenAPISchemaValidator() before using this method.'
            );
        }

        return self::$openAPISchemaValidator;
    }

    protected static function setOpenAPISchemaValidator(OpenAPISchemaValidatorInterface $validator): void
    {
        self::$openAPISchemaValidator = $validator;
    }

    protected static function assertRequestMatchesOpenAPISchema(RequestInterface $request): void
    {
        try {
            self::getOpenAPISchemaValidator()->validateRequest($request);
        } catch (OpenAPISchemaValidationFailedException $exception) {
            self::fail('OpenAPI request validation failed: ' . $exception->getMessage());
        }
    }

    protected static function assertResponseMatchesOpenAPISchema(
        ResponseInterface $response,
        string $path,
        string $method,
    ): void {
        try {
            self::getOpenAPISchemaValidator()->validateResponse(
                response: $response,
                path: $path,
                method: $method
            );
        } catch (OpenAPISchemaValidationFailedException $exception) {
            self::fail('OpenAPI response validation failed: ' . $exception->getMessage());
        }
    }
}

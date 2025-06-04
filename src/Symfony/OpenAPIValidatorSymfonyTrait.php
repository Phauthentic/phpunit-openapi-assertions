<?php

declare(strict_types=1);

namespace Phauthentic\PHPUnit\OpenAPIValidator\Symfony;

use Phauthentic\PHPUnit\OpenAPIValidator\OpenAPISchemaValidationFailedException;
use Phauthentic\PHPUnit\OpenAPIValidator\OpenAPISchemaValidatorInterface;
use PHPUnit\Framework\Exception;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @phpstan-ignore-next-line
 */
trait OpenAPIValidatorSymfonyTrait
{
    protected static ?OpenAPISymfonySchemaValidator $openAPISchemaValidator;

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

    protected static function setOpenAPISchemaValidator(OpenAPISymfonySchemaValidator $validator): void
    {
        self::$openAPISchemaValidator = $validator;
    }

    protected static function assertRequestMatchesOpenAPISchema(?RequestInterface $request = null): void
    {
        if (null === $request) {
            self::assertWebTestCase();
            $request = self::getClient()->getRequest();
        }

        try {
            self::getOpenAPISchemaValidator()->validateRequest($request);
        } catch (OpenAPISchemaValidationFailedException $exception) {
            self::fail('OpenAPI request validation failed: ' . $exception->getMessage());
        }
    }

    protected static function assertResponseMatchesOpenAPISchema(
        ?ResponseInterface $response,
        string $path,
        string $method,
    ): void {
        if (null === $response) {
            self::assertWebTestCase();
            $response = self::getClient()->getResponse();
        }

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

    private static function assertWebTestCase(): void
    {
        if (!is_subclass_of(static::class, WebTestCase::class)) {
            throw new RuntimeException(
                'OpenAPIValidatorSymfonyTrait can only be used in a class that extends '
                . 'Symfony\Bundle\FrameworkBundle\Test\WebTestCase.'
            );
        }
    }
}

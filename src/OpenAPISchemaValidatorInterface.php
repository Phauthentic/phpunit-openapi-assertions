<?php

declare(strict_types=1);

namespace Phauthentic\PHPUnit\OpenAPIValidator;

use Psr\Http\Message\RequestInterface as PsrRequestInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

/**
 * Helper Class to validate requests and responses against an OpenAPI schema.
 *
 * - Converts Symfony HTTP Foundation requests and responses to PSR-7 requests and responses.
 * - Validates requests and responses against the OpenAPI schema.
 */
interface OpenAPISchemaValidatorInterface
{
    /**
     * Validates a PSR-7 request object against the OpenAPI schema.
     *
     * @param PsrRequestInterface $request The PSR-7 request to validate.
     *
     * @throws OpenAPISchemaValidationFailedException If the request validation fails.
     */
    public function validateRequest(PsrRequestInterface $request): void;

    /**
     * Validates a PSR-7 response object against the OpenAPI schema.
     *
     * @param PsrResponseInterface $response The PSR-7 response to validate.
     * @param string $path The OpenAPI path to validate against.
     * @param string $method The HTTP method (e.g., GET, POST) to validate against.
     *
     * @throws OpenAPISchemaValidationFailedException If the response validation fails.
     */
    public function validateResponse(PsrResponseInterface $response, string $path, string $method): void;
}

<?php

declare(strict_types=1);

namespace Phauthentic\PHPUnit\OpenAPIValidator;

use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\RequestInterface as PsrRequestInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * Helper Class to validate requests and responses against an OpenAPI schema.
 *
 * - Converts Symfony HTTP Foundation requests and responses to PSR-7 requests and responses.
 * - Validates requests and responses against the OpenAPI schema.
 */
class OpenAPISymfonySchemaValidator extends OpenAPISchemaValidator
{
    protected PsrHttpFactory $psrHttpFactory;

    public function __construct(
        readonly string $openApiSchemaPath,
    ) {
        $psr17Factory = new Psr17Factory();
        $this->psrHttpFactory = new PsrHttpFactory($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);

        parent::__construct($openApiSchemaPath);
    }

    /**
     * @inheritDoc
     */
    public function validateRequest(SymfonyRequest | PsrRequestInterface $request): void
    {
        if ($request instanceof SymfonyRequest) {
            $request = $this->psrHttpFactory->createRequest($request);
        }

        parent::validateRequest($request);
    }

    /**
     * @inheritDoc
     */
    public function validateResponse(
        SymfonyResponse | PsrResponseInterface $response,
        string $path,
        string $method,
    ): void {
        if ($response instanceof SymfonyResponse) {
            $response = $this->psrHttpFactory->createResponse($response);
        }

        parent::validateResponse($response, $path, $method);
    }
}

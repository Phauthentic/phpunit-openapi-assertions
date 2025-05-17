<?php

declare(strict_types=1);

namespace Phauthentic\PHPUnit\OpenAPIValidator;

use League\OpenAPIValidation\PSR7\Exception\ValidationFailed;
use League\OpenAPIValidation\PSR7\OperationAddress;
use League\OpenAPIValidation\PSR7\RequestValidator;
use League\OpenAPIValidation\PSR7\ResponseValidator;
use League\OpenAPIValidation\PSR7\ValidatorBuilder;
use Psr\Http\Message\RequestInterface as PsrRequestInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

/**
 * Helper Class to validate requests and responses against an OpenAPI schema.
 *
 * - Converts Symfony HTTP Foundation requests and responses to PSR-7 requests and responses.
 * - Validates requests and responses against the OpenAPI schema.
 */
class OpenAPISchemaValidator implements OpenAPISchemaValidatorInterface
{
    protected ResponseValidator $responseValidator;
    protected RequestValidator $requestValidator;
    protected ValidatorBuilder $validator;

    public function __construct(
        private readonly string $openApiSchemaPath,
    ) {
        $this->validator = (new ValidatorBuilder())->fromYamlFile($this->openApiSchemaPath);
        $this->responseValidator = $this->validator->getResponseValidator();
        $this->requestValidator = $this->validator->getRequestValidator();
    }

    /**
     * @inheritDoc
     */
    public function validateRequest(PsrRequestInterface $request): void
    {
        try {
            $this->requestValidator->validate(
                request: $request
            );
        } catch (ValidationFailed $exception) {
            throw new OpenAPISchemaValidationFailedException(
                message: $exception->getMessage(),
                previous: $exception,
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function validateResponse(
        PsrResponseInterface $response,
        string $path,
        string $method,
    ): void {
        try {
            $this->responseValidator->validate(
                opAddr: new OperationAddress($path, $method),
                response: $response
            );
        } catch (ValidationFailed $exception) {
            throw new OpenAPISchemaValidationFailedException(
                message: $exception->getMessage(),
                previous: $exception,
            );
        }
    }
}

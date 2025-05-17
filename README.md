# Open API Validation for PHPUnit Tests

This small library will make it very easy for you to validate your request and response objects against a given Open API Schemas.

Under the hood `league/openapi-psr7-validator` is used but abstracted in a way that it can be replaced with any other implementation.

## Testing the OpenAPI Schema

* Your test case must use the [OpenAPIValidatorTrait](src/OpenAPIValidatorTrait.php) or extend [App\Tests\ApiTestCase](../tests/ApiTestCase.php)
* Your test case must instantiate and set the [App\Tests\OpenAPISchemaValidator](../tests/OpenAPISchemaValidator.php) with the right schema. If you don't do this, the next steps will fail with an exception.
* Call `assertRequestMatchesOpenAPISchema($request)` to validate the request against the OpenAPI schema.
* Call `assertResponseMatchesOpenAPISchema($response)` to validate the response against the OpenAPI schema.

### Example:

```php
use Phauthentic\PHPUnit\OpenAPIValidator\OpenAPIValidatorTrait;

class MyTestCase extends TestCase 
{
    use OpenAPIValidatorTrait;

    public function setUp(): void
    {
        parent::setUp();
    
        // Load your OpenAPI schema
        self::setOpenAPISchemaValidator = new OpenAPISchemaValidator(
            'path/to/openapi.yaml',
        );
    }
    
    public function testSomeAPIIntegration(): void
    {
        // Create a client and make a request or whatever your framework
        // provides you to make such calls.
        $client = $this->createClient();
        $client->request('POST', '/api/v1/products', [
            'productName' => 'PHP',
        ]);

        // Assert the request and response against the OpenAPI schema
        self::assertRequestMatchesOpenAPISchema($client->getRequest());
        self::assertResponseMatchesOpenAPISchema($client->getResponse());
            path: '/api/v1/follows',
            method: 'post'
        );
    }
}
```

## Symfony Support

Symfony does not support the PSR-7 interface for requests and responses. This means that you cannot use the OpenAPISchemaValidator directly in your Symfony tests. This package provides a workaround for this limitation by using the Symfony Bridge for PHPUnit. This bridge provides a way to use the PSR-7 interface in your Symfony tests.

You need to add those dependencies to your project via Composer:

* nyholm/psr7
* symfony/phpunit-bridge

Instead of using the OpenAPISchemaValidator use the OpenAPISymfonySchemaValidator in your test case.

```php
self::setOpenAPISchemaValidator = new OpenAPISymfonySchemaValidator(
    'path/to/openapi.yaml',
);
```

## License

This bundle is under the [MIT license](LICENSE).

Copyright Florian Krämer
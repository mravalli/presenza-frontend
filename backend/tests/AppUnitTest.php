<?php declare(strict_types = 1);

namespace Neuro3\Presenza;

use Exception;
use Neuro3\Exceptions\InvalidDataException;
use League\Route\Http\Exception as HttpException;
use League\Route\Router;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\ServerRequest;

class AppUnitTest extends TestCase
{
    private App $SUT;

    /**
     * @var Router & MockObject
     */
    private $router;

    protected function setUp(): void
    {
        $this->router = $this->createMock(Router::class);
        $this->SUT = new App($this->router);
    }

    /**
     * @dataProvider exceptionProvider
     * @throws Exception
     */
    public function testItHandlesHttpAndDomainExceptions(Exception $exception): void
    {
        $request = new ServerRequest();

        $this->router
            ->method('dispatch')
            ->willThrowException($exception)
        ;

        $response = $this->SUT->handle($request);
        $expectedStatusCode = $exception instanceof HttpException ? $exception->getStatusCode(): 400;
        assertSame($expectedStatusCode, $response->getStatusCode());
        assertInstanceOf(JsonResponse::class, $response); /** @var JsonResponse $response */
        $payload = $response->getPayload();
        assertArrayHasKey('error', $payload);
        assertEquals(exception_to_array($exception), $payload['error']);
    }

    /**
     * @return array[]
     */
    public function exceptionProvider(): array
    {
        return [
            [ new HttpException(401, 'foo') ],
            [ new InvalidDataException() ],
        ];
    }
}

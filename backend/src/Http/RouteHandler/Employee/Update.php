<?php declare(strict_types=1);

namespace Neuro3\Presenza\Http\RouteHandler\Employee;

use Neuro3\Presenza\Http\RouteHandler;
use Neuro3\Presenza\Model\InvalidDataException;
use Neuro3\Presenza\Model\EmployeeDataMapper;
use League\Route\Http\Exception\BadRequestException;
use League\Route\Http\Exception\NotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
// use Ramsey\Uuid\Uuid;
use Laminas\Diactoros\Response\JsonResponse;

class Update implements RouteHandler
{
    /**
     * @var EmployeeDataMapper
     */
    private $employeeDataMapper;

    public function __construct(EmployeeDataMapper $employeeDataMapper)
    {
        $this->employeeDataMapper = $employeeDataMapper;
    }

    /**
     * @param Request $request
     * @param string[] $args
     *
     * @return Response
     *
     * @throws BadRequestException
     * @throws InvalidDataException
     * @throws NotFoundException
     */
    public function __invoke(Request $request, array $args): Response
    {
        // if (! Uuid::isValid($args['id'])) {
        //     throw new BadRequestException('Invalid UUID');
        // }

        $item = $this->employeeDataMapper->find((int) $args['id']);

        if ($item === null) {
            throw new NotFoundException('Resource not found');
        }

        $requestBody = $request->getParsedBody();

        if (! is_array($requestBody)) {
            throw new BadRequestException('Invalid request body');
        }

        $item->updateFromArray($requestBody);

        $this->employeeDataMapper->update($item);

        return new JsonResponse($item, 200);
    }
}

<?php declare(strict_types=1);

namespace Neuro3\Presenza\Http\RouteHandler\Employee;

use League\Route\Http;
use Neuro3\Presenza\Http\RouteHandler;
use Neuro3\Presenza\Model\EmployeeDataMapper;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
//use Ramsey\Uuid\Uuid;
use Laminas\Diactoros\Response\EmptyResponse;

class Delete implements RouteHandler
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
     * @throws Http\Exception\BadRequestException
     * @throws Http\Exception\NotFoundException
     */
    public function __invoke(Request $request, array $args): Response
    {
        // if (! Uuid::isValid($args['id'])) {
        //     throw new Http\Exception\BadRequestException('Invalid UUID');
        // }

        $item = $this->employeeDataMapper->find((int) $args['id']);

        if ($item === null) {
            throw new Http\Exception\NotFoundException('Resource not found');
        }

        $this->employeeDataMapper->delete((int) $args['id']);

        return new EmptyResponse();
    }
}

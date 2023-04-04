<?php declare(strict_types=1);

namespace Neuro3\Presenza\Http\RouteHandler\Employee;

use Neuro3\Presenza\Http\RouteHandler;
use Neuro3\Presenza\Model\EmployeeDataMapper;
use League\Route\Http\Exception\BadRequestException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Laminas\Diactoros\Response\JsonResponse;

class Lists implements RouteHandler
{
    private const MAX_PAGE_SIZE = 20;
    private EmployeeDataMapper $employeeDataMapper;

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
     */
    public function __invoke(Request $request, array $args): Response
    {
        $query  = $request->getQueryParams();
        $search = $query['search'] ?? '';
        $sortBy = isset($query['sort_by']) ? preg_replace('/\./', ' ', $query['sort_by']) : 'firstname ASC';

        $page = (int) ($query['page'] ?? 1);
        if ($page < 1) {
            throw new BadRequestException('Page must be greater than 0');
        }

        $pageSize = (int) ($query['pageSize'] ?? EmployeeDataMapper::DEFAULT_PAGE_SIZE);

        if ($pageSize < 1 || $pageSize > self::MAX_PAGE_SIZE) {
            throw new BadRequestException(sprintf('Page size must be between 1 and %s', self::MAX_PAGE_SIZE));
        }

        list($totalResults, $totalPages) = $this->employeeDataMapper->countPages($search, $pageSize);
        $items = $this->employeeDataMapper->getAll($search, $sortBy, $page, $pageSize);

        // $totalResults = count($items);

        $prev = $page > 1 ? min($page - 1, $totalPages) : null;
        $next = $page < $totalPages ? $page + 1 : null;

        return new JsonResponse(compact('items', 'totalPages', 'totalResults', 'pageSize', 'prev', 'next', 'page'), 200);
    }
}

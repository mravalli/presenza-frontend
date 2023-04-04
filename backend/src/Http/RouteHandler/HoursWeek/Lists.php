<?php

/**
 * This file is part of the "Presenza"
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Mario Ravalli <mario@raval.li>
 * @license https://www.lareclameitalia.com/software/Presenza/license
 * @link https://www.lareclameitalia.com/software/Presenza
 *
 * @Author: Mario Ravalli
 * @Date:   2020-11-08 13:23:49
 * @Last Modified by:   Mario Ravalli
 * @Last Modified time: 2020-11-08 13:26:18
 */
declare(strict_types=1);

namespace Neuro3\Presenza\Http\RouteHandler\HoursWeek;

use Neuro3\Presenza\Http\RouteHandler;
use Neuro3\Presenza\Model\HoursWeekDataMapper;
use League\Route\Http\Exception\BadRequestException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Laminas\Diactoros\Response\JsonResponse;

class Lists implements RouteHandler
{
    private const MAX_PAGE_SIZE = 100;

    /**
     * @var HoursWeekDataMapper
     */
    private $hoursWeekDataMapper;

    public function __construct(HoursWeekDataMapper $hoursWeekDataMapper)
    {
        $this->hoursWeekDataMapper = $hoursWeekDataMapper;
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

        $page = (int) ($query['page'] ?? 1);
        if ($page < 1) {
            throw new BadRequestException('Page must be greater than 0');
        }

        $pageSize = (int) ($query['pageSize'] ?? HoursWeekDataMapper::DEFAULT_PAGE_SIZE);

        if ($pageSize < 1 || $pageSize > self::MAX_PAGE_SIZE) {
            throw new BadRequestException(sprintf('Page size must be between 1 and %s', self::MAX_PAGE_SIZE));
        }

        $totalPages = $this->hoursWeekDataMapper->countPages($search, $pageSize);
        $items      = $this->hoursWeekDataMapper->getAll($search, $page, $pageSize);

        $totalResults = count($items);

        $prev = $page > 1 ? min($page - 1, $totalPages) : null;
        $next = $page < $totalPages ? $page + 1 : null;

        return new JsonResponse(compact('items', 'totalPages', 'totalResults', 'pageSize', 'prev', 'next'), 200);
    }
}

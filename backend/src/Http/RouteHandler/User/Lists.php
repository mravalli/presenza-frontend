<?php

/**
 * This file is part of the project "Presenza"
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author:    Mario Ravalli <mario@raval.li>
 * @copyright: Copyright (c) Mario Ravalli <mario@raval.li>
 * @license:   GNU General Public License v3.0 or later
 * 
 * Creation Date: 2020-12-17 18:15:43
 * Modified by:   Mario Ravalli
 * Last Modified: 2020-12-17 18:33:53
 */
declare(strict_types=1);

namespace Neuro3\Presenza\Http\RouteHandler\User;

use Neuro3\Presenza\Http\RouteHandler;
use Neuro3\Presenza\Model\UserDataMapper;
use League\Route\Http\Exception\BadRequestException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Laminas\Diactoros\Response\JsonResponse;

class Lists implements RouteHandler
{
    private const MAX_PAGE_SIZE = 100;

    /**
     * @var UserDataMapper
     */
    private $userDataMapper;

    public function __construct(UserDataMapper $userDataMapper)
    {
        $this->userDataMapper = $userDataMapper;
    }

    /**
     * @param Request $request
     * @param string[] $args
     *
     * @return Response
     *
     * @throws Http\Exception\BadRequestException
     */
    public function __invoke(Request $request, array $args): Response
    {
        $query  = $request->getQueryParams();
        $search = $query['search'] ?? '';

        $page = (int) ($query['page'] ?? 1);
        if ($page < 1) {
            throw new BadRequestException('Page must be greater than 0');
        }

        $pageSize = (int) ($query['pageSize'] ?? UserDataMapper::DEFAULT_PAGE_SIZE);

        if ($pageSize < 1 || $pageSize > self::MAX_PAGE_SIZE) {
            throw new BadRequestException(sprintf('Page size must be between 1 and %s', self::MAX_PAGE_SIZE));
        }

        $totalPages = $this->userDataMapper->countPages($search, $pageSize);
        $items      = $this->userDataMapper->getAll($search, $page, $pageSize);

        $totalResults = count($items);

        $prev = $page > 1 ? min($page - 1, $totalPages) : null;
        $next = $page < $totalPages ? $page + 1 : null;

        return new JsonResponse(compact('items', 'totalPages', 'totalResults', 'pageSize', 'prev', 'next'), 200);
    }
}

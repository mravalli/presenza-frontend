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
 * Creation Date: 2020-10-07 21:04:14
 * Modified by:   Mario Ravalli
 * Last Modified: 2020-11-18 16:04:52
 */
declare(strict_types=1);

namespace Neuro3\Presenza\Http\RouteHandler\Employee;

use League\Route\Http;
use Neuro3\Presenza\Http\RouteHandler;
use Neuro3\Presenza\Model\EmployeeDataMapper;
use Neuro3\Presenza\Model\HoursWeekDataMapper;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Laminas\Diactoros\Response\JsonResponse;

class Read implements RouteHandler
{
    /**
     * @var EmployeeDataMapper
     */
    private $employeeDataMapper;
    /**
     * @var HoursWeekDataMapper
     */
    private $hoursWeekDataMapper;

    public function __construct(EmployeeDataMapper $employeeDataMapper, HoursWeekDataMapper $hoursWeekDataMapper)
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
        $item = $this->employeeDataMapper->find((int) $args['id']);

        if ($item === null) {
            throw new Http\Exception\NotFoundException('Resource not found');
        }

        return new JsonResponse(['employee' => $item]);
    }
}

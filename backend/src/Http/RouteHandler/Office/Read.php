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
 * Creation Date: 2020-10-30 18:09:11
 * Modified by:   Mario Ravalli
 * Last Modified: 2021-07-29 01:30:38
 */
declare(strict_types=1);

namespace Neuro3\Presenza\Http\RouteHandler\Office;

use League\Route\Http;
use Neuro3\Presenza\Http\RouteHandler;
use Neuro3\Presenza\Model\EmployeeDataMapper;
use Neuro3\Presenza\Model\HoursWeekDataMapper;
use Neuro3\Presenza\Model\OfficeDataMapper;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Laminas\Diactoros\Response\JsonResponse;

class Read implements RouteHandler
{
    /**
     * @var OfficeDataMapper
     */
    private $officeDataMapper;
    
    /**
     * @var EmployeeDataMapper
     */
    private $employeeDataMapper;

    public function __construct(OfficeDataMapper $officeDataMapper, EmployeeDataMapper $employeeDataMapper)
    {
        $this->employeeDataMapper = $employeeDataMapper;
        $this->officeDataMapper = $officeDataMapper;
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
        $office = $this->officeDataMapper->find((int) $args['id']);

        if ($office === null) {
            throw new Http\Exception\NotFoundException('Resource not found');
        }

        $employees = $this->employeeDataMapper->getAll('', 'firstname asc', 1, 1000);

        return new JsonResponse(['office' => $office, 'employees' => $employees]);
    }
}

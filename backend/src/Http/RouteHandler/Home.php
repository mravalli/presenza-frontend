<?php declare(strict_types=1);

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
 * Creation Date: 2020-11-04 10:53:48
 * Modified by:   Mario Ravalli
 * Last Modified: 2021-07-21 12:24:42
 */

namespace Neuro3\Presenza\Http\RouteHandler;

use Exception;
use League\Route\Http\Exception\BadRequestException;
use Neuro3\Exceptions\InvalidDataException;
use DateTime;
use Laminas\Diactoros\Response\JsonResponse;
use Neuro3\Presenza\Http\RouteHandler;
use Neuro3\Presenza\Model\Calendar;
use Neuro3\Presenza\Model\CalendarDataMapper;
use Neuro3\Presenza\Model\OfficeDataMapper;
use Neuro3\Presenza\Model\JustificationDataMapper;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Home implements RouteHandler
{
    private CalendarDataMapper $calendarDataMapper;
    private OfficeDataMapper $officeDataMapper;
    private JustificationDataMapper $justificationDataMapper;

    public function __construct(OfficeDataMapper $officeDataMapper, CalendarDataMapper $calendarDataMapper, JustificationDataMapper $justificationDataMapper)
    {
        $this->calendarDataMapper = $calendarDataMapper;
        $this->officeDataMapper   = $officeDataMapper;
        $this->justificationDataMapper = $justificationDataMapper;
    }

    /**
     * @param Request $request
     * @param string[] $args
     *
     * @return Response
     * @throws Exception
     */
    public function __invoke(Request $request, array $args): Response
    {
        $offices = $this->officeDataMapper->getAll();
        $justifications = $this->justificationDataMapper->getAll();
        $first_day   = new DateTime($request->getQueryParams()['first_day']);
        $last_day    = new DateTime($request->getQueryParams()['last_day']);

        $_employees = [];
        $idx = 0;
        foreach ($offices as $office) {
            foreach ((array) $office->getEmployees() as $employee) {
                if (!is_null($employee->getActualEngagement($first_day, $last_day))) {
                    $_employees[$employee->getId()][$office->getId()] = [
                        'id' => $idx++,
                        'fullname' => $employee->getFullname(),
                        'employee' => $employee,
                        'office' => $office
                    ];
                }
            }
        }

        $employees = [];
        foreach ($_employees as $employee) {
            foreach ($employee as $value) {
                $employees[] = $value;
            }
        }

        return new JsonResponse([
            'offices' => $offices[0]->getEmployees(),
            'employees' => $employees,
            'justifications' => $justifications,
        ]);
    }

    public function getDays(Request $request, array $args): Response
    {
        $first_day   = $request->getQueryParams()['first_day'];
        $last_day    = $request->getQueryParams()['last_day'];
        $office_id   = (int) $request->getQueryParams()['office_id'];
        $employee_id = (int) $request->getQueryParams()['employee_id'];

        $days = $this->calendarDataMapper->getAll($first_day, $last_day, $office_id, $employee_id);
        
        return new JsonResponse($days);
    }

    /**
     * @throws BadRequestException
     * @throws InvalidDataException
     */
    public function setDay(Request $request, array $args): Response
    {
        $requestBody = $request->getParsedBody();
        if (! is_array($requestBody)) {
            throw new BadRequestException('Invalid request body');
        }
        $item = $this->calendarDataMapper->alreadyExist(
            $requestBody['day']['day'],
            $requestBody['officeId'],
            $requestBody['employeeId']
        );
        $row = [
            'day' => $requestBody['day']['day'],
            'hours' => $requestBody['day']['hours'],
            'disease' => $requestBody['day']['disease'],
            'justificationCode' => $requestBody['day']['justificationCode'],
            'officeId' => $requestBody['officeId'],
            'employeeId' => $requestBody['employeeId'],
        ];

        if ($item === null) {
            $item = Calendar::createFromArray($row);
            $this->calendarDataMapper->insert($item);
        } else {
            $item->updateFromArray($row);
            $this->calendarDataMapper->update($item);
        }
        
        return new JsonResponse($item);
    }
}

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
 * Creation Date: 2020-11-27 08:16:26
 * Modified by:   Mario Ravalli
 * Last Modified: 2020-11-27 10:50:37
 */
declare(strict_types=1);

namespace Neuro3\Presenza\Http\RouteHandler;

use \DateInterval;
use \DateTime;
use const Neuro3\Presenza\DATE_FORMAT;
use function Neuro3\Presenza\date_from_string;
use League\Route\Http\Exception\BadRequestException;
use Neuro3\Presenza\Http\RouteHandler;
use Neuro3\Presenza\Model\CalendarDataMapper;
use Neuro3\Presenza\Model\OfficeDataMapper;
use Neuro3\Response\ExcellResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Export
{
    protected string $filename = "presenze";
    protected string $separator = "\t";

    /**
    * @var CalendarDataMapper
    */
    private $calendarDataMapper;

    /**
     * @var OfficeDataMapper
     */
    private $officeDataMapper;

    public function __construct(OfficeDataMapper $officeDataMapper, CalendarDataMapper $calendarDataMapper)
    {
        $this->calendarDataMapper = $calendarDataMapper;
        $this->officeDataMapper   = $officeDataMapper;
    }

    /**
     * @param Request $request
     * @param string[] $args
     *
     * @return Response
     */
    public function __invoke(Request $request, array $args): Response
    {
        $offices = $this->officeDataMapper->getAll();
        $first_day   = new DateTime($request->getQueryParams()['first_day']);
        $last_day    = new DateTime($request->getQueryParams()['last_day']);

        $_employees = [];
        foreach ($offices as $office) {
            foreach ($office->getEmployees() as $employee) {
                if (!is_null($employee->getActualEngagement($first_day, $last_day))) {
                    $days_registered = [];
                    foreach ($this->calendarDataMapper->getAll($first_day->format(DATE_FORMAT), $last_day->format(DATE_FORMAT), $office->getId(), $employee->getId()) as $_day) {
                        $days_registered[$_day->getDayAsString()] = [
                            'hours' => $_day->getHours(),
                            'disease' => $_day->getDisease(),
                            'justification' => $_day->getJustificationCode(),
                        ];
                    }

                    $days = [];
                    $day_iterate = clone $first_day;
                    while ($day_iterate <= $last_day) {
                        $hours = '';
                        $disease = '';
                        $justification = '';
                        if (isset($days_registered[$day_iterate->format(DATE_FORMAT)])) {
                            $hours = $days_registered[$day_iterate->format(DATE_FORMAT)]['hours'];
                            $disease = $days_registered[$day_iterate->format(DATE_FORMAT)]['disease'];
                            $justification = $days_registered[$day_iterate->format(DATE_FORMAT)]['justification'];
                        }
                        $days[$day_iterate->format(DATE_FORMAT)] = [
                            'hours' => $hours,
                            'disease' => $disease,
                            'justification' => $justification,
                        ];
                        $day_iterate->add(new DateInterval('P1D'));
                    }
                    $_employees[$employee->getId()][$office->getId()] = [
                        'fullname' => str_replace($this->separator . "$", "", $employee->getFullname()),
                        'days' => $days,
                        'office' => str_replace($this->separator . "$", "", $office->getName()),
                    ];
                }
            }
        }
        

        $row_header = ['Collaboratore', 'Sede'];
        $rows = [];
        foreach ($_employees as $employee) {
            foreach ($employee as $value) {
                $hours = [];
                $disease = [];
                $justification = [];
                foreach ($value['days'] as $date => $day) {
                    array_push($row_header, $date);
                    $hours[] = $day['hours'];
                    $disease[] = $day['disease'];
                    $justification[] = $day['justification'];
                }
                $rows[] = $value['fullname'] . $this->separator . $value['office'] . $this->separator . implode($this->separator, $hours);
                $rows[] = '' . $this->separator . '' . $this->separator . implode($this->separator, $disease);
                $rows[] = '' . $this->separator . '' . $this->separator . implode($this->separator, $justification);
            }
        }

        return new ExcellResponse(implode($this->separator, $row_header) . "\n" . implode("\n", $rows));
    }
}

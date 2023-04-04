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
 * Creation Date: 2020-11-06 17:24:13
 * Modified by:   Mario Ravalli
 * Last Modified: 2021-08-03 18:05:40
 */
declare(strict_types=1);

namespace Neuro3\Presenza\Model;

use Assert\Assert;
use Closure;
use Neuro3\Model\DbAdapter;
use RuntimeException;

class CalendarDataMapper extends DataMapper
{
    public const DEFAULT_PAGE_SIZE = 30;

    /**
     * @var OfficeDataMapper
     */
    protected $officeDataMapper;

    /**
     * @var EmployeeDataMapper
     */
    protected $employeeDataMapper;

    /**
     * @var JustificationDataMapper
     */
    protected $justificationDataMapper;

    public function __construct(
        DbAdapter $dba,
        OfficeDataMapper $officeDataMapper,
        EmployeeDataMapper $employeeDataMapper,
        JustificationDataMapper $justificationDataMapper
    ) {
        parent::__construct($dba);
        $this->officeDataMapper = $officeDataMapper;
        $this->employeeDataMapper = $employeeDataMapper;
        $this->justificationDataMapper = $justificationDataMapper;
    }

    public function insert(Calendar $calendar): void
    {
        $stmt = $this->link->prepare("INSERT INTO `calendar` (
            `day`,
            `office_id`,
            `employee_id`,
            `hours`,
            `disease`,
            `justification_code`
        ) VALUES (?,?,?,?,?,?)");

        $day             = $calendar->getDayAsString();
        $officeId        = $calendar->getOfficeId();
        $employeeId      = $calendar->getEmployeeId();
        $hours           = $calendar->getHours();
        $disease         = $calendar->getDisease();
        $justificationCode = $calendar->getJustificationCode();

        $stmt->bind_param(
            'siidds',
            $day,
            $officeId,
            $employeeId,
            $hours,
            $disease,
            $justificationCode
        );

        $result = $stmt->execute();
        if ($result === false) {
            throw new RuntimeException('MySQL unable to insert new day in calendar:' + $this->link);
        }
    }

    public function update(Calendar $calendar): void
    {
        $stmt = $this->link->prepare("UPDATE `calendar`
            SET
                `day` = ?,
                `office_id` = ?,
                `employee_id` = ?,
                `hours` = ?,
                `disease` = ?,
                `justification_code` = ?
            WHERE `id` = ?
        ");

        $idx             = $calendar->getId();
        $day             = $calendar->getDayAsString();
        $officeId        = $calendar->getOfficeId();
        $employeeId      = $calendar->getEmployeeId();
        $hours           = $calendar->getHours();
        $disease         = $calendar->getDisease();
        $justificationCode = $calendar->getJustificationCode();

        $stmt->bind_param(
            'siiddsi',
            $day,
            $officeId,
            $employeeId,
            $hours,
            $disease,
            $justificationCode,
            $idx
        );

        $result = $stmt->execute();
        if ($result === false) {
            throw new RuntimeException('MySQL unable to insert new day in calendar');
        }
    }

    /**
     * @return [Calendar]
     */
    public function getAll($fromDate = null, $toDate = null, int $officeId = null, int $employeeId = null): ?array
    {
        $where = [];
        
        if (!is_null($fromDate) && !is_null($toDate)) {
            $where[] = "(`day` BETWEEN '$fromDate' AND '$toDate')";
        } elseif (!is_null($fromDate)) {
            $where[] = "`day` >= '$fromDate'";
        } elseif (!is_null($toDate)) {
            $where[] = "`day` <= '$toDate'";
        }

        if (!is_null($officeId)) {
            $where[] = "`office_id` = $officeId";
        }

        if (!is_null($employeeId)) {
            $where[] = "`employee_id` = $employeeId";
        }

        if (!empty($where)) {
            $where = 'WHERE ' . implode(" AND ", $where);
        } else {
            $where = '';
        }

        $stmt = $this->link->prepare("SELECT
                `id`,
                `day`,
                `office_id` AS `officeId`,
                `employee_id` AS `employeeId`,
                `hours`,
                `disease`,
                `justification_code` AS `justificationCode`,
                DATE_FORMAT(`created_at`, '%Y-%m-%dT%T.000Z') as `created_at`
            FROM `calendar`
            $where
        ");

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result === false) {
            throw new RuntimeException('MySQL failed to execute a statement: ' . $stmt->error, $stmt->errno);
        }

        $rows = $result->fetch_all(MYSQLI_ASSOC);

        $calendarFactory = Closure::fromCallable([$this, 'createCalendarFromRow']);

        return array_map($calendarFactory, $rows);
    }

    /**
     * @param  int   $idx
     * @return mixed      Calendar if exist
     */
    public function alreadyExist(string $day, int $officeId, int $employeeId): ?Calendar
    {
        $stmt = $this->link->prepare("SELECT
                `id`,
                `day`,
                `office_id` AS `officeId`,
                `employee_id` AS `employeeId`,
                `hours`,
                `disease`,
                `justification_code` AS `justificationCode`,
                DATE_FORMAT(`created_at`, '%Y-%m-%dT%T.000Z') as `created_at`
            FROM `calendar`
            WHERE `day` = ? AND `office_id` = ? AND `employee_id` = ?
        ");

        $stmt->bind_param(
            'sii',
            $day,
            $officeId,
            $employeeId
        );
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result === false) {
            throw new RuntimeException('MySQL failed to execute a statement: ' . $stmt->error, $stmt->errno);
        }

        $row = $result->fetch_assoc();
        if ($row === null) {
            return null;
        }

        return $this->createCalendarFromRow($row);
    }

    /**
     * @param  int   $idx
     * @return mixed      Calendar if exist
     */
    public function find(int $idx): ?Calendar
    {
        $stmt = $this->link->prepare("SELECT
                `id`,
                `day`,
                `office_id` AS `officeId`,
                `employee_id` AS `employeeId`,
                `hours`,
                `disease`,
                `justification_code` AS `justificationCode`,
                DATE_FORMAT(`created_at`, '%Y-%m-%dT%T.000Z') as `created_at`
            FROM `calendar`
            WHERE `id` = ?
        ");

        $stmt->bind_param('i', $idx);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result === false) {
            throw new RuntimeException('MySQL failed to execute a statement: ' . $stmt->error, $stmt->errno);
        }

        $row = $result->fetch_assoc();
        if ($row === null) {
            return null;
        }

        return $this->createCalendarFromRow($row);
    }

    public function delete(int $idx): void
    {
        $stmt = $this->link->prepare("DELETE FROM `calendar` WHERE `id` = ?");
        $stmt->bind_param('i', $idx);
        $result = $stmt->execute();

        if ($result === false) {
            throw new RuntimeException('Mysql unable to delete a day from calendar');
        }
    }

    private function createCalendarFromRow(array $row): Calendar
    {
        return Calendar::createFromArray($row)->withId($row['id'])->withCreatedAt($row['created_at']);
    }
}

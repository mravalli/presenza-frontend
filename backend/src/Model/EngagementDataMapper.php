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
 * Creation Date: 2020-11-12 18:22:54
 * Modified by:   Mario Ravalli
 * Last Modified: 2021-08-03 18:16:40
 */
declare(strict_types=1);

namespace Neuro3\Presenza\Model;

use Assert\Assert;
use Closure;
use Neuro3\Model\DbAdapter;
use RuntimeException;

class EngagementDataMapper extends DataMapper
{
    public const DEFAUL_PAGE_SIZE = 5;
    public const ERROR_CONFLICT = 11;

    /**
     * @var HoursWeekDataMapper
     */
    protected $hoursWeekDataMapper;

    public function __construct(
        DbAdapter $dba,
        HoursWeekDataMapper $hoursWeekDataMapper
    ) {
        parent::__construct($dba);
        $this->hoursWeekDataMapper = $hoursWeekDataMapper;
    }

    public function insert(Engagement $engagement): void
    {
        $employeeId  = $engagement->getEmployeeId();
        $hoursWeekId = $engagement->getHoursWeekId();
        $begin = $engagement->getBegin() ? $engagement->getBegin()->format('Ymd') : null;
        $end = $engagement->getEnd() ? $engagement->getEnd()->format('Ymd') : null;

        $stmt = $this->link->prepare("INSERT INTO engagements (
            `begin`,
            `end`,
            `employee_id`,
            `hours_week_id`
        ) VALUES (?,?,?,?)");

        $stmt->bind_param(
            'ssii',
            $begin,
            $end,
            $employeeId,
            $hoursWeekId
        );

        $result = $stmt->execute();

        if ($result === false) {
            throw new RuntimeException('MySQL failed to execute a statement: ' . $stmt->error, $stmt->errno);
        }
    }

    public function update(Engagement $engagement): void
    {
        $stmt = $this->link->prepare("UPDATE `engagements`
            SET
                `begin` = ?,
                `end`   = ?,
                `employee_id` = ?,
                `hours_week_id` = ?
            WHERE `id` = ?");

        $id = $engagement->getId();
        $employeeId  = $engagement->getEmployeeId();
        $hoursWeekId = $engagement->getHoursWeekId();
        $begin = $engagement->getBegin() ? $engagement->getBegin()->format('Ymd') : null;
        $end = $engagement->getEnd() ? $engagement->getEnd()->format('Ymd') : null;

        $stmt->bind_param(
            'ssiii',
            $begin,
            $end,
            $employeeId,
            $hoursWeekId,
            $id
        );

        $result = $stmt->execute();

        if ($result === false) {
            throw new RuntimeException('MySQL failed to execute update statement');
        }
    }

    /**
     * Check if Engagement it's in conflict
     * @param  Engagement $engagement
     * @return bool
     */
    public function conflict(Engagement $engagement): bool
    {
        $employeeId  = $engagement->getEmployeeId();
        $begin = $engagement->getBegin() ? $engagement->getBegin()->format('Ymd') : null;
        $end = $engagement->getEnd() ? $engagement->getEnd()->format('Ymd') : null;

        $stmt = $this->link->prepare("SELECT * 
            FROM `engagements`
            WHERE `employee_id` = ? AND `begin` <= ? AND `end` >= ?
            LIMIT 0,1");
        
        $stmt->bind_param(
            'iss',
            $employeeId,
            $end,
            $begin
        );
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            return true;
        }
        return false;
    }

    /**
     * @return Engagement
     */
    public function getAll(string $search = '', int $page = 1, int $pageSize = self::DEFAUL_PAGE_SIZE): array
    {
        Assert::that($page)->greaterThan(0);
        Assert::that($pageSize)->greaterThan(0);
        $where = ($search !== '') ? " WHERE $search " : '';

        $offset = ($page - 1) * $pageSize;
        $limit = $pageSize;

        $stmt = $this->link->prepare("SELECT
                `id`,
                `begin`,
                `end`,
                `employee_id` AS `employeeId`,
                `hours_week_id` AS `hoursWeekId`,
                DATE_FORMAT(`created_at`, '%Y-%m-%dT%T.000Z') as `created_at`
            FROM `engagements`
            $where
            ORDER BY `end` LIMIT $limit OFFSET $offset;
        ");

        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result === false) {
            throw new RuntimeException('MySQL failed to execute a statement: ' . $stmt->error, $stmt->errno);
        }

        $rows = $result->fetch_all(MYSQLI_ASSOC);

        $engagementFactory = Closure::fromCallable([$this, 'createEngagementFromRow']);

        return array_map($engagementFactory, $rows);
    }

    public function getAllFor(int $employeeId): ?array
    {
        $stmt = $this->link->prepare("SELECT
                `id`,
                `begin`,
                `end`,
                `employee_id` AS `employeeId`,
                `hours_week_id` AS `hoursWeekId`,
                DATE_FORMAT(`created_at`, '%Y-%m-%dT%T.000Z') as `created_at`,
                (SELECT true from engagements e2 where `end` < now() and e.id = e2.id) as `disabled`
            FROM engagements e
            WHERE `employee_id` = $employeeId
            ORDER BY `end` ASC;
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result === false) {
            throw new RuntimeException('MySQL failed to execute a statement: ' . $stmt->error, $stmt->errno);
        }

        $rows = $result->fetch_all(MYSQLI_ASSOC);

        $engagementFactory = Closure::fromCallable([$this, 'createEngagementFromRow']);

        return array_map($engagementFactory, $rows);
    }

    public function countPages(string $search = '', int $pageSize = self::DEFAULT_PAGE_SIZE): int
    {
        Assert::that($pageSize)->greaterThan(0);

        $where = ($search !== '') ? " WHERE $search " : '';

        $stmt = $this->link->prepare("SELECT COUNT(*) FROM `engagements` $where");

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result === false) {
            throw new RuntimeException('MySQL failed to execute a statement: ' . $stmt->error, $stmt->errno);
        }

        $count = $stmt->num_rows;

        // if ($count === false) {
        //     throw new RuntimeException('PDO failed to fetch a row');
        // }

        if ($count <= $pageSize) {
            return 1;
        }

        return (int) ceil($count / $pageSize);
    }

    public function find(int $id): ?Engagement
    {
        $stmt = $this->link->prepare("SELECT
                `id`,
                `begin`,
                `end`,
                `employee_id` AS `employeeId`,
                `hours_week_id` AS `hoursWeekId`,
                DATE_FORMAT(`created_at`, '%Y-%m-%dT%T.000Z') as `created_at`
            FROM `engagements`
            WHERE `id` = ?
        ");
        $stmt->bind_param('i', $id);
        $stmt->execute();

        $result = $stmt->get_result();
        if ($result === false) {
            throw new RuntimeException('MySQL failed to execute a statement: ' . $stmt->error, $stmt->errno);
        }

        $engagement = $result->fetch_assoc();
        if ($engagement === null) {
            return null;
        }

        return $this->createEngagementFromRow($engagement);
    }

    public function findActualFor(int $employeeId): ?Engagement
    {
        $stmt = $this->link->prepare("SELECT
                `id`,
                `begin`,
                `end`,
                `employee_id` AS `employeeId`,
                `hours_week_id` AS `hoursWeekId`,
                DATE_FORMAT(`created_at`, '%Y-%m-%dT%T.000Z') as `created_at`
            FROM `engagements`
            WHERE `employee_id` = ? AND `end` >= NOW()
            ORDER BY `end` ASC LIMIT 0,1
        ");
        $stmt->bind_param('i', $employeeId);
        $stmt->execute();

        $result = $stmt->get_result();
        if ($result === false) {
            throw new RuntimeException('MySQL failed to execute a statement: ' . $stmt->error, $stmt->errno);
        }

        $engagement = $result->fetch_assoc();
        if ($engagement === null) {
            return null;
        }

        return $this->createEngagementFromRow($engagement);
    }

    public function delete(int $id): void
    {
        $stmt = $this->link->prepare("DELETE FROM `engagements` WHERE `id` = ?");
        $stmt->bind_param('i', $id);
        $result = $stmt->execute();

        if ($result === false) {
            throw new RuntimeException('MySQL failed to execute a statement: ' . $stmt->error, $stmt->errno);
        }
    }

    /**
     * @param array $row
     * @return Engagement
     */
    private function createEngagementFromRow(array $row): Engagement
    {
        $engagement = Engagement::createFromArray($row)->withId($row['id'])->withcreatedAt($row['created_at']);
        $hoursWeek = $this->hoursWeekDataMapper->find($engagement->getHoursWeekId());
        $engagement->setHoursWeek($hoursWeek);
        return $engagement;
    }
}

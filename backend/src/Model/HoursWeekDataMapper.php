<?php
/**
 * This file is part of the project: Presenza
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 *
 * @author:    Mario Ravalli <mario@raval.li>
 * @copyright: Copyright (c) 2020 Mario Ravalli
 * @license:   GNU General Public License v3.0 or later
 *
 *
 * Creation Date: Thursday October 22nd 2020
 * Modified By:   Thursday October 22nd 2020 17:19:36
 * Last Modified: 2021-08-03 18:50:31
 */
declare(strict_types=1);

namespace Neuro3\Presenza\Model;

use Assert\Assert;
use Closure;
use RuntimeException;

class HoursWeekDataMapper extends DataMapper
{
    public const DEFAULT_PAGE_SIZE = 20;

    public function insert(HoursWeek $hoursWeek): void
    {
        $stmt = $this->link->prepare("INSERT INTO hours_week (
            `type`,
            `days`,
            `mon`,
            `tue`,
            `wed`,
            `thu`,
            `fri`,
            `sat`,
            `sun`
        ) VALUES (?,?,?,?,?,?,?,?,?)");

        $type = $hoursWeek->getType();
        $days = $hoursWeek->getDays();
        $mon  = $hoursWeek->getMon();
        $tue  = $hoursWeek->getTue();
        $wed  = $hoursWeek->getWed();
        $thu  = $hoursWeek->getThu();
        $fri  = $hoursWeek->getFri();
        $sat  = $hoursWeek->getSat();
        $sun  = $hoursWeek->getSun();

        $stmt->bind_param(
            'siddddddd',
            $type,
            $days,
            $mon,
            $tue,
            $wed,
            $thu,
            $fri,
            $sat,
            $sun
        );

        $result = $stmt->execute();

        if ($result === false) {
            throw new RuntimeException('MySQL failed to execute a statement: ' . $stmt->error, $stmt->errno);
        }
    }

    public function update(HoursWeek $hoursWeek): void
    {
        $stmt = $this->link->prepare("UPDATE `hours_week`
            SET
                `type` = ?,
                `days` = ?,
                `mon`  = ?,
                `tue`  = ?,
                `wed`  = ?,
                `thu`  = ?,
                `fri`  = ?,
                `sat`  = ?,
                `sun`  = ?
            WHERE `id` = ?
        ");

        $id = $hoursWeek->getId();
        $type = $hoursWeek->getType();
        $days = $hoursWeek->getDays();
        $mon  = $hoursWeek->getMon();
        $tue  = $hoursWeek->getTue();
        $wed  = $hoursWeek->getWed();
        $thu  = $hoursWeek->getThu();
        $fri  = $hoursWeek->getFri();
        $sat  = $hoursWeek->getSat();
        $sun  = $hoursWeek->getSun();

        $stmt->bind_param(
            'sidddddddi',
            $type,
            $days,
            $mon,
            $tue,
            $wed,
            $thu,
            $fri,
            $sat,
            $sun,
            $id
        );

        $result = $stmt->execute();

        if ($result === false) {
            throw new RuntimeException('MySQL failed to execute update statement');
        }
    }

    /**
     * @return [HoursWeek]
     */
    public function getAll(string $search = '', int $page = 1, int $pageSize = self::DEFAULT_PAGE_SIZE): array
    {
        Assert::that($page)->greaterThan(0);
        Assert::that($pageSize)->greaterThan(0);
        $where = ($search !== '') ? " WHERE $search " : '';

        $offset = ($page - 1) * $pageSize;
        $limit = $pageSize;

        $stmt = $this->link->prepare("SELECT
                `id`,
                `type`,
                `days`,
                `mon`,
                `tue`,
                `wed`,
                `thu`,
                `fri`,
                `sat`,
                `sun`,
                DATE_FORMAT(`created_at`, '%Y-%m-%dT%T.000Z') as `created_at` 
            FROM `hours_week`
            $where
        ");
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result === false) {
            throw new RuntimeException('MySQL failed to execute a statement: ' . $stmt->error, $stmt->errno);
        }

        $rows = $result->fetch_all(MYSQLI_ASSOC);

        $hoursWeekFactory = Closure::fromCallable([$this, 'createHoursWeekFromRow']);

        return array_map($hoursWeekFactory, $rows);
    }

    public function countPages(string $search = '', int $pageSize = self::DEFAULT_PAGE_SIZE): int
    {
        Assert::that($pageSize)->greaterThan(0);

        $where = ($search !== '') ? " WHERE $search " : '';

        $stmt = $this->link->prepare("SELECT COUNT(*) FROM hours_week $where");

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

    public function find(int $id): ?HoursWeek
    {
        $stmt = $this->link->prepare("SELECT
                `id`,
                `type`,
                `days`,
                `mon`,
                `tue`,
                `wed`,
                `thu`,
                `fri`,
                `sat`,
                `sun`,
                DATE_FORMAT(`created_at`, '%Y-%m-%dT%T.000Z') as `created_at` 
            FROM `hours_week`
            WHERE `id` = ?
        ");
        $stmt->bind_param('i', $id);
        $stmt->execute();

        $result = $stmt->get_result();
        if ($result === false) {
            throw new RuntimeException('MySQL failed to execute a statement: ' . $stmt->error, $stmt->errno);
        }

        $row = $result->fetch_assoc();
        if ($row === null) {
            return null;
        }

        return $this->createHoursWeekFromRow($row);
    }

    public function delete(int $id): void
    {
        $stmt = $this->link->prepare("DELETE FROM `hours_week` WHERE `id` = ?");
        $stmt->bind_param('i', $id);
        $result = $stmt->execute();

        if ($result === false) {
            throw new RuntimeException('MySQL failed to execute a statement: ' . $stmt->error, $stmt->errno);
        }
    }

    /**
     * @param string[] $row
     */
    private function createHoursWeekFromRow(array $row): HoursWeek
    {
        return HoursWeek::createFromArray($row)->withId($row['id'])->withCreatedAt($row['created_at']);
    }
}

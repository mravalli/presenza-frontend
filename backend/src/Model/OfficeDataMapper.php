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
 * Creation Date: 2020-10-30 09:56:33
 * Modified by:   Mario Ravalli
 * Last Modified: 2021-08-05 17:54:34
 */
declare(strict_types=1);

namespace Neuro3\Presenza\Model;

use Assert\Assert;
use Closure;
use Neuro3\Model\DbAdapter;
use RuntimeException;

class OfficeDataMapper extends DataMapper
{
    public const DEFAULT_PAGE_SIZE = 20;

    /**
     * @var EmployeeDataMapper
     */
    protected $employeeDataMapper;

    public function __construct(DbAdapter $dba, EmployeeDataMapper $employeeDataMapper)
    {
        parent::__construct($dba);
        $this->employeeDataMapper = $employeeDataMapper;
    }

    public function insert(Office $office): void
    {
        $stmt = $this->link->prepare("INSERT INTO `offices` (
            `name`,
            `color`,
            `location`,
            `manager_id`
        ) VALUES (?,?,?,?)");

        $name      = $office->getName();
        $color     = $office->getColor();
        $location  = $office->getLocation();
        $managerId = $office->getManagerId();

        $stmt->bind_param(
            'sisi',
            $name,
            $color,
            $location,
            $managerId
        );

        $result = $stmt->execute();

        if ($result === false) {
            throw new RuntimeException("MySQL failed to execute a statement");
        }
    }

    public function update(Office $office): void
    {
        $stmt = $this->link->prepare("UPDATE `offices`
            SET
                `name`       = ?,
                `color`      = ?,
                `location`   = ?,
                `manager_id` = ?
            WHERE `id` = ?
        ");

        $id = $office->getId();
        $name = $office->getName();
        $color = $office->getColor();
        $location = $office->getLocation();
        $managerId = $office->getManagerId();

        $stmt->bind_param(
            'sisii',
            $name,
            $color,
            $location,
            $managerId,
            $id
        );

        $result = $stmt->execute();

        if ($result === false) {
            throw new RuntimException('MySQL failed to execute update statement');
        }

        $this->removeAllEmployee($id);
        if (!empty($office->getEmployees())) {
            foreach ($office->getEmployees() as $employee) {
                $this->addEmployee($id, $employee->getId());
            }
        }
    }

    /**
     * @return [Office]
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
                `name`,
                `color`,
                `location`,
                `manager_id` AS `managerId`,
                DATE_FORMAT(`created_at`, '%Y-%m-%dT%T.000Z') as `created_at`
            FROM `offices`
            $where
        ");
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result === false) {
            throw new RuntimeException('MySQL failed to execute a statement: ' . $stmt->error, $stmt->errno);
        }

        $rows = $result->fetch_all(MYSQLI_ASSOC);

        for ($i = 0; $i++; $i < count($rows)) {
            $rows[$i]['manager'] = $this->employeeDataMapper->find($row[$i]['manager_id']);
        }

        $officeFactory = Closure::fromCallable([$this, 'createOfficeFromRow']);

        return array_map($officeFactory, $rows);
    }

    public function countPages(string $search = '', int $pageSize = self::DEFAULT_PAGE_SIZE): int
    {
        Assert::that($pageSize)->greaterThan(0);

        $where = ($search !== '') ? " WHERE $search " : '';

        $stmt = $this->link->prepare("SELECT COUNT(*) FROM offices $where");

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

    public function getOfficesByEmployee(int $employeeId)
    {
        $stmt = $this->link->prepare("SELECT
                `o`.`id`,
                `o`.`name`,
                `0`.`color`
            FROM `offices` `o`, `office_employees` `oe`
            WHERE `o`.`id` = `oe`.`office_id` AND `oe`.`employee_id` = ?
        ");
        $stmt->bind_param('i', $employeeId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result === false) {
            throw new RuntimeException('MySQL failed to execute a statement: ' . $stmt->error, $stmt->errno);
        }

        $rows = $result->fetch_all(MYSQLI_ASSOC);

        return $rows;
    }

    public function find(int $id): ?Office
    {
        $stmt = $this->link->prepare("SELECT
                `id`,
                `name`,
                `color`,
                `location`,
                `manager_id` as `managerId`,
                DATE_FORMAT(`created_at`, '%Y-%m-%dT%T.000Z') as `created_at`
            FROM `offices`
            WHERE `id` = ?
        ");
        $stmt->bind_param('i', $id);
        $stmt->execute();

        $result = $stmt->get_result();
        if ($result === false) {
            throw new RuntimeException('MySQL failed to execute a statement: ' . $stmt->error, $stmt->errno);
        }

        $office = $result->fetch_assoc();
        if ($office === null) {
            return null;
        }

        return $this->createOfficeFromRow($office);
    }

    public function delete(int $id): void
    {
        $stmt = $this->link->prepare("DELETE FROM `offices` WHERE `id` = ?");
        $stmt->bind_param('i', $id);
        $result = $stmt->execute();

        if ($result === false) {
            throw new RuntimeException('MySQL failed to execute a statement: ' . $stmt->error, $stmt->errno);
        }
    }

    /**
     * @param  array  $row
     * @return Office
     */
    private function createOfficeFromRow(array $row): Office
    {
        if (!empty($row['managerId'])) {
            $row['manager'] = $this->employeeDataMapper->find($row['managerId']);
        }
        $row['employees'] = $this->getEmployees($row['id']);
        
        return Office::createFromArray($row)->withId($row['id'])->withCreatedAt($row['created_at']);
    }

    private function getEmployees(int $officeId): ?array
    {
        $stmt = $this->link->prepare("SELECT
                `e`.`id`
            FROM `office_employees` `o`, `employees` `e`
            WHERE `o`.`employee_id` = `e`.`id` AND `o`.`office_id` = ?
        ");
        $stmt->bind_param('i', $officeId);
        $stmt->execute();

        $result = $stmt->get_result();
        if ($result === false) {
            throw new RuntimeException('MySQL failed to execute a statement: ' . $stmt->error, $stmt->errno);
        }
        $employees = [];
        foreach ($result->fetch_all(MYSQLI_ASSOC) as $employee) {
            $employees[] = $this->employeeDataMapper->find($employee['id']);
        }

        if (empty($employees)) {
            return null;
        }

        return $employees;
    }

    private function addEmployee(int $officeId, int $employeeId)
    {
        $stmt = $this->link->prepare("INSERT INTO `office_employees` (
            `office_id`,
            `employee_id`
        ) VALUES (?,?)");

        $stmt->bind_param('ii', $officeId, $employeeId);

        $result = $stmt->execute();
        if ($result === false) {
            throw new RuntimException('MySQL failed to execute update statement');
        }
    }

    // private function delEmployee(int $officeId, int $employeeId)
    // {
    //     $stmt = $this->link->prepare("DELETE FROM `office_employees` WHERE `office_id` = ? AND `employee_id` = ?");

    //     $stmt->bind_param('ii', $officeId, $employeeId);

    //     $result = $stmt->execute();
    //     if ($result === false) {
    //         throw new RuntimException('MySQL failed to execute update statement');
    //     }
    // }

    private function removeAllEmployee(int $officeId): void
    {
        $stmt = $this->link->prepare("DELETE FROM `office_employees` WHERE `office_id` = ?");
        $stmt->bind_param('i', $officeId);
        $result = $stmt->execute();

        if ($result === false) {
            throw new RuntimeException('Unable to remove employees associated');
        }
    }
}

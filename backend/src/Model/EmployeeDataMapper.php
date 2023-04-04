<?php declare(strict_types=1);

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
 * Creation Date: Tue Sep 08 2020
 * Modified by:   Mario Ravalli
 * Last Modified: 2021-08-05 18:48:57
 */

namespace Neuro3\Presenza\Model;

use function Neuro3\Presenza\logger;
use const Neuro3\Presenza\DATE_FORMAT;
use Assert\Assert;
use Closure;
use Neuro3\Model\DbAdapter;
use RuntimeException;

/**
 * TODO: getAll non gestisce correttamente il sort passato a vuoto
 */
class EmployeeDataMapper extends DataMapper
{
    public const DEFAULT_PAGE_SIZE = 20;
    protected EngagementDataMapper $engagementDataMapper;

    public function __construct(DbAdapter $dba, EngagementDataMapper $engagementDataMapper) {
        parent::__construct($dba);
        $this->engagementDataMapper = $engagementDataMapper;
    }

    public function insert(Employee $employee): void
    {
        $stmt = $this->link->prepare("INSERT INTO employees (
                    `badge_id`,
                    `firstname`,
                    `lastname`,
                    `address`,
                    `cap`,
                    `city`,
                    `cf`,
                    `phone`,
                    `mobile`,
                    `email`,
                    `first_engagement`,
                    `birthday`,
                    `notes`
                ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,to_base64(?))");

        list(
            $badge_id,
            $firstname,
            $lastname,
            $address,
            $cap,
            $city,
            $cf,
            $phone,
            $mobile,
            $email,
            $first_engagement,
            $birthday,
            $notes
        ) = $this->populateData($employee);

        $stmt->bind_param(
            'sssssssssssss',
            $badge_id,
            $firstname,
            $lastname,
            $address,
            $cap,
            $city,
            $cf,
            $phone,
            $mobile,
            $email,
            $first_engagement,
            $birthday,
            $notes
        );
        $result = $stmt->execute();

        if ($result === false) {
            throw new RuntimeException('MySQL failed to execute a statement: ' . $stmt->error, $stmt->errno);
        }

        $employee->setId($stmt->insert_id);
    }

    public function update(Employee $employee): void
    {
        $stmt = $this->link->prepare("UPDATE `employees`
            SET
                `badge_id`  = ?,
                `firstname` = ?,
                `lastname`  = ?,
                `address`   = ?,
                `cap`       = ?,
                `city`      = ?,
                `cf`        = ?,
                `phone`     = ?,
                `mobile`    = ?,
                `email`     = ?,
                `first_engagement` = ?,
                `birthday`  = ?,
                `notes`     = to_base64(?)
            WHERE `id` = ?
        ");

        $id = $employee->getId();
        list(
            $badge_id,
            $firstname,
            $lastname,
            $address,
            $cap,
            $city,
            $cf,
            $phone,
            $mobile,
            $email,
            $first_engagement,
            $birthday,
            $notes
        ) = $this->populateData($employee);


        $stmt->bind_param(
            'sssssssssssssi',
            $badge_id,
            $firstname,
            $lastname,
            $address,
            $cap,
            $city,
            $cf,
            $phone,
            $mobile,
            $email,
            $first_engagement,
            $birthday,
            $notes,
            $id
        );
        $result = $stmt->execute();

        if ($result === false) {
            throw new RuntimeException('MySQL failed to execute update statement');
        }
    }

    /**
     * @return Employee[]
     */
    public function getAll(string $search = '', string $sort = 'firstname ASC', int $page = 1, int $pageSize = self::DEFAULT_PAGE_SIZE): array
    {
        Assert::that($page)->greaterThan(0);
        Assert::that($pageSize)->greaterThan(0);
        $where = ($search !== '') ? " WHERE $search " : '';

        $offset = ($page - 1) * $pageSize;
        $limit = $pageSize;

        $stmt = $this->link->prepare("SELECT 
                `id`,
                `badge_id`,
                `firstname`,
                `lastname`,
                `address`,
                `cap`,
                `city`,
                `cf`,
                `phone`,
                `mobile`,
                `email`,
                DATE_FORMAT(`first_engagement`, '%Y-%m-%d') as first_engagement,
                DATE_FORMAT(`birthday`, '%Y-%m-%d') as birthday,
                from_base64(`notes`) as `notes`,
                DATE_FORMAT(`created_at`, '%Y-%m-%dT%T.000Z') as `created_at` 
            FROM `employees` 
            $where
            ORDER BY $sort LIMIT $limit OFFSET $offset;
            ");

        logger()->error(print_r($this->link, true));
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result === false) {
            throw new RuntimeException('MySQL failed to execute a statement: ' . $stmt->error, $stmt->errno);
        }

        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $employeeFactory = Closure::fromCallable([ $this, 'createEmployeeFromRow' ]);

        return array_map($employeeFactory, $rows);
    }

    public function countPages(string $search = '', int $pageSize = self::DEFAULT_PAGE_SIZE): array
    {
        Assert::that($pageSize)->greaterThan(0);

        $where = ($search !== '') ? " WHERE $search " : '';

        $stmt = $this->link->prepare("SELECT id FROM employees $where");

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result === false) {
            throw new RuntimeException('MySQL failed to execute a statement: ' . $stmt->error, $stmt->errno);
        }

        if ($result->num_rows <= $pageSize) {
            return [ $result->num_rows, 1 ];
        }

        return [ $result->num_rows, (int) ceil($result->num_rows / $pageSize) ];
    }

    public function find(int $id): ?Employee
    {
        $stmt = $this->link->prepare("SELECT 
                `id`,
                `badge_id`,
                `firstname`,
                `lastname`,
                `address`,
                `cap`,
                `city`,
                `cf`,
                `phone`,
                `mobile`,
                `email`,
                DATE_FORMAT(`first_engagement`, '%Y-%m-%d') as first_engagement,
                DATE_FORMAT(`birthday`, '%Y-%m-%d') as birthday,
                from_base64(`notes`) as `notes`,
                DATE_FORMAT(`created_at`, '%Y-%m-%dT%T.000Z') as `created_at` 
            FROM `employees` 
            WHERE `id` = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result === false) {
            throw new RuntimeException('MySQL failed to execute a statement: ' . $stmt->error, $stmt->errno);
        }

        $row = $result->fetch_assoc();

        if ($row === false) {
            return null;
        }

        return $this->createEmployeeFromRow($row);
    }

    public function delete(int $id): void
    {
        $stmt = $this->link->prepare("DELETE FROM `employees` WHERE `id` = ?");
        $stmt->bind_param('i', $id);
        $result = $stmt->execute();

        if ($result === false) {
            throw new RuntimeException('MySQL failed to execute a statement: ' . $stmt->error, $stmt->errno);
        }
    }

    /**
     * @param string[] $row
     */
    private function createEmployeeFromRow(array $row): Employee
    {
        if (!empty($row['birthday'])) {
            $row['birthday'] = date(DATE_FORMAT, strtotime($row['birthday']));
        }
        if (!empty($row['first_engagement'])) {
            $row['first_engagement'] = date(DATE_FORMAT, strtotime($row['first_engagement']));
        }
        $employee = Employee::createFromArray($row)->withId($row['id'])->withCreatedAt($row['created_at']);

        $actualEngagement = $this->engagementDataMapper->findActualFor((int)$row['id']);
        $engagements = $this->engagementDataMapper->getAllFor((int)$row['id']);

        if (!is_null($actualEngagement)) {
            $employee->setActualEngagement($actualEngagement);
        }
        if (!empty($engagements)) {
            $employee->setEngagements($engagements);
        }

        return $employee;
    }

    private function populateData(Employee $employee): array
    {
        $badge_id = $employee->getBadgeId();
        $firstname = $employee->getFirstname();
        $lastname = $employee->getLastname();
        $address = $employee->getAddress();
        $cap = $employee->getCap();
        $city = $employee->getCity();
        $cf = $employee->getCf();
        $phone = $employee->getPhone();
        $mobile = $employee->getMobile();
        $email = $employee->getEmail();
        $first_engagement = $employee->getFirstEngagement() ? $employee->getFirstEngagement()->format('Y-m-d') : null;
        $birthday = $employee->getBirthday() ? $employee->getBirthday()->format('Y-m-d') : null;
        $notes = $employee->getNotes();

        return [
            $badge_id,
            $firstname,
            $lastname,
            $address,
            $cap,
            $city,
            $cf,
            $phone,
            $mobile,
            $email,
            $first_engagement,
            $birthday,
            $notes
        ];
    }
}

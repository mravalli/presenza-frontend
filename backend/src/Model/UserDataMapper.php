<?php
/**
* This file is part of the project: Neuro3 toolkit
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code
*
* @author:    Mario Ravalli <mario@raval.li>
* @copyright: Copyright (c) 2020 Mario Ravalli
* @license:   GNU General Public License v3.0 or later
*
*
* Creation Date:      Wed Sep 09 2020
* Last Modified by:   Mario Ravalli
* Last Modified time: Wed Sep 09 2020
*/
declare(strict_types=1);

namespace Neuro3\Presenza\Model;

use Assert\Assert;
use Closure;
use RuntimeException;

class UserDataMapper extends DataMapper
{
    public const DEFAULT_PAGE_SIZE = 20;

    public function insert(User $user): void
    {
        $stmt = $this->link->prepare("INSERT INTO users (
            `firstname`,
            `lastname`,
            `email`,
            `username`,
            `password_hash`,
            `status`,
            `role`
        ) VALUES (?,?,?,?,?,?,?)");
        $firstname = $user->getFirstname();
        $lastname = $user->getLastname();
        $email = $user->getEmail();
        $username = $user->getUsername();
        $passwordHash = $user->getPasswordHash();
        $status = $user->getStatus();
        $role = $user->getRole();

        $stmt->bind_param(
            'sssssii',
            $firstname,
            $lastname,
            $email,
            $username,
            $passwordHash,
            $status,
            $role
        );
        $result = $stmt->execute();

        if ($result === false) {
            throw new RuntimeException('MySQL failed to execute a statement: ' . $stmt->error, $stmt->errno);
        }
    }

    public function update(User $user): void
    {
        $stmt = $this->link->prepare("UPDATE users
            SET
                `firstname` = ?,
                `lastname` = ?,
                `username` = ?,
                `email` = ?,
                `role` = ?
            WHERE `id` = ?
        ");
        $firstname = $user->getFirstname();
        $lastname = $user->getLastname();
        $username = $user->getUsername();
        $email = $user->getEmail();
        $role = $user->getRole();
        $id = $user->getId();

        $stmt->bind_param(
            'ssssii',
            $firstname,
            $lastname,
            $username,
            $email,
            $role,
            $id
        );
        $result = $stmt->execute();

        if ($result === false) {
            throw new RuntimeException('MySQL failed to execute a statement: ' . $stmt->error, $stmt->errno);
        }
    }

    public function updateCredentials(User $user): void
    {
        $stmt = $this->link->prepare("UPDATE users
            SET
                `username` = ?,
                `password_hash` = ?
            WHERE `id` = ?
        ");
        $username = $user->getUsername();
        $passwordHash = $user->getPasswordHash();
        $id = $user->getId();

        $stmt->bind_param(
            'ssi',
            $username,
            $passwordHash,
            $id
        );
        $result = $stmt->execute();

        if ($result === false) {
            throw new RuntimeException('MySQL failed to execute a statement: ' . $stmt->error, $stmt->errno);
        }
    }

    /**
     * @return [User]
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
                `firstname`,
                `lastname`,
                `username`,
                `status`,
                `role`,
                DATE_FORMAT(`created_at`, '%Y-%m-%dT%T.000Z') as `created_at`
            FROM `users`
            $where
        ");
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result === false) {
            throw new RuntimeException('MySQL failed to execute a statement: ' . $stmt->error, $stmt->errno);
        }

        $rows = $result->fetch_all(MYSQLI_ASSOC);

        $userFactory = Closure::fromCallable([$this, 'createUserFromRow']);

        return array_map($userFactory, $rows);
    }

    public function countPages(string $search = '', int $pageSize = self::DEFAULT_PAGE_SIZE): int
    {
        Assert::that($pageSize)->greaterThan(0);

        $where = ($search !== '') ? " WHERE $search " : '';

        $stmt = $this->link->prepare("SELECT COUNT(*) FROM users $where");

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

    public function find(int $id) : ?User
    {
        $stmt = $this->link->prepare("SELECT
                `id`,
                `firstname`,
                `lastname`,
                `email`,
                `username`,
                `password_hash` AS `passwordHash`,
                `primary_key` AS `primaryKey`,
                `secondary_key` AS `secondaryKey`,
                `status`,
                `role`,
                DATE_FORMAT(`created_at`, '%Y-%m-%dT%T.000Z') as `created_at`
            FROM `users`
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

        return $this->createUserFromRow($row);
    }

    public function delete(int $id): void
    {
        $stmt = $this->link->prepare("DELETE FROM `users` WHERE `id` = ?");
        $stmt->bind_param('i', $id);
        $result = $stmt->execute();

        if ($result === false) {
            throw new RuntimeException('MySQL failed to execute a statement: ' . $stmt->error, $stmt->errno);
        }
    }

    protected function createUserFromRow(array $row): User
    {
        return User::createFromArray($row)->withId($row['id'])->withCreatedAt($row['created_at']);
    }
}

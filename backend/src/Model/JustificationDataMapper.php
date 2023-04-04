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
 * Creation Date: 2020-11-06 22:05:32
 * Modified by:   Mario Ravalli
 * Last Modified: 2021-08-03 19:08:46
 */
declare(strict_types=1);

namespace Neuro3\Presenza\Model;

use Assert\Assert;
use Closure;
use RuntimeException;

class JustificationDataMapper extends DataMapper
{
    public const DEFAULT_PAGE_SIZE = 20;

    public function insert(Justification $justification): void
    {
        $stmt = $this->link->prepare("INSERT INTO `justifications` (
            `name`,
            `code`,
            `limit`
        ) VALUES (?,?,?)");

        $name  = $justification->getName();
        $code  = $justification->getCode();
        $limit = $justification->getLimit();

        $stmt->bind_param(
            'ssi',
            $name,
            $code,
            $limit
        );

        $result = $stmt->execute();
        if ($result === false) {
            throw new RuntimeException("MySQL unable to insert new justification");
        }
    }

    public function update(Justification $justification): void
    {
        $stmt = $this->link->prepare("UPDATE `justifications` 
            SET
                `name`  = ?,
                `code`  = ?,
                `limit` = ?
            WHERE id = ?
        ");

        $idx   = $justification->getId();
        $name  = $justification->getName();
        $code  = $justification->getCode();
        $limit = $justification->getLimit();

        $stmt->bind_param(
            'ssii',
            $name,
            $code,
            $limit,
            $idx
        );

        $result = $stmt->execute();
        if ($result === false) {
            throw new RuntimeException("MySQL unable to update justification");
        }
    }

    public function getAll(): ?array
    {
        $stmt = $this->link->prepare("SELECT
                `id`,
                `name`,
                `code`,
                `limit`
            FROM `justifications`
        ");

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result === false) {
            throw new RuntimeException('MySQL failed to execute a statement: ' . $stmt->error, $stmt->errno);
        }

        $rows = $result->fetch_all(MYSQLI_ASSOC);

        $justificationFactory = Closure::fromCallable([$this, 'createJustificationFromRow']);

        return array_map($justificationFactory, $rows);
    }

    public function countPages(string $search = '', int $pageSize = self::DEFAULT_PAGE_SIZE): int
    {
        Assert::that($pageSize)->greaterThan(0);

        $where = ($search !== '') ? " WHERE $search " : '';

        $stmt = $this->link->prepare("SELECT COUNT(*) FROM justifications $where");

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

    public function find(int $idx): ?Justification
    {
        $stmt = $this->link->prepare("SELECT
                `id`,
                `name`,
                `code`,
                `limit`
            FROM `justifications`
            WHERE `id` = ?
        ");

        $stmt->bind_param('i', $idx);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result === false) {
            throw new RuntimeException('MySQL failed to execute a statement: ' . $stmt->error, $stmt->errno);
        }

        $row = $result->fetch_assoc();

        return $this->createJustificationFromRow($row);
    }

    public function delete(int $idx): void
    {
        $stmt = $this->link->prepare("DELETE FROM `justification` WHERE `id` = ?");
        $stmt->bind_param('i', $idx);
        $result = $stmt->execute();

        if ($result === false) {
            throw new RuntimeException('Mysql unable to delete a day from calendar');
        }
    }

    private function createJustificationFromRow(array $row): Justification
    {
        return Justification::createFromArray($row)->withId($row['id']);
    }
}

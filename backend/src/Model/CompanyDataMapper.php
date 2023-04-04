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
 * Creation Date:      Wed Oct 14 2020
 * Last Modified by:   Mario Ravalli
 * Last Modified time: Wed Oct 14 2020
 */

declare(strict_types=1);

namespace Neuro3\Presenza\Model;

use Assert\Assert;
use Closure;
use RuntimeException;

class CompanyDataMapper extends DataMapper
{
    public function insert(Company $company): void
    {
        $stmt = $this->link->prepare("INSERT INTO company (
            `id`,
            `fullname`,
            `vat`,
            `cf`,
            `address`,
            `cap`,
            `city`,
            `phone`,
            `email`,
            `days_off`,
            `hours_leave`,
            `notes`
            ) VALUES (1,?,?,?,?,?,?,?,?,?,?,to_base64(?))");

        $fullname = $company->getFullname();
        $address  = $company->getAddress();
        $cap = $company->getCap();
        $city = $company->getCity();
        $cf = $company->getCf();
        $vat = $company->getVat();
        $phone = $company->getPhone();
        $email = $company->getEmail();
        $daysOff = $company->getDaysOff();
        $hoursLeave = $company->getHoursLeave();
        $notes = $company->getNotes();
        $stmt->bind_param(
            'ssssssssiis',
            $fullname,
            $vat,
            $cf,
            $address,
            $cap,
            $city,
            $phone,
            $email,
            $daysOff,
            $hoursLeave,
            $notes
        );
        $result = $stmt->execute();

        if ($result === false) {
            throw new RuntimeException('MySQL failed to execute a statement: ' . $stmt->error, $stmt->errno);
        }
    }

    public function update(Company $company): void
    {
        $stmt = $this->link->prepare("UPDATE `company`
            SET 
                `fullname`    = ?,
                `address`     = ?,
                `cap`         = ?,
                `city`        = ?,
                `cf`          = ?,
                `vat`         = ?,
                `phone`       = ?,
                `email`       = ?,
                `days_off`    = ?,
                `hours_leave` = ?,
                `notes`       = to_base64(?)
            WHERE `id` = 1
        ");

        $fullname = $company->getFullname();
        $address = $company->getAddress();
        $cap = $company->getCap();
        $city = $company->getCity();
        $cf = $company->getCf();
        $vat = $company->getVat();
        $phone = $company->getPhone();
        $email = $company->getEmail();
        $daysOff = $company->getDaysOff();
        $hoursLeave = $company->getHoursLeave();
        $notes = $company->getNotes();
        $stmt->bind_param(
            'ssssssssiis',
            $fullname,
            $address,
            $cap,
            $city,
            $cf,
            $vat,
            $phone,
            $email,
            $daysOff,
            $hoursLeave,
            $notes
        );
        $result = $stmt->execute();

        if ($result === false) {
            throw new RuntimeException('MySQL failed to execute update statement');
        }
    }

    public function get(): Company
    {
        $stmt = $this->link->prepare("SELECT
                `id`,
                `fullname`,
                `vat`,
                `cf`,
                `address`,
                `cap`,
                `city`,
                `phone`,
                `email`,
                `days_off` as `daysOff`,
                `hours_leave` as `hoursLeave`,
                from_base64(`notes`) as `notes`,
                DATE_FORMAT(`created_at`, '%Y-%m-%dT%T.000Z') as `created_at`
            FROM `company`
            WHERE `id` = 1");
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result === false) {
            throw new RuntimeException('MySQL failed to retrieve company');
        }

        $row = $result->fetch_assoc();

        if ($row === false) {
            return null;
        }

        return $this->createCompanyFromRow($row);
    }

    /**
     * @param string[] $row
     */
    private function createCompanyFromRow(array $row): Company
    {
        return Company::createFromArray($row)->withId($row['id'])->withCreatedAt($row['created_at']);
    }
}

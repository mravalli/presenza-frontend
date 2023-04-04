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
 * Creation Date: 2020-11-12 17:14:21
 * Modified by:   Mario Ravalli
 * Last Modified: 2020-11-18 22:42:16
 */

namespace Neuro3\Presenza\Model;

use const Neuro3\Presenza\DATE_FORMAT;
use function Neuro3\Presenza\date_from_string;
use Assert\Assert;
use Assert\LazyAssertionException;
use function Neuro3\Presenza\now;
use DateTimeImmutable;
use Neuro3\Exceptions\InvalidDataException;

class Engagement extends Model
{
    use ModelTrait;
    protected int $employeeId;
    protected int $hoursWeekId;
    protected ?DateTimeImmutable $begin = null;
    protected ?DateTimeImmutable $end = null;
    protected DateTimeImmutable $createdAt;

    protected ?HoursWeek $hoursWeek = null;
    protected int $disabled = 0;
    // protected ?Employee $employee = null;

    /**
     * @throws InvalidDataException
     */
    public function __construct(
        int $employeeId,
        int $hoursWeekId,
        string $begin = null,
        string $end = null
    ) {
        $this->createdAt = now();
        $this->validate(compact(
            'employeeId',
            'hoursWeekId',
            'begin',
            'end'
        ));
        $this->begin = $begin ? date_from_string($begin) : null;
        $this->end = $end ? date_from_string($end) : null;
        $this->employeeId = $employeeId;
        $this->hoursWeekId = $hoursWeekId;
    }

    public function getEmployeeId(): int
    {
        return $this->employeeId;
    }
    public function getHoursWeekId(): int
    {
        return $this->hoursWeekId;
    }
    public function getBegin(): ?DateTimeImmutable
    {
        return $this->begin;
    }
    public function getEnd(): ?DateTimeImmutable
    {
        return $this->end;
    }
    // public function getEmployee(): Employee
    // {
    //     return $this->employee;
    // }
    // public function setEmployee(Employee $employee): void
    // {
    //     $this->employee = $employee;
    // }
    public function getHoursWeek(): HoursWeek
    {
        return $this->hoursWeek;
    }
    public function setHoursWeek(HoursWeek $hoursWeek): void
    {
        $this->hoursWeek = $hoursWeek;
    }

    public function jsonSerialize(): array
    {
        return [
            'id'          => $this->id,
            'employeeId'  => $this->employeeId,
            'hoursWeekId' => $this->hoursWeekId,
            'begin'       => $this->begin ? $this->begin->format(DATE_FORMAT) : null,
            'end'         => $this->end ? $this->end->format(DATE_FORMAT) : null,
            'hoursWeek'   => $this->hoursWeek,
            'disabled'    => $this->disabled,
            'createdAt'   => $this->getCreatedAtAsString()
        ];
    }

    /**
     * @throws InvalidDataException
     */
    public function updateFromArray(array $data): void
    {
        $this->validate($data);

        $this->employeeId  = $data['employeeId'] ?? $this->employeeId;
        $this->hoursWeekId = $data['hoursWeekId'] ?? $this->hoursWeekId;
        $this->begin       = $data['begin'] ? date_from_string($data['begin']) : $this->begin;
        $this->end         = $data['end'] ? date_from_string($data['end']) : $this->end;
        $this->disabled    = $data['disabled'] ?? $this->disabled;
    }

    /**
     * @throws InvalidDataException
     */
    private function validate(array $data): void
    {
        $assert = Assert::lazy()->tryAll();

        if (isset($data['employeeId'])) {
            $assert->that($data['employeeId'])->notEmpty()->integer();
        }
        if (isset($data['hoursWeekId'])) {
            $assert->that($data['hoursWeekId'])->notEmpty()->integer();
        }
        if (isset($data['begin'])) {
            $assert->that($data['begin'])->nullOr()->string()->notBlank();
        }
        if (isset($data['end'])) {
            $assert->that($data['end'])->nullOr()->string()->notBlank();
        }

        try {
            $assert->verifyNow();
        } catch (LazyAssertionException $lae) {
            throw InvalidDataException::fromLazyAssertionException($lae);
        }
    }
}

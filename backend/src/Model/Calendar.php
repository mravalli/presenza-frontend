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
 * Creation Date: 2020-11-06 16:53:44
 * Modified by:   Mario Ravalli
 * Last Modified: 2020-11-11 16:28:07
 */
declare(strict_types=1);

namespace Neuro3\Presenza\Model;

use const Neuro3\Presenza\DATE_FORMAT;
use function Neuro3\Presenza\date_from_string;
use function Neuro3\Presenza\now;
use Assert\Assert;
use Assert\LazyAssertionException;
use DateTimeImmutable;
use JsonSerializable;
use Neuro3\Exceptions\InvalidDataException;

class Calendar extends Model
{
    use ModelTrait;
    protected DateTimeImmutable $day;
    protected int $officeId;
    protected int $employeeId;
    protected float $hours;
    protected float $disease;
    protected ?string $justificationCode = null;
    protected DateTimeImmutable $createdAt;

    /**
     * @throws InvalidDataException
     */
    public function __construct(
        string $day,
        int $officeId,
        int $employeeId,
        float $hours,
        float $disease,
        string $justificationCode
    ) {
        $this->createdAt = now();

        $this->validate(compact(
            'day',
            'officeId',
            'employeeId',
            'hours',
            'disease',
            'justificationCode'
        ));

        $this->day             = date_from_string($day);
        $this->officeId        = $officeId;
        $this->employeeId      = $employeeId;
        $this->hours           = $hours;
        $this->disease         = $disease;
        $this->justificationCode = $justificationCode;
    }

    public function getDay(): DateTimeImmutable
    {
        return $this->day;
    }
    public function getDayAsString(): string
    {
        return $this->day->format(DATE_FORMAT);
    }
    public function getOfficeId(): int
    {
        return $this->officeId;
    }
    public function getEmployeeId(): int
    {
        return $this->employeeId;
    }
    public function getHours(): float
    {
        return $this->hours;
    }
    public function getDisease(): float
    {
        return $this->disease;
    }
    public function getJustificationCode(): ?string
    {
        return $this->justificationCode;
    }

    /**
     * @return mixed[]
     */
    public function jsonSerialize(): array
    {
        return [
            'id'              => $this->id,
            'day'             => $this->getDayAsString(),
            'officeId'        => $this->officeId,
            'employeeId'      => $this->employeeId,
            'hours'           => $this->hours,
            'disease'         => $this->disease,
            'justificationCode' => $this->justificationCode,
            'createdAt'       => $this->getCreatedAtAsString(),
        ];
    }

    /**
     * @param  mixed[] $data
     * @throws InvalidDataException
     */
    public function updateFromArray(array $data): void
    {
        $data['hours'] = (float) $data['hours'];
        $data['disease'] = (float) $data['disease'];
        $this->validate($data);

        $this->day             = $data['day'] ? date_from_string($data['day']) : $this->day;
        $this->officeId        = $data['officeId'] ?? $this->officeId;
        $this->employeeId      = $data['employeeId'] ?? $this->employeeId;
        $this->hours           = $data['hours'] ?? $this->hours;
        $this->disease         = $data['disease'] ?? $this->disease;
        $this->justificationCode = $data['justificationCode'] ?? $this->justificationCode;
    }

    /**
     * @param  array $data
     * @throws InvalidDataException
     * TODO: day missing validate
     */
    private function validate(array $data): void
    {
        $assert = Assert::lazy()->tryAll();

        if (isset($data['officeId'])) {
            $assert->that($data['officeId'], 'officeId')->integer()->min(1);
        }
        if (isset($data['employeeId'])) {
            $assert->that($data['employeeId'], 'employeeId')->integer()->min(1);
        }
        // if (isset($data['hours'])) {
        //     $assert->that($data['hours'], 'hours')->numeric()->between(0, 8);
        // }
        // if (isset($data['justificationId'])) {
        //     $assert->that($data['justificationId'], 'justificationId')->integer()->min(1);
        // }
        
        try {
            $assert->verifyNow();
        } catch (LazyAssertionException $lae) {
            throw InvalidDataException::fromLazyAssertionException($lae);
        }
    }
}

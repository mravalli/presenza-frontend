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
* Creation Date:      Tue Oct 13 2020
* Last Modified by:   Mario Ravalli
* Last Modified time: Wed Oct 14 2020
*/

declare(strict_types=1);

namespace Neuro3\Presenza\Model;

use function Neuro3\Presenza\datetime_from_string;
use function Neuro3\Presenza\now;
use Assert\Assert;
use Assert\LazyAssertionException;
use DateTimeImmutable;
use JsonSerializable;
use Neuro3\Exceptions\InvalidDataException;

/**
 * TODO: We can link a table "Contracts" to company for obtain specific
 * settings
 */
class Company extends Model
{
    use ModelTrait;
    protected ?string $vat = null;
    protected ?string $notes = null;
    protected ?string $fullname = null;
    protected string $cf;
    protected string $address;
    protected string $cap;
    protected string $city;
    protected string $phone;
    protected string $email;
    protected int $daysOff = 26;
    protected int $hoursLeave = 32;
    protected DateTimeImmutable $createdAt;

    /**
     * @throws InvalidDataException
     */
    public function __construct(
        string $fullname,
        string $cf,
        string $address,
        string $cap,
        string $city,
        string $phone,
        string $email,
        string $vat = null,
        int $daysOff = 26,
        int $hoursLeave = 32,
        string $notes = null
    ) {
        $this->createdAt = now();

        $this->validate([
            'fullname'   => $fullname,
            'cf'         => $cf,
            'address'    => $address,
            'cap'        => $cap,
            'city'       => $city,
            'phone'      => $phone,
            'email'      => $email,
            'vat'        => $vat,
            'daysOff'    => $daysOff,
            'hoursLeave' => $hoursLeave,
            'notes'      => $notes,
        ]);

        $this->fullname   = $fullname;
        $this->cf         = $cf;
        $this->address    = $address;
        $this->cap        = $cap;
        $this->city       = $city;
        $this->phone      = $phone;
        $this->email      = $email;
        $this->vat        = $vat;
        $this->daysOff    = $daysOff;
        $this->hoursLeave = $hoursLeave;
        $this->notes      = $notes;
    }

    public function getFullname(): string
    {
        return $this->fullname;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function getCap(): ?string
    {
        return $this->cap;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getCf(): string
    {
        return $this->cf;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getVat(): ?string
    {
        return $this->vat;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getDaysOff(): int
    {
        return $this->daysOff;
    }

    public function getHoursLeave(): int
    {
        return $this->hoursLeave;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    /**
     * @return mixed[]
     */
    public function jsonSerialize(): array
    {
        return [
            'id'         => $this->id,
            'fullname'   => $this->fullname,
            'address'    => $this->address,
            'cap'        => $this->cap,
            'city'       => $this->city,
            'cf'         => $this->cf,
            'phone'      => $this->phone,
            'email'      => $this->email,
            'vat'        => $this->vat,
            'daysOff'    => $this->daysOff,
            'hoursLeave' => $this->hoursLeave,
            'notes'      => $this->notes,
            'createdAt'  => $this->getCreatedAtAsString(),
        ];
    }

    /**
     * @param mixed[] $data
     * @throws InvalidDataException
     */
    public function updateFromArray(array $data): void
    {
        $this->validate($data);

        $this->fullname    = $data['fullname'] ?? $this->fullname;
        $this->address     = $data['address'] ?? $this->address;
        $this->cap         = $data['cap'] ?? $this->cap;
        $this->city        = $data['city'] ?? $this->city;
        $this->cf          = $data['cf'] ?? $this->cf;
        $this->phone       = $data['phone'] ?? $this->phone;
        $this->email       = $data['email'] ?? $this->email;
        $this->vat         = $data['vat'] ?? $this->vat;
        $this->daysOff     = $data['daysOff'] ?? $this->daysOff;
        $this->hoursLeave  = $data['hoursLeave'] ?? $this->hoursLeave;
        $this->notes       = $data['notes'] ?? $this->notes;
    }

    /**
     * @param  array $data
     * @throws InvalidDataException
     * TODO: day missing validate
     */
    private function validate(array $data): void
    {
        $assert = Assert::lazy()->tryAll();

        if (isset($data['fullname'])) {
            $assert->that($data['fullname'], 'fullname')->notEmpty()->string()->notBlank();
        }
        if (isset($data['address'])) {
            $assert->that($data['address'], 'address')->notEmpty()->string()->notBlank();
        }
        if (isset($data['cap'])) {
            $assert->that($data['cap'], 'cap')->string()->notBlank()->regex('/\d{5}/');
        }
        if (isset($data['city'])) {
            $assert->that($data['city'], 'city')->string()->notBlank()->regex('/^[a-zA-Z]+(\s[a-zA-Z]+)*$/');
        }
        if (isset($data['phone'])) {
            $assert->that($data['phone'], 'phone')->nullOr()->string()->notBlank()->regex('/((\+|00)?\d{2,3})?\s?\d{1,4}\s?(\d{3}\s(\d{3,4}|(\d{2}\s\d{2}))|\d{2}\s?\d{2}\s?\d{2,3}|\d{5,7})$/');
        }
        if (isset($data['mobile'])) {
            $assert->that($data['mobile'], 'mobile')->nullOr()->string()->notBlank()->regex('/((\+|00)?\d{2,3})?\s?\d{1,4}\s?(\d{3}\s(\d{3,4}|(\d{2}\s\d{2}))|\d{2}\s?\d{2}\s?\d{2,3}|\d{5,7})$/');
        }
        if (isset($data['email'])) {
            $assert->that($data['email'], 'email')->email();
        }
        if (isset($data['vat'])) {
            $assert->that($data['vat'], 'vat')->string()->notBlank()->regex('/[a-zA-Z]{0,2}[0-9]{11}/');
        }
        if (isset($data['daysOff'])) {
            $assert->that($data['daysOff'], 'daysOff')->integer()->min(1);
        }
        if (isset($data['hoursLeave'])) {
            $assert->that($data['hoursLeave'], 'hoursLeave')->integer()->min(1);
        }

        try {
            $assert->verifyNow();
        } catch (LazyAssertionException $lae) {
            throw InvalidDataException::fromLazyAssertionException($lae);
        }
    }
}

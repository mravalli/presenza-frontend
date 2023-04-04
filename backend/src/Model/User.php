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
 * Creation Date: 2020-11-24 19:27:24
 * Modified by:   Mario Ravalli
 * Last Modified: 2020-12-17 23:42:45
 */
declare(strict_types=1);

namespace Neuro3\Presenza\Model;

use const Neuro3\Presenza\DATETIME_FORMAT;
use function Neuro3\Presenza\datetime_from_string;
use function Neuro3\Presenza\now;
use Assert\Assert;
use Assert\LazyAssertionException;
use DateTimeImmutable;
use JsonSerializable;
use Neuro3\Exceptions\InvalidDataException;

class User extends Model
{
    use ModelTrait;

    protected string $firstname;
    protected string $lastname;
    protected ?string $username = null;
    protected ?string $passwordHash = null;
    protected ?string $email = null;
    protected ?string $primaryKey = null;
    protected ?string $secondaryKey = null;
    protected int $status = 1;
    protected int $role = 9;
    protected DateTimeImmutable $createdAt;

    public function __construct(
        string $firstname,
        string $lastname,
        int $status,
        int $role,
        string $email = null
    ) {
        $this->createdAt = now();

        $this->validate(compact('firstname', 'lastname', 'status', 'role', 'email'));
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->status = $status;
        $this->role = $role;
        $this->email = $email;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getRole(): int
    {
        return $this->role;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function setPasswordHash($password)
    {
        $this->passwordHash = password_hash($password, PASSWORD_BCRYPT);
    }

    public function getPrimaryKey(): string
    {
        return $this->primaryKey;
    }

    public function setPrimaryKey($primaryKey)
    {
        $this->primaryKey = $primaryKey;
    }

    public function getSecondaryKey(): string
    {
        return $this->secondaryKey;
    }

    public function setSecondaryKey($secondaryKey)
    {
        $this->secondaryKey = $secondaryKey;
    }

    public function jsonserialize(): array
    {
        return [
            'id'           => $this->id,
            'firstname'    => $this->firstname,
            'lastname'     => $this->lastname,
            'username'     => $this->username,
            'passwordHash' => $this->passwordHash,
            'primaryKey'   => $this->primaryKey,
            'secondaryKey' => $this->secondaryKey,
            'email'        => $this->email,
            'status'       => $this->status,
            'role'         => $this->role
        ];
    }

    public function updateFromArray(array $data): void
    {
        $this->validate($data);

        $this->firstname    = $data['firstname'] ?? $this->firstname;
        $this->lastname     = $data['lastname'] ?? $this->lastname;
        $this->username     = $data['username'] ?? $this->username;
        $this->passwordHash = $data['passwordHash'] ?? $this->passwordHash;
        $this->primaryKey   = $data['primaryKey'] ?? $this->primaryKey;
        $this->secondaryKey = $data['secondaryKey'] ?? $this->secondaryKey;
        $this->email        = $data['email'] ?? $this->email;
        $this->status       = $data['status'] ?? $this->status;
        $this->role         = $data['role'] ?? $this->role;

        if (isset($data['password'])) {
            $this->setPasswordHash($data['password']);
        }
    }

    private function validate(array $data): void
    {
        $assert = Assert::lazy()->tryAll();

        if (isset($data['firstname'])) {
            $assert->that($data['firstname'])->string()->notBlank();
        }
        if (isset($data['lastname'])) {
            $assert->that($data['lastname'])->string()->notBlank();
        }
        if (isset($data['username'])) {
            $assert->that($data['username'])->string()->notBlank();
        }
        if (isset($data['status'])) {
            $assert->that($data['status'])->notEmpty()->integer();
        }
        if (isset($data['role'])) {
            $assert->that($data['role'])->notEmpty()->integer();
        }
        if (isset($data['email'])) {
            $assert->that($data['email'])->nullOr()->email();
        }

        try {
            $assert->verifyNow();
        } catch (LazyAssertionException $lae) {
            throw InvalidDataException::fromLazyAssertionException($lae);
        }
    }
}

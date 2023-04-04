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

namespace Neuro3\Presenza\Security;

use Env\Env;
use Firebase\JWT\JWT;
use Neuro3\Presenza\Model\User;
use Neuro3\Presenza\Model\UserDataMapper;
use Neuro3\Security\AuthenticationAdapterInterface;
use Neuro3\Security\Result;

class AuthenticationAdapter extends UserDataMapper implements AuthenticationAdapterInterface
{
    private string $username;
    private string $password;
    private string $primaryKey;
    private string $secondaryKey;
    private User $user;

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }
    public function setPrimaryKey(string $primaryKey): void
    {
        $this->primaryKey = $primaryKey;
    }
    public function setSecondaryKey(string $secondaryKey): void
    {
        $this->secondaryKey = $secondaryKey;
    }

    /**
     * Performs an authentication attempt
     *
     * @return \Neuro3\Security\Result
     * @throws \Neuro3\Exception\AuthenticationException;
     *     If authentication cannot be performed
     */
    public function authenticate() : Result
    {
        $stmt = $this->link->prepare("SELECT
                `id`,
                `firstname`,
                `lastname`,
                `email`,
                `username`,
                `password_hash` AS `passwordHash`,
                `status`,
                `role`,
                DATE_FORMAT(`created_at`, '%Y-%m-%dT%T.000Z') as `created_at`
            FROM `users`
            WHERE `username` = ?
        ");
        $stmt->bind_param('s', $this->username);
        $stmt->execute();

        $result = $stmt->get_result();
        if ($result === false) {
            throw new RuntimeException('MySQL failed to execute a statement');
        }

        $row = $result->fetch_assoc();
        if ($row === null) {
            return new Result(Result::FAILURE_IDENTITY_NOT_FOUND, null, 'Identity Not Found');
        }

        if (password_verify($this->password, $row['passwordHash'])) {
            $this->user = $this->createUserFromRow($row);
            
            $access_token = $this->createAccessToken();
            $refresh_token = $this->createRefreshToken();
            return new Result(Result::SUCCESS, ['access_token' => $access_token, 'refresh_token' => $refresh_token], 'Authentication Success');
        } else {
            return new Result(Result::FAILURE_CREDENTIAL_INVALID, null, 'Authentication Failed');
        }
    }

    public function refreshToken($oldToken)
    {
        $this->user = $this->find((int) $oldToken->data->id);
        if ($this->user === null) {
            return new Result(Result::FAILURE_IDENTITY_NOT_FOUND, null, 'Identity Not Found');
        }
        if ($this->user->getSecondaryKey() !== $oldToken->prm) {
            return new Result(Result::FAILURE_IDENTITY_AMBIGUOUS, null, 'Identity Ambigous');
        }
        $access_token = $this->createAccessToken();

        return new Result(Result::SUCCESS, ['access_token' => $access_token], 'Token Refreshed Successfully');
    }

    private function createAccessToken()
    {
        $secret_key = Env::get('SECRET_KEY');
        $issuer_claim = Env::get('ISSUER');
        $issued_at = time();
        $not_before = $issued_at;
        $expire = $issued_at + 3600;
        $this->primaryKey = bin2hex(random_bytes(16));
        $this->savePrimaryKey();
        return JWT::encode([
            "iss"  => $issuer_claim,
            "iat"  => $issued_at,
            "nbf"  => $not_before,
            "exp"  => $expire,
            "prm"  => $this->primaryKey,
            "data" => [
                "id" => $this->user->getId(),
                "firstname" => $this->user->getFirstname(),
                "lastname" => $this->user->getLastname(),
                "email" => $this->user->getEmail(),
                "role" => $this->user->getRole()
            ]
        ], $secret_key);
    }

    private function createRefreshToken()
    {
        $secret_key = Env::get('SECRET_KEY');
        $issuer_claim = Env::get('ISSUER');
        $issued_at = time();
        $not_before = $issued_at + 3;
        $expire = $issued_at + 7776000;
        $this->secondaryKey = bin2hex(random_bytes(16));
        $this->saveSecondaryKey();
        return JWT::encode([
            "iss"  => $issuer_claim,
            "iat"  => $issued_at,
            "nbf"  => $not_before,
            "exp"  => $expire,
            "prm"  => $this->secondaryKey,
            "data" => [
                "id" => $this->user->getId(),
            ]
        ], $secret_key);
    }

    private function savePrimaryKey(): void
    {
        $stmt = $this->link->prepare("UPDATE users
            SET
                `primary_key` = ?
            WHERE `id` = ?
        ");
        $id = $this->user->getId();

        $stmt->bind_param(
            'si',
            $this->primaryKey,
            $id
        );
        $result = $stmt->execute();

        if ($result === false) {
            throw new RuntimeException('MySQL failed to execute a statement');
        }
    }

    private function saveSecondaryKey(): void
    {
        $stmt = $this->link->prepare("UPDATE users
            SET
                `secondary_key` = ?
            WHERE `id` = ?
        ");
        $id = $this->user->getId();

        $stmt->bind_param(
            'si',
            $this->secondaryKey,
            $id
        );
        $result = $stmt->execute();

        if ($result === false) {
            throw new RuntimeException('MySQL failed to execute a statement');
        }
    }
}

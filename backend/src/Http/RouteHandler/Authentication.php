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
 * Creation Date: 2020-11-24 17:15:50
 * Modified by:   Mario Ravalli
 * Last Modified: 2021-08-03 16:52:29
 */
declare(strict_types=1);

namespace Neuro3\Presenza\Http\RouteHandler;

use Env\Env;
use Firebase\JWT\JWT;
use Laminas\Diactoros\Response\JsonResponse;
use League\Route\Http\Exception\BadRequestException;
use Neuro3\Security\Result;
use Neuro3\Presenza\Http\RouteHandler;
use Neuro3\Presenza\Security\AuthenticationAdapter;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Authentication
{
    /**
     * @var AuthAdapter
     */
    private $authAdapter;

    public function __construct(AuthenticationAdapter $authAdapter)
    {
        $this->authAdapter = $authAdapter;
    }
    
    public function login(Request $request, array $args): Response
    {
        $requestBody = $request->getParsedBody();

        if (!is_array($requestBody) &&
            !isset($requestBody['username']) &&
            !isset($requestBody['password']) &&
            (empty($requestBody['username']) || empty($requestBody['password']))
        ) {
            throw new BadRequestException('Invalid request body');
        }
        $this->authAdapter->setUsername($requestBody['username']);
        $this->authAdapter->setPassword($requestBody['password']);
        $result = $this->authAdapter->authenticate();
        $httpCode = 200;
        if ($result->getCode() !== Result::SUCCESS) {
            $httpCode = 403;
        }
        return new JsonResponse(['result' => $result, 'jwt' => $result->getIdentity()], $httpCode);
    }

    public function add(Request $request, array $args): Response
    {
        $requestBody = $request->getParsedBody();
        if (! is_array($requestBody)) {
            throw new BadRequestException('Invalid request body');
        }
    }

    public function refresh(Request $request, array $args): Response
    {
        $requestBody = $request->getParsedBody();
        if (!is_array($requestBody) && !isset($requestBody['token']) && empty($requestBody['token'])) {
            throw new BadRequestException('Invalid Token');
        }
        $secret_key = Env::get('SECRET_KEY');
        try {
            $decodedToken = JWT::decode($requestBody['token'], $secret_key, ['HS256']);
            $newToken = $this->authAdapter->refreshToken($decodedToken);
            $httpCode = 200;
            if ($newToken->getCode() !== Result::SUCCESS) {
                $httpCode = 403;
            }
            return new JsonResponse(['result' => $newToken, 'token' => $newToken->getIdentity()], $httpCode);
        } catch (\Exception $exc) {
            $exc;
        }

        return new JsonResponse('Token Not Valid or Expired', 403);
    }
}

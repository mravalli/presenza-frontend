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

namespace Neuro3\Security;

use function Neuro3\Presenza\logger;
use Env\Env;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * TODO: Remove Access-Control in Production
 */
class AuthMiddleware implements MiddlewareInterface
{
    protected $authService;

    public function __construct(AuthenticationService $authService)
    {
        $this->authService = $authService;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $authHeader = $request->getHeader('AUTHORIZATION');
        if (!empty($authHeader)) {
            $secret_key = Env::get('SECRET_KEY');
            $jwt = (explode(" ", $authHeader[0]))[1];

            try {
                $decode = JWT::decode($jwt, $secret_key, ['HS256']);
                $this->authService->setIdentity($decode->data->id);
                return $handler->handle($request);
            } catch (ExpiredException $expiredException) {
                return new JsonResponse('Token Expired', 403, ['Access-Control-Allow-Origin' => '*']);
            } catch (\Exception $err) {
                logger()->error($err, ['exception' => $err, 'request' => $request ?? null]);
                return new JsonResponse('Ooops, Exception', 500);
            }
        }
        
        return new JsonResponse('Access Denied '. $this->authService->getIdentity(), 401);
    }
}

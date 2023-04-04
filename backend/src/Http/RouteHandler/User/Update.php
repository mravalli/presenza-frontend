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
 * Creation Date: 2020-12-17 17:05:47
 * Modified by:   Mario Ravalli
 * Last Modified: 2020-12-17 23:26:30
 */
declare(strict_types=1);

namespace Neuro3\Presenza\Http\RouteHandler\User;

use Neuro3\Presenza\Http\RouteHandler;
use Neuro3\Presenza\Model\InvalidDataException;
use Neuro3\Presenza\Model\UserDataMapper;
use League\Route\Http\Exception\BadRequestException;
use League\Route\Http\Exception\NotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Laminas\Diactoros\Response\JsonResponse;

class Update implements RouteHandler
{
    /**
     * @var UserDataMapper
     */
    private $userDataMapper;

    public function __construct(UserDataMapper $userDataMapper)
    {
        $this->userDataMapper = $userDataMapper;
    }

    /**
     * @param Request $request
     * @param string[] $args
     *
     * @return Response
     *
     * @throws BadRequestException
     * @throws InvalidDataException
     * @throws NotFoundException
     */
    public function __invoke(Request $request, array $args): Response
    {
        $user = $this->userDataMapper->find((int) $args['id']);

        if ($user === null) {
            throw new NotFoundException('Resource not found');
        }

        $requestBody = $request->getParsedBody();

        if (! is_array($requestBody)) {
            throw new BadRequestException('Invalid request body');
        }

        $user->updateFromArray($requestBody);
        $this->userDataMapper->update($user);

        if (isset($requestBody['password'])) {
            $this->userDataMapper->updateCredentials($user);
        }

        return new JsonResponse($user, 200);
    }
}

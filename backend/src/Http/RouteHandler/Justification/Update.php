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
 * Creation Date: 2020-11-10 17:55:04
 * Modified by:   Mario Ravalli
 * Last Modified: 2020-11-10 18:33:18
 */
declare(strict_types=1);

namespace Neuro3\Presenza\Http\RouteHandler\Justification;

use Neuro3\Presenza\Http\RouteHandler;
use Neuro3\Presenza\Model\InvalidDataException;
use Neuro3\Presenza\Model\JustificationDataMapper;
use League\Route\Http\Exception\BadRequestException;
use League\Route\Http\Exception\NotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Laminas\Diactoros\Response\JsonResponse;

class Update implements RouteHandler
{
    /**
     * @var JustificationDataMapper
     */
    private $justificationDataMapper;

    public function __construct(JustificationDataMapper $justificationDataMapper)
    {
        $this->justificationDataMapper = $justificationDataMapper;
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
        // if (! Uuid::isValid($args['id'])) {
        //     throw new BadRequestException('Invalid UUID');
        // }

        $item = $this->justificationDataMapper->find((int) $args['id']);

        if ($item === null) {
            throw new NotFoundException('Resource not found');
        }

        $requestBody = $request->getParsedBody();

        if (! is_array($requestBody)) {
            throw new BadRequestException('Invalid request body');
        }

        $item->updateFromArray($requestBody);

        $this->justificationDataMapper->update($item);

        return new JsonResponse($item, 200);
    }
}

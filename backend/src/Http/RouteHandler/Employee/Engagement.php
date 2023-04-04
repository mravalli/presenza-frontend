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
 * Creation Date: 2020-11-18 17:55:22
 * Modified by:   Mario Ravalli
 * Last Modified: 2020-11-19 21:28:52
 */
declare(strict_types=1);

namespace Neuro3\Presenza\Http\RouteHandler\Employee;

use Laminas\Diactoros\Response\JsonResponse;
use League\Route\Http\Exception\BadRequestException;
use League\Route\Http\Exception\NotFoundException;
use Neuro3\Presenza\Http\RouteHandler;
use Neuro3\Presenza\Model\InvalidDataException;
use Neuro3\Presenza\Model\Engagement as EngagementModel;
use Neuro3\Presenza\Model\EngagementDataMapper;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Engagement implements RouteHandler
{
    /**
     * @var EngagementDataMapper
     */
    private $engagementDataMapper;

    public function __construct(EngagementDataMapper $engagementDataMapper)
    {
        $this->engagementDataMapper = $engagementDataMapper;
    }

    /**
     * @param Request $request
     * @param string[] $args
     *
     * @return Response
     *
     * @throws Http\Exception\BadRequestException
     * @throws Http\Exception\NotFoundException
     */
    public function __invoke(Request $request, array $args): Response
    {
        $item = $this->engagementDataMapper->findActualFor((int) $args['id']);

        if ($item === null) {
            throw new Http\Exception\NotFoundException('Resource not found');
        }

        return new JsonResponse(['engagement' => $item]);
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
    public function add(Request $request, array $args): Response
    {
        $requestBody = $request->getParsedBody();

        if (! is_array($requestBody)) {
            throw new BadRequestException('Invalid request body');
        }

        $item = EngagementModel::createFromArray($requestBody);

        if ($this->engagementDataMapper->conflict($item)) {
            return new JsonResponse(['error' => 'There is a Conflict', 'errno' => EngagementDataMapper::ERROR_CONFLICT], 422);
        }

        $this->engagementDataMapper->insert($item);
        $items = $this->engagementDataMapper->getAllFor((int) $args['id']);

        return new JsonResponse($items, 201);
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
    public function update(Request $request, array $args): Response
    {
        $item = $this->engagementDataMapper->find((int) $args['eid']);

        if ($item === null) {
            throw new NotFoundException('Resource not found');
        }

        $requestBody = $request->getParsedBody();

        if (! is_array($requestBody)) {
            throw new BadRequestException('Invalid request body');
        }

        $item->updateFromArray($requestBody);

        $this->engagementDataMapper->update($item);
        $items = $this->engagementDataMapper->getAllFor((int) $args['id']);

        return new JsonResponse($items, 201);
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
    public function delete(Request $request, array $args): Response
    {
        $item = $this->engagementDataMapper->find((int) $args['eid']);

        if ($item === null) {
            throw new NotFoundException('Resource not found');
        }

        $this->engagementDataMapper->delete((int) $args['eid']);
        $items = $this->engagementDataMapper->getAllFor((int) $args['id']);

        return new JsonResponse($items, 201);
    }
}

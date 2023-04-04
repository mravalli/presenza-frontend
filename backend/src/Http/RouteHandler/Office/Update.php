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
 * Creation Date: 2020-10-30 18:15:25
 * Modified by:   Mario Ravalli
 * Last Modified: 2020-10-30 18:16:41
 */
declare(strict_types=1);

namespace Neuro3\Presenza\Http\RouteHandler\Office;

use Neuro3\Presenza\Http\RouteHandler;
use Neuro3\Presenza\Model\InvalidDataException;
use Neuro3\Presenza\Model\OfficeDataMapper;
use League\Route\Http\Exception\BadRequestException;
use League\Route\Http\Exception\NotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Laminas\Diactoros\Response\JsonResponse;

class Update implements RouteHandler
{
    /**
     * @var OfficeDataMapper
     */
    private $officeDataMapper;

    public function __construct(OfficeDataMapper $officeDataMapper)
    {
        $this->officeDataMapper = $officeDataMapper;
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

        $item = $this->officeDataMapper->find((int) $args['id']);

        if ($item === null) {
            throw new NotFoundException('Resource not found');
        }

        $requestBody = $request->getParsedBody();

        if (! is_array($requestBody)) {
            throw new BadRequestException('Invalid request body');
        }

        $item->updateFromArray($requestBody);

        $this->officeDataMapper->update($item);

        return new JsonResponse($item, 200);
    }
}

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
 * Creation Date: 2020-10-30 17:20:32
 * Modified by:   Mario Ravalli
 * Last Modified: 2020-10-30 17:22:28
 */
declare(strict_types=1);

namespace Neuro3\Presenza\Http\RouteHandler\Office;

use Neuro3\Presenza\Http\RouteHandler;
use Neuro3\Presenza\Model\InvalidDataException;
use Neuro3\Presenza\Model\Office;
use Neuro3\Presenza\Model\OfficeDataMapper;
use League\Route\Http\Exception\BadRequestException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Laminas\Diactoros\Response\JsonResponse;

class Create implements RouteHandler
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
     */
    public function __invoke(Request $request, array $args): Response
    {
        $requestBody = $request->getParsedBody();

        if (! is_array($requestBody)) {
            throw new BadRequestException('Invalid request body');
        }

        $item = Office::createFromArray($requestBody);

        $this->officeDataMapper->insert($item);

        return new JsonResponse($item, 201);
    }
}

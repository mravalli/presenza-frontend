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
 * Creation Date: 2020-11-10 17:48:42
 * Modified by:   Mario Ravalli
 * Last Modified: 2020-11-10 18:33:32
 */
declare(strict_types=1);

namespace Neuro3\Presenza\Http\RouteHandler\Justification;

use Neuro3\Presenza\Http\RouteHandler;
use Neuro3\Presenza\Model\InvalidDataException;
use Neuro3\Presenza\Model\Justification;
use Neuro3\Presenza\Model\JustificationDataMapper;
use League\Route\Http\Exception\BadRequestException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Laminas\Diactoros\Response\JsonResponse;

class Create implements RouteHandler
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
     */
    public function __invoke(Request $request, array $args): Response
    {
        $requestBody = $request->getParsedBody();

        if (! is_array($requestBody)) {
            throw new BadRequestException('Invalid request body');
        }

        $item = Justification::createFromArray($requestBody);

        $this->justificationDataMapper->insert($item);

        return new JsonResponse($item, 201);
    }
}

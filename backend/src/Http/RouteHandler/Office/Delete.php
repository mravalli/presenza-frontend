<?php

/**
 * This file is part of the "backend"
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Mario Ravalli <mario@raval.li>
 * @license https://www.lareclameitalia.com/software/backend/license
 * @link https://www.lareclameitalia.com/software/backend
 *
 * @Author: Mario Ravalli
 * @Date:   2020-11-08 16:05:11
 * @Last Modified by:   Mario Ravalli
 * @Last Modified time: 2020-11-08 16:06:06
 */
declare(strict_types=1);

namespace Neuro3\Presenza\Http\RouteHandler\Office;

use RuntimeException;
use League\Route\Http;
use Neuro3\Presenza\Http\RouteHandler;
use Neuro3\Presenza\Model\OfficeDataMapper;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\JsonResponse;

class Delete implements RouteHandler
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
     * @throws Http\Exception\BadRequestException
     * @throws Http\Exception\NotFoundException
     */
    public function __invoke(Request $request, array $args): Response
    {
        // if (! Uuid::isValid($args['id'])) {
        //     throw new Http\Exception\BadRequestException('Invalid UUID');
        // }

        $item = $this->officeDataMapper->find((int) $args['id']);

        if ($item === null) {
            throw new Http\Exception\NotFoundException('Resource not found');
        }

        try {
            $this->officeDataMapper->delete((int) $args['id']);
        } catch (RuntimeException $exc) {
            return new JsonResponse('Cannot Delete a filled Resource', 412);
        }

        return new EmptyResponse();
    }
}

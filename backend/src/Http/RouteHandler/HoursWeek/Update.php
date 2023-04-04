<?php

/**
 * This file is part of the "Presenza"
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Mario Ravalli <mario@raval.li>
 * @license https://www.lareclameitalia.com/software/Presenza/license
 * @link https://www.lareclameitalia.com/software/Presenza
 *
 * @Author: Mario Ravalli
 * @Date:   2020-11-08 13:29:02
 * @Last Modified by:   Mario Ravalli
 * @Last Modified time: 2020-11-08 13:30:00
 */
declare(strict_types=1);

namespace Neuro3\Presenza\Http\RouteHandler\HoursWeek;

use Neuro3\Presenza\Http\RouteHandler;
use Neuro3\Presenza\Model\InvalidDataException;
use Neuro3\Presenza\Model\HoursWeekDataMapper;
use League\Route\Http\Exception\BadRequestException;
use League\Route\Http\Exception\NotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Laminas\Diactoros\Response\JsonResponse;

class Update implements RouteHandler
{
    /**
     * @var HoursWeekDataMapper
     */
    private $hoursWeekDataMapper;

    public function __construct(HoursWeekDataMapper $hoursWeekDataMapper)
    {
        $this->hoursWeekDataMapper = $hoursWeekDataMapper;
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

        $item = $this->hoursWeekDataMapper->find((int) $args['id']);

        if ($item === null) {
            throw new NotFoundException('Resource not found');
        }

        $requestBody = $request->getParsedBody();

        if (! is_array($requestBody)) {
            throw new BadRequestException('Invalid request body');
        }

        $item->updateFromArray($requestBody);

        $this->hoursWeekDataMapper->update($item);

        return new JsonResponse($item, 200);
    }
}

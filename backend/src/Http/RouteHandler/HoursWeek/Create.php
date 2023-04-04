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
 * @Date:   2020-11-08 13:25:35
 * @Last Modified by:   Mario Ravalli
 * @Last Modified time: 2020-11-08 14:55:21
 */
declare(strict_types=1);

namespace Neuro3\Presenza\Http\RouteHandler\HoursWeek;

use Neuro3\Presenza\Http\RouteHandler;
use Neuro3\Presenza\Model\InvalidDataException;
use Neuro3\Presenza\Model\HoursWeek;
use Neuro3\Presenza\Model\HoursWeekDataMapper;
use League\Route\Http\Exception\BadRequestException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Laminas\Diactoros\Response\JsonResponse;

class Create implements RouteHandler
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
     */
    public function __invoke(Request $request, array $args): Response
    {
        $requestBody = $request->getParsedBody();

        if (! is_array($requestBody)) {
            throw new BadRequestException('Invalid request body');
        }

        $item = HoursWeek::createFromArray($requestBody);

        $this->hoursWeekDataMapper->insert($item);

        return new JsonResponse($item, 201);
    }
}

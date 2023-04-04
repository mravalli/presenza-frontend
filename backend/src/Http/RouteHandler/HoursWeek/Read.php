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
 * @Date:   2020-11-08 13:27:04
 * @Last Modified by:   Mario Ravalli
 * @Last Modified time: 2020-11-08 15:39:04
 */
declare(strict_types=1);

namespace Neuro3\Presenza\Http\RouteHandler\HoursWeek;

use League\Route\Http;
use Neuro3\Presenza\Http\RouteHandler;
use Neuro3\Presenza\Model\HoursWeekDataMapper;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Laminas\Diactoros\Response\JsonResponse;

class Read implements RouteHandler
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
     * @throws Http\Exception\BadRequestException
     * @throws Http\Exception\NotFoundException
     */
    public function __invoke(Request $request, array $args): Response
    {
        $hoursWeek = $this->hoursWeekDataMapper->find((int) $args['id']);

        if ($hoursWeek === null) {
            throw new Http\Exception\NotFoundException('Resource not found');
        }

        return new JsonResponse(['hoursWeek' => $hoursWeek]);
    }
}

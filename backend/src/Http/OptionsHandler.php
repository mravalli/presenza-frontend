<?php declare(strict_types=1);
/**
* This file is part of the project: Presenza
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code
*
* @author:    Mario Ravalli <mario@raval.li>
* @copyright: Copyright (c) 2020 Mario Ravalli
* @license:   GNU General Public License v3.0 or later
*
*
* Creation Date:      Wed Oct 07 2020
* Last Modified by:   Mario Ravalli
* Last Modified time: Mon Oct 12 2020
*/

namespace Neuro3\Presenza\Http;

use Neuro3\Presenza\Http\RouteHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Laminas\Diactoros\Response\EmptyResponse;

class OptionsHandler implements RouteHandler
{
    /**
     * @param Request $request
     * @param string[] $args
     *
     * @return Response
     */
    public function __invoke(Request $request, array $args): Response
    {
        return new EmptyResponse(200, [
            'Access-Control-Allow-Methods' => 'POST, GET, OPTIONS, PATCH, DELETE',
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Headers' => 'Authorization, Content-Type',
            'Access-Control-Max-Age' => '86400'
        ]);
    }
}

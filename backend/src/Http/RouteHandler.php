<?php
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
 * Creation Date: Tuesday September 8th 2020
 * Modified By:
 * Last Modified: 
 */
declare(strict_types=1);

namespace Neuro3\Presenza\Http;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

interface RouteHandler
{
    /**
     * @param Request $request
     * @param string[] $args
     * @return Response
     */
    public function __invoke(Request $request, array $args): Response;
}

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
 * Creation Date: 2020-11-10 17:53:46
 * Modified by:   Mario Ravalli
 * Last Modified: 2020-11-11 11:32:29
 */
declare(strict_types=1);

namespace Neuro3\Presenza\Http\RouteHandler\Justification;

use League\Route\Http;
use Neuro3\Presenza\Http\RouteHandler;
use Neuro3\Presenza\Model\JustificationDataMapper;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Laminas\Diactoros\Response\JsonResponse;

class Read implements RouteHandler
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
     * @throws Http\Exception\BadRequestException
     * @throws Http\Exception\NotFoundException
     */
    public function __invoke(Request $request, array $args): Response
    {
        $justification = $this->justificationDataMapper->find((int) $args['id']);

        if ($justification === null) {
            throw new Http\Exception\NotFoundException('Resource not found');
        }

        return new JsonResponse(['justification' => $justification]);
    }
}

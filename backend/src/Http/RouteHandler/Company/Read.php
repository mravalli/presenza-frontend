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
 * Creation Date:      Wed Oct 14 2020
 * Last Modified by:   Mario Ravalli
 * Last Modified time: Wed Oct 14 2020
 */
declare(strict_types=1);

namespace Neuro3\Presenza\Http\RouteHandler\Company;

use League\Route\Http;
use Neuro3\Presenza\Http\RouteHandler;
use Neuro3\Presenza\Model\CompanyDataMapper;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Ramsey\Uuid\Uuid;
use Laminas\Diactoros\Response\JsonResponse;

class Read implements RouteHandler
{
    /**
     * @var CompanyDataMapper
     */
    private $companyDataMapper;

    public function __construct(CompanyDataMapper $companyDataMapper)
    {
        $this->companyDataMapper = $companyDataMapper;
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

        $item = $this->companyDataMapper->get();

        if ($item === null) {
            throw new Http\Exception\NotFoundException('Resource not found');
        }

        return new JsonResponse($item);
    }
}

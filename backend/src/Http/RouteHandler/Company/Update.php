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

use Neuro3\Presenza\Http\RouteHandler;
use Neuro3\Presenza\Model\InvalidDataException;
use Neuro3\Presenza\Model\CompanyDataMapper;
use League\Route\Http\Exception\BadRequestException;
use League\Route\Http\Exception\NotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
// use Ramsey\Uuid\Uuid;
use Laminas\Diactoros\Response\JsonResponse;

class Update implements RouteHandler
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
     * @throws BadRequestException
     * @throws InvalidDataException
     * @throws NotFoundException
     */
    public function __invoke(Request $request, array $args): Response
    {
        // if (! Uuid::isValid($args['id'])) {
        //     throw new BadRequestException('Invalid UUID');
        // }

        $item = $this->companyDataMapper->get();

        if ($item === null) {
            throw new NotFoundException('Resource not found');
        }

        $requestBody = $request->getParsedBody();

        if (! is_array($requestBody)) {
            throw new BadRequestException('Invalid request body');
        }

        $item->updateFromArray($requestBody);

        $this->companyDataMapper->update($item);

        return new JsonResponse($item, 200);
    }
}

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
 * Creation Date: 2020-11-25 16:48:32
 * Modified by:   Mario Ravalli
 * Last Modified: 2020-11-25 22:42:10
 */
declare(strict_types=1);

namespace Neuro3\Presenza\Config;

use Neuro3\Security\AuthenticationService;
use Neuro3\Security\AuthenticationServiceInterface;
use Neuro3\Presenza\Security\AuthenticationAdapter;

use Psr\Container\ContainerInterface as Container;

class AuthenticationFactory
{
    public function __invoke(Container $container): AuthenticationServiceInterface
    {
        return new AuthenticationService($container->get(AuthenticationAdapter::class));
    }
}

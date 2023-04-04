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
 * Creation Date: 2020-11-25 17:54:58
 * Modified by:   Mario Ravalli
 * Last Modified: 2020-11-25 17:56:19
 */
declare(strict_types=1);

namespace Neuro3\Security;

interface AuthenticationServiceInterface
{
    public function authenticate();

    public function hasIdentity();

    public function getIdentity();

    public function clearIdentity();
}

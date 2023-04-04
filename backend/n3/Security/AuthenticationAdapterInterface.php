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
 * Creation Date: 2020-11-25 19:03:22
 * Modified by:   Mario Ravalli
 * Last Modified: 2020-11-25 19:04:10
 */
declare(strict_types=1);

namespace Neuro3\Security;

interface AuthenticationAdapterInterface
{
    public function authenticate();
}

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
 * Creation Date: 2020-11-25 17:53:58
 * Modified by:   Mario Ravalli
 * Last Modified: 2020-11-25 23:50:48
 */
declare(strict_types=1);

namespace Neuro3\Security;

class AuthenticationService implements AuthenticationServiceInterface
{
    protected $identity = null;

    protected $adapter;

    public function __construct(AuthenticationAdapterInterface $adapter)
    {
        if (null !== $adapter) {
            $this->adapter = $adapter;
        }
    }

    public function authenticate()
    {
        if (!$this->adapter) {
            throw new Exception("An adapter must be set or passed prior to calling authenticate()", 1);
        }
        $result = $this->adapter->authenticate();

        if ($this->hasIdentity()) {
            $this->clearIdentity();
        }

        if ($result) {
            $this->identity = $result->getIdentity();
        }

        return $result;
    }

    public function hasIdentity()
    {
        return !$this->identity;
    }

    public function getIdentity()
    {
        return $this->identity;
    }

    public function setIdentity(int $id)
    {
        $this->identity = $this->adapter->find($id);
    }

    public function clearIdentity()
    {
        $this->identity = null;
    }
}

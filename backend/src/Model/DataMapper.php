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
 * Creation Date: Thursday October 22nd 2020
 * Modified By:   Thursday October 22nd 2020 17:23:22
 * Last Modified: 2021-08-03 17:41:13
 */
declare(strict_types=1);

namespace Neuro3\Presenza\Model;

use Neuro3\Model\DbAdapter;

class DataMapper
{
    /**
     * @var MySQLi
     */
    protected $link;

    public function __construct(DbAdapter $dba)
    {
        $this->link = $dba->link;
    }
}

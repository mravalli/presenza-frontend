<?php
/**
* This file is part of the project: Neuro3 toolkit
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code
*
* @author:    Mario Ravalli <mario@raval.li>
* @copyright: Copyright (c) 2020 Mario Ravalli
* @license:   GNU General Public License v3.0 or later
*
*
* Creation Date:      Wed Sep 09 2020
* Last Modified by:   Mario Ravalli
* Last Modified time: Wed Sep 09 2020
*/
declare(strict_types=1);

namespace Neuro3\Security;

use Env\Env;
use mysqli;
use RuntimeException;

class DbManager
{
    /**
     * @var MySQLi
     */
    private $link;

    public function __construct()
    {
        $this->link = new MySQLi(Env::get('DB_HOST'), Env::get('DB_USER'), Env::get('DB_PASS'), Env::get('DB_NAME'));
        $this->link->set_charset("utf8");
    }

    public function fetchRow($sql, $params = null)
    {
        if (!is_null($params)) {
            $sql = str_replace(array_keys($params), $params, $sql);
        }
        if ($rst = $this->link->query($sql)) {
            $obj = $rst->fetch_object();
            $rst->close();
            return $obj;
        } elseif ($this->link->errno) {
            throw new RuntimeException("Failed to execute query: (" . $this->link->errno . ") " . $this->link->error . PHP_EOL . $sql . PHP_EOL);
        }
    }
}

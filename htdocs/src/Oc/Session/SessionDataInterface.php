<?php
/***************************************************************************
 * for license information see LICENSE.md
 *
 *
 *  Interface for session data handling
 ***************************************************************************/

namespace Oc\Session;

interface SessionDataInterface
{
    public function __construct();

    public function set($name, $value, $default = null);

    public function get($name, $default = null);

    public function is_set($name);

    public function un_set($name);

    public function header();

    public function close();
}

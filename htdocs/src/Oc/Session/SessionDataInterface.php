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

    public function set(string $name, $value, $default = null);

    public function get(string $name, $default = null);

    public function is_set(string $name);

    public function un_set(string $name);

    public function header();

    public function close();
}

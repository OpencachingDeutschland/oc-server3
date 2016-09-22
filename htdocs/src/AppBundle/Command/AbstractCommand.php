<?php
/***************************************************************************
 *  For license information see doc/license.txt
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;

abstract class AbstractCommand extends Command
{
    const CODE_SUCCESS  = 0;
    const CODE_ERROR    = 1;
    const CODE_WARNING  = 2;

    /**
     * @var string
     */
    protected $rootPath;

    /**
     * @param string|null $name
     *
     * @throws \Symfony\Component\Console\Exception\LogicException
     */
    public function __construct($name = null)
    {
        parent::__construct($name);

        $this->rootPath = dirname(__DIR__ . '/../../../');
    }
}

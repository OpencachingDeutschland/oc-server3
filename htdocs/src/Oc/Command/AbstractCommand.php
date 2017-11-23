<?php
/***************************************************************************
 * for license information see LICENSE.md
 ***************************************************************************/

namespace Oc\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;

/**
 * Class AbstractCommand
 */
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
     * @throws LogicException
     */
    public function __construct($name = null)
    {
        parent::__construct($name);

        $this->rootPath = dirname(dirname(dirname(__DIR__))) . '/';
    }
}

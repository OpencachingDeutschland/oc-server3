<?php
/***************************************************************************
 * for license information see LICENSE.md
 ***************************************************************************/

namespace Oc\Command;

use Symfony\Component\Console\Command\Command;

abstract class AbstractCommand extends Command
{
    /**
     * @var int
     */
    public const CODE_SUCCESS = 0;

    /**
     * @var int
     */
    public const CODE_ERROR = 1;

    /**
     * @var int
     */
    public const CODE_WARNING = 2;

    /**
     * @var string
     */
    protected $rootPath;

    /**
     * @param string|null $name
     */
    public function __construct($name = null)
    {
        parent::__construct($name);

        $this->rootPath = dirname(__DIR__, 3) . '/';
    }
}

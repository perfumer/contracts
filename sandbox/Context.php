<?php

namespace Perfumer\Component\Bdd\Sandbox;

class Context extends \Perfumer\Component\Bdd\Context
{
    /**
     * @var string
     */
    protected $namespace_prefix = 'Perfumer\\Component\\Bdd\\Sandbox';

    /**
     * @var string
     */
    protected $namespace;

    /**
     * @var string
     */
    protected $name = 'Sample';

    /**
     * @var string
     */
    protected $name_suffix = 'Controller';

    /**
     * @var string
     */
    protected $extends_class = '\\Perfumer\\Component\\Bdd\\Sandbox\\ParentController';

    /**
     * @var string
     */
    protected $extends_test;

    /**
     * @var string
     */
    protected $src_dir = 'controller';

    /**
     * Context constructor.
     */
    public function __construct()
    {
        $this->actions = [
            new ActionOne(),
            new ActionTwo()
        ];
    }
}
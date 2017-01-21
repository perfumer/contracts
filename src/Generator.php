<?php

namespace Perfumer\Component\Bdd;

use Perfumer\Component\Bdd\Step\AbstractStep;

class Generator
{
    /**
     * @var \TwigGenerator\Builder\Generator
     */
    private $generator;

    /**
     * @var string
     */
    private $root_dir;

    /**
     * @var array
     */
    private $contexts;

    /**
     * @param string $root_dir
     * @param array $options
     */
    public function __construct($root_dir, $options = [])
    {
        $this->root_dir = $root_dir;

        $this->generator = new \TwigGenerator\Builder\Generator();
    }

    /**
     * @param Context $context
     * @return $this
     */
    public function addContext(Context $context)
    {
        $this->contexts[] = $context;

        return $this;
    }

    public function generate()
    {
        foreach ($this->contexts as $context) {
            /** @var Context $context */
            $tests = false;
            $steps = [];

            /** @var Action $action */
            foreach ($context->getActions() as $action) {
                /** @var AbstractStep $step */
                foreach ($action->getSteps() as $step) {
                    switch ($step->getType()) {
                        case 'validator':
                        case 'formatter':
                            $tests = true;
                            break;
                    }

                    if (!isset($steps[$step->getFunctionName()])) {
                        $steps[$step->getFunctionName()] = $step;
                    }
                }
            }

            $this->generateBaseClass($context, $steps);
            $this->generateClass($context, $steps);

            if ($tests) {
                $this->generateBaseTest($context, $steps);
                $this->generateTest($context, $steps);
            }
        }
    }

    private function generateBaseClass(Context $context, array $steps)
    {
        $output_name = str_replace('\\', '/', $context->getNamespace()) . '/' . $context->getName() . $context->getNameSuffix() . '.php';

        $builder = new Builder();
        $builder->setGenerator($this->generator);
        $builder->setOutputName($output_name);
        $builder->setVariables([
            'context' => $context,
            'steps' => $steps,
        ]);

        $builder->writeOnDisk($this->root_dir . '/generated/src/' . ucfirst($context->getSrcDir()));
    }

    private function generateClass(Context $context, array $steps)
    {
        $output_name = str_replace('\\', '/', $context->getNamespace()) . '/' . $context->getName() . $context->getNameSuffix() . '.php';

        $builder = new Builder();
        $builder->setGenerator($this->generator);
        $builder->setOutputName($output_name);
        $builder->setVariables([
            'context' => $context,
            'steps' => $steps,
        ]);

        $builder->writeOnDisk($this->root_dir . '/src/' . ucfirst($context->getSrcDir()));
    }

    private function generateBaseTest(Context $context, array $steps)
    {
        $output_name = str_replace('\\', '/', $context->getNamespace()) . '/' . $context->getName() . $context->getNameSuffix() . 'Test.php';

        $builder = new Builder();
        $builder->setGenerator($this->generator);
        $builder->setOutputName($output_name);
        $builder->setVariables([
            'context' => $context,
            'steps' => $steps,
        ]);

        $builder->writeOnDisk($this->root_dir . '/generated/tests/' . ucfirst($context->getSrcDir()));
    }

    private function generateTest(Context $context, array $steps)
    {
        $output_name = str_replace('\\', '/', $context->getNamespace()) . '/' . $context->getName() . $context->getNameSuffix() . 'Test.php';

        $builder = new Builder();
        $builder->setGenerator($this->generator);
        $builder->setOutputName($output_name);
        $builder->setVariables([
            'context' => $context,
            'steps' => $steps,
        ]);

        $builder->writeOnDisk($this->root_dir . '/tests/' . ucfirst($context->getSrcDir()));
    }
}
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
     * @var string
     */
    private $base_src_path = 'generated/src';

    /**
     * @var string
     */
    private $base_test_path = 'generated/tests';

    /**
     * @var string
     */
    private $src_path = 'src';

    /**
     * @var string
     */
    private $test_path = 'tests';

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
        $this->generator = new \TwigGenerator\Builder\Generator();

        $this->root_dir = $root_dir;

        if (isset($options['base_src_path'])) {
            $this->base_src_path = (string) $options['base_src_path'];
        }

        if (isset($options['base_test_path'])) {
            $this->base_test_path = (string) $options['base_test_path'];
        }

        if (isset($options['src_path'])) {
            $this->src_path = (string) $options['src_path'];
        }

        if (isset($options['test_path'])) {
            $this->test_path = (string) $options['test_path'];
        }
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
        $builder->setMustOverwriteIfExists(true);
        $builder->setTemplateName('BaseClassBuilder.php.twig');
        $builder->addTemplateDir(__DIR__ . '/template');
        $builder->setGenerator($this->generator);
        $builder->setOutputName($output_name);
        $builder->setVariables([
            'context' => $context,
            'steps' => $steps,
        ]);

        $builder->writeOnDisk($this->root_dir . '/' . $this->base_src_path . '/' . $context->getSrcDir());
    }

    private function generateClass(Context $context, array $steps)
    {
        $output_name = str_replace('\\', '/', $context->getNamespace()) . '/' . $context->getName() . $context->getNameSuffix() . '.php';

        $builder = new Builder();
        $builder->setMustOverwriteIfExists(false);
        $builder->setTemplateName('ClassBuilder.php.twig');
        $builder->addTemplateDir(__DIR__ . '/template');
        $builder->setGenerator($this->generator);
        $builder->setOutputName($output_name);
        $builder->setVariables([
            'context' => $context,
            'steps' => $steps,
        ]);

        $builder->writeOnDisk($this->root_dir . '/' . $this->src_path . '/' . $context->getSrcDir());
    }

    private function generateBaseTest(Context $context, array $steps)
    {
        $output_name = str_replace('\\', '/', $context->getNamespace()) . '/' . $context->getName() . $context->getNameSuffix() . 'Test.php';

        $builder = new Builder();
        $builder->setMustOverwriteIfExists(true);
        $builder->setTemplateName('BaseTestBuilder.php.twig');
        $builder->addTemplateDir(__DIR__ . '/template');
        $builder->setGenerator($this->generator);
        $builder->setOutputName($output_name);
        $builder->setVariables([
            'context' => $context,
            'steps' => $steps,
        ]);

        $builder->writeOnDisk($this->root_dir . '/' . $this->base_test_path . '/' . $context->getSrcDir());
    }

    private function generateTest(Context $context, array $steps)
    {
        $output_name = str_replace('\\', '/', $context->getNamespace()) . '/' . $context->getName() . $context->getNameSuffix() . 'Test.php';

        $builder = new Builder();
        $builder->setMustOverwriteIfExists(false);
        $builder->setTemplateName('TestBuilder.php.twig');
        $builder->addTemplateDir(__DIR__ . '/template');
        $builder->setGenerator($this->generator);
        $builder->setOutputName($output_name);
        $builder->setVariables([
            'context' => $context,
            'steps' => $steps,
        ]);

        $builder->writeOnDisk($this->root_dir . '/' . $this->test_path . '/' . $context->getSrcDir());
    }
}
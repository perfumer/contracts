<?php

namespace Perfumer\Component\Bdd;

use Perfumer\Component\Bdd\Step\AbstractStep;
use Perfumer\Component\Bdd\Step\CallStep;

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
        /** @var Context $context */
        foreach ($this->contexts as $context) {
            $context_data = [
                'properties'
            ];
            $action_data = [];
            $steps = [];
            $tests = false;

            /** @var Action $action */
            foreach ($context->getActions() as $action) {
                if (!isset($action_data[$action->getName()])) {
                    $action_data[$action->getName()] = [
                        'defaults' => []
                    ];
                }

                /** @var AbstractStep $step */
                foreach ($action->getSteps() as $step) {
                    switch ($step->getType()) {
                        case 'validator':
                        case 'formatter':
                            $tests = true;
                            break;
                        case 'call':
                            /** @var CallStep $step */
                            if ($step->getService()) {
                                $context_data['properties'][] = $step->getService();
                            }
                            break;
                    }

                    if (!isset($steps[$step->getFunctionName()])) {
                        $steps[$step->getFunctionName()] = $step;
                    }

                    if ($step->getArguments()) {
                        foreach ($step->getArguments() as $argument) {
                            if (substr($argument, 0, 5) == 'this.') {
                                $context_data['properties'][] = substr($argument, 5);
                            }
                        }
                    }

                    if ($step->getReturn() && $step->getReturn() != 'return') {
                        if (substr($step->getReturn(), 0, 5) == 'this.') {
                            $context_data['properties'][] = substr($step->getReturn(), 5);
                        } else {
                            $action_data[$action->getName()]['defaults'][] = $step->getReturn();
                        }
                    }
                }

                $action_data[$action->getName()]['defaults'] = array_unique($action_data[$action->getName()]['defaults']);
            }

            $context_data['properties'] = array_unique($context_data['properties']);

            $this->generateBaseClass($context, $context_data, $action_data, $steps);
            $this->generateClass($context, $context_data, $action_data, $steps);

            if ($tests) {
                $this->generateBaseTest($context, $context_data, $action_data, $steps);
                $this->generateTest($context, $context_data, $action_data, $steps);
            }
        }
    }

    private function generateBaseClass(Context $context, array $context_data, array $action_data, array $steps)
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
            'context_data' => $context_data,
            'action_data' => $action_data,
            'steps' => $steps,
        ]);

        $builder->writeOnDisk($this->root_dir . '/' . $this->base_src_path . '/' . ucfirst($context->getSrcDir()));
    }

    private function generateClass(Context $context, array $context_data, array $action_data, array $steps)
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
            'context_data' => $context_data,
            'action_data' => $action_data,
            'steps' => $steps,
        ]);

        $builder->writeOnDisk($this->root_dir . '/' . $this->src_path . '/' . ucfirst($context->getSrcDir()));
    }

    private function generateBaseTest(Context $context, array $context_data, array $action_data, array $steps)
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
            'context_data' => $context_data,
            'action_data' => $action_data,
            'steps' => $steps,
        ]);

        $builder->writeOnDisk($this->root_dir . '/' . $this->base_test_path . '/' . ucfirst($context->getSrcDir()));
    }

    private function generateTest(Context $context, array $context_data, array $action_data, array $steps)
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
            'context_data' => $context_data,
            'action_data' => $action_data,
            'steps' => $steps,
        ]);

        $builder->writeOnDisk($this->root_dir . '/' . $this->test_path . '/' . ucfirst($context->getSrcDir()));
    }
}
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
     * @var StepParser
     */
    private $step_parser;

    /**
     * @param string $root_dir
     * @param array $options
     */
    public function __construct($root_dir, $options = [])
    {
        $this->generator = new \TwigGenerator\Builder\Generator();
        $this->step_parser = new StepParser();

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
            $runtime_context = new RuntimeContext();

            $namespace = implode('\\', [
                $context->getNamespacePrefix(),
                ucfirst($context->getSrcDir())
            ]);

            if ($context->getNamespace()) {
                $namespace .= '\\' . $context->getNamespace();
            }

            $runtime_context->setNamespace($namespace);
            $runtime_context->setClassName($context->getName() . $context->getNameSuffix());
            $runtime_context->setExtendsClass($context->getExtendsClass());
            $runtime_context->setExtendsTest($context->getExtendsTest());

            $tests = false;

            /** @var Action $action */
            foreach ($context->getActions() as $action) {
                $runtime_action = new RuntimeAction();
                $runtime_action->setMethodName($action->getName());

                $runtime_action->setMethodArguments(array_map(function($value) {
                    return '$' . $value;
                }, $action->getArguments()));

                /** @var AbstractStep $step */
                foreach ($action->getSteps() as $step) {
                    $runtime_step = new RuntimeStep();
                    $runtime_step->setStep($step);

                    foreach ($step->getArguments() as $argument) {
                        $argument_var = $this->step_parser->parseForMethod($argument);
                        $argument_value = $this->step_parser->parseForCall($argument);

                        $runtime_step->addArgument($argument_var);

                        if (!in_array($argument_var, $runtime_action->getMethodArguments())) {
                            $runtime_action->addLocalVariable($argument_var, $argument_value);
                        }
                    }

                    switch ($step->getType()) {
                        case 'validator':
                        case 'formatter':
                            $tests = true;
                            break;
                        case 'call':
                            /** @var CallStep $step */
                            if ($step->getService() && $step->getService() !== '_parent') {
                                $runtime_context->addProperty($step->getService());
                            }
                            break;
                    }

                    if ($step->getArguments()) {
                        foreach ($step->getArguments() as $argument) {
                            if (substr($argument, 0, 5) == 'this.') {
                                $runtime_context->addProperty(substr($argument, 5));
                            }
                        }
                    }

                    if ($step->getReturn() && $step->getReturn() != '_return') {
                        if (substr($step->getReturn(), 0, 5) == 'this.') {
                            $runtime_context->addProperty(substr($step->getReturn(), 5));
                        } else {
                            $runtime_action->addLocalVariable('$' . $step->getReturn(), null);
                        }
                    }

                    if (!$runtime_context->hasStep($step->getFunctionName())) {
                        $runtime_context->addStep($step->getFunctionName(), $runtime_step);
                    }

                    $runtime_action->addStep($step->getFunctionName(), $runtime_step);
                }

                $runtime_context->addAction($runtime_action);
            }

            $this->generateBaseClass($context, $runtime_context);
            $this->generateClass($context, $runtime_context);

            if ($tests) {
                $this->generateBaseTest($context, $runtime_context);
                $this->generateTest($context, $runtime_context);
            }
        }
    }

    private function generateBaseClass(Context $context, RuntimeContext $runtime_context)
    {
        $output_name = str_replace('\\', '/', $context->getNamespace()) . '/' . $context->getName() . $context->getNameSuffix() . '.php';

        $builder = new Builder();
        $builder->setMustOverwriteIfExists(true);
        $builder->setTemplateName('BaseClassBuilder.php.twig');
        $builder->addTemplateDir(__DIR__ . '/template');
        $builder->setGenerator($this->generator);
        $builder->setOutputName($output_name);
        $builder->setVariables([
            'context' => $runtime_context
        ]);

        $builder->writeOnDisk($this->root_dir . '/' . $this->base_src_path . '/' . ucfirst($context->getSrcDir()));
    }

    private function generateClass(Context $context, RuntimeContext $runtime_context)
    {
        $output_name = str_replace('\\', '/', $context->getNamespace()) . '/' . $context->getName() . $context->getNameSuffix() . '.php';

        $builder = new Builder();
        $builder->setMustOverwriteIfExists(false);
        $builder->setTemplateName('ClassBuilder.php.twig');
        $builder->addTemplateDir(__DIR__ . '/template');
        $builder->setGenerator($this->generator);
        $builder->setOutputName($output_name);
        $builder->setVariables([
            'context' => $runtime_context
        ]);

        $builder->writeOnDisk($this->root_dir . '/' . $this->src_path . '/' . ucfirst($context->getSrcDir()));
    }

    private function generateBaseTest(Context $context, RuntimeContext $runtime_context)
    {
        $output_name = str_replace('\\', '/', $context->getNamespace()) . '/' . $context->getName() . $context->getNameSuffix() . 'Test.php';

        $builder = new Builder();
        $builder->setMustOverwriteIfExists(true);
        $builder->setTemplateName('BaseTestBuilder.php.twig');
        $builder->addTemplateDir(__DIR__ . '/template');
        $builder->setGenerator($this->generator);
        $builder->setOutputName($output_name);
        $builder->setVariables([
            'context' => $runtime_context
        ]);

        $builder->writeOnDisk($this->root_dir . '/' . $this->base_test_path . '/' . ucfirst($context->getSrcDir()));
    }

    private function generateTest(Context $context, RuntimeContext $runtime_context)
    {
        $output_name = str_replace('\\', '/', $context->getNamespace()) . '/' . $context->getName() . $context->getNameSuffix() . 'Test.php';

        $builder = new Builder();
        $builder->setMustOverwriteIfExists(false);
        $builder->setTemplateName('TestBuilder.php.twig');
        $builder->addTemplateDir(__DIR__ . '/template');
        $builder->setGenerator($this->generator);
        $builder->setOutputName($output_name);
        $builder->setVariables([
            'context' => $runtime_context
        ]);

        $builder->writeOnDisk($this->root_dir . '/' . $this->test_path . '/' . ucfirst($context->getSrcDir()));
    }
}

<?php

namespace Perfumer\Component\Contracts;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Perfumer\Component\Contracts\Annotations\Collection;
use Perfumer\Component\Contracts\Annotations\Context;
use Perfumer\Component\Contracts\Annotations\Custom;
use Perfumer\Component\Contracts\Annotations\Errors;
use Perfumer\Component\Contracts\Annotations\Extend;
use Perfumer\Component\Contracts\Annotations\Call;
use Perfumer\Component\Contracts\Annotations\Output;
use Perfumer\Component\Contracts\Annotations\Property;
use Perfumer\Component\Contracts\Annotations\ServiceProperty;
use Perfumer\Component\Contracts\Annotations\Template;
use Perfumer\Component\Contracts\Annotations\Test;

class Generator
{
    /**
     * @var \TwigGenerator\Builder\Generator
     */
    private $generator;

    /**
     * @var string
     */
    private $contract_prefix;

    /**
     * @var string
     */
    private $class_prefix;

    /**
     * @var string
     */
    private $context_prefix;

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
    private $classes = [];

    /**
     * @var array
     */
    private $contexts = [];

    /**
     * @var array
     */
    private $template_directories = [];

    /**
     * @param string $root_dir
     * @param array $options
     */
    public function __construct($root_dir, $options = [])
    {
        $this->addTemplateDirectory(__DIR__ . '/templates');
        $this->addAnnotations(__DIR__ . '/Annotations.php');

        $this->generator = new \TwigGenerator\Builder\Generator();

        $this->root_dir = $root_dir;

        if (isset($options['contract_prefix'])) {
            $this->contract_prefix = (string) $options['contract_prefix'];
        }

        if (isset($options['class_prefix'])) {
            $this->class_prefix = (string) $options['class_prefix'];
        }

        if (isset($options['context_prefix'])) {
            $this->context_prefix = (string) $options['context_prefix'];
        }

        if (isset($options['base_src_path'])) {
            $this->base_src_path = (string) $options['base_src_path'];
        }

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
     * @param string $directory
     */
    public function addTemplateDirectory(string $directory)
    {
        $this->template_directories[] = $directory;
    }

    /**
     * @param string $filename
     */
    public function addAnnotations(string $filename)
    {
        AnnotationRegistry::registerFile($filename);
    }

    /**
     * @param string $context
     * @return $this
     */
    public function addContext(string $context)
    {
        $this->contexts[] = $context;

        return $this;
    }

    /**
     * @param string $class
     * @return $this
     */
    public function addClass(string $class)
    {
        $this->classes[] = $class;

        return $this;
    }

    public function generateContexts()
    {
        $reader = new AnnotationReader();

        foreach ($this->contexts as $class) {
            $reflection = new \ReflectionClass($class);
            $class_annotations = $reader->getClassAnnotations($reflection);
            $tests = false;

            $runtime_context = new RuntimeContext();

            $namespace = $reflection->getNamespaceName();

            $runtime_context->setNamespace($namespace);
            $runtime_context->setClassName($reflection->getShortName());

            foreach ($class_annotations as $annotation) {
                if ($annotation instanceof Extend) {
                    $runtime_context->setExtendsClass($annotation->class);
                }
            }

            foreach ($reflection->getMethods() as $method) {
                $method_annotations = $reader->getMethodAnnotations($method);

                foreach ($method_annotations as $annotation) {
                    if ($annotation instanceof Test) {
                        $tests = true;

                        $runtime_step = new RuntimeStep();
                        $runtime_step->setFunctionName($method->name);
                        $runtime_step->setContext($class);

                        foreach ($method->getParameters() as $parameter) {
                            $runtime_step->addHeaderArgument('$' . $parameter->name);
                        }

                        $runtime_context->addStep($runtime_step->getFunctionName(), $runtime_step);
                    }
                }
            }

            if ($tests) {
                $this->generateBaseContextTest($reflection, $runtime_context);
                $this->generateContextTest($reflection, $runtime_context);
            }
        }
    }

    public function generateClasses()
    {
        $reader = new AnnotationReader();

        foreach ($this->classes as $class) {
            $contexts = [];

            $reflection = new \ReflectionClass($class);
            $class_annotations = $reader->getClassAnnotations($reflection);

            $runtime_context = new RuntimeContext();

            $namespace = str_replace($this->contract_prefix, $this->class_prefix, $reflection->getNamespaceName());

            $runtime_context->setNamespace($namespace);
            $runtime_context->setClassName($reflection->getShortName());

            foreach ($class_annotations as $annotation) {
                if ($annotation instanceof Template) {
                    $runtime_context->setTemplate($annotation->name);
                }

                if ($annotation instanceof Extend) {
                    $runtime_context->setExtendsClass($annotation->class);
                }

                if ($annotation instanceof Context) {
                    $contexts[$annotation->name] = $annotation->class;
                }
            }

            $runtime_context->setContexts($contexts);

            foreach ($reflection->getMethods() as $method) {
                $runtime_action = new RuntimeAction();
                $runtime_action->setMethodName($method->name);

                foreach ($method->getParameters() as $parameter) {
                    $runtime_action->addHeaderArgument('$' . $parameter->name);
                }

                $method_annotations = $reader->getMethodAnnotations($method);

                foreach ($method_annotations as $annotation) {
                    if (!$this->validateStepAnnotation($annotation)) {
                        continue;
                    }

                    if ($annotation instanceof Collection) {
                        foreach ($annotation->steps as $index => $step) {
                            $runtime_step = new RuntimeStep();

                            if ($index === 0) {
                                $runtime_step->setBeforeCode($annotation->before());
                            }

                            if ($index === count($annotation->steps) - 1) {
                                $runtime_step->setAfterCode($annotation->after());
                            }

                            $this->processStepAnnotation($step, $runtime_step, $runtime_action, $runtime_context, $contexts);
                        }
                    } else {
                        $runtime_step = new RuntimeStep();

                        $this->processStepAnnotation($annotation, $runtime_step, $runtime_action, $runtime_context, $contexts);
                    }

                }

                $runtime_context->addAction($runtime_action);
            }

            $this->generateBaseClass($reflection, $runtime_context);
            $this->generateClass($reflection, $runtime_context);
        }
    }

    /**
     * @param Annotation $annotation
     * @param RuntimeStep $runtime_step
     * @param RuntimeAction $runtime_action
     * @param RuntimeContext $runtime_context
     * @param array $contexts
     */
    private function processStepAnnotation(Annotation $annotation, RuntimeStep $runtime_step, RuntimeAction $runtime_action, RuntimeContext $runtime_context, array $contexts)
    {
        if ($annotation instanceof Step) {
            $runtime_step->setPrependCode($annotation->prepend());
            $runtime_step->setAppendCode($annotation->append());
        }

        if ($annotation instanceof Call || $annotation instanceof Errors) {
            $runtime_context->addProperty('_context_' . $annotation->na);

            $runtime_step->setContext($contexts[$annotation->na]);
            $runtime_step->setContextName($annotation->na);
            $runtime_step->setMethod($annotation->me);
            $runtime_step->setFunctionName($annotation->na . ucfirst($annotation->me));
        }

        if ($annotation instanceof Custom) {
            $runtime_step->setMethod($annotation->me);
            $runtime_step->setFunctionName($annotation->me);
        }

        if ($annotation instanceof Service) {
            $runtime_step->setService($annotation->getExpression());
            $runtime_step->setMethod($annotation->me);

            if ($annotation instanceof ServiceProperty) {
                $runtime_context->addProperty($annotation->na);
            }
        }

        if ($annotation instanceof Step && $annotation->if) {
            $local_variable = $annotation->if instanceof Variable ? $annotation->if->asHeader() : '$' . $annotation->if;
            $body_argument = $annotation->if instanceof Variable ? $annotation->if->asArgument() : '$' . $annotation->if;

            $runtime_step->setCondition($body_argument);

            if (!$runtime_action->hasLocalVariable($local_variable)) {
                $runtime_action->addLocalVariable($local_variable, null);
            }
        }

        if ($annotation instanceof Step && $annotation->re) {
            if (is_array($annotation->re)) {
                $vars = array_map(function ($v) {
                    return '$' . $v;
                }, $annotation->re);

                $expression = 'list(' . implode(', ', $vars) . ') = ';
            } else {
                $expression = $annotation->re instanceof Variable ? $annotation->re->asReturn() : '$' . $annotation->re . ' = ';
            }

            $runtime_step->setReturnExpression($expression);

            if (!$annotation->re instanceof Output) {
                if (is_array($annotation->re)) {
                    foreach ($annotation->re as $var) {
                        $runtime_action->addLocalVariable('$' . $var, null);
                    }
                } elseif ($annotation->re instanceof Property) {
                    $runtime_context->addProperty($annotation->re->name);
                } else {
                    $value = $annotation->va ? 'true' : 'null';

                    $runtime_action->addLocalVariable('$' . $annotation->re, $value);
                }
            }
        }

        if ($annotation instanceof Errors) {
            $runtime_step->setReturnExpression('$_return = ');
        }

        if ($annotation->va) {
            $runtime_step->setReturnExpression('$_valid = ' . $runtime_step->getReturnExpression());
        }

        foreach ($annotation->ar as $argument) {
            $argument_var = $argument instanceof Variable ? $argument->asHeader() : '$' . $argument;
            $argument_value = $argument instanceof Variable ? $argument->asArgument() : '$' . $argument;

            $runtime_step->addHeaderArgument($argument_var);
            $runtime_step->addBodyArgument($argument_value);

            if ($argument instanceof Property) {
                $runtime_context->addProperty($argument->name);
            }
        }

        if ($annotation instanceof Errors) {
            $runtime_step->setValid(false);
        } else {
            $runtime_step->setValid(true);
        }

        if (!$runtime_context->hasStep($runtime_step->getFunctionName())) {
            $runtime_context->addStep($runtime_step->getFunctionName(), $runtime_step);
        }

        $runtime_action->addStep($runtime_step);
    }

    /**
     * @param \ReflectionClass $reflection
     * @param RuntimeContext $runtime_context
     */
    private function generateBaseClass(\ReflectionClass $reflection, RuntimeContext $runtime_context)
    {
        $output_name = str_replace('\\', '/', trim(str_replace($this->contract_prefix, '', $reflection->getNamespaceName()), '\\'));

        if ($output_name) {
            $output_name .= '/';
        }

        $output_name = $output_name . $reflection->getShortName() . '.php';

        $builder = new Builder();
        $builder->setMustOverwriteIfExists(true);
        $builder->setTemplateName($runtime_context->getTemplate() . '.php.twig');
        $builder->setTemplateDirs($this->template_directories);
        $builder->setGenerator($this->generator);
        $builder->setOutputName($output_name);
        $builder->setVariables([
            'context' => $runtime_context
        ]);

        $builder->writeOnDisk($this->root_dir . '/' . $this->base_src_path);
    }

    /**
     * @param \ReflectionClass $reflection
     * @param RuntimeContext $runtime_context
     */
    private function generateClass(\ReflectionClass $reflection, RuntimeContext $runtime_context)
    {
        $output_name = str_replace('\\', '/', trim(str_replace($this->contract_prefix, '', $reflection->getNamespaceName()), '\\'));

        if ($output_name) {
            $output_name .= '/';
        }

        $output_name = $output_name . $reflection->getShortName() . '.php';

        $builder = new Builder();
        $builder->setMustOverwriteIfExists(false);
        $builder->setTemplateName('Class.php.twig');
        $builder->setTemplateDirs($this->template_directories);
        $builder->setGenerator($this->generator);
        $builder->setOutputName($output_name);
        $builder->setVariables([
            'context' => $runtime_context
        ]);

        $builder->writeOnDisk($this->root_dir . '/' . $this->src_path);
    }

    /**
     * @param \ReflectionClass $reflection
     * @param RuntimeContext $runtime_context
     */
    private function generateBaseContextTest(\ReflectionClass $reflection, RuntimeContext $runtime_context)
    {
        $output_name = str_replace('\\', '/', trim(str_replace($this->context_prefix, '', $reflection->getNamespaceName()), '\\'));

        if ($output_name) {
            $output_name .= '/';
        }

        $output_name = $output_name . $reflection->getShortName() . 'Test.php';

        $builder = new Builder();
        $builder->setMustOverwriteIfExists(true);
        $builder->setTemplateName('BaseContextTest.php.twig');
        $builder->setTemplateDirs($this->template_directories);
        $builder->setGenerator($this->generator);
        $builder->setOutputName($output_name);
        $builder->setVariables([
            'context' => $runtime_context
        ]);

        $builder->writeOnDisk($this->root_dir . '/' . $this->base_test_path);
    }

    /**
     * @param \ReflectionClass $reflection
     * @param RuntimeContext $runtime_context
     */
    private function generateContextTest(\ReflectionClass $reflection, RuntimeContext $runtime_context)
    {
        $output_name = str_replace('\\', '/', trim(str_replace($this->context_prefix, '', $reflection->getNamespaceName()), '\\'));

        if ($output_name) {
            $output_name .= '/';
        }

        $output_name = $output_name . $reflection->getShortName() . 'Test.php';

        $builder = new Builder();
        $builder->setMustOverwriteIfExists(false);
        $builder->setTemplateName('ContextTest.php.twig');
        $builder->setTemplateDirs($this->template_directories);
        $builder->setGenerator($this->generator);
        $builder->setOutputName($output_name);
        $builder->setVariables([
            'context' => $runtime_context
        ]);

        $builder->writeOnDisk($this->root_dir . '/' . $this->test_path);
    }

    /**
     * @param Annotation $annotation
     * @return bool
     */
    private function validateStepAnnotation(Annotation $annotation): bool
    {
        return $annotation instanceof Step || $annotation instanceof Collection;
    }
}

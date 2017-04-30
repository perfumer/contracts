<?php

namespace Perfumer\Component\Contracts;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Perfumer\Component\Contracts\Annotations\Collection;
use Perfumer\Component\Contracts\Annotations\Context;
use Perfumer\Component\Contracts\Annotations\Custom;
use Perfumer\Component\Contracts\Annotations\Def;
use Perfumer\Component\Contracts\Annotations\Error;
use Perfumer\Component\Contracts\Annotations\Extend;
use Perfumer\Component\Contracts\Annotations\Call;
use Perfumer\Component\Contracts\Annotations\Inject;
use Perfumer\Component\Contracts\Annotations\Output;
use Perfumer\Component\Contracts\Annotations\Property;
use Perfumer\Component\Contracts\Annotations\ServiceProperty;
use Perfumer\Component\Contracts\Annotations\Skip;
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
        try {
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
        } catch (ContractsException $e) {
            exit($e->getMessage() . PHP_EOL);
        }
    }

    public function generateClasses()
    {
        try {
            $reader = new AnnotationReader();

            foreach ($this->classes as $class) {
                $contexts = [];
                $injected = [];

                $reflection = new \ReflectionClass($class);
                $class_annotations = $reader->getClassAnnotations($reflection);

                $runtime_context = new RuntimeContext();

                $namespace = str_replace($this->contract_prefix, $this->class_prefix, $reflection->getNamespaceName());

                $runtime_context->setNamespace($namespace);
                $runtime_context->setClassName($reflection->getShortName());
                $runtime_context->addInterface('\\' . $class);

                foreach ($class_annotations as $annotation) {
                    if ($annotation instanceof Template) {
                        $runtime_context->setTemplate($annotation->name);
                    }

                    if ($annotation instanceof Extend) {
                        $runtime_context->setExtendsClass($annotation->class);
                    }

                    if ($annotation instanceof Inject) {
                        $injected[$annotation->name] = $annotation->type;
                        $runtime_context->addPrivateProperty('_injected_' . $annotation->name, $annotation->type);
                    }

                    if ($annotation instanceof Context) {
                        $contexts[$annotation->name] = $annotation->class;
                        $runtime_context->addPrivateProperty('_context_' . $annotation->name, $annotation->class);
                    }
                }

                $runtime_context->setContexts($contexts);
                $runtime_context->setInjected($injected);

                foreach ($reflection->getMethods() as $method) {
                    $runtime_action = new RuntimeAction();
                    $runtime_action->setMethodName($method->name);

                    $type = (string) $method->getReturnType();

                    if ($type && !$method->getReturnType()->isBuiltin()) {
                        $type = '\\' . $type;
                    }

                    $runtime_action->setReturnType($type);

                    foreach ($method->getParameters() as $parameter) {
                        $type = (string) $parameter->getType();

                        if ($type && !$parameter->getType()->isBuiltin()) {
                            $type = '\\' . $type;
                        }

                        $runtime_action->addHeaderArgument('$' . $parameter->name, $type);
                    }

                    $method_annotations = $reader->getMethodAnnotations($method);

                    // Set validate=true
                    foreach ($method_annotations as $annotation) {
                        if ($annotation instanceof Skip) {
                            continue(2);
                        }

                        if ($annotation instanceof Def) {
                            $value = $annotation->variable instanceof Variable ? $annotation->variable->asArgument() : $annotation->variable;

                            $runtime_action->addLocalVariable('$' . $annotation->name, $value);
                        }

                        if ($annotation instanceof Error) {
                            foreach ($method_annotations as $a) {
                                if ($a instanceof Step && $a->return === $annotation->unless) {
                                    $a->validate = true;
                                }
                            }
                        }
                    }

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

                                $this->processStepAnnotation($step, $runtime_step, $runtime_action, $runtime_context, $contexts, $injected);
                            }
                        } else {
                            $runtime_step = new RuntimeStep();

                            $this->processStepAnnotation($annotation, $runtime_step, $runtime_action, $runtime_context, $contexts, $injected);
                        }

                    }

                    $runtime_context->addAction($runtime_action);
                }

                $this->generateBaseClass($reflection, $runtime_context);
                $this->generateClass($reflection, $runtime_context);
                $this->generateBaseClassTest($reflection, $runtime_context);
                $this->generateClassTest($reflection, $runtime_context);
            }
        } catch (ContractsException $e) {
            exit($e->getMessage() . PHP_EOL);
        }
    }

    /**
     * @param Annotation $annotation
     * @param RuntimeStep $runtime_step
     * @param RuntimeAction $runtime_action
     * @param RuntimeContext $runtime_context
     * @param array $contexts
     * @param array $injected
     * @throws ContractsException
     */
    private function processStepAnnotation(Annotation $annotation, RuntimeStep $runtime_step, RuntimeAction $runtime_action, RuntimeContext $runtime_context, array $contexts, array $injected)
    {
        if ($annotation instanceof Step) {
            $runtime_step->setPrependCode($annotation->prepend());
            $runtime_step->setAppendCode($annotation->append());
        }

        if ($annotation instanceof Call || $annotation instanceof Error) {
            if (!isset($contexts[$annotation->name]) && !isset($injected[$annotation->name])) {
                throw new ContractsException(sprintf('%s\\%s -> %s -> %s context or injected is not registered',
                    $runtime_context->getNamespace(),
                    $runtime_context->getClassName(),
                    $runtime_action->getMethodName(),
                    $annotation->name
                ));
            }

            if (isset($contexts[$annotation->name])) {
                $runtime_step->setContext($contexts[$annotation->name]);
                $runtime_step->setContextName($annotation->name);
                $runtime_step->setMethod($annotation->method);
                $runtime_step->setFunctionName($annotation->name . ucfirst($annotation->method));
            } else {
                $runtime_step->setService("\$this->_injected_{$annotation->name}->");
                $runtime_step->setMethod($annotation->method);
            }
        }

        if ($annotation instanceof Custom) {
            $runtime_step->setMethod($annotation->method);
            $runtime_step->setFunctionName($annotation->method);
        }

        if ($annotation instanceof Service) {
            $runtime_step->setService($annotation->getExpression());
            $runtime_step->setMethod($annotation->method);

            if ($annotation instanceof ServiceProperty) {
                $runtime_context->addProtectedProperty($annotation->name);
            }
        }

        if ($annotation instanceof Step && ($annotation->if || $annotation->unless)) {
            $condition = $annotation->if ?: $annotation->unless;

            $body_argument = $condition instanceof Variable ? $condition->asArgument() : '$' . $condition;

            if ($annotation->unless) {
                $body_argument = '!' . $body_argument;
            }

            $runtime_step->setCondition($body_argument);

            if (!$condition instanceof Variable) {
                $local_variable = '$' . $condition;

                if (!$runtime_step->hasLocalDependency($local_variable)) {
                    $runtime_step->addLocalDependency($local_variable);
                }

                if (!$runtime_action->hasLocalVariable($local_variable)) {
                    $runtime_action->addLocalVariable($local_variable, null);
                }
            }
        }

        if ($annotation instanceof Step && $annotation->return) {
            if (is_array($annotation->return)) {
                foreach ($annotation->return as $item) {
                    if ($item instanceof Output) {
                        $runtime_action->setHasReturn(true);
                    }
                }

                $vars = array_map(function ($v) {
                    return $v instanceof Variable ? $v->asReturn() : '$' . $v;
                }, $annotation->return);

                $expression = 'list(' . implode(', ', $vars) . ')';
            } else {
                if ($annotation->return instanceof Output) {
                    $runtime_action->setHasReturn(true);
                }

                $expression = $annotation->return instanceof Variable ? $annotation->return->asReturn() : '$' . $annotation->return;
            }

            $runtime_step->setReturnExpression($expression);

            $return_values = is_array($annotation->return) ? $annotation->return : [$annotation->return];

            foreach ($return_values as $var) {
                if (!$var instanceof Variable) {
                    $value = $annotation->validate ? 'true' : 'null';

                    if ($runtime_action->hasLocalVariable('$' . $var)) {
                        throw new ContractsException(sprintf('%s\\%s -> %s -> %s.%s returns "%s" which is already in use.',
                            $runtime_context->getNamespace(),
                            $runtime_context->getClassName(),
                            $runtime_action->getMethodName(),
                            $annotation->name,
                            $annotation->method,
                            $var
                        ));
                    }

                    $runtime_action->addLocalVariable('$' . $var, $value);

                    $runtime_step->addLocalReturn('$' . $var);
                } elseif ($var instanceof Property) {
                    $runtime_context->addProtectedProperty($var->name);
                }
            }
        }

        if ($annotation instanceof Error) {
            $runtime_action->setHasReturn(true);

            $runtime_step->setReturnExpression('$_return');
        }

        if ($annotation->validate) {
            $runtime_action->setHasValidation(true);

            $runtime_step->setReturnExpression('$_valid = (bool) ' . $runtime_step->getReturnExpression());
        }

        $annotation_arguments = $annotation->arguments;

        if (($annotation instanceof Call || $annotation instanceof Error) && (isset($contexts[$annotation->name]) || isset($injected[$annotation->name]))) {
            $is_context = isset($contexts[$annotation->name]);

            if ($is_context) {
                $reflection_context = new \ReflectionClass($contexts[$annotation->name]);
            } else {
                $reflection_context = new \ReflectionClass($injected[$annotation->name]);
            }

            foreach ($reflection_context->getMethods() as $method) {
                if ($method->getName() !== $annotation->method) {
                    continue;
                }

                $reader = new AnnotationReader();
                $method_annotations = $reader->getMethodAnnotations($method);
                $tmp_arguments = [];

                foreach ($method->getParameters() as $parameter) {
                    $found = false;

                    foreach ($method_annotations as $method_annotation) {
                        if ($is_context && $method_annotation instanceof Inject && $parameter->getName() == $method_annotation->name) {
                            $tmp_arguments[] = $method_annotation->variable;
                            $found = true;
                        }
                    }

                    if (!$found) {
                        $tmp_arguments[] = $annotation->arguments ? array_shift($annotation_arguments) : $parameter->getName();
                    }
                }

                if (count($annotation_arguments) > 0) {
                    throw new ContractsException(sprintf('%s\\%s -> %s -> %s.%s has excessive arguments.',
                        $runtime_context->getNamespace(),
                        $runtime_context->getClassName(),
                        $runtime_action->getMethodName(),
                        $annotation->name,
                        $annotation->method
                    ));
                }

                if ($tmp_arguments) {
                    $annotation_arguments = $tmp_arguments;
                }
            }
        }

        foreach ($annotation_arguments as $argument) {
            $argument_var = $argument instanceof Variable ? $argument->asHeader() : '$' . $argument;
            $argument_value = $argument instanceof Variable ? $argument->asArgument() : '$' . $argument;

            $runtime_step->addHeaderArgument($argument_var);
            $runtime_step->addBodyArgument($argument_value);

            if ($argument instanceof Property) {
                $runtime_context->addProtectedProperty($argument->name);
            }

            if (!$argument instanceof Variable && !$runtime_step->hasLocalDependency($argument_var)) {
                $runtime_step->addLocalDependency($argument_var);
            }
        }

        if ($annotation instanceof Error) {
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
    private function generateBaseClassTest(\ReflectionClass $reflection, RuntimeContext $runtime_context)
    {
        $output_name = str_replace('\\', '/', trim(str_replace($this->contract_prefix, '', $reflection->getNamespaceName()), '\\'));

        if ($output_name) {
            $output_name .= '/';
        }

        $output_name = $output_name . $reflection->getShortName() . 'Test.php';

        $builder = new Builder();
        $builder->setMustOverwriteIfExists(true);
        $builder->setTemplateName('BaseClassTest.php.twig');
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
    private function generateClassTest(\ReflectionClass $reflection, RuntimeContext $runtime_context)
    {
        $output_name = str_replace('\\', '/', trim(str_replace($this->contract_prefix, '', $reflection->getNamespaceName()), '\\'));

        if ($output_name) {
            $output_name .= '/';
        }

        $output_name = $output_name . $reflection->getShortName() . 'Test.php';

        $builder = new Builder();
        $builder->setMustOverwriteIfExists(false);
        $builder->setTemplateName('ClassTest.php.twig');
        $builder->setTemplateDirs($this->template_directories);
        $builder->setGenerator($this->generator);
        $builder->setOutputName($output_name);
        $builder->setVariables([
            'context' => $runtime_context
        ]);

        $builder->writeOnDisk($this->root_dir . '/' . $this->test_path);
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

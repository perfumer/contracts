<?php

namespace Perfumerlabs\Perfumer;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Perfumerlabs\Perfumer\Annotation\After;
use Perfumerlabs\Perfumer\Annotation\Before;
use Perfumerlabs\Perfumer\Annotation\Error;
use Perfumerlabs\Perfumer\Annotation\Returns;
use Perfumerlabs\Perfumer\Annotation\Set;
use Perfumerlabs\Perfumer\Annotation\Test;
use Perfumerlabs\Perfumer\Data\ClassData;
use Perfumerlabs\Perfumer\Data\MethodData;
use Perfumerlabs\Perfumer\Data\StepData;
use Perfumerlabs\Perfumer\Data\TestCaseData;
use Perfumerlabs\Perfumer\Step\SharedClassStep;
use Perfumerlabs\Perfumer\Step\ExpressionStep;
use Perfumerlabs\Perfumer\Step\PlainStep;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\DocBlockGenerator;
use Zend\Code\Generator\MethodGenerator;
use Zend\Code\Generator\ParameterGenerator;
use Zend\Code\Generator\PropertyGenerator;

class Generator
{
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
    private $base_annotations_path = 'generated/annotation';

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
    private $contracts = [];

    /**
     * @var array
     */
    private $contexts = [];

    /**
     * @var AnnotationReader
     */
    private $reader;

    /**
     * @param string $root_dir
     * @param array $options
     */
    public function __construct($root_dir, $options = [])
    {
        $this->reader = new AnnotationReader();

        /** @noinspection PhpDeprecationInspection */
        AnnotationRegistry::registerLoader('class_exists');

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

        if (isset($options['base_annotations_path'])) {
            $this->base_annotations_path = (string) $options['base_annotations_path'];
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
     * @param string $class
     * @return $this
     */
    public function addContract(string $class)
    {
        $this->contracts[] = $class;

        return $this;
    }

    /**
     * @param string $class
     * @param string $extends
     * @return $this
     */
    public function addContext(string $class, ?string $extends = null)
    {
        if (!$extends) {
            $extends = SharedClassStep::class;
        }

        $this->contexts[] = [
            'class' => $class,
            'extends' => $extends,
        ];

        return $this;
    }

    public function generateAll()
    {
        $this->generateContexts($this->contexts);

        try {
            $bundle = new Bundle();

            foreach ($this->contracts as $class) {
                $reflection = new \ReflectionClass($class);

                $namespace = str_replace($this->contract_prefix, $this->class_prefix, $reflection->getNamespaceName());

                $test_case_data = new TestCaseData();

                $test_case_generator = $test_case_data->getGenerator();
                $test_case_generator->setNamespaceName('Generated\\Tests\\' . $namespace);
                $test_case_generator->setAbstract(true);
                $test_case_generator->setName($reflection->getShortName() . 'Test');
                $test_case_generator->setExtendedClass('PHPUnit\\Framework\\TestCase');

                $class_data = new ClassData();

                $class_generator = $class_data->getGenerator();
                $class_generator->setAbstract(true);
                $class_generator->setNamespaceName($namespace);
                $class_generator->setName($reflection->getShortName());

                if ($reflection->isInterface()) {
                    $class_generator->setImplementedInterfaces(array_merge($class_generator->getImplementedInterfaces(), ['\\' . $class]));
                } else {
                    $class_generator->setExtendedClass('\\' . $class);
                }

                $class_annotations = $this->reader->getClassAnnotations($reflection);

                foreach ($class_annotations as $annotation) {
                    if (!$annotation instanceof Annotation) {
                        continue;
                    }

                    $annotation->setReflectionClass($reflection);
                    $annotation->setClassData($class_data);
                    $annotation->setTestCaseData($test_case_data);

                    $annotation->onCreate();
                }

                $this->generateContexts($class_data->getContexts());

                foreach ($reflection->getMethods() as $method) {
                    $method_data = new MethodData();

                    $method_generator = $method_data->getGenerator();
                    $method_generator->setFinal(true);
                    $method_generator->setName($method->name);
                    $method_generator->setVisibility('public');

                    if ($method->getReturnType() !== null) {
                        $type = (string) $method->getReturnType();

                        if ($type && !$method->getReturnType()->isBuiltin()) {
                            $type = '\\' . $type;
                        }

                        $method_generator->setReturnType($type);
                    }

                    foreach ($method->getParameters() as $parameter) {
                        $argument = new ParameterGenerator();
                        $argument->setName($parameter->getName());
                        $argument->setPosition($parameter->getPosition());

                        if ($parameter->getType() !== null) {
                            $argument->setType($parameter->getType());
                        }

                        if ($parameter->isDefaultValueAvailable()) {
                            $argument->setDefaultValue($parameter->getDefaultValue());
                        }

                        $method_generator->setParameter($argument);
                    }

                    foreach ($class_annotations as $class_annotation) {
                        if ($class_annotation instanceof Before) {
                            foreach ($class_annotation->steps as $step) {
                                $this->onCreateMethodAnnotation($step, $reflection, $method, $class_data, $test_case_data, $method_data);

                                if ($step instanceof PlainStep) {
                                    if ($step instanceof Set) {
                                        $method_data->addSet($step);
                                    } else {
                                        $method_data->addStep($step->getStepData());
                                    }

                                    if ($step instanceof SharedClassStep) {
                                        $context_annotations = $step->getAnnotations();

                                        foreach ($context_annotations as $context_annotation) {
                                            if ($context_annotation instanceof Set) {
                                                $method_data->addSet($context_annotation);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }

                    $method_annotations = $this->reader->getMethodAnnotations($method);

                    $validated_returns = [];

                    foreach ($method_annotations as $annotation) {
                        if ($annotation instanceof Error && $annotation->unless) {
                            $validated_returns[] = $annotation->unless;
                        }
                    }

                    foreach ($method_annotations as $annotation) {
                        if ($annotation instanceof ExpressionStep && is_string($annotation->return) && in_array($annotation->return, $validated_returns)) {
                            $annotation->validate = true;
                        }

                        if ($annotation instanceof SharedClassStep && isset($annotation->out) && in_array($annotation->out, $validated_returns)) {
                            $annotation->validate = true;
                        }

                        $this->onCreateMethodAnnotation($annotation, $reflection, $method, $class_data, $test_case_data, $method_data);

                        if ($annotation instanceof PlainStep) {
                            if ($annotation instanceof Set) {
                                $method_data->addSet($annotation);
                            } else {
                                $method_data->addStep($annotation->getStepData());
                            }

                            if ($annotation instanceof SharedClassStep) {
                                $context_annotations = $annotation->getAnnotations();

                                foreach ($context_annotations as $context_annotation) {
                                    if ($context_annotation instanceof Set) {
                                        $method_data->addSet($context_annotation);
                                    }
                                }
                            }
                        }
                    }

                    foreach ($method_annotations as $annotation) {
                        if ($annotation instanceof ExpressionStep && $annotation->validate === true) {
                            $method_data->setValidation(true);
                        }
                    }

                    foreach ($class_annotations as $class_annotation) {
                        if ($class_annotation instanceof After) {
                            foreach ($class_annotation->steps as $step) {
                                $this->onCreateMethodAnnotation($step, $reflection, $method, $class_data, $test_case_data, $method_data);

                                if ($step instanceof PlainStep) {
                                    if ($step instanceof Set) {
                                        $method_data->addSet($step);
                                    } else {
                                        $method_data->addStep($step->getStepData());
                                    }

                                    if ($step instanceof SharedClassStep) {
                                        $context_annotations = $step->getAnnotations();

                                        foreach ($context_annotations as $context_annotation) {
                                            if ($context_annotation instanceof Set) {
                                                $method_data->addSet($context_annotation);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }

                    if (count($method_data->getSteps()) > 0) {
                        $method_data->generate();

                        $class_generator->addMethodFromGenerator($method_generator);
                    }
                }

                $bundle->addClass($class_data);
                $bundle->addTestCase($test_case_data);
            }

            foreach ($bundle->getClasses() as $class) {
                $this->generateBaseClass($class);
                $this->generateClass($class);
            }

            foreach ($bundle->getTestCases() as $test_case) {
                $this->generateBaseClassTest($test_case);
                $this->generateClassTest($test_case);
            }

            shell_exec("vendor/bin/php-cs-fixer fix {$this->base_annotations_path} --rules=@Symfony");
            shell_exec("vendor/bin/php-cs-fixer fix {$this->base_src_path} --rules=@Symfony");
            shell_exec("vendor/bin/php-cs-fixer fix {$this->base_test_path} --rules=@Symfony");
        } catch (\Exception $e) {
            exit($e->getMessage() . PHP_EOL);
        }
    }

    private function generateContexts($contexts)
    {
        try {
            foreach ($contexts as $context) {
                $class = is_string($context) ? $context : $context['class'];
                /** @noinspection PhpIllegalStringOffsetInspection */
                $extends = is_string($context) ? SharedClassStep::class : $context['extends'];

                $reflection = new \ReflectionClass($class);
                $tests = false;

                $class_generator = new ClassGenerator();

                $namespace = $reflection->getNamespaceName();

                $class_generator->setNamespaceName('Generated\\Tests\\' . $namespace);
                $class_generator->setAbstract(true);
                $class_generator->setName($reflection->getShortName() . 'Test');
                $class_generator->setExtendedClass('PHPUnit\\Framework\\TestCase');

                $data_providers = [];
                $test_methods = [];
                $assertions = [];

                foreach ($reflection->getMethods() as $method) {
                    $method_annotations = $this->reader->getMethodAnnotations($method);

                    foreach ($method_annotations as $annotation) {
                        if ($annotation instanceof Test) {
                            $tests = true;

                            $data_provider = new MethodGenerator();
                            $data_provider->setAbstract(true);
                            $data_provider->setVisibility('public');
                            $data_provider->setName($method->name . 'DataProvider');

                            $data_providers[] = $data_provider;

                            $doc_block = DocBlockGenerator::fromArray([
                                'tags' => [
                                    [
                                        'name'        => 'dataProvider',
                                        'description' => $method->name . 'DataProvider',
                                    ]
                                ],
                            ]);

                            $test = new MethodGenerator();
                            $test->setDocBlock($doc_block);
                            $test->setFinal(true);
                            $test->setVisibility('public');
                            $test->setName('test' . ucfirst($method->name));

                            foreach ($method->getParameters() as $parameter) {
                                $argument = new ParameterGenerator();
                                $argument->setName($parameter->getName());
                                $argument->setPosition($parameter->getPosition());

                                if ($parameter->getType() !== null) {
                                    $argument->setType($parameter->getType());
                                }

                                if ($parameter->isDefaultValueAvailable()) {
                                    $argument->setDefaultValue($parameter->getDefaultValue());
                                }

                                $test->setParameter($argument);
                            }

                            $test->setParameter('expected');

                            $arguments = array_map(function($value) {
                                /** @var \ReflectionParameter $value */
                                return '$' . $value->getName();
                            }, $method->getParameters());

                            $body = '$_class_instance = new \\' . ltrim($class, '\\') . '();' . PHP_EOL . PHP_EOL;
                            $body .= '$this->assertTest' . ucfirst($method->name) . '($expected, $_class_instance->' . $method->name . '(' . implode(', ', $arguments) . '));';

                            $test->setBody($body);

                            $test_methods[] = $test;

                            $assertion = new MethodGenerator();
                            $assertion->setVisibility('protected');
                            $assertion->setName('assertTest' . ucfirst($method->name));
                            $assertion->setParameter('expected');
                            $assertion->setParameter('result');
                            $assertion->setBody('$this->assertEquals($expected, $result);');

                            $assertions[] = $assertion;
                        }
                    }

                    $this->generateAnnotation($reflection, $method, $extends);
                }

                foreach ($data_providers as $data_provider) {
                    $class_generator->addMethodFromGenerator($data_provider);
                }

                foreach ($test_methods as $test_method) {
                    $class_generator->addMethodFromGenerator($test_method);
                }

                foreach ($assertions as $assertion) {
                    $class_generator->addMethodFromGenerator($assertion);
                }

                if ($tests) {
                    $this->generateBaseContextTest($class_generator);
                    $this->generateContextTest($class_generator);
                }
            }
        } catch (\Exception $e) {
            exit($e->getMessage() . PHP_EOL);
        }
    }

    /**
     * @param ClassData $keeper
     */
    private function generateBaseClass(ClassData $keeper)
    {
        $class_generator = $keeper->getGenerator();

        $output_name = str_replace('\\', '/', trim(str_replace($this->class_prefix, '', $class_generator->getNamespaceName()), '\\'));

        if ($output_name) {
            $output_name .= '/';
        }

        @mkdir($this->root_dir . '/' . $this->base_src_path . '/' . $output_name, 0777, true);

        $output_name = $this->root_dir . '/' . $this->base_src_path . '/' . $output_name . $class_generator->getName() . '.php';

        $namespace = $class_generator->getNamespaceName();

        $class_generator->setNamespaceName('Generated\\' . $namespace);

        $code = '<?php' . PHP_EOL . PHP_EOL . $keeper->generate();

        file_put_contents($output_name, $code);

        $class_generator->setNamespaceName($namespace);
    }

    private function generateAnnotation(\ReflectionClass $class, \ReflectionMethod $method, $extends)
    {
        $namespace = str_replace('\\', '/', $class->getNamespaceName()) . '/' . $class->getShortName();

        $class_name = ucfirst($method->getName());

        @mkdir($this->root_dir . '/' . $this->base_annotations_path . '/' . $namespace, 0777, true);

        $output_name = $this->root_dir . '/' . $this->base_annotations_path . '/' . $namespace . '/' .$class_name . '.php';

        $doc_block = DocBlockGenerator::fromArray([
            'tags' => [
                [
                    'name' => 'Annotation',
                ],
                [
                    'name' => 'Target({"METHOD", "ANNOTATION"})'
                ]
            ],
        ]);

        $class_generator = new ClassGenerator();
        $class_generator->setDocBlock($doc_block);
        $class_generator->setNamespaceName('Generated\\Annotation\\' . $class->getName());
        $class_generator->setName($class_name);

        $annotations = $this->collectAnnotations($method);

        $returns_annotation = null;

        foreach ($annotations as $annotation) {
            if ($annotation instanceof Returns) {
                $returns_annotation = $annotation;
            }
        }

        if ($extends[0] !== '\\') {
            $extends = '\\' . $extends;
        }

        $class_generator->setExtendedClass($extends);

        foreach ($method->getParameters() as $parameter) {
            $doc_block = DocBlockGenerator::fromArray([
                'tags' => [
                    [
                        'name'        => 'var',
                        'description' => 'string',
                    ]
                ],
            ]);

            $property = new PropertyGenerator();
            $property->setDocBlock($doc_block);
            $property->setVisibility('public');
            $property->setName($parameter->getName());
            $property->setDefaultValue($parameter->getName());

            $class_generator->addPropertyFromGenerator($property);
        }

        $doc_block = DocBlockGenerator::fromArray([
            'tags' => [
                [
                    'name'        => 'var',
                    'description' => 'string',
                ]
            ],
        ]);

        if ($returns_annotation) {
            foreach ($returns_annotation->names as $name) {
                $property = new PropertyGenerator();
                $property->setDocBlock($doc_block);
                $property->setVisibility('public');
                $property->setName('out_' . $name);

                $class_generator->addPropertyFromGenerator($property);
            }
        } else {
            $property = new PropertyGenerator();
            $property->setDocBlock($doc_block);
            $property->setVisibility('public');
            $property->setName('out');

            $class_generator->addPropertyFromGenerator($property);
        }

        $method_generator = new MethodGenerator();
        $method_generator->setName('onCreate');
        $method_generator->setVisibility('public');
        $method_generator->setReturnType('void');

        $in = [];

        foreach ($method->getParameters() as $parameter) {
            $in[] = $parameter->getName();
        }

        $in = array_map(function ($v) {
            return '$this->' . $v;
        }, $in);

        $body = '$this->class = \'' . str_replace('\\', '\\\\', $class->getNamespaceName()) . '\\\\' . $class->getShortName() . '\';
        $this->method = \'' . $method->getName() . '\';
        $this->arguments = [' . implode(', ', $in) . '];' . PHP_EOL;

        if ($returns_annotation) {
            if ($returns_annotation->assoc) {
                $body .= '$this->return = [' . PHP_EOL;

                foreach ($returns_annotation->names as $name) {
                    $body .= '$this->out_' . $name . ' => true,' . PHP_EOL;
                }

                $body .= '];';
            } else {
                $names = array_map(function ($v) {
                    return '$this->out_' . $v;
                }, $returns_annotation->names);

                $body .= '$this->return = [' . implode(', ', $names) . '];';
            }
        } else {
            $body .= '$this->return = $this->out;';
        }

        $body .= '
        
        parent::onCreate();';

        $method_generator->setBody($body);

        $class_generator->addMethodFromGenerator($method_generator);

        $code = '<?php' . PHP_EOL . PHP_EOL . $class_generator->generate();

        file_put_contents($output_name, $code);
    }

    /**
     * @param ClassData $keeper
     */
    private function generateClass(ClassData $keeper)
    {
        $class_generator = $keeper->getGenerator();

        $output_name = str_replace('\\', '/', trim(str_replace($this->class_prefix, '', $class_generator->getNamespaceName()), '\\'));

        if ($output_name) {
            $output_name .= '/';
        }

        @mkdir($this->root_dir . '/' . $this->src_path . '/' . $output_name, 0777, true);

        $output_name = $this->root_dir . '/' . $this->src_path . '/' . $output_name . $class_generator->getName() . '.php';

        if (is_file($output_name)) {
            return;
        }

        $class = new ClassGenerator();
        $class->setNamespaceName($class_generator->getNamespaceName());
        $class->setName($class_generator->getName());
        $class->setExtendedClass('\\Generated\\' . $class_generator->getNamespaceName() . '\\' . $class_generator->getName());

        foreach ($class_generator->getMethods() as $method_generator) {
            if ($method_generator->isAbstract()) {
                $method = new MethodGenerator();
                $method->setName($method_generator->getName());
                $method->setParameters($method_generator->getParameters());
                $method->setVisibility($method_generator->getVisibility());
                $method->setReturnType($method_generator->getReturnType());
                $method->setBody('throw new \Exception(\'Method "' . $method->getName() . '" is not implemented yet.\');');

                $class->addMethodFromGenerator($method);
            }
        }

        $code = '<?php' . PHP_EOL . PHP_EOL . $class->generate();

        file_put_contents($output_name, $code);
    }

    /**
     * @param TestCaseData $keeper
     */
    private function generateBaseClassTest(TestCaseData $keeper)
    {
        $generator = $keeper->getGenerator();

        $output_name = str_replace('\\', '/', trim(str_replace('Generated\\Tests\\' . $this->class_prefix, '', $generator->getNamespaceName()), '\\'));

        if ($output_name) {
            $output_name .= '/';
        }

        @mkdir($this->root_dir . '/' . $this->base_test_path . '/' . $output_name, 0777, true);

        $output_name = $this->root_dir . '/' . $this->base_test_path . '/' . $output_name . $generator->getName() . '.php';

        $code = '<?php' . PHP_EOL . PHP_EOL . $generator->generate();

        file_put_contents($output_name, $code);
    }

    /**
     * @param TestCaseData $keeper
     */
    private function generateClassTest(TestCaseData $keeper)
    {
        $generator = $keeper->getGenerator();

        $output_name = str_replace('\\', '/', trim(str_replace('Generated\\Tests\\' . $this->class_prefix, '', $generator->getNamespaceName()), '\\'));

        if ($output_name) {
            $output_name .= '/';
        }

        @mkdir($this->root_dir . '/' . $this->test_path . '/' . $output_name, 0777, true);

        $output_name = $this->root_dir . '/' . $this->test_path . '/' . $output_name . $generator->getName() . '.php';

        if (is_file($output_name)) {
            return;
        }

        $class = new ClassGenerator();
        $class->setNamespaceName(str_replace('Generated\\', '', $generator->getNamespaceName()));
        $class->setName($generator->getName());
        $class->setExtendedClass($generator->getNamespaceName() . '\\' . $generator->getName());

        $code = '<?php' . PHP_EOL . PHP_EOL . $class->generate();

        file_put_contents($output_name, $code);
    }

    /**
     * @param ClassGenerator $class_generator
     */
    private function generateBaseContextTest(ClassGenerator $class_generator)
    {
        // If context is from another package
        if (strpos($class_generator->getNamespaceName(), 'Generated\\Tests\\' . $this->context_prefix) !== 0) {
            return;
        }

        $output_name = str_replace('\\', '/', trim(str_replace('Generated\\Tests\\' . $this->context_prefix, '', $class_generator->getNamespaceName()), '\\'));

        if ($output_name) {
            $output_name .= '/';
        }

        @mkdir($this->root_dir . '/' . $this->base_test_path . '/' . $output_name, 0777, true);

        $output_name = $this->root_dir . '/' . $this->base_test_path . '/' . $output_name . $class_generator->getName() . '.php';

        $code = '<?php' . PHP_EOL . PHP_EOL . $class_generator->generate();

        file_put_contents($output_name, $code);
    }

    /**
     * @param ClassGenerator $class_generator
     */
    private function generateContextTest(ClassGenerator $class_generator)
    {
        // If context is from another package
        if (strpos($class_generator->getNamespaceName(), 'Generated\\Tests\\' . $this->context_prefix) !== 0) {
            return;
        }

        $output_name = str_replace('\\', '/', trim(str_replace('Generated\\Tests\\' . $this->context_prefix, '', $class_generator->getNamespaceName()), '\\'));

        if ($output_name) {
            $output_name .= '/';
        }

        @mkdir($this->root_dir . '/' . $this->test_path . '/' . $output_name, 0777, true);

        $output_name = $this->root_dir . '/' . $this->test_path . '/' . $output_name . $class_generator->getName() . '.php';

        if (is_file($output_name)) {
            return;
        }

        $class = new ClassGenerator();
        $class->setNamespaceName(str_replace('Generated\\', '', $class_generator->getNamespaceName()));
        $class->setName($class_generator->getName());
        $class->setExtendedClass($class_generator->getNamespaceName() . '\\' . $class_generator->getName());

        foreach ($class_generator->getMethods() as $method_generator) {
            if ($method_generator->isAbstract()) {
                $method = new MethodGenerator();
                $method->setName($method_generator->getName());
                $method->setParameters($method_generator->getParameters());
                $method->setVisibility($method_generator->getVisibility());
                $method->setReturnType($method_generator->getReturnType());
                $method->setBody('throw new \Exception(\'Method "' . $method->getName() . '" is not implemented yet.\');');

                $class->addMethodFromGenerator($method);
            }
        }

        $code = '<?php' . PHP_EOL . PHP_EOL . $class->generate();

        file_put_contents($output_name, $code);
    }

    private function onCreateMethodAnnotation(
        $annotation,
        \ReflectionClass $reflection,
        \ReflectionMethod $method,
        ClassData $class_keeper,
        TestCaseData $test_case_keeper,
        MethodData $method_keeper
    )
    {
        if (!$annotation instanceof Annotation) {
            return;
        }

        $annotation->setReflectionClass($reflection);
        $annotation->setReflectionMethod($method);
        $annotation->setClassData($class_keeper);
        $annotation->setTestCaseData($test_case_keeper);
        $annotation->setMethodData($method_keeper);

        if ($annotation instanceof PlainStep) {
            $annotation->setStepData(new StepData());
        }

        $annotation->onCreate();
    }

    private function collectAnnotations(\ReflectionMethod $method)
    {
        $annotations = [];

        $reader = new AnnotationReader();
        /** @noinspection PhpDeprecationInspection */
        AnnotationRegistry::registerLoader('class_exists');
        $method_annotations = $reader->getMethodAnnotations($method);

        foreach ($method_annotations as $method_annotation) {
            if ($method_annotation instanceof Annotation) {
                $annotations[] = $method_annotation;
            }
        }

        return $annotations;
    }
}

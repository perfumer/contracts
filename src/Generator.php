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
use Perfumerlabs\Perfumer\Data\AbstractData;
use Perfumerlabs\Perfumer\Data\BaseClassData;
use Perfumerlabs\Perfumer\Data\BaseTestData;
use Perfumerlabs\Perfumer\Data\ClassData;
use Perfumerlabs\Perfumer\Data\MethodData;
use Perfumerlabs\Perfumer\Data\TestData;
use Perfumerlabs\Perfumer\Step\ClassCallStep;
use Perfumerlabs\Perfumer\Step\ExpressionStep;
use Perfumerlabs\Perfumer\Step\SharedClassCallStep;
use Perfumerlabs\Perfumer\Step\Step;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\DocBlockGenerator;
use Zend\Code\Generator\MethodGenerator;
use Zend\Code\Generator\ParameterGenerator;
use Zend\Code\Generator\PropertyGenerator;

final class Generator implements GeneratorInterface
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
    private $generated_annotation_path = 'generated/annotation';

    /**
     * @var string
     */
    private $generated_src_path = 'generated/src';

    /**
     * @var string
     */
    private $generated_tests_path = 'generated/tests';

    /**
     * @var string
     */
    private $src_path = 'src';

    /**
     * @var string
     */
    private $tests_path = 'tests';

    /**
     * @var array
     */
    private $modules = [];

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

    public function __construct(string $root_dir, array $options = [])
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

        if (isset($options['generated_annotation_path'])) {
            $this->generated_annotation_path = (string) $options['generated_annotation_path'];
        }

        if (isset($options['generated_src_path'])) {
            $this->generated_src_path = (string) $options['generated_src_path'];
        }

        if (isset($options['generated_tests_path'])) {
            $this->generated_tests_path = (string) $options['generated_tests_path'];
        }

        if (isset($options['src_path'])) {
            $this->src_path = (string) $options['src_path'];
        }

        if (isset($options['tests_path'])) {
            $this->tests_path = (string) $options['tests_path'];
        }
    }

    public function addModule(string $class, ?string $regex = null, array $exceptions = [])
    {
        $this->modules[] = [
            'class' => $class,
            'regex' => $regex,
            'exceptions' => $exceptions,
            'annotations' => []
        ];

        return $this;
    }

    public function addContract(string $class, bool $has_default_context = false)
    {
        $this->contracts[] = [
            'class' => $class,
            'has_default_context' => $has_default_context
        ];

        return $this;
    }

    public function addContext(string $class, $options = [], $properties = [])
    {
        $this->contexts[] = [
            'class' => $class,
            'options' => $options,
            'properties' => $properties,
        ];

        return $this;
    }

    private function collectModuleAnnotations()
    {
        foreach ($this->modules as &$module) {
            $reflection = new \ReflectionClass($module['class']);

            $module['annotations'] = $this->reader->getClassAnnotations($reflection);
        }
    }

    public function generateAll()
    {
        $this->generateContexts($this->contexts);

        $this->collectModuleAnnotations();

        try {
            $bundle = new Bundle();

            foreach ($this->contracts as $contract) {
                $class = $contract['class'];

                $reflection = new \ReflectionClass($class);

                if ($contract['has_default_context']) {
                    $this->generateContexts([$class . 'Context']);
                }

                $namespace = str_replace($this->contract_prefix, $this->class_prefix, $reflection->getNamespaceName());

                $test_data = new TestData();
                $test_generator = $test_data->getGenerator();
                $test_generator->setNamespaceName('Tests\\' . $namespace);
                $test_generator->setName($reflection->getShortName() . 'Test');
                $test_generator->setExtendedClass('Generated\\Tests\\' . $namespace . '\\' . $reflection->getShortName() . 'Test');

                $base_test_data = new BaseTestData();
                $base_test_generator = $base_test_data->getGenerator();
                $base_test_generator->setNamespaceName('Generated\\Tests\\' . $namespace);
                $base_test_generator->setAbstract(true);
                $base_test_generator->setName($reflection->getShortName() . 'Test');
                $base_test_generator->setExtendedClass('PHPUnit\\Framework\\TestCase');

                $class_data = new ClassData();
                $class_generator = $class_data->getGenerator();
                $class_generator->setNamespaceName($namespace);
                $class_generator->setName($reflection->getShortName());
                $class_generator->setExtendedClass('\\Generated\\' . $namespace . '\\' . $reflection->getShortName());

                $base_class_data = new BaseClassData();
                $base_class_generator = $base_class_data->getGenerator();
                $base_class_generator->setAbstract(true);
                $base_class_generator->setNamespaceName('Generated\\' . $namespace);
                $base_class_generator->setName($reflection->getShortName());

                if ($reflection->isInterface()) {
                    $base_class_generator->setImplementedInterfaces(array_merge($base_class_generator->getImplementedInterfaces(), ['\\' . $class]));
                } else {
                    $base_class_generator->setExtendedClass('\\' . $class);
                }

                $class_annotations = [];

                foreach ($this->modules as $module) {
                    if ($module['regex'] === null || (!in_array($class, $module['exceptions']) && preg_match($module['regex'], $class))) {
                        $class_annotations = array_merge($class_annotations, $module['annotations']);
                    }
                }

                $reader_annotations = $this->reader->getClassAnnotations($reflection);

                foreach ($reader_annotations as $reader_annotation) {
                    if ($reader_annotation instanceof ClassAnnotation) {
                        $class_annotations[] = $reader_annotation;
                    }
                }

                foreach ($class_annotations as $annotation) {
                    if (!$annotation instanceof ClassAnnotation) {
                        continue;
                    }

                    $annotation->setReflectionClass($reflection);
                    $annotation->setBaseClassData($base_class_data);
                    $annotation->setBaseTestData($base_test_data);
                    $annotation->setClassData($class_data);
                    $annotation->setTestData($test_data);

                    $annotation->onCreate();
                    $annotation->onBuild();
                }

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

                    $method_annotations = [];

                    foreach ($class_annotations as $class_annotation) {
                        if ($class_annotation instanceof Before) {
                            foreach ($class_annotation->steps as $reader_annotation) {
                                if ($reader_annotation instanceof Step) {
                                    $method_annotations[] = $reader_annotation;
                                }
                            }
                        }
                    }

                    $reader_annotations = $this->reader->getMethodAnnotations($method);

                    foreach ($reader_annotations as $reader_annotation) {
                        if ($reader_annotation instanceof Step) {
                            $method_annotations[] = $reader_annotation;
                        }
                    }

                    for ($i = count($class_annotations) - 1; $i >= 0; $i--) {
                        $class_annotation = $class_annotations[$i];

                        if ($class_annotation instanceof After) {
                            foreach ($class_annotation->steps as $reader_annotation) {
                                if ($reader_annotation instanceof Step) {
                                    $method_annotations[] = $reader_annotation;
                                }
                            }
                        }
                    }

                    $set_annotations = [];
                    $step_annotations = [];

                    foreach ($method_annotations as $annotation) {
                        if ($annotation instanceof Set) {
                            $set_annotations[] = $annotation;
                        } else {
                            $step_annotations[] = $annotation;
                        }
                    }

                    foreach ($method_annotations as $annotation) {
                        $add_set_annotations = $this->onCreateMethodAnnotation($annotation, $reflection, $method, $base_class_data, $base_test_data, $class_data, $test_data, $method_data);

                        $set_annotations = array_merge($set_annotations, $add_set_annotations);
                    }

                    $validated_returns = [];

                    foreach ($step_annotations as $annotation) {
                        if ($annotation instanceof Error && $annotation->unless) {
                            $validated_returns[] = $annotation->unless;
                        }
                    }

                    foreach ($step_annotations as $annotation) {
                        if ($annotation instanceof ExpressionStep && is_string($annotation->getReturn()) && in_array($annotation->getReturn(), $validated_returns)) {
                            $annotation->validate = true;
                        }
                    }

                    $method_annotations = array_merge($set_annotations, $step_annotations);

                    /** @var Step $annotation */
                    foreach ($method_annotations as $annotation) {
                        $annotation->onBuild();
                    }

                    if (count($method_data->getSteps()) > 0 || count($method_data->getSets()) > 0) {
                        $method_data->generate();

                        $base_class_generator->addMethodFromGenerator($method_generator);
                    }
                }

                $bundle->addBaseClassData($base_class_data);
                $bundle->addClassData($class_data);
                $bundle->addBaseTestData($base_test_data);
                $bundle->addTestData($test_data);
            }

            foreach ($bundle->getBaseClassData() as $base_class_data) {
                $this->generateClass($base_class_data, $base_class_data->getGenerator(), $this->generated_src_path, 'Generated\\', true);
            }

            foreach ($bundle->getClassData() as $class_data) {
                $this->generateClass($class_data, $class_data->getGenerator(), $this->src_path, '', false);
            }

            foreach ($bundle->getBaseTestData() as $base_test_data) {
                $this->generateClass($base_test_data, $base_test_data->getGenerator(), $this->generated_tests_path, 'Generated\\Tests\\', true);
            }

            foreach ($bundle->getTestData() as $test_data) {
                $this->generateClass($test_data, $test_data->getGenerator(), $this->tests_path, 'Tests\\', false);
            }

            shell_exec("vendor/bin/php-cs-fixer fix {$this->generated_annotation_path} --rules=@Symfony");
            shell_exec("vendor/bin/php-cs-fixer fix {$this->generated_src_path} --rules=@Symfony");
            shell_exec("vendor/bin/php-cs-fixer fix {$this->generated_tests_path} --rules=@Symfony");
        } catch (PerfumerException $e) {
            exit($e->getMessage() . PHP_EOL);
        }
    }

    private function onCreateMethodAnnotation(
        Step $annotation,
        \ReflectionClass $reflection_class,
        \ReflectionMethod $reflection_method,
        BaseClassData $base_class_data,
        BaseTestData $base_test_data,
        ClassData $class_data,
        TestData $test_data,
        MethodData $method_data
    )
    {
        $annotation->setReflectionClass($reflection_class);
        $annotation->setReflectionMethod($reflection_method);
        $annotation->setBaseClassData($base_class_data);
        $annotation->setBaseTestData($base_test_data);
        $annotation->setClassData($class_data);
        $annotation->setTestData($test_data);
        $annotation->setMethodData($method_data);
        $annotation->onCreate();

        $add_annotations = [];

        if ($annotation instanceof Set) {
            $method_data->addSet($annotation);
        } else {
            $method_data->addStep($annotation);
        }

        if ($annotation instanceof ClassCallStep) {
            $context_annotations = $this->collectMethodAnnotations($annotation->getClass(), $annotation->getMethod());

            foreach ($context_annotations as $context_annotation) {
                if ($context_annotation instanceof Set) {
                    // Do not set annotations with different tags
                    if ($context_annotation->tags && !array_intersect($base_class_data->getTags(), $annotation->tags)) {
                        continue;
                    }

                    $context_annotation->setReflectionClass($reflection_class);
                    $context_annotation->setReflectionMethod($reflection_method);
                    $context_annotation->setBaseClassData($base_class_data);
                    $context_annotation->setBaseTestData($base_test_data);
                    $context_annotation->setClassData($class_data);
                    $context_annotation->setTestData($test_data);
                    $context_annotation->setMethodData($method_data);
                    $context_annotation->onCreate();

                    $method_data->addSet($context_annotation);

                    $add_annotations[] = $context_annotation;
                }
            }
        }

        return $add_annotations;
    }

    private function collectAnnotations($class, $method)
    {
        $annotations = [];

        if (!$method instanceof \ReflectionMethod) {
            if (!$class instanceof \ReflectionClass) {
                $class = new \ReflectionClass($class);
            }

            foreach ($class->getMethods() as $class_method) {
                if ($class_method->getName() === $method) {
                    $method = $class_method;
                }
            }
        }

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

    private function collectMethodAnnotations($class, $method)
    {
        $annotations = $this->collectAnnotations($class, $method);

        $method_annotations = [];

        foreach ($annotations as $annotation) {
            if ($annotation instanceof Step) {
                $method_annotations[] = $annotation;
            }
        }

        return $method_annotations;
    }

    private function generateContexts($contexts)
    {
        try {
            foreach ($contexts as $context) {
                if (is_string($context)) {
                    $class = $context;
                    $extends = SharedClassCallStep::class;
                    $properties = [];
                } else {
                    $class = $context['class'];
                    $extends = $context['options']['extends'] ?? SharedClassCallStep::class;
                    $properties = $context['properties'] ?? [];
                }

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

                    $this->generateAnnotation($reflection, $method, $extends, $properties);
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

    private function generateAnnotation(\ReflectionClass $class, \ReflectionMethod $method, $extends, $properties = [])
    {
        $namespace = str_replace('\\', '/', $class->getNamespaceName()) . '/' . $class->getShortName();

        $class_name = ucfirst($method->getName());

        @mkdir($this->root_dir . '/' . $this->generated_annotation_path . '/' . $namespace, 0777, true);

        $output_name = $this->root_dir . '/' . $this->generated_annotation_path . '/' . $namespace . '/' .$class_name . '.php';

        $doc_block = DocBlockGenerator::fromArray([
            'tags' => [
                [
                    'name' => 'Annotation',
                ],
                [
                    'name' => 'Target({"CLASS", "METHOD", "ANNOTATION"})'
                ]
            ],
        ]);

        $class_generator = new ClassGenerator();
        $class_generator->setDocBlock($doc_block);
        $class_generator->setNamespaceName('Generated\\Annotation\\' . $class->getName());
        $class_generator->setName($class_name);

        $annotations = $this->collectAnnotations($class, $method);

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
            $property->setName('in_' . $parameter->getName());

            $class_generator->addPropertyFromGenerator($property);

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

        $body = '$this->_class = \'' . str_replace('\\', '\\\\', $class->getNamespaceName()) . '\\\\' . $class->getShortName() . '\';
        $this->_method = \'' . $method->getName() . '\';' . PHP_EOL;

        foreach ($method->getParameters() as $parameter) {
            $body .= sprintf('$in_%s = $this->in_%s ?: $this->%s;', $parameter->getName(), $parameter->getName(), $parameter->getName()) . PHP_EOL;

            $body .= sprintf('if (!$in_%s) {
                $in_%s = \'%s\';
            }', $parameter->getName(), $parameter->getName(), $parameter->getName()) . PHP_EOL;
        }

        $in = array_map(function ($v) {
            return '$in_' . $v;
        }, $in);

        $body .= '$this->_arguments = [' . implode(', ', $in) . '];' . PHP_EOL;

        if ($returns_annotation) {
            if ($returns_annotation->assoc) {
                $body .= '$this->_return = [' . PHP_EOL;

                foreach ($returns_annotation->names as $name) {
                    $body .= '$this->out_' . $name . ' => true,' . PHP_EOL;
                }

                $body .= '];';
            } else {
                $names = array_map(function ($v) {
                    return '$this->out_' . $v;
                }, $returns_annotation->names);

                $body .= '$this->_return = [' . implode(', ', $names) . '];';
            }
        } else {
            $body .= '$this->_return = $this->out;';
        }

        foreach ($properties as $key => $property) {
            $body .= sprintf('$this->%s = \'%s\';', $key, $property) . PHP_EOL;
        }

        $body .= '
        
        parent::onCreate();';

        $method_generator->setBody($body);

        $class_generator->addMethodFromGenerator($method_generator);

        $code = '<?php' . PHP_EOL . PHP_EOL . $class_generator->generate();

        file_put_contents($output_name, $code);
    }

    private function generateClass(AbstractData $data, ClassGenerator $generator, string $path, string $prefix, bool $overwrite): void
    {
        $output_name = str_replace('\\', '/', trim(str_replace($prefix . $this->class_prefix, '', $generator->getNamespaceName()), '\\'));

        if ($output_name) {
            $output_name .= '/';
        }

        @mkdir($this->root_dir . '/' . $path . '/' . $output_name, 0777, true);

        $output_name = $this->root_dir . '/' . $path . '/' . $output_name . $generator->getName() . '.php';

        if ($overwrite === false && is_file($output_name)) {
            return;
        }

        $code = '<?php' . PHP_EOL . PHP_EOL . $data->generate();

        file_put_contents($output_name, $code);
    }

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

        @mkdir($this->root_dir . '/' . $this->generated_tests_path . '/' . $output_name, 0777, true);

        $output_name = $this->root_dir . '/' . $this->generated_tests_path . '/' . $output_name . $class_generator->getName() . '.php';

        $code = '<?php' . PHP_EOL . PHP_EOL . $class_generator->generate();

        file_put_contents($output_name, $code);
    }

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

        @mkdir($this->root_dir . '/' . $this->tests_path . '/' . $output_name, 0777, true);

        $output_name = $this->root_dir . '/' . $this->tests_path . '/' . $output_name . $class_generator->getName() . '.php';

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
}

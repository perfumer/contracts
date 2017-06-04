<?php

namespace Perfumer\Contracts;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Perfumer\Contracts\Annotations\Test;
use Perfumer\Contracts\Decorator\ClassAnnotationDecorator;
use Perfumer\Contracts\Decorator\ClassDecorator;
use Perfumer\Contracts\Decorator\MethodAnnotationDecorator;
use Perfumer\Contracts\Decorator\MethodDecorator;
use Perfumer\Contracts\Decorator\TestCaseDecorator;
use Perfumer\Contracts\Exception\ContractsException;
use Perfumer\Contracts\Exception\DecoratorException;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\DocBlockGenerator;
use Zend\Code\Generator\MethodGenerator;
use Zend\Code\Generator\ParameterGenerator;

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

        $this->addAnnotations(__DIR__ . '/Annotations.php');

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
     * @param string $filename
     */
    public function addAnnotations(string $filename)
    {
        AnnotationRegistry::registerFile($filename);
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

    private function generateContext(string $class)
    {
        try {
            $reflection = new \ReflectionClass($class);
            $tests = false;

            $class_builder = new ClassBuilder();

            $namespace = $reflection->getNamespaceName();

            $class_builder->setNamespaceName('Generated\\Tests\\' . $namespace);
            $class_builder->setAbstract(true);
            $class_builder->setName($reflection->getShortName() . 'Test');
            $class_builder->setExtendedClass('PHPUnit\\Framework\\TestCase');

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

                        $body = '$_class_instance = new ' . $class . '();' . PHP_EOL . PHP_EOL;
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
            }

            foreach ($data_providers as $data_provider) {
                $class_builder->addMethodFromGenerator($data_provider);
            }

            foreach ($test_methods as $test_method) {
                $class_builder->addMethodFromGenerator($test_method);
            }

            foreach ($assertions as $assertion) {
                $class_builder->addMethodFromGenerator($assertion);
            }

            if ($tests) {
                $this->generateBaseContextTest($class_builder);
                $this->generateContextTest($class_builder);
            }
        } catch (ContractsException $e) {
            exit($e->getMessage() . PHP_EOL);
        }
    }

    public function generateClasses()
    {
        try {
            $bundle = new Bundle();

            foreach ($this->classes as $class) {
                $reflection = new \ReflectionClass($class);

                $namespace = str_replace($this->contract_prefix, $this->class_prefix, $reflection->getNamespaceName());

                $test_case_builder = new TestCaseBuilder();
                $test_case_builder->setNamespaceName('Generated\\Tests\\' . $namespace);
                $test_case_builder->setAbstract(true);
                $test_case_builder->setName($reflection->getShortName() . 'Test');
                $test_case_builder->setExtendedClass('PHPUnit\\Framework\\TestCase');

                $reflection_test = new MethodGenerator();
                $reflection_test->setFinal(true);
                $reflection_test->setName('testSyntax');
                $reflection_test->setBody('new \\ReflectionClass(\\' . $namespace . '\\' . $reflection->getShortName() . '::class);');

                $test_case_builder->addMethodFromGenerator($reflection_test);

                $class_builder = new ClassBuilder();
                $class_builder->setAbstract(true);
                $class_builder->setContract($reflection);
                $class_builder->setNamespaceName($namespace);
                $class_builder->setName($reflection->getShortName());

                if ($reflection->isInterface()) {
                    $class_builder->setImplementedInterfaces(array_merge($class_builder->getImplementedInterfaces(), ['\\' . $class]));
                } else {
                    $class_builder->setExtendedClass('\\' . $class);
                }

                $class_annotations = $this->reader->getClassAnnotations($reflection);

                try {
                    foreach ($class_annotations as $annotation) {
                        if (!$annotation instanceof Annotation) {
                            continue;
                        }

                        $annotation->setReflectionClass($reflection);

                        if ($annotation instanceof ClassAnnotationDecorator) {
                            foreach ($class_annotations as $another) {
                                if ($annotation instanceof Annotation && $annotation !== $another) {
                                    $annotation->decorateClassAnnotation($another);
                                }
                            }
                        }
                    }

                    foreach ($class_annotations as $annotation) {
                        if (!$annotation instanceof Annotation) {
                            continue;
                        }

                        if ($annotation instanceof ClassDecorator) {
                            $annotation->decorateClass($class_builder);
                        }

                        if ($annotation instanceof TestCaseDecorator) {
                            $annotation->decorateTestCase($test_case_builder);
                        }
                    }
                } catch (DecoratorException $e) {
                    throw new ContractsException(sprintf('%s\\%s: ' . $e->getMessage(),
                        $class_builder->getNamespaceName(),
                        $class_builder->getName()
                    ));
                }

                foreach ($reflection->getMethods() as $method) {
                    $method_builder = new MethodBuilder();
                    $method_builder->setFinal(true);
                    $method_builder->setName($method->name);
                    $method_builder->setVisibility('public');

                    if ($method->getReturnType() !== null) {
                        $type = (string) $method->getReturnType();

                        if ($type && !$method->getReturnType()->isBuiltin()) {
                            $type = '\\' . $type;
                        }

                        $method_builder->setReturnType($type);
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

                        $method_builder->setParameter($argument);
                    }

                    $method_annotations = $this->reader->getMethodAnnotations($method);

                    try {
                        foreach ($class_annotations as $annotation) {
                            if (!$annotation instanceof Annotation) {
                                continue;
                            }

                            if ($annotation instanceof MethodAnnotationDecorator) {
                                foreach ($method_annotations as $another) {
                                    if ($annotation instanceof Annotation) {
                                        $annotation->decorateMethodAnnotation($another);
                                    }
                                }
                            }
                        }

                        foreach ($method_annotations as $annotation) {
                            if (!$annotation instanceof Annotation) {
                                continue;
                            }

                            $annotation->setReflectionMethod($method);

                            if ($annotation instanceof MethodAnnotationDecorator) {
                                foreach ($method_annotations as $another) {
                                    if ($annotation instanceof Annotation && $annotation !== $another) {
                                        $annotation->decorateMethodAnnotation($another);
                                    }
                                }
                            }
                        }

                        foreach ($method_annotations as $annotation) {
                            if (!$annotation instanceof Annotation) {
                                continue;
                            }

                            if ($annotation instanceof ClassDecorator) {
                                $annotation->decorateClass($class_builder);
                            }

                            if ($annotation instanceof MethodDecorator) {
                                $annotation->decorateMethod($method_builder);
                            }

                            if ($annotation instanceof TestCaseDecorator) {
                                $annotation->decorateTestCase($test_case_builder);
                            }
                        }

                        foreach ($class_annotations as $annotation) {
                            if ($annotation instanceof MethodDecorator) {
                                $annotation->decorateMethod($method_builder);
                            }
                        }

                        foreach ($method_annotations as $annotation) {
                            if ($annotation instanceof Step) {
                                $step_builders = $annotation->getBuilder();

                                if ($step_builders === null) {
                                    continue;
                                }

                                if (!is_array($step_builders)) {
                                    $step_builders = [$step_builders];
                                }

                                foreach ($step_builders as $step_builder) {
                                    $method_builder->addStep($step_builder);
                                }
                            }
                        }
                    } catch (DecoratorException $e) {
                        throw new ContractsException(sprintf('%s\\%s -> %s: ' . $e->getMessage(),
                            $class_builder->getNamespaceName(),
                            $class_builder->getName(),
                            $method_builder->getName()
                        ));
                    }

                    if (count($method_builder->getSteps()) > 0) {
                        $class_builder->addMethodFromGenerator($method_builder);
                    }
                }

                $bundle->getClassBuilders()->append($class_builder);
                $bundle->getTestCaseBuilders()->append($test_case_builder);
            }

            foreach ($bundle->getClassBuilders() as $class_builder) {
                $this->generateBaseClass($class_builder);
                $this->generateClass($class_builder);

                foreach ($class_builder->getContexts() as $context) {
                    $this->generateContext($context);
                }
            }

            foreach ($bundle->getTestCaseBuilders() as $builder) {
                $this->generateBaseClassTest($builder);
                $this->generateClassTest($builder);
            }

            shell_exec("vendor/bin/php-cs-fixer fix {$this->base_src_path} --rules=@Symfony");
            shell_exec("vendor/bin/php-cs-fixer fix {$this->base_test_path} --rules=@Symfony");
        } catch (ContractsException $e) {
            exit($e->getMessage() . PHP_EOL);
        }
    }

    /**
     * @param ClassBuilder $class_builder
     */
    private function generateBaseClass(ClassBuilder $class_builder)
    {
        $output_name = str_replace('\\', '/', trim(str_replace($this->class_prefix, '', $class_builder->getNamespaceName()), '\\'));

        if ($output_name) {
            $output_name .= '/';
        }

        $output_name = $this->root_dir . '/' . $this->base_src_path . '/' . $output_name . $class_builder->getName() . '.php';

        $namespace = $class_builder->getNamespaceName();

        $class_builder->setNamespaceName('Generated\\' . $namespace);

        $code = '<?php' . PHP_EOL . PHP_EOL . $class_builder->generate();

        file_put_contents($output_name, $code);

        $class_builder->setNamespaceName($namespace);
    }

    /**
     * @param ClassBuilder $class_builder
     */
    private function generateClass(ClassBuilder $class_builder)
    {
        $output_name = str_replace('\\', '/', trim(str_replace($this->class_prefix, '', $class_builder->getNamespaceName()), '\\'));

        if ($output_name) {
            $output_name .= '/';
        }

        $output_name = $this->root_dir . '/' . $this->src_path . '/' . $output_name . $class_builder->getName() . '.php';

        if (is_file($output_name)) {
            return;
        }

        $class = new ClassGenerator();
        $class->setNamespaceName($class_builder->getNamespaceName());
        $class->setName($class_builder->getName());
        $class->setExtendedClass('\\Generated\\' . $class_builder->getNamespaceName() . '\\' . $class_builder->getName());

        foreach ($class_builder->getMethods() as $method_builder) {
            if ($method_builder->isAbstract()) {
                $method = new MethodGenerator();
                $method->setName($method_builder->getName());
                $method->setParameters($method_builder->getParameters());
                $method->setVisibility($method_builder->getVisibility());
                $method->setReturnType($method_builder->getReturnType());
                $method->setBody('throw new \Exception(\'Method "' . $method->getName() . '" is not implemented yet.\');');

                $class->addMethodFromGenerator($method);
            }
        }

        $code = '<?php' . PHP_EOL . PHP_EOL . $class->generate();

        file_put_contents($output_name, $code);
    }

    /**
     * @param TestCaseBuilder $builder
     */
    private function generateBaseClassTest(TestCaseBuilder $builder)
    {
        $output_name = str_replace('\\', '/', trim(str_replace('Generated\\Tests\\' . $this->class_prefix, '', $builder->getNamespaceName()), '\\'));

        if ($output_name) {
            $output_name .= '/';
        }

        $output_name = $this->root_dir . '/' . $this->base_test_path . '/' . $output_name . $builder->getName() . '.php';

        $code = '<?php' . PHP_EOL . PHP_EOL . $builder->generate();

        file_put_contents($output_name, $code);
    }

    /**
     * @param TestCaseBuilder $builder
     */
    private function generateClassTest(TestCaseBuilder $builder)
    {
        $output_name = str_replace('\\', '/', trim(str_replace('Generated\\Tests\\' . $this->class_prefix, '', $builder->getNamespaceName()), '\\'));

        if ($output_name) {
            $output_name .= '/';
        }

        $output_name = $this->root_dir . '/' . $this->test_path . '/' . $output_name . $builder->getName() . '.php';

        if (is_file($output_name)) {
            return;
        }

        $class = new ClassGenerator();
        $class->setNamespaceName(str_replace('Generated\\', '', $builder->getNamespaceName()));
        $class->setName($builder->getName());
        $class->setExtendedClass($builder->getNamespaceName() . '\\' . $builder->getName());

        $code = '<?php' . PHP_EOL . PHP_EOL . $class->generate();

        file_put_contents($output_name, $code);
    }

    /**
     * @param ClassBuilder $class_builder
     */
    private function generateBaseContextTest(ClassBuilder $class_builder)
    {
        // If context is from another package
        if (strpos($class_builder->getNamespaceName(), 'Generated\\Tests\\' . $this->context_prefix) !== 0) {
            return;
        }

        $output_name = str_replace('\\', '/', trim(str_replace('Generated\\Tests\\' . $this->context_prefix, '', $class_builder->getNamespaceName()), '\\'));

        if ($output_name) {
            $output_name .= '/';
        }

        $output_name = $this->root_dir . '/' . $this->base_test_path . '/' . $output_name . $class_builder->getName() . '.php';

        $code = '<?php' . PHP_EOL . PHP_EOL . $class_builder->generate();

        file_put_contents($output_name, $code);
    }

    /**
     * @param ClassBuilder $class_builder
     */
    private function generateContextTest(ClassBuilder $class_builder)
    {
        // If context is from another package
        if (strpos($class_builder->getNamespaceName(), 'Generated\\Tests\\' . $this->context_prefix) !== 0) {
            return;
        }

        $output_name = str_replace('\\', '/', trim(str_replace('Generated\\Tests\\' . $this->context_prefix, '', $class_builder->getNamespaceName()), '\\'));

        if ($output_name) {
            $output_name .= '/';
        }

        $output_name = $this->root_dir . '/' . $this->test_path . '/' . $output_name . $class_builder->getName() . '.php';

        if (is_file($output_name)) {
            return;
        }

        $class = new ClassGenerator();
        $class->setNamespaceName(str_replace('Generated\\', '', $class_builder->getNamespaceName()));
        $class->setName($class_builder->getName());
        $class->setExtendedClass($class_builder->getNamespaceName() . '\\' . $class_builder->getName());

        foreach ($class_builder->getMethods() as $method_builder) {
            if ($method_builder->isAbstract()) {
                $method = new MethodGenerator();
                $method->setName($method_builder->getName());
                $method->setParameters($method_builder->getParameters());
                $method->setVisibility($method_builder->getVisibility());
                $method->setReturnType($method_builder->getReturnType());
                $method->setBody('throw new \Exception(\'Method "' . $method->getName() . '" is not implemented yet.\');');

                $class->addMethodFromGenerator($method);
            }
        }

        $code = '<?php' . PHP_EOL . PHP_EOL . $class->generate();

        file_put_contents($output_name, $code);
    }
}

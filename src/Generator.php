<?php

namespace Barman;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Barman\Annotation\Test;
use Barman\Decorator\ClassAnnotationDecorator;
use Barman\Decorator\MethodAnnotationDecorator;
use Barman\Decorator\MethodGeneratorDecorator;
use Barman\Decorator\StepGeneratorDecorator;
use Barman\Exception\ContractsException;
use Barman\Exception\DecoratorException;
use Barman\Generator\ClassGenerator;
use Barman\Generator\MethodGenerator;
use Barman\Generator\StepGenerator;
use Barman\Generator\TestCaseGenerator;
use Zend\Code\Generator\ClassGenerator as BaseClassGenerator;
use Zend\Code\Generator\DocBlockGenerator;
use Zend\Code\Generator\MethodGenerator as BaseMethodGenerator;
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

        $this->addAnnotations(__DIR__ . '/Annotation/Alias.php');
        $this->addAnnotations(__DIR__ . '/Annotation/Context.php');
        $this->addAnnotations(__DIR__ . '/Annotation/Custom.php');
        $this->addAnnotations(__DIR__ . '/Annotation/Error.php');
        $this->addAnnotations(__DIR__ . '/Annotation/Inject.php');
        $this->addAnnotations(__DIR__ . '/Annotation/Injection.php');
        $this->addAnnotations(__DIR__ . '/Annotation/Output.php');
        $this->addAnnotations(__DIR__ . '/Annotation/Property.php');
        $this->addAnnotations(__DIR__ . '/Annotation/ServiceObject.php');
        $this->addAnnotations(__DIR__ . '/Annotation/ServiceParent.php');
        $this->addAnnotations(__DIR__ . '/Annotation/ServiceProperty.php');
        $this->addAnnotations(__DIR__ . '/Annotation/ServiceSelf.php');
        $this->addAnnotations(__DIR__ . '/Annotation/ServiceStatic.php');
        $this->addAnnotations(__DIR__ . '/Annotation/ServiceThis.php');
        $this->addAnnotations(__DIR__ . '/Annotation/Test.php');

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
    public function addContract(string $class)
    {
        $this->contracts[] = $class;

        return $this;
    }

    /**
     * @param string $class
     * @return $this
     */
    public function addContext(string $class)
    {
        $this->contexts[] = $class;

        return $this;
    }

    public function generateAll()
    {
        try {
            $bundle = new Bundle();

            foreach ($this->contracts as $class) {
                $reflection = new \ReflectionClass($class);

                $namespace = str_replace($this->contract_prefix, $this->class_prefix, $reflection->getNamespaceName());

                $test_case_generator = new TestCaseGenerator();
                $test_case_generator->setNamespaceName('Generated\\Tests\\' . $namespace);
                $test_case_generator->setAbstract(true);
                $test_case_generator->setName($reflection->getShortName() . 'Test');
                $test_case_generator->setExtendedClass('PHPUnit\\Framework\\TestCase');

                $reflection_test = new BaseMethodGenerator();
                $reflection_test->setFinal(true);
                $reflection_test->setName('testSyntax');
                $reflection_test->setBody('new \\ReflectionClass(\\' . $namespace . '\\' . $reflection->getShortName() . '::class);');

                $test_case_generator->addMethodFromGenerator($reflection_test);

                $class_generator = new ClassGenerator();
                $class_generator->setAbstract(true);
                $class_generator->setNamespaceName($namespace);
                $class_generator->setName($reflection->getShortName());

                if ($reflection->isInterface()) {
                    $class_generator->setImplementedInterfaces(array_merge($class_generator->getImplementedInterfaces(), ['\\' . $class]));
                } else {
                    $class_generator->setExtendedClass('\\' . $class);
                }

                $class_annotations = $this->reader->getClassAnnotations($reflection);

                try {
                    foreach ($class_annotations as $annotation) {
                        if (!$annotation instanceof Annotation) {
                            continue;
                        }

                        $annotation->setReflectionClass($reflection);
                        $annotation->setClassGenerator($class_generator);
                        $annotation->setTestCaseGenerator($test_case_generator);
                        $annotation->setIsClassAnnotation(true);

                        $annotation->onCreate();
                    }

                    foreach ($class_annotations as $annotation) {
                        if (!$annotation instanceof Annotation) {
                            continue;
                        }

                        if ($annotation instanceof ClassAnnotationDecorator) {
                            foreach ($class_annotations as $another) {
                                if ($another instanceof Annotation && $annotation !== $another) {
                                    $annotation->decorateClassAnnotation($another);
                                }
                            }
                        }
                    }

                    foreach ($class_annotations as $annotation) {
                        if (!$annotation instanceof Annotation) {
                            continue;
                        }

                        $annotation->onDecorate();
                    }
                } catch (DecoratorException $e) {
                    throw new ContractsException(sprintf('%s\\%s: ' . $e->getMessage(),
                        $class_generator->getNamespaceName(),
                        $class_generator->getName()
                    ));
                }

                foreach ($reflection->getMethods() as $method) {
                    $method_generator = new MethodGenerator();
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

                    $method_annotations = $this->reader->getMethodAnnotations($method);

                    try {
                        foreach ($method_annotations as $annotation) {
                            if (!$annotation instanceof Annotation) {
                                continue;
                            }

                            $annotation->setReflectionClass($reflection);
                            $annotation->setReflectionMethod($method);
                            $annotation->setClassGenerator($class_generator);
                            $annotation->setTestCaseGenerator($test_case_generator);
                            $annotation->setMethodGenerator($method_generator);
                            $annotation->setIsMethodAnnotation(true);

                            if ($annotation instanceof Step) {
                                $annotation->setStepGenerator(new StepGenerator());
                            }

                            $annotation->onCreate();

                            if ($annotation instanceof Collection) {
                                foreach ($annotation->steps as $step) {
                                    if ($step instanceof Step) {
                                        $step->setReflectionClass($reflection);
                                        $step->setReflectionMethod($method);
                                        $step->setClassGenerator($class_generator);
                                        $step->setTestCaseGenerator($test_case_generator);
                                        $step->setMethodGenerator($method_generator);
                                        $step->setIsMethodAnnotation(true);
                                        $step->setStepGenerator(new StepGenerator());

                                        $step->onCreate();
                                    }
                                }
                            }
                        }

                        foreach ($class_annotations as $annotation) {
                            if (!$annotation instanceof Annotation) {
                                continue;
                            }

                            if ($annotation instanceof MethodAnnotationDecorator) {
                                foreach ($method_annotations as $another) {
                                    if ($another instanceof Annotation) {
                                        $annotation->decorateMethodAnnotation($another);
                                    }
                                }
                            }
                        }

                        foreach ($method_annotations as $annotation) {
                            if (!$annotation instanceof Annotation) {
                                continue;
                            }

                            if ($annotation instanceof MethodAnnotationDecorator) {
                                foreach ($method_annotations as $another) {
                                    if ($another instanceof Annotation && $annotation !== $another) {
                                        $annotation->decorateMethodAnnotation($another);
                                    }
                                }
                            }
                        }

                        foreach ($method_annotations as $annotation) {
                            if (!$annotation instanceof Annotation) {
                                continue;
                            }

                            $annotation->onDecorate();

                            if ($annotation instanceof Collection) {
                                foreach ($annotation->steps as $step) {
                                    if ($step instanceof Annotation) {
                                        $step->onDecorate();
                                    }
                                }
                            }
                        }

                        foreach ($method_annotations as $annotation) {
                            if (!$annotation instanceof Annotation) {
                                continue;
                            }

                            if ($annotation instanceof StepGeneratorDecorator) {
                                foreach ($method_annotations as $another) {
                                    if ($another instanceof Step && $annotation !== $another) {
                                        $annotation->decorateStepGenerator($another->getStepGenerator());
                                    }
                                }
                            }
                        }

                        foreach ($class_annotations as $annotation) {
                            if (!$annotation instanceof Annotation) {
                                continue;
                            }

                            if ($annotation instanceof StepGeneratorDecorator) {
                                foreach ($method_annotations as $another) {
                                    if ($another instanceof Step) {
                                        $annotation->decorateStepGenerator($another->getStepGenerator());
                                    }
                                }
                            }
                        }

                        foreach ($class_annotations as $annotation) {
                            if ($annotation instanceof MethodGeneratorDecorator) {
                                $annotation->decorateMethodGenerator($method_generator);
                            }
                        }

                        foreach ($method_annotations as $annotation) {
                            if ($annotation instanceof Step) {
                                $method_generator->addStep($annotation->getStepGenerator());
                            }

                            if ($annotation instanceof Collection) {
                                foreach ($annotation->getStepGenerators() as $step_generator) {
                                    $method_generator->addStep($step_generator);
                                }
                            }
                        }
                    } catch (DecoratorException $e) {
                        throw new ContractsException(sprintf('%s\\%s -> %s: ' . $e->getMessage(),
                            $class_generator->getNamespaceName(),
                            $class_generator->getName(),
                            $method_generator->getName()
                        ));
                    }

                    if (count($method_generator->getSteps()) > 0) {
                        $class_generator->addMethodFromGenerator($method_generator);
                    }
                }

                $bundle->addClassGenerator($class_generator);
                $bundle->addTestCaseGenerator($test_case_generator);
            }

            foreach ($bundle->getClassGenerators() as $class_generator) {
                $this->generateBaseClass($class_generator);
                $this->generateClass($class_generator);

                $this->contexts = array_merge($this->contexts, array_values($class_generator->getContexts()));
            }

            $this->generateContexts();

            foreach ($bundle->getTestCaseGenerators() as $generator) {
                $this->generateBaseClassTest($generator);
                $this->generateClassTest($generator);
            }

            shell_exec("vendor/bin/php-cs-fixer fix {$this->base_src_path} --rules=@Symfony");
            shell_exec("vendor/bin/php-cs-fixer fix {$this->base_test_path} --rules=@Symfony");
        } catch (ContractsException $e) {
            exit($e->getMessage() . PHP_EOL);
        }
    }

    private function generateContexts()
    {
        try {
            foreach ($this->contexts as $class) {
                $reflection = new \ReflectionClass($class);
                $tests = false;

                $class_generator = new BaseClassGenerator();

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

                            $data_provider = new BaseMethodGenerator();
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

                            $test = new BaseMethodGenerator();
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

                            $assertion = new BaseMethodGenerator();
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
        } catch (ContractsException $e) {
            exit($e->getMessage() . PHP_EOL);
        }
    }

    /**
     * @param ClassGenerator $class_generator
     */
    private function generateBaseClass(ClassGenerator $class_generator)
    {
        $output_name = str_replace('\\', '/', trim(str_replace($this->class_prefix, '', $class_generator->getNamespaceName()), '\\'));

        if ($output_name) {
            $output_name .= '/';
        }

        @mkdir($this->root_dir . '/' . $this->base_src_path . '/' . $output_name, 0777, true);

        $output_name = $this->root_dir . '/' . $this->base_src_path . '/' . $output_name . $class_generator->getName() . '.php';

        $namespace = $class_generator->getNamespaceName();

        $class_generator->setNamespaceName('Generated\\' . $namespace);

        $code = '<?php' . PHP_EOL . PHP_EOL . $class_generator->generate();

        file_put_contents($output_name, $code);

        $class_generator->setNamespaceName($namespace);
    }

    /**
     * @param ClassGenerator $class_generator
     */
    private function generateClass(ClassGenerator $class_generator)
    {
        $output_name = str_replace('\\', '/', trim(str_replace($this->class_prefix, '', $class_generator->getNamespaceName()), '\\'));

        if ($output_name) {
            $output_name .= '/';
        }

        @mkdir($this->root_dir . '/' . $this->src_path . '/' . $output_name, 0777, true);

        $output_name = $this->root_dir . '/' . $this->src_path . '/' . $output_name . $class_generator->getName() . '.php';

        if (is_file($output_name)) {
            return;
        }

        $class = new BaseClassGenerator();
        $class->setNamespaceName($class_generator->getNamespaceName());
        $class->setName($class_generator->getName());
        $class->setExtendedClass('\\Generated\\' . $class_generator->getNamespaceName() . '\\' . $class_generator->getName());

        foreach ($class_generator->getMethods() as $method_generator) {
            if ($method_generator->isAbstract()) {
                $method = new BaseMethodGenerator();
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
     * @param TestCaseGenerator $generator
     */
    private function generateBaseClassTest(TestCaseGenerator $generator)
    {
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
     * @param TestCaseGenerator $generator
     */
    private function generateClassTest(TestCaseGenerator $generator)
    {
        $output_name = str_replace('\\', '/', trim(str_replace('Generated\\Tests\\' . $this->class_prefix, '', $generator->getNamespaceName()), '\\'));

        if ($output_name) {
            $output_name .= '/';
        }

        @mkdir($this->root_dir . '/' . $this->test_path . '/' . $output_name, 0777, true);

        $output_name = $this->root_dir . '/' . $this->test_path . '/' . $output_name . $generator->getName() . '.php';

        if (is_file($output_name)) {
            return;
        }

        $class = new BaseClassGenerator();
        $class->setNamespaceName(str_replace('Generated\\', '', $generator->getNamespaceName()));
        $class->setName($generator->getName());
        $class->setExtendedClass($generator->getNamespaceName() . '\\' . $generator->getName());

        $code = '<?php' . PHP_EOL . PHP_EOL . $class->generate();

        file_put_contents($output_name, $code);
    }

    /**
     * @param BaseClassGenerator $class_generator
     */
    private function generateBaseContextTest(BaseClassGenerator $class_generator)
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
     * @param BaseClassGenerator $class_generator
     */
    private function generateContextTest(BaseClassGenerator $class_generator)
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

        $class = new BaseClassGenerator();
        $class->setNamespaceName(str_replace('Generated\\', '', $class_generator->getNamespaceName()));
        $class->setName($class_generator->getName());
        $class->setExtendedClass($class_generator->getNamespaceName() . '\\' . $class_generator->getName());

        foreach ($class_generator->getMethods() as $method_generator) {
            if ($method_generator->isAbstract()) {
                $method = new BaseMethodGenerator();
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

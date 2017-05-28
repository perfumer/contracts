<?php

namespace Perfumer\Contracts;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Perfumer\Contracts\Annotations\Test;
use Zend\Code\Generator\ClassGenerator;
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

            $class_builder->setNamespaceName($namespace);
            $class_builder->setName($reflection->getShortName());

            foreach ($reflection->getMethods() as $method) {
                $method_annotations = $this->reader->getMethodAnnotations($method);

                foreach ($method_annotations as $annotation) {
                    if ($annotation instanceof Test) {
                        $tests = true;

                        $method_builder = new MethodBuilder();
                        $method_builder->setName($method->name);

                        foreach ($method->getParameters() as $parameter) {
                            $method_builder->setParameter(ParameterGenerator::fromReflection($parameter));
                        }

                        $class_builder->addMethod($method_builder);
                    }
                }
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
                $class_annotations = $this->reader->getClassAnnotations($reflection);

                $namespace = str_replace($this->contract_prefix, $this->class_prefix, $reflection->getNamespaceName());

                $class_builder = new ClassBuilder();
                $class_builder->setContract($reflection);
                $class_builder->setNamespaceName($namespace);
                $class_builder->setName($reflection->getShortName());

                if ($reflection->isInterface()) {
                    $class_builder->setImplementedInterfaces(array_merge($class_builder->getImplementedInterfaces(), ['\\' . $class]));
                } else {
                    $class_builder->setExtendedClass('\\' . $class);
                }

                foreach ($class_annotations as $annotation) {
                    if (!$annotation instanceof Annotation) {
                        continue;
                    }

                    $annotation->apply($class_builder);
                }

                foreach ($reflection->getMethods() as $method) {
                    $method_builder = new MethodBuilder();
                    $method_builder->setFinal(true);
                    $method_builder->setName($method->name);
                    $method_builder->setVisibility('public');

                    $type = (string) $method->getReturnType();

                    if ($type && !$method->getReturnType()->isBuiltin()) {
                        $type = '\\' . $type;
                    }

                    $method_builder->setReturnType($type);

                    foreach ($method->getParameters() as $parameter) {
                        $method_builder->setParameter(ParameterGenerator::fromReflection($parameter));
                        $method_builder->addTestVariable($parameter->name, false);
                    }

                    $method_annotations = $this->reader->getMethodAnnotations($method);

                    // Set validate=true
                    foreach ($method_annotations as $annotation) {
                        if ($annotation instanceof Decorator) {
                            $annotation->decorate($method_annotations);
                        }
                    }

                    foreach ($method_annotations as $annotation) {
                        if (!$annotation instanceof Annotation) {
                            continue;
                        }

                        $annotation->apply($class_builder, $method_builder);

                        if ($annotation instanceof Step) {
                            $step_builders = $annotation->getBuilder($class_builder, $method_builder);

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

                    if (count($method_builder->getSteps()) > 0) {
                        $class_builder->addMethod($method_builder);
                    }
                }

                $bundle->getClassBuilders()->append($class_builder);
            }

            foreach ($bundle->getClassBuilders() as $class_builder) {
                $this->generateBaseClass($class_builder);
                $this->generateClass($class_builder);
//                $this->generateBaseClassTest($class_builder);
//                $this->generateClassTest($class_builder);
//
//                foreach ($class_builder->getContexts() as $context) {
//                    $this->generateContext($context);
//                }
            }

            //shell_exec("vendor/bin/php-cs-fixer fix {$this->base_src_path} --rules=@Symfony");
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

        file_put_contents($output_name, $class_builder->generate());
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

                $class->addMethod($method);
            }
        }

        file_put_contents($output_name, $class->generate());
    }

    /**
     * @param ClassBuilder $class_builder
     */
    private function generateBaseClassTest(ClassBuilder $class_builder)
    {
        $output_name = str_replace('\\', '/', trim(str_replace($this->class_prefix, '', $class_builder->getNamespaceName()), '\\'));

        if ($output_name) {
            $output_name .= '/';
        }

        $output_name = $this->root_dir . '/' . $this->base_test_path . '/' . $output_name . $class_builder->getName() . 'Test.php';

        $content = $this->twig->render('BaseClassTest.php.twig', [
            'builder' => $class_builder
        ]);

        file_put_contents($output_name, $content);
    }

    /**
     * @param ClassBuilder $class_builder
     */
    private function generateClassTest(ClassBuilder $class_builder)
    {
        $output_name = str_replace('\\', '/', trim(str_replace($this->class_prefix, '', $class_builder->getNamespaceName()), '\\'));

        if ($output_name) {
            $output_name .= '/';
        }

        $output_name = $this->root_dir . '/' . $this->test_path . '/' . $output_name . $class_builder->getName() . 'Test.php';

        if (is_file($output_name)) {
            return;
        }

        $content = $this->twig->render('ClassTest.php.twig', [
            'builder' => $class_builder
        ]);

        file_put_contents($output_name, $content);
    }

    /**
     * @param ClassBuilder $class_builder
     */
    private function generateBaseContextTest(ClassBuilder $class_builder)
    {
        // If context is from another package
        if (strpos($class_builder->getNamespaceName(), $this->context_prefix) !== 0) {
            return;
        }

        $output_name = str_replace('\\', '/', trim(str_replace($this->context_prefix, '', $class_builder->getNamespaceName()), '\\'));

        if ($output_name) {
            $output_name .= '/';
        }

        $output_name = $this->root_dir . '/' . $this->base_test_path . '/' . $output_name . $class_builder->getName() . 'Test.php';

        $content = $this->twig->render('BaseContextTest.php.twig', [
            'builder' => $class_builder
        ]);

        file_put_contents($output_name, $content);
    }

    /**
     * @param ClassBuilder $class_builder
     */
    private function generateContextTest(ClassBuilder $class_builder)
    {
        // If context is from another package
        if (strpos($class_builder->getNamespaceName(), $this->context_prefix) !== 0) {
            return;
        }

        $output_name = str_replace('\\', '/', trim(str_replace($this->context_prefix, '', $class_builder->getNamespaceName()), '\\'));

        if ($output_name) {
            $output_name .= '/';
        }

        $output_name = $this->root_dir . '/' . $this->test_path . '/' . $output_name . $class_builder->getName() . 'Test.php';

        if (is_file($output_name)) {
            return;
        }

        $content = $this->twig->render('ContextTest.php.twig', [
            'builder' => $class_builder
        ]);

        file_put_contents($output_name, $content);
    }
}

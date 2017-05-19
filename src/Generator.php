<?php

namespace Perfumer\Component\Contracts;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Perfumer\Component\Contracts\Annotations\Skip;
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
    private $template_directories = [];

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
        $this->addTemplateDirectory(__DIR__ . '/../tpl');
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

        $this->reader = new AnnotationReader();
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

            $class_builder->setNamespace($namespace);
            $class_builder->setClassName($reflection->getShortName());

            foreach ($reflection->getMethods() as $method) {
                $method_annotations = $this->reader->getMethodAnnotations($method);

                foreach ($method_annotations as $annotation) {
                    if ($annotation instanceof Test) {
                        $tests = true;

                        $method_builder = new MethodBuilder();
                        $method_builder->setName($method->name);

                        foreach ($method->getParameters() as $parameter) {
                            $method_builder->getArguments()->append($parameter->name);
                        }

                        $class_builder->getMethods()->append($method_builder);
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
                $class_builder->setNamespace($namespace);
                $class_builder->setClassName($reflection->getShortName());
                $class_builder->getInterfaces()->append('\\' . $class);

                foreach ($class_annotations as $annotation) {
                    if (!$annotation instanceof Annotation) {
                        continue;
                    }

                    $annotation->apply($class_builder);
                }

                foreach ($reflection->getMethods() as $method) {
                    $method_builder = new MethodBuilder();
                    $method_builder->setIsFinal(true);
                    $method_builder->setName($method->name);
                    $method_builder->setAccess('public');

                    $type = (string) $method->getReturnType();

                    if ($type && !$method->getReturnType()->isBuiltin()) {
                        $type = '\\' . $type;
                    }

                    $method_builder->setReturnType($type);

                    foreach ($method->getParameters() as $parameter) {
                        $type = (string) $parameter->getType();

                        if ($type && !$parameter->getType()->isBuiltin()) {
                            $type = '\\' . $type;
                        }

                        $method_builder->getArguments()->offsetSet($parameter->name, $type);
                        $method_builder->getTestVariables()->append([$parameter->name, false]);
                    }

                    $method_annotations = $this->reader->getMethodAnnotations($method);

                    // Set validate=true
                    foreach ($method_annotations as $annotation) {
                        if ($annotation instanceof Skip) {
                            continue(2);
                        }

                        if ($annotation instanceof Decorator) {
                            $method_annotations = $annotation->decorate($method_annotations);
                        }
                    }

                    foreach ($method_annotations as $annotation) {
                        if (!$annotation instanceof Annotation) {
                            continue;
                        }

                        $annotation->apply($class_builder, $method_builder);

                        $steps = $method_builder->getSteps();

                        if ($annotation instanceof Step) {
                            $step_builders = $annotation->getBuilder($class_builder, $method_builder);

                            if ($step_builders === null) {
                                continue;
                            }

                            if (!is_array($step_builders)) {
                                $step_builders = [$step_builders];
                            }

                            foreach ($step_builders as $step_builder) {
                                $steps->append($step_builder);
                            }
                        }
                    }

                    $class_builder->getMethods()->append($method_builder);
                }

                $bundle->getClassBuilders()->append($class_builder);
            }

            foreach ($bundle->getClassBuilders() as $class_builder) {
                $this->generateBaseClass($class_builder);
                $this->generateClass($class_builder);
                $this->generateBaseClassTest($class_builder);
                $this->generateClassTest($class_builder);

                foreach ($class_builder->getContexts() as $context) {
                    $this->generateContext($context);
                }
            }
        } catch (ContractsException $e) {
            exit($e->getMessage() . PHP_EOL);
        }
    }

    /**
     * @param ClassBuilder $class_builder
     */
    private function generateBaseClass(ClassBuilder $class_builder)
    {
        $output_name = str_replace('\\', '/', trim(str_replace($this->class_prefix, '', $class_builder->getNamespace()), '\\'));

        if ($output_name) {
            $output_name .= '/';
        }

        $output_name = $output_name . $class_builder->getClassName() . '.php';

        $builder = new Builder();
        $builder->setMustOverwriteIfExists(true);
        $builder->setTemplateName('BaseClass.php.twig');
        $builder->setTemplateDirs($this->template_directories);
        $builder->setGenerator($this->generator);
        $builder->setOutputName($output_name);
        $builder->setVariables([
            'class_builder' => $class_builder
        ]);

        $builder->writeOnDisk($this->root_dir . '/' . $this->base_src_path);
    }

    /**
     * @param ClassBuilder $class_builder
     */
    private function generateClass(ClassBuilder $class_builder)
    {
        $output_name = str_replace('\\', '/', trim(str_replace($this->class_prefix, '', $class_builder->getNamespace()), '\\'));

        if ($output_name) {
            $output_name .= '/';
        }

        $output_name = $output_name . $class_builder->getClassName() . '.php';

        $builder = new Builder();
        $builder->setMustOverwriteIfExists(false);
        $builder->setTemplateName('Class.php.twig');
        $builder->setTemplateDirs($this->template_directories);
        $builder->setGenerator($this->generator);
        $builder->setOutputName($output_name);
        $builder->setVariables([
            'class_builder' => $class_builder
        ]);

        $builder->writeOnDisk($this->root_dir . '/' . $this->src_path);
    }

    /**
     * @param ClassBuilder $class_builder
     */
    private function generateBaseClassTest(ClassBuilder $class_builder)
    {
        $output_name = str_replace('\\', '/', trim(str_replace($this->class_prefix, '', $class_builder->getNamespace()), '\\'));

        if ($output_name) {
            $output_name .= '/';
        }

        $output_name = $output_name . $class_builder->getClassName() . 'Test.php';

        $builder = new Builder();
        $builder->setMustOverwriteIfExists(true);
        $builder->setTemplateName('BaseClassTest.php.twig');
        $builder->setTemplateDirs($this->template_directories);
        $builder->setGenerator($this->generator);
        $builder->setOutputName($output_name);
        $builder->setVariables([
            'class_builder' => $class_builder
        ]);

        $builder->writeOnDisk($this->root_dir . '/' . $this->base_test_path);
    }

    /**
     * @param ClassBuilder $class_builder
     */
    private function generateClassTest(ClassBuilder $class_builder)
    {
        $output_name = str_replace('\\', '/', trim(str_replace($this->class_prefix, '', $class_builder->getNamespace()), '\\'));

        if ($output_name) {
            $output_name .= '/';
        }

        $output_name = $output_name . $class_builder->getClassName() . 'Test.php';

        $builder = new Builder();
        $builder->setMustOverwriteIfExists(false);
        $builder->setTemplateName('ClassTest.php.twig');
        $builder->setTemplateDirs($this->template_directories);
        $builder->setGenerator($this->generator);
        $builder->setOutputName($output_name);
        $builder->setVariables([
            'class_builder' => $class_builder
        ]);

        $builder->writeOnDisk($this->root_dir . '/' . $this->test_path);
    }

    /**
     * @param ClassBuilder $class_builder
     */
    private function generateBaseContextTest(ClassBuilder $class_builder)
    {
        // If context is from another package
        if (strpos($class_builder->getNamespace(), $this->context_prefix) !== 0) {
            return;
        }

        $output_name = str_replace('\\', '/', trim(str_replace($this->context_prefix, '', $class_builder->getNamespace()), '\\'));

        if ($output_name) {
            $output_name .= '/';
        }

        $output_name = $output_name . $class_builder->getClassName() . 'Test.php';

        $builder = new Builder();
        $builder->setMustOverwriteIfExists(true);
        $builder->setTemplateName('BaseContextTest.php.twig');
        $builder->setTemplateDirs($this->template_directories);
        $builder->setGenerator($this->generator);
        $builder->setOutputName($output_name);
        $builder->setVariables([
            'class_builder' => $class_builder
        ]);

        $builder->writeOnDisk($this->root_dir . '/' . $this->base_test_path);
    }

    /**
     * @param ClassBuilder $class_builder
     */
    private function generateContextTest(ClassBuilder $class_builder)
    {
        // If context is from another package
        if (strpos($class_builder->getNamespace(), $this->context_prefix) !== 0) {
            return;
        }

        $output_name = str_replace('\\', '/', trim(str_replace($this->context_prefix, '', $class_builder->getNamespace()), '\\'));

        if ($output_name) {
            $output_name .= '/';
        }

        $output_name = $output_name . $class_builder->getClassName() . 'Test.php';

        $builder = new Builder();
        $builder->setMustOverwriteIfExists(false);
        $builder->setTemplateName('ContextTest.php.twig');
        $builder->setTemplateDirs($this->template_directories);
        $builder->setGenerator($this->generator);
        $builder->setOutputName($output_name);
        $builder->setVariables([
            'class_builder' => $class_builder
        ]);

        $builder->writeOnDisk($this->root_dir . '/' . $this->test_path);
    }
}

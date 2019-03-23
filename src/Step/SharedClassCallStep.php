<?php

namespace Perfumerlabs\Perfumer\Step;

abstract class SharedClassCallStep extends ExpressionStep
{
    /**
     * @var string
     */
    protected $_class;

    /**
     * @var string
     */
    protected $_method;

    public function onCreate(): void
    {
        $name = str_replace('\\', '_', $this->_class);

        $this->_expression = '$this->get_' . $name . '()->' . $this->_method;

        parent::onCreate();
    }

    public function onBuild(): void
    {
        parent::onBuild();

        $this->getClassData()->addContext($this->_class);
    }

    public function getClass(): ?string
    {
        return $this->_class;
    }

    public function setClass(string $class): void
    {
        $this->_class = $class;
    }

    public function getMethod(): ?string
    {
        return $this->_method;
    }

    public function setMethod(string $method): void
    {
        $this->_method = $method;
    }
}

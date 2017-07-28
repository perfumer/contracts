<?php

namespace Barman\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use Barman\Annotation;
use Barman\Service;
use Zend\Code\Generator\PropertyGenerator;

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class ServiceProperty extends Service
{
    public function onDecorate(): void
    {
        if (!$this->getClassGenerator()->hasProperty($this->name)) {
            $this->getClassGenerator()->addProperty($this->name, null, PropertyGenerator::FLAG_PROTECTED);
        }

        parent::onDecorate();
    }

    /**
     * @return string
     */
    public function getCallExpression(): string
    {
        return '$this->' . $this->name . '->';
    }
}

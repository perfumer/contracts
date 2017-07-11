<?php

namespace Perfumer\Contracts\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use Perfumer\Contracts\Annotation;
use Perfumer\Contracts\Service;
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

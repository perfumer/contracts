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
    public function onMutate(): void
    {
        if (!$this->getClassKeeper()->getGenerator()->hasProperty($this->name)) {
            $this->getClassKeeper()->getGenerator()->addProperty($this->name, null, PropertyGenerator::FLAG_PROTECTED);
        }

        parent::onMutate();
    }

    /**
     * @return string
     */
    public function getCallExpression(): string
    {
        return '$this->' . $this->name . '->';
    }
}

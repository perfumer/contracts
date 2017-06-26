<?php

namespace Perfumer\Contracts\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;
use Perfumer\Contracts\Annotation;
use Perfumer\Contracts\Generator\ClassGenerator;
use Perfumer\Contracts\Service;
use Zend\Code\Generator\PropertyGenerator;

/**
 * @Annotation
 * @Target({"METHOD", "ANNOTATION"})
 */
class ServiceProperty extends Service
{
    /**
     * @param ClassGenerator $generator
     */
    public function decorateClassGenerator(ClassGenerator $generator): void
    {
        if (!$generator->hasProperty($this->name)) {
            $generator->addProperty($this->name, null, PropertyGenerator::FLAG_PROTECTED);
        }

        parent::decorateClassGenerator($generator);
    }

    /**
     * @return string
     */
    public function getCallExpression(): string
    {
        return '$this->' . $this->name . '->';
    }
}

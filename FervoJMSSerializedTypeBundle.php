<?php

namespace Fervo\JMSSerializedTypeBundle;

use Fervo\JMSSerializedTypeBundle\Doctrine\JMSSerializedArrayType;
use Fervo\JMSSerializedTypeBundle\Doctrine\JMSSerializedObjectType;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Doctrine\DBAL\Types\Type;

class FervoJMSSerializedTypeBundle extends Bundle
{
    public function __construct()
    {
        if (!Type::hasType('jmsobject')) {
            Type::addType('jmsobject', JMSSerializedObjectType::class);
        }

        if (!Type::hasType('jmsarray')) {
            Type::addType('jmsarray', JMSSerializedArrayType::class);
        }
    }

    public function boot()
    {
        $serializer = $this->container->get('jms_serializer');

        /** @var JMSSerializedObjectType $customType */
        $customType = Type::getType('jmsobject');
        $customType->setJMSSerializer($serializer);

        /** @var JMSSerializedArrayType $customType */
        $customType = Type::getType('jmsarray');
        $customType->setJMSSerializer($serializer);

    }
}

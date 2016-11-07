<?php

namespace Fervo\JMSSerializedTypeBundle\Doctrine;

use Doctrine\DBAL\Types\JsonArrayType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use JMS\Serializer\SerializerInterface;

/**
 *
 */
class JMSSerializedArrayType extends JsonArrayType
{
    /** @var SerializerInterface */
    protected $serializer;

    public function setJMSSerializer(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return null;
        }

        return array_map(function($element) {
            $data = $this->serializer->serialize($element, 'json');

            return json_encode([
                'class' => get_class($element),
                'data' => json_decode($data, true),
            ]);
        }, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = (is_resource($value)) ? stream_get_contents($value) : $value;

        $data = json_decode($value, true);

        return array_map(function($element) {
            return $this->serializer->deserialize(json_encode($element['data']), $element['class'], 'json');
        }, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'jmsarray';
    }

    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }
}

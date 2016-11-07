<?php

namespace Fervo\JMSSerializedTypeBundle\Doctrine;

use Doctrine\DBAL\Types\JsonArrayType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use JMS\Serializer\SerializerInterface;

/**
* 
*/
class JMSSerializedObjectType extends JsonArrayType
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

        $data = $this->serializer->serialize($value, 'json');

        return json_encode([
            'class' => get_class($value),
            'data' => json_decode($data, true),
        ]);
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

        return $this->serializer->deserialize(json_encode($data['data']), $data['class'], 'json');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'jmsobject';
    }

    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }
}

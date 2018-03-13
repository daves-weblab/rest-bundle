<?php

namespace DavesWeblab\RestBundle\Serializer\Context\Embedded;

use DavesWeblab\RestBundle\Serializer\Json\JsonEntity;

class EmbeddedRestContext extends AbstractEmbeddedContext implements \JsonSerializable
{
    protected $json;

    /**
     * {@inheritdoc}
     */
    public function add($data, array $config = null, bool $isEmbedded = false)
    {
        if (!$isEmbedded) {
            return $this->getRootContext()->add($data, $config, false);
        }

        $entity = new JsonEntity($data);
        $entity->setConfig($config);

        if ($this->json === null) {
            $this->json = $entity;
        } else {
            if ($this->json instanceof JsonEntity) {
                $this->json = [$this->json];
            }

            $this->json[] = $entity;
        }

        $this->push($entity);

        return $entity;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->json;
    }
}
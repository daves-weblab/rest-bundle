<?php

namespace DavesWeblab\RestBundle\Serializer\Context\Embedded;

use DavesWeblab\RestBundle\Serializer\Json\JsonEntity;

class EmbeddedRestContext extends AbstractEmbeddedContext implements \JsonSerializable
{
    protected $json;

    /**
     * {@inheritdoc}
     */
    public function add($data, array $config = null)
    {
        // ignore cyclic dependencies
        // maybe this should be changed if necessary
//        if ($this->hasObject($data)) {
//            return;
//        }
//
//        $this->trackObject($data);

        $entity = new JsonEntity($data);

        if ($this->json === null) {
            $this->json = $entity;
        } else {
            if ($this->json instanceof JsonEntity) {
                $this->json = [$this->json];
            }

            $this->json[] = $entity;
        }

        $entity->setConfig($config);
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
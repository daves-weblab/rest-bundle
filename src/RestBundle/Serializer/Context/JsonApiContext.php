<?php

namespace DavesWeblab\RestBundle\Serializer\Context;

use DavesWeblab\RestBundle\Serializer\Json\JsonApiEntity;
use ICanBoogie\Inflector;

class JsonApiContext extends RestContext
{
    /**
     * {@inheritdoc}
     */
    protected function getNamespace($data)
    {
        $namespace = parent::getNamespace($data);

        return Inflector::get()->pluralize($namespace);
    }

    /**
     * {@inheritdoc}
     */
    public function add($data, array $config = null)
    {
        if ($this->hasObject($data)) {
            return;
        }

        $this->trackObject($data);

        $namespace = $this->getNamespace($data);

        $root = empty($this->json);

        if (!$root && !$this->areRelationsIncluded()) {
            return;
        }

        $entity = new JsonApiEntity($data);

        $attribute = $root ? "data" : "included";

        if ($root || $namespace == $this->rootNamespace) {
            $this->rootNamespace = $namespace;
            $entity->setRoot(true);
        }

        if ($data instanceof \Traversable) {
            if (!array_key_exists($attribute, $this->json)) {
                $this->json[$attribute] = [];
            }
        } else if ($root) {
            $entity->set("type", $namespace);
            $this->json[$attribute] = $entity;
        } else {
            if (!array_key_exists($attribute, $this->json)) {
                $this->json[$attribute] = [];
            }

            $entity->set("type", $namespace);
            $this->json[$attribute][] = $entity;
        }

        $entity->setConfig($config);
        $this->push($entity);

        return $entity;
    }
}
<?php

namespace DavesWeblab\RestBundle\Serializer\Context;

use DavesWeblab\RestBundle\Serializer\Context\Embedded\EmbeddedJsonApiContext;
use DavesWeblab\RestBundle\Serializer\Json\JsonApiEntity;
use ICanBoogie\Inflector;

class JsonApiContext extends RestContext
{
    /**
     * {@inheritdoc}
     */
    public function getNamespace($data)
    {
        $namespace = parent::getNamespace($data);

        return Inflector::get()->pluralize($namespace);
    }

    /**
     * {@inheritdoc}
     */
    public function add($data, array $config = null, bool $isEmbedded = false)
    {
        if ($this->hasObject($data)) {
            return;
        }

        $this->trackObject($data);

        $namespace = $this->getNamespace($data);

        $root = empty($this->json) || $namespace === $this->rootNamespace;

        if (!$root && !$this->areRelationsIncluded()) {
            return;
        }

        $entity = new JsonApiEntity($data);

        $attribute = $root ? "data" : "included";

        if ($root) {
            $this->rootNamespace = $namespace;
            $entity->setRoot(true);
        }

        if ($data instanceof \Traversable) {
            if (!array_key_exists($attribute, $this->json)) {
                $this->json[$attribute] = [];
            }
        } else if ($root) {
            $entity->set("type", $namespace);

            if (!array_key_exists($attribute, $this->json)) {
                $this->json[$attribute] = $entity;
            } else {
                if(!is_array($this->json[$attribute])) {
                    if(empty($this->json[$attribute])) {
                        $this->json[$attribute] = [];
                    } else {
                        $this->json[$attribute] = [$this->json[$attribute]];
                    }
                }

                $this->json[$attribute][] = $entity;
            }
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

    /**
     * {@inheritdoc}
     */
    public function buildEmbeddedContext($data)
    {
        return new EmbeddedJsonApiContext();
    }
}
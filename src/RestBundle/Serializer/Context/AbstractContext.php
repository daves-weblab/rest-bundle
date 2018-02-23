<?php

namespace DavesWeblab\RestBundle\Serializer\Context;

use DavesWeblab\RestBundle\Config\Config;
use DavesWeblab\RestBundle\Data\DataType;
use DavesWeblab\RestBundle\Normalizer\Transformer\Transformer;
use DavesWeblab\RestBundle\Serializer\EntityInterface;
use Pimcore\Model\DataObject\ClassDefinition\Data;

abstract class AbstractContext implements ContextInterface
{
    /**
     * @var Config $config
     */
    private $config;

    /**
     * @var string $view
     */
    private $view;

    /**
     * @var Transformer[] $transformers
     */
    private $transformers = [];

    /**
     * @var bool $includeRelations
     */
    private $includeRelations = true;

    /**
     * @var array $objectStore
     */
    private $objectStore = [];

    /**
     * @var EntityInterface[] $stack
     */
    private $stack = [];

    /**
     * @var DataType $dataType
     */
    private $dataType;

    /**
     * @param DataType $dataType
     */
    public function setDataType(DataType $dataType)
    {
        $this->dataType = $dataType;
    }

    /**
     * {@inheritdoc}
     */
    public function isRelation(Data $fieldDefinition = null)
    {
        if(!$fieldDefinition) {
            return false;
        }

        return $this->dataType->isRelationType($fieldDefinition);
    }

    /**
     * {@inheritdoc}
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return string
     */
    public function getView(): string
    {
        return $this->view;
    }

    /**
     * @param string $view
     */
    public function setView(string $view)
    {
        $this->view = $view;
    }

    /**
     * {@inheritdoc}
     */
    public function setTransformers(array $transformers)
    {
        $this->transformers = $transformers;
    }

    /**
     * {@inheritdoc}
     */
    public function getTransformers(): array
    {
        return $this->transformers;
    }

    /**
     * {@inheritdoc}
     */
    public function transform(Data $fieldDefinition = null, $data, array $config = null)
    {
        foreach ($this->getTransformers() as $transformer) {
            if ($transformer->supports($fieldDefinition ?: $data)) {
                try {
                    $transformed = $transformer->transform($data, $this, $config);
                    return $transformed;
                } catch (\Exception $e) {
                    continue;
                }
            }
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function stopsNormalization(Data $fieldDefinition = null, $data)
    {
        foreach ($this->getTransformers() as $transformer) {
            if ($transformer->supports($fieldDefinition ?: $data)) {
                return $transformer->stopsNormalization();
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function areRelationsIncluded(): bool
    {
        return $this->includeRelations;
    }

    /**
     * @param $object
     *
     * @return bool
     */
    protected function hasObject($object)
    {
        $hash = spl_object_hash($object);

        return array_key_exists($hash, $this->objectStore);
    }

    /**
     * @param $object
     */
    protected function trackObject($object)
    {
        $hash = spl_object_hash($object);

        $this->objectStore[$hash] = $object;
    }

    /**
     * @param EntityInterface $entity
     */
    protected function push(EntityInterface $entity)
    {
        $this->stack[] = $entity;
    }

    /**
     * {@inheritdoc}
     */
    public function pop()
    {
        return array_pop($this->stack);
    }
}
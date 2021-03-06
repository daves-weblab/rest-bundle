<?php

namespace DavesWeblab\RestBundle\Serializer\Context;

use DavesWeblab\RestBundle\Config\Config;
use DavesWeblab\RestBundle\Data\DataType;
use DavesWeblab\RestBundle\Normalizer\Transformer\Transformer;
use DavesWeblab\RestBundle\Property\Computed;
use DavesWeblab\RestBundle\Property\Computed\Listing;
use DavesWeblab\RestBundle\Serializer\Context\Embedded\EmbeddedContextInterface;
use DavesWeblab\RestBundle\Serializer\EntityInterface;
use Pimcore\Model\DataObject\ClassDefinition\Data;

interface ContextInterface
{
    /**
     * @param Computed[] $computeds
     */
    public function setComputeds(array $computeds = []);

    /**
     * @param Computed[] $computeds
     */
    public function addComputeds(array $computeds = []);

    /**
     * @param mixed $data
     *
     * @return Listing
     */
    public function getComputeds($data);

    /**
     * @param $value
     * @param Data $fieldDefinition
     * @param mixed $data
     * @param array $config
     *
     * @return mixed
     */
    public function buildNormalizedValueFromFieldDefinition($value, Data $fieldDefinition = null, $data = null, array $config = []);

    /**
     * @param Computed $computed
     *
     * @return mixed
     */
    public function buildNormalizedValueFromComputed(Computed $computed);

    /**
     * @param DataType $dataType
     */
    public function setDataType(DataType $dataType);

    /**
     * @param Data $fieldDefinition
     *
     * @return bool
     */
    public function isRelation(Data $fieldDefinition = null);

    /**
     * @param Config $config
     *
     * @return mixed
     */
    public function setConfig(Config $config);

    /**
     * @return Config
     */
    public function getConfig();

    /**
     * @param string $view
     */
    public function setView(string $view);

    /**
     * @return string
     */
    public function getView(): string;

    /**
     * @param string $format
     *
     * @return bool
     */
    public static function supports(string $format): bool;

    /**
     * @param Transformer[] $transformers
     */
    public function setTransformers(array $transformers);

    /**
     * @return Transformer[]
     */
    public function getTransformers(): array;

    /**
     * @param mixed $data
     *
     * @return mixed
     */
    public function transform(Data $fieldDefinition = null, $data, array $config = []);

    /**
     * @param Data|null $fieldDefinition
     * @param mixed $data
     *
     * @return bool
     */
    public function stopsNormalization(Data $fieldDefinition = null, $data = null, array $config = []);

    /**
     * @return bool
     */
    public function areRelationsIncluded(): bool;

    /**
     * @param mixed $data
     */
    public function add($data, array $config = null, bool $isEmbedded = false);

    /**
     * @return EntityInterface|null
     */
    public function pop();

    /**
     * @return bool
     */
    public function isEmpty();

    /**
     * @param $data
     *
     * @return bool
     */
    public function hasObject($object);

    /**
     * @param $data
     */
    public function trackObject($object);

    /**
     * @param EntityInterface $entity
     */
    public function push(EntityInterface $entity);

    /**
     * @return mixed
     */
    public function encode();

    /**
     * @param $id
     * @param $data
     *
     * @return mixed
     */
    public function buildRelationData($id, $data);

    /**
     * @return array
     */
    public function getMandatoryAttributes();

    /**
     * @param $data
     *
     * @return EmbeddedContextInterface
     */
    public function buildEmbeddedContext($data);
}
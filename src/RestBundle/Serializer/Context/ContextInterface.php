<?php

namespace DavesWeblab\RestBundle\Serializer\Context;

use DavesWeblab\RestBundle\Config\Config;
use DavesWeblab\RestBundle\Data\DataType;
use DavesWeblab\RestBundle\Normalizer\Transformer\Transformer;
use DavesWeblab\RestBundle\Serializer\EntityInterface;
use Pimcore\Model\DataObject\ClassDefinition\Data;

interface ContextInterface
{
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
    public function transform(Data $fieldDefinition = null, $data, array $config = null);

    /**
     * @param Data|null $fieldDefinition
     * @param mixed $data
     *
     * @return bool
     */
    public function stopsNormalization(Data $fieldDefinition = null, $data);

    /**
     * @return bool
     */
    public function areRelationsIncluded(): bool;

    /**
     * @param mixed $data
     */
    public function add($data, array $config = null);

    /**
     * @return EntityInterface|null
     */
    public function pop();

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
}
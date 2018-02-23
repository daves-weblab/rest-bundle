<?php

namespace DavesWeblab\RestBundle\Data;

use DavesWeblab\RestBundle\Config\Config;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Fieldcollection;
use Pimcore\Model\DataObject\Objectbrick;

class DataType
{
    /**
     * @var string[] $relationTypes
     */
    protected $relationTypes;

    public function __construct(Config $config)
    {
        $dataType = $config->getDataTypeConfig();

        $this->relationTypes = array_merge([
            "image",
            "hotspotimage",
            "video",
            "user",
            "fieldcollections",
            "objectbricks"
        ], $dataType->get("relationTypes", []));
    }

    /**
     * Check if a field definition is a relation.
     *
     * @param Data $fieldDefinition
     *
     * @return bool
     */
    public function isRelationType(Data $fieldDefinition)
    {
        if ($fieldDefinition->isRelationType()) {
            return true;
        }

        return in_array($fieldDefinition->getFieldtype(), $this->relationTypes);
    }

    /**
     * @param Data $fieldDefinition
     * @param string $type
     *
     * @return bool
     */
    public function isRelationTypeAllowed(Data $fieldDefinition, string $type)
    {
        $getter = "get" . ucfirst($type) . "Allowed";

        if (method_exists($fieldDefinition, $getter)) {
            return $fieldDefinition->$getter();
        }

        return false;
    }

    /**
     * @param Data $fieldDefinition
     * @param string $type
     * @param string $subType
     *
     * @return bool
     */
    public function isRelationSubTypeAllowed(Data $fieldDefinition, string $type, string $subType)
    {
        $getter = "get" . ucfirst($type) . "Types";

        if (method_exists($fieldDefinition, $getter)) {
            $subTypes = $fieldDefinition->$getter();

            if (is_array($subTypes)) {
                return in_array($subType, $subTypes);
            }
        }

        return false;
    }

    /**
     * Check if a value is a recursive data type.
     *
     * @param $value
     *
     * @return bool
     */
    public function isRecursive($value)
    {
        // todo block?

        switch (true) {
            case $value instanceof Concrete:
            case $value instanceof Fieldcollection:
            case $value instanceof Objectbrick:
                return true;

            default:
                return false;
        }
    }

    public function isIterable($value)
    {
        return $value instanceof \Traversable || is_array($value) || is_iterable($value);
    }
}
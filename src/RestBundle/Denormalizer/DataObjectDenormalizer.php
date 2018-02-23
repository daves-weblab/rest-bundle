<?php

namespace DavesWeblab\RestBundle\Denormalizer;

use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Psr\Log\InvalidArgumentException;

class DataObjectDenormalizer extends AbstractDenormalizer
{
    public function supports(Data $fieldDefinition)
    {
        return $this->dataType->isRelationType($fieldDefinition) && $this->dataType->isRelationTypeAllowed($fieldDefinition, "objects");
    }

    public function denormalize($value)
    {
        if (is_numeric($value) && $object = AbstractObject::getById($value)) {
            return $object;
        }

        if ($value instanceof AbstractObject) {
            return $value;
        }

        if (is_array($value)) {
            $objects = [];
            foreach ($value as $val) {
                if ($object = AbstractObject::getById($val)) {
                    $objects[] = $object;
                }
            }

            return $objects;
        }

        throw new InvalidArgumentException("Not a valid data object.");
    }
}
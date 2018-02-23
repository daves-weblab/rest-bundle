<?php

namespace DavesWeblab\RestBundle\Denormalizer;

use Pimcore\Model\Asset\Image;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Psr\Log\InvalidArgumentException;

class ImageDenormalizer extends AbstractDenormalizer
{
    public function supports(Data $fieldDefinition)
    {
        $type = $fieldDefinition->getFieldtype();

        if (in_array($type, ["image", "hotspotimage"])) {
            return true;
        }

        if (
            $this->dataType->isRelationType($fieldDefinition )&&
            $this->dataType->isRelationTypeAllowed($fieldDefinition, "assets") &&
            $this->dataType->isRelationSubTypeAllowed($fieldDefinition, "asset", "image")
        ) {
            return true;
        }

        return false;
    }

    public function denormalize($value)
    {
        if ($value instanceof Image) {
            return $value;
        }

        if (is_numeric($value)) {
            return Image::getById($value);
        }

        if (is_array($value)) {
            $assets = [];

            foreach ($value as $val) {
                if ($asset = Image::getById($val)) {
                    $assets[] = $asset;
                }
            }

            return $assets;
        }

        throw new InvalidArgumentException("Not a valid asset.");
    }
}
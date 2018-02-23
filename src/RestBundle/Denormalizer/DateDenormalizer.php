<?php

namespace DavesWeblab\RestBundle\Denormalizer;

use Carbon\Carbon;
use Pimcore\Model\DataObject\ClassDefinition\Data;

class DateDenormalizer extends AbstractDenormalizer
{
    public function supports(Data $fieldDefinition)
    {
        return in_array($fieldDefinition->getFieldtype(), ["date", "datetime"]);
    }

    public function denormalize($value)
    {
        if ($value instanceof Carbon) {
            return $value;
        }

        return new Carbon($value);
    }
}
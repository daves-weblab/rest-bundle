<?php

namespace DavesWeblab\RestBundle\Denormalizer;

use Pimcore\Model\DataObject\ClassDefinition\Data;

interface DenormalizerInterface
{
    public function supports(Data $fieldDefinition);

    public function denormalize($value);
}
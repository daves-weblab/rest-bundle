<?php

namespace DavesWeblab\RestBundle\Denormalizer;

use DavesWeblab\RestBundle\Data\DataType;

abstract class AbstractDenormalizer implements DenormalizerInterface
{
    /**
     * @var DataType $dataType
     */
    protected $dataType;

    public function __construct(DataType $dataType)
    {
        $this->dataType = $dataType;
    }
}
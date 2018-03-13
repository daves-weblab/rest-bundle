<?php

namespace DavesWeblab\RestBundle\Normalizer\Transformer;

use DavesWeblab\RestBundle\Config\Config;
use DavesWeblab\RestBundle\Data\DataType;

abstract class AbstractTransformer implements Transformer
{
    /**
     * @var Config $config
     */
    protected $config;

    /**
     * @var DataType $dataType
     */
    protected $dataType;

    public function __construct(Config $config, DataType $dataType)
    {
        $this->config = $config;
        $this->dataType = $dataType;
    }

    /**
     * @return Config
     */
    protected function getConfig()
    {
        return $this->config;
    }

    /**
     * @return DataType
     */
    protected function getDataType()
    {
        return $this->dataType;
    }
}
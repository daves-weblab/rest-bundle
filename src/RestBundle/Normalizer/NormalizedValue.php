<?php

namespace DavesWeblab\RestBundle\Normalizer;

class NormalizedValue
{
    private $value;
    private $transformed;
    private $config;
    private $stopNormalization;
    private $isRelationType;
    private $embed;

    public function __construct($value, $transformed, $config = null, $stopNormalization = false, $isRelation = false, $isEmbed = false)
    {
        $this->value = $value;
        $this->transformed = $transformed;
        $this->config = $config;
        $this->stopNormalization = $stopNormalization;
        $this->isRelationType = $isRelation;
        $this->embed = (bool) $isEmbed;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return mixed
     */
    public function getTransformed()
    {
        return $this->transformed;
    }

    /**
     * @return mixed
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return bool
     */
    public function stopsNormalization()
    {
        return $this->stopNormalization;
    }

    /**
     * @return bool
     */
    public function isRelation(): bool
    {
        return $this->isRelationType;
    }

    public function isEmbedded(): bool
    {
        return $this->embed;
    }
}
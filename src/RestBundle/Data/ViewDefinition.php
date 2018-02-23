<?php

namespace DavesWeblab\RestBundle\Data;

class ViewDefinition
{
    private $supportedAttributes = [];
    private $mappedAttributes = [];
    private $attributeConfigs = [];

    public static function emptyViewDefinition() {
        return new ViewDefinition([]);
    }

    public function __construct($config)
    {
        foreach ($config as $key => $value) {
            list($attribute, $attributeConfig) = is_numeric($key) ? [$value, $value] : [$key, $value];

            // attribute is mapped to another name
            // attributeName: newName
            // newName will be used for de-/ normalization
            if (is_string($attributeConfig)) {
                $this->addMappedAttribute($attribute, $attributeConfig);
                continue;
            }

            // configuration for attribute
            if (is_array($attributeConfig)) {
                // attribute can be mapped inside the config as well
                // to a different name
                if ($mappedAttribute = @$attributeConfig["name"]) {
                    $this->addMappedAttribute($attribute, $mappedAttribute);
                } else {
                    $this->supportedAttributes[] = $attribute;
                }

                $this->attributeConfigs[$attribute] = $attributeConfig;
                continue;
            }

            $this->supportedAttributes[] = $attribute;
        }
    }

    protected function addMappedAttribute($attribute, $mappedAttribute)
    {
        $this->supportedAttributes[] = $mappedAttribute;
        $this->mappedAttributes[$mappedAttribute] = $attribute;
    }

    /**
     * @return string[]
     */
    public function getSupportedAttributes()
    {
        return $this->supportedAttributes;
    }

    /**
     * @param $attribute
     *
     * @return array|null
     */
    public function getAttributeConfig($attribute)
    {
        if (array_key_exists($attribute, $this->attributeConfigs)) {
            return $this->attributeConfigs[$attribute];
        }

        return null;
    }

    /**
     * @param string $attribute
     *
     * @return bool
     */
    public function isMappedAttribute(string $attribute)
    {
        return array_key_exists($attribute, $this->mappedAttributes);
    }

    /**
     * @param string $attribute
     *
     * @return string
     */
    public function getMappedAttribute(string $attribute)
    {
        return $this->mappedAttributes[$attribute];
    }
}
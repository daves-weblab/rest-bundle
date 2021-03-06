<?php

namespace DavesWeblab\RestBundle\Normalizer;

use DavesWeblab\RestBundle\Normalizer\Transformer\ObjectBrickAsIdTransformer;
use DavesWeblab\RestBundle\Serializer\Context\ContextInterface;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\Objectbrick\Data\AbstractData;
use Pimcore\Model\DataObject\Objectbrick\Definition;

class ObjectbrickNormalizer implements NormalizerInterface
{
    /**
     * @param mixed $data
     *
     * @return bool
     */
    public function supports($data)
    {
        return $data instanceof AbstractData;
    }

    /**
     * @param AbstractData $data
     * @param ContextInterface $context
     *
     * @return string[]
     */
    public function getSupportedAttributes($data, ContextInterface $context)
    {
        $viewDefinition = $context->getConfig()->getViewDefinitionForObjectbrick($data->getType());

        if (!$viewDefinition->isEmpty()) {
            return $viewDefinition->getSupportedAttributes();
        }

        $attributes = [];

        /**
         * @var Definition $definition
         */
        $definition = $data->getDefinition();

        /**
         * @var Data $fieldDefinition
         */
        foreach ($definition->getFieldDefinitions() as $fieldDefinition) {
            $attributes[] = $fieldDefinition->getName();
        }

        return $attributes;
    }

    /**
     * @param array $attributes
     * @return array|string[]
     */
    public function removeUnsupportedAttributes(array $attributes)
    {
        return $attributes;
    }

    /**
     * @param AbstractData $data
     * @param string $attribute
     * @param ContextInterface $context
     *
     * @param array|null $config
     * @return NormalizedValue
     */
    public function getAttribute($data, string $attribute, ContextInterface $context, array $config = null)
    {
        if ($attribute == "id") {
            $id = ObjectBrickAsIdTransformer::transformObjectbrickId($data);

            return new NormalizedValue($id, $id);
        }

        $viewConfig = $context->getConfig()->getViewDefinitionForFieldCollection($data->getType());

        if ($viewConfig->isMappedAttribute($attribute)) {
            $attribute = $viewConfig->getMappedAttribute($attribute);
        }

        /**
         * @var Definition $definition
         */
        $definition = $data->getDefinition();
        $fieldDefinition = $definition->getFieldDefinition($attribute) ?: null;

        $getter = "get" . ucfirst($attribute);

        if (!method_exists($data, $getter)) {
            return null;
        }

        $value = $data->$getter();

        return $context->buildNormalizedValueFromFieldDefinition(
            $value,
            $fieldDefinition,
            $data,
            $viewConfig->getAttributeConfig($attribute)
        );
    }
}
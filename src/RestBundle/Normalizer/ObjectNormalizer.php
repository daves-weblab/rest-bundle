<?php

namespace DavesWeblab\RestBundle\Normalizer;

use DavesWeblab\RestBundle\Serializer\Context\ContextInterface;
use Pimcore\Model\DataObject\Concrete;

class ObjectNormalizer implements NormalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports($data)
    {
        return $data instanceof Concrete;
    }

    /**
     * @param Concrete $data
     * @param ContextInterface $context
     *
     * @return string[]
     */
    public function getSupportedAttributes($data, ContextInterface $context)
    {
        $viewDefinition = $context->getConfig()->getViewDefinitionForObject($data->getClassName());
        $attributes = [];

        if ($viewDefinition) {
            return $viewDefinition->getSupportedAttributes();
        }

        foreach ($data->getClass()->getFieldDefinitions() as $fieldDefinition) {
            $attributes[] = $fieldDefinition->getName();
        }

        return $attributes;
    }

    /**
     * @param Concrete $data
     * @param string $attribute
     * @param ContextInterface $context
     *
     * @param array|null $config
     * @return NormalizedValue
     */
    public function getAttribute($data, string $attribute, ContextInterface $context, array $config = null)
    {
        $viewConfig = $context->getConfig()->getViewDefinitionForObject($data->getClassName());

        if ($viewConfig->isMappedAttribute($attribute)) {
            $attribute = $viewConfig->getMappedAttribute($attribute);
        }

        $fieldDefinition = $data->getClass()->getFieldDefinition($attribute) ?: null;
        $getter = "get" . ucfirst($attribute);

        if (!method_exists($data, $getter)) {
            return null;
        }

        $value = $data->$getter();

        return $context->buildNormalizedValueFromFieldDefinition(
            $value,
            $fieldDefinition,
            $data,
            $viewConfig->getMappedAttribute($attribute)
        );
    }
}
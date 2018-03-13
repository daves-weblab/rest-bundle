<?php

namespace DavesWeblab\RestBundle\Normalizer;

use DavesWeblab\RestBundle\Serializer\Context\ContextInterface;
use Pimcore\Model\DataObject\Objectbrick;
use Pimcore\Model\DataObject\Objectbrick\Data\AbstractData;

class ObjectbrickFieldNormalizer implements NormalizerInterface
{
    /**
     * @param mixed $data
     *
     * @return bool
     */
    public function supports($data)
    {
        return $data instanceof Objectbrick;
    }

    /**
     * @param Objectbrick $data
     * @param ContextInterface $context
     *
     * @return string[]
     */
    public function getSupportedAttributes($data, ContextInterface $context)
    {
        return $data->getAllowedBrickTypes();
    }

    /**
     * @param array $attributes
     * @return array|string[]
     */
    public function removeUnsupportedAttributes(array $attributes)
    {
        $unsupported = ["id"];
        return array_filter($attributes, function ($attribute) use ($unsupported) {
            return !in_array($attribute, $unsupported);
        });
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
        $getter = "get" . ucfirst($attribute);

        if (!method_exists($data, $getter)) {
            return null;
        }

        $value = $data->$getter();

        return $context->buildNormalizedValueFromFieldDefinition(
            $value,
            null,
            $data
        );
    }
}
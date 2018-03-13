<?php

namespace DavesWeblab\RestBundle\Normalizer;

use DavesWeblab\RestBundle\Serializer\Context\ContextInterface;
use Pimcore\Model\Asset;
use ICanBoogie\Inflector;

class AssetNormalizer implements NormalizerInterface
{
    const ATTRIBUTE_THUMBNAILS = "thumbnails";

    /**
     * @param mixed $data
     *
     * @return bool
     */
    public function supports($data)
    {
        return $data instanceof Asset;
    }

    /**
     * @param Asset $data
     * @param ContextInterface $context
     *
     * @return string[]
     */
    public function getSupportedAttributes($data, ContextInterface $context)
    {
        $viewConfig = $context->getConfig()->getViewDefinitionForAssets();

        $defaultAttributes = [self::ATTRIBUTE_THUMBNAILS];

        return array_merge($defaultAttributes, $viewConfig->getSupportedAttributes());
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
     * @param Asset $data
     * @param array $config
     *
     * @return string[]
     */
    protected function getThumbnails(Asset $data, array $config = null)
    {
        $thumbnails = [];

        if (!$data instanceof Asset\Image) {
            return $thumbnails;
        }

        if ($config && ($thumbnailNames = @$config[self::ATTRIBUTE_THUMBNAILS]) && !empty($thumbnailNames)) {
            foreach ($thumbnailNames as $thumbnailName) {
                $thumbnails[Inflector::get()->camelize($thumbnailName, Inflector::DOWNCASE_FIRST_LETTER)] = $data->getThumbnail($thumbnailName)->getPath();
            }
        }

        return $thumbnails;
    }

    /**
     * @param Asset $data
     * @param string $attribute
     * @param ContextInterface $context
     *
     * @param array|null $config
     * @return NormalizedValue
     */
    public function getAttribute($data, string $attribute, ContextInterface $context, array $config = null)
    {
        if ($attribute === self::ATTRIBUTE_THUMBNAILS) {
            $thumbnails = $this->getThumbnails($data, $config);

            return new NormalizedValue(null, $thumbnails);
        }

        $viewConfig = $context->getConfig()->getViewDefinitionForAssets();

        if ($viewConfig->isMappedAttribute($attribute)) {
            $attribute = $viewConfig->getMappedAttribute($attribute);
        }

        $getter = "get" . ucfirst($attribute);

        if (!method_exists($data, $getter)) {
            return null;
        }

        $value = $data->$getter();

        return new NormalizedValue(
            $value,
            $context->transform(null, $value, $viewConfig->getAttributeConfig($attribute)),
            $viewConfig->getAttributeConfig($attribute)
        );
    }
}
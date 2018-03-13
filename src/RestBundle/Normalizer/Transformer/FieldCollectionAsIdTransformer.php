<?php

namespace DavesWeblab\RestBundle\Normalizer\Transformer;

use DavesWeblab\RestBundle\Serializer\Context\ContextInterface;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\Fieldcollection;

class FieldCollectionAsIdTransformer extends AbstractTransformer
{
    /**
     * {@inheritdoc}
     */
    public function supports($data, array $config = [], bool $supportOnly = false)
    {
        if($this->isEmbed($data, $config) && !$supportOnly) {
            return false;
        }

        return $data instanceof Data\Fieldcollections;
    }

    /**
     * {@inheritdoc}
     */
    public function isEmbed($data, array $config = [])
    {
        return @$config["embed"] || $this->getConfig()->embedFieldcollections();
    }


    /**
     * {@inheritdoc}
     */
    public function stopsNormalization()
    {
        return false;
    }

    /**
     * @param Data\Fieldcollections $data
     * @param ContextInterface $context
     * @param array $config
     *
     * @return mixed
     */
    public function transform($data, ContextInterface $context, array $config = null)
    {
        $ids = [];

        /**
         * @var Fieldcollection\Data\AbstractData $item
         */
        foreach ($data as $item) {
            $ids[] = $context->buildRelationData(self::transformFieldcollectionId($item), $item);
        }

        return $ids;
    }

    /**
     * @param Fieldcollection\Data\AbstractData $item
     *
     * @return string
     */
    public static function transformFieldcollectionId(Fieldcollection\Data\AbstractData $item)
    {
        return "{$item->getObject()->getId()}-{$item->getIndex()}";
    }
}
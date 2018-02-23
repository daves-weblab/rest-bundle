<?php

namespace DavesWeblab\RestBundle\Normalizer\Transformer;

use DavesWeblab\RestBundle\Data\DataType;
use DavesWeblab\RestBundle\Serializer\Context\ContextInterface;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\Fieldcollection;

class FieldCollectionAsIdTransformer implements Transformer
{
    /**
     * @var DataType $dataType
     */
    private $dataType;

    public function __construct()
    {
        $this->dataType = $dataType = \Pimcore::getContainer()->get("dwl.rest.data");
    }

    /**
     * @param Data|mixed $data
     *
     * @return bool
     */
    public function supports($data)
    {
        return $data instanceof Data\Fieldcollections;
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
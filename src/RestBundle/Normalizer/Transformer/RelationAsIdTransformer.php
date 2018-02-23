<?php

namespace DavesWeblab\RestBundle\Normalizer\Transformer;

use DavesWeblab\RestBundle\Data\DataType;
use DavesWeblab\RestBundle\Serializer\Context\ContextInterface;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\Element\ElementInterface;

class RelationAsIdTransformer implements Transformer
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
        if ($data instanceof Data) {
            return $this->dataType->isRelationType($data);
        }

        return $data instanceof ElementInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function stopsNormalization()
    {
        return false;
    }

    /**
     * @param ElementInterface $data
     * @param ContextInterface $context
     * @param array $config
     *
     * @return mixed
     */
    public function transform($data, ContextInterface $context, array $config = null)
    {
        if ($this->dataType->isIterable($data)) {
            $ids = [];

            /**
             * @var ElementInterface $item
             */
            foreach ($data as $item) {
                $ids[] = $context->buildRelationData($item->getId(), $item);
            }

            return $ids;
        }

        if($data instanceof ElementInterface) {
            return $context->buildRelationData($data->getId(), $data);
        }

        // some relations just return the id (User)
        return $data;
    }
}
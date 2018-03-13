<?php

namespace DavesWeblab\RestBundle\Normalizer\Transformer;

use DavesWeblab\RestBundle\Serializer\Context\ContextInterface;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\Element\ElementInterface;

class RelationAsIdTransformer extends AbstractTransformer
{
    /**
     * {@inheritdoc}
     */
    public function supports($data, array $config = [], bool $supportOnly = false)
    {
        if ($this->isEmbed($data, $config) && !$supportOnly) {
            return false;
        }

        if ($data instanceof Data) {
            return $this->getDataType()->isRelationType($data);
        }

        return $data instanceof ElementInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function isEmbed($data, array $config = [])
    {
        return @$config["embed"] || $this->getConfig()->embedRelations();
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
        if ($this->getDataType()->isIterable($data)) {
            $ids = [];

            /**
             * @var ElementInterface $item
             */
            foreach ($data as $item) {
                $ids[] = $context->buildRelationData($item->getId(), $item);
            }

            return $ids;
        }

        if ($data instanceof ElementInterface) {
            return $context->buildRelationData($data->getId(), $data);
        }

        // some relations just return the id (User)
        return $data;
    }
}
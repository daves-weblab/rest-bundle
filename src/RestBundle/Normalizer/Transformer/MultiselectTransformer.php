<?php

namespace DavesWeblab\RestBundle\Normalizer\Transformer;

use DavesWeblab\RestBundle\Serializer\Context\ContextInterface;
use Pimcore\Model\DataObject\ClassDefinition\Data;

class MultiselectTransformer extends AbstractTransformer
{
    /**
     * {@inheritdoc}
     */
    public function supports($data, array $config = [], bool $supportOnly = false)
    {
        if ($data instanceof Data) {
            return in_array($data->getFieldtype(), ["multiselect"]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isEmbed($data, array $config = [])
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function stopsNormalization()
    {
        return true;
    }

    /**
     * @param mixed $data
     * @param ContextInterface $context
     * @param array $config
     *
     * @return mixed
     */
    public function transform($data, ContextInterface $context, array $config = null)
    {
        if (!$data) {
            return [];
        }

        return $data;
    }
}
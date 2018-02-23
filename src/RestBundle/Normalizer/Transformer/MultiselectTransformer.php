<?php

namespace DavesWeblab\RestBundle\Normalizer\Transformer;

use DavesWeblab\RestBundle\Serializer\Context\ContextInterface;
use Pimcore\Model\DataObject\ClassDefinition\Data;

class MultiselectTransformer implements Transformer
{
    /**
     * {@inheritdoc}
     */
    public function supports($data)
    {
        if ($data instanceof Data) {
            return in_array($data->getFieldtype(), ["multiselect"]);
        }
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
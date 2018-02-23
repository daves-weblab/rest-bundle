<?php

namespace DavesWeblab\RestBundle\Normalizer\Transformer;

use Carbon\Carbon;
use DavesWeblab\RestBundle\Serializer\Context\ContextInterface;
use Pimcore\Model\DataObject\ClassDefinition\Data;

class DateTransformer implements Transformer
{
    /**
     * {@inheritdoc}
     */
    public function supports($data)
    {
        if ($data instanceof Data) {
            return in_array($data->getFieldtype(), ["date", "datetime"]);
        }

        return $data instanceof Carbon;
    }

    /**
     * {@inheritdoc}
     */
    public function stopsNormalization()
    {
        return false;
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
        if ($data instanceof Carbon) {
            return $data->toDateTimeString();
        }

        throw new \InvalidArgumentException("No valid date given to DateTransformer.");
    }
}
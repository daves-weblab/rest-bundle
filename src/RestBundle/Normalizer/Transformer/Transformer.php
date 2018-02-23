<?php

namespace DavesWeblab\RestBundle\Normalizer\Transformer;

use DavesWeblab\RestBundle\Serializer\Context\ContextInterface;
use Pimcore\Model\DataObject\ClassDefinition\Data;

interface Transformer
{
    /**
     * @param Data|mixed $data
     *
     * @return bool
     */
    public function supports($data);

    /**
     * @return bool
     */
    public function stopsNormalization();

    /**
     * @param mixed $data
     * @param ContextInterface $context
     * @param array $config
     *
     * @return mixed
     */
    public function transform($data, ContextInterface $context, array $config = null);
}
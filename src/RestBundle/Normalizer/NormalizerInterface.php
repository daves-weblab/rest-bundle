<?php

namespace DavesWeblab\RestBundle\Normalizer;

use DavesWeblab\RestBundle\Serializer\Context\ContextInterface;

interface NormalizerInterface
{
    /**
     * @param mixed $data
     *
     * @return bool
     */
    public function supports($data);

    /**
     * @param mixed $data
     * @param ContextInterface $context
     *
     * @return string[]
     */
    public function getSupportedAttributes($data, ContextInterface $context);

    /**
     * @param mixed $data
     * @param string $attribute
     * @param ContextInterface $context
     *
     * @param array|null $config
     * @return NormalizedValue
     */
    public function getAttribute($data, string $attribute, ContextInterface $context, array $config = null);
}
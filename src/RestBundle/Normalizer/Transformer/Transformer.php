<?php

namespace DavesWeblab\RestBundle\Normalizer\Transformer;

use DavesWeblab\RestBundle\Serializer\Context\ContextInterface;
use Pimcore\Model\DataObject\ClassDefinition\Data;

interface Transformer
{
    /**
     * @param Data|mixed $data
     * @param array $config
     * @param bool $supportOnly
     *
     * @return bool
     */
    public function supports($data, array $config = [], bool $supportOnly = false);

    /**
     * @param $data
     * @param array $config
     *
     * @return mixed
     */
    public function isEmbed($data, array $config = []);

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
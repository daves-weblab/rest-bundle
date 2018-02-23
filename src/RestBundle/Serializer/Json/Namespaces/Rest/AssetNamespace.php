<?php

namespace DavesWeblab\RestBundle\Serializer\Json\Namespaces\Rest;

use DavesWeblab\RestBundle\Serializer\Json\Namespaces\JsonNamespace;
use Pimcore\Model\Asset;

class AssetNamespace implements JsonNamespace
{
    /**
     * {@inheritdoc}
     */
    public function supports($data)
    {
        return $data instanceof Asset;
    }

    /**
     * @param Asset $data
     *
     * @return string
     */
    public function getNamespace($data)
    {
        return "asset";
    }
}
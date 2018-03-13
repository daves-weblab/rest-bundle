<?php

namespace DavesWeblab\RestBundle\Serializer\Json\Namespaces\Rest;

use DavesWeblab\RestBundle\Serializer\Json\Namespaces\JsonNamespace;
use ICanBoogie\Inflector;
use Pimcore\Model\DataObject\Fieldcollection\Data\AbstractData;
use Pimcore\Model\DataObject\Objectbrick;

class ObjectbrickNamespace implements JsonNamespace
{
    /**
     * {@inheritdoc}
     */
    public function supports($data)
    {
        return $data instanceof Objectbrick\Data\AbstractData;
    }

    /**
     * @param AbstractData $data
     *
     * @return string
     */
    public function getNamespace($data)
    {
        $inflector = Inflector::get();

        return $inflector->dasherize($inflector->underscore($data->getType()));
    }
}
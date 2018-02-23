<?php

namespace DavesWeblab\RestBundle\Serializer\Json\Namespaces\Rest;

use DavesWeblab\RestBundle\Serializer\Json\Namespaces\JsonNamespace;
use ICanBoogie\Inflector;
use Pimcore\Model\DataObject\Listing\Concrete;
use Pimcore\Model\Listing\AbstractListing;

class ObjectListingNamespace implements JsonNamespace
{
    /**
     * @param $data
     *
     * @return mixed
     */
    public function supports($data)
    {
        return $data instanceof AbstractListing;
    }

    /**
     * @param Concrete $data
     *
     * @return string
     */
    public function getNamespace($data)
    {
        $inflector = Inflector::get();

        return $inflector->dasherize($inflector->underscore($data->getClassName()));
    }
}
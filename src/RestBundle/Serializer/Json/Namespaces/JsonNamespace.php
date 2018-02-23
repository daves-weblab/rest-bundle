<?php

namespace DavesWeblab\RestBundle\Serializer\Json\Namespaces;

interface JsonNamespace
{
    /**
     * @param $data
     *
     * @return mixed
     */
    public function supports($data);

    /**
     * @param mixed $data
     *
     * @return string
     */
    public function getNamespace($data);
}
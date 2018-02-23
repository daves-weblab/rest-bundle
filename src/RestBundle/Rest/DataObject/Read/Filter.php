<?php

namespace DavesWeblab\RestBundle\Rest\DataObject\Read;

use Pimcore\Model\DataObject\Listing\Concrete;
use Symfony\Component\HttpFoundation\Request;

interface Filter
{
    /**
     * @param Request $request
     * @param Concrete $listing
     */
    public function apply(Request $request, Concrete $listing);
}
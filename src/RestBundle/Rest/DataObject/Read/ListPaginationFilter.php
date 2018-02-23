<?php

namespace DavesWeblab\RestBundle\Rest\DataObject\Read;

use Pimcore\Model\DataObject\Listing\Concrete;
use Symfony\Component\HttpFoundation\Request;

class ListPaginationFilter implements Filter
{
    /**
     * {@inheritdoc}
     */
    public function apply(Request $request, Concrete $listing)
    {
        if(!$request->get("page")) {
            return;
        }

        $page = intval($request->get("page"));


    }
}
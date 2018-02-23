<?php

namespace DavesWeblab\RestBundle\Rest\DataObject\Read;

use Pimcore\Model\DataObject\Listing\Concrete;
use Symfony\Component\HttpFoundation\Request;

class ListIdsFilter implements Filter
{
    /**
     * {@inheritdoc}
     */
    public function apply(Request $request, Concrete $listing)
    {
        if (!$request->get("ids")) {
            return;
        }

        $ids = [];

        foreach ($request->get("ids") as $id) {
            $ids[] = intval($id);
        }

        $listing->addConditionParam("o_id in (" . implode(",", $ids) . ")");
    }
}
<?php

namespace DavesWeblab\RestBundle\Controller;

use DavesWeblab\RestBundle\Rest\DataObject\Read\Filter;
use DavesWeblab\RestBundle\Rest\DataObject\Read\ListIdsFilter;
use DavesWeblab\RestBundle\Rest\DataObject\Read\ListPaginationFilter;
use DavesWeblab\RestBundle\Serializer\Serializer;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\Listing\AbstractListing;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

trait RestDataObjectRead
{
    /**
     * @return string
     */
    abstract protected function getClassName();

    protected function afterGet($object)
    {
    }

    protected function getClass()
    {
        return "\\Pimcore\\Model\\DataObject\\{$this->getClassName()}";
    }

    /**
     * @return ClassDefinition
     */
    protected function getClassDefinition()
    {
        return ClassDefinition::getByName($this->getClassName());
    }

    /**
     * @return Filter[]
     */
    protected function getFilters()
    {
        return [];
    }

    /**
     * @return Filter[]
     */
    protected function getAllFilters()
    {
        return array_unique(array_merge([
            new ListIdsFilter(),
            new ListPaginationFilter()
        ], $this->getListFilters()));
    }

    /**
     * @return Filter[]
     */
    protected function getListFilters()
    {
        return [];
    }

    /**
     * @Route("")
     * @Method("GET")
     */
    public function listAction(Serializer $serializer, Request $request)
    {
        $filters = $this->getListFilters();

        $listClass = "\\Pimcore\\Model\\DataObject\\{$this->getClassName()}";

        if (!class_exists($listClass)) {
            throw new \InvalidArgumentException("Invalid class name given. {$listClass}");
        }

        try {
            /**
             * @var AbstractListing $list
             */
            $list = $listClass::getList();
        } catch (\Exception $e) {
            throw new \InvalidArgumentException("Invalid object class given. {$listClass}::getList");
        }

        foreach ($filters as $filter) {
            $filter->apply($request, $list);
        }

        return $serializer->json($list, $request->get("view", "default"));
    }

    /**
     * @Route("/{id}")
     * @Method("GET")
     */
    public function getAction($id, Serializer $serializer, Request $request)
    {
        $className = $this->getClass();

        try {
            $object = $className::getById($id);

            $this->afterGet($object);
        } catch (\Throwable $e) {
            $object = null;
        }

        if ($object) {
            return $serializer->json($object, $request->get("view", "default"));
        }

        return new Response("No such object", Response::HTTP_NOT_FOUND);
    }

    /**
     * @Route(
     *     "/{id}",
     *     defaults = {"id" = 0}
     * )
     * @Method("OPTIONS")
     */
    public function optionsAction()
    {
        return new Response("OK");
    }
}
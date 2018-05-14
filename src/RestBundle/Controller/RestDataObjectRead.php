<?php

namespace DavesWeblab\RestBundle\Controller;

use DavesWeblab\RestBundle\Rest\DataObject\Read\Filter;
use DavesWeblab\RestBundle\Rest\DataObject\Read\ListIdsFilter;
use DavesWeblab\RestBundle\Rest\DataObject\Read\ListPaginationFilter;
use DavesWeblab\RestBundle\Serializer\Serializer;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\Listing\AbstractListing;
use DavesWeblab\RestBundle\Exception\InvalidNamespaceException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\AbstractObject;

trait RestDataObjectRead
{
    /**
     * To avoid any InvalidNamespaceException's, create a new model class (e.g. MyModel) and extend from
     * the AbstractObject class. Afterwards, return MyModel::class.
     *
     * @return string the class (including its namespace) that is to be used as model.
     */
    abstract protected function getClassName();

    protected function afterGet($object)
    {
    }

    /**
     * Alias for this#getClassName().
     * @return string
     */
    protected function getClass()
    {
        return $this->getClassName();
    }

    /**
     * @return ClassDefinition|null null is returned if an error occurred.
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
        $this->assertClass();

        $listClass = $this->getClassName();
        $list = $listClass::getList();

        $filters = $this->getListFilters();

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
        $this->assertClass();

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

    /**
     * @return string the valid namespace. This will be used to check against the given classes namespace.
     */
    private function getValidClass()
    {
        return AbstractObject::class;
    }

    /**
     * Asserts the class that was set by the user.
     */
    private function assertClass()
    {
        $className = $this->getClass();

        $this->assertClassExistence($className);
        $this->assertNamespace($className);
    }

    /**
     * Asserts the existence of the class name.
     *
     * @param string $className the class name that was set by the user
     * @throws InvalidNamespaceException if the class does not exist.
     */
    private function assertClassExistence($className)
    {
        if (!class_exists($className)) {
            throw new InvalidNamespaceException($this->getValidClass());
        }
    }

    /**
     * Asserts the namepsace of the class name. Call this#assertClassExistence() beforehand, to ensure the class exists.
     *
     * @param string $className the class name that was set by the user
     * @throws InvalidNamespaceException if the class is not within a valid namespace.
     */
    private function assertNamespace($className)
    {
        if(!is_a($className, $this->getValidClass(), true)){
            throw new InvalidNamespaceException($this->getValidClass());
        }
    }
}
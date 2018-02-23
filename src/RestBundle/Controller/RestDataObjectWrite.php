<?php

namespace DavesWeblab\RestBundle\Controller;

use DavesWeblab\RestBundle\Serializer\Serializer;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Service;
use Pimcore\Model\Factory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

trait RestDataObjectWrite
{
    /**
     * @return string
     */
    abstract function getParent($object);

    /**
     * @return string
     */
    abstract function getKey($object);

    protected function beforeUpdate(AbstractObject $object)
    {
    }

    protected function beforeSave($object)
    {
    }

    protected function afterCreate($object)
    {
    }

    protected function afterUpdate($object)
    {
    }

    protected function afterSave($object)
    {
    }

    /**
     * @param $object
     *
     * @return bool|mixed
     */
    protected function beforeDelete($object)
    {
    }

    protected function afterDelete($object)
    {
    }

    protected function getParentFolder($object)
    {
        $parent = $this->getParent($object);

        if (is_string($parent)) {
            return Service::createFolderByPath($parent);
        }

        return $parent;
    }

    protected function update(Serializer $serializer, Request $request, $object, $create = false)
    {
        $this->beforeUpdate($object);

        /**
         * @var ClassDefinition $class
         */
        $class = $this->getClassDefinition();
        $params = $request->get(lcfirst($class->getName()));

        $serializer->deserialize($params, $object);

        if ($create) {
            $key = $this->getKey($object);
            $object->setParent($this->getParentFolder($object));
            $object->setKey($key);

            $this->afterCreate($object);
        }

        $this->afterUpdate($object);
        $this->beforeSave($object);

        $object->save();

        $this->afterSave($object);
    }

    /**
     * @Route("")
     * @Method("POST")
     */
    public function createAction(Request $request, Serializer $serializer, Factory $factory)
    {
        $className = $this->getClass();

        $object = $factory->build($className);
        $object->setPublished(true);

        try {
            $this->update($serializer, $request, $object, true);

            return $serializer->json($object);
        } catch (\Throwable $e) {
            return new Response($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/{id}")
     * @Method("PUT")
     */
    public function updateAction($id, Serializer $serializer, Request $request)
    {
        $object = AbstractObject::getById($id);

        if (!$object) {
            return new Response("No such data object", Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->update($serializer, $request, $object);

            return $serializer->json($object);
        } catch (\Throwable $e) {
            return new Response($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/{id}")
     * @Method("DELETE")
     */
    public function deleteAction($id)
    {
        $object = AbstractObject::getById($id);

        if ($object) {
            $delete = $this->beforeDelete($object);

            if ($delete !== false) {
                $object->delete();
                $this->afterDelete($object);

                return new Response("", Response::HTTP_NO_CONTENT);
            }
        }

        return new Response("Did not delete object.", Response::HTTP_BAD_REQUEST);
    }
}
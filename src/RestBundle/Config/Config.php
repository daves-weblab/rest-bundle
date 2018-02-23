<?php

namespace DavesWeblab\RestBundle\Config;

use DavesWeblab\RestBundle\Data\ViewDefinition;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Config
{
    /**
     * @var \Pimcore\Config\Config $config
     */
    private $config;

    /**
     * @var array
     */
    private $viewDefinitionCache = [];

    public function __construct(ContainerInterface $container)
    {
        $this->config = new \Pimcore\Config\Config($container->getParameter("dwl_rest_config") ?: [], false);
    }

    /**
     * @param $key
     * @param Config $config
     *
     * @return mixed|\Pimcore\Config\Config
     */
    protected function getConfigIfAvailable($key, Config $config = null)
    {
        $config = $config ?: $this->config;

        return $config->get($key, new \Pimcore\Config\Config([], false));
    }

    /**
     * @return \Pimcore\Config\Config
     */
    public function getNormalizationConfig()
    {
        return $this->getConfigIfAvailable("normalization");
    }

    public function getDataTypeConfig()
    {
        return $this->getConfigIfAvailable("dataType");
    }

    /**
     * @param string $className
     *
     * @return null|ViewDefinition
     */
    public function getViewDefinition(string $root, string $className, string $view = "default")
    {
        if (!array_key_exists($root, $this->viewDefinitionCache)) {
            $this->viewDefinitionCache[$root] = [];
        }

        if (array_key_exists($className, $this->viewDefinitionCache[$root])) {
            return $this->viewDefinitionCache[$root][$className];
        }

        $objects = $this->getConfigIfAvailable($root);

        // try upper and lower case version
        $config = $objects->get($className, $objects->get(lcfirst($className)));

        if (!$config) {
            return ViewDefinition::emptyViewDefinition();
        }

        /**
         * @var \Pimcore\Config\Config $viewConfig
         */
        $viewConfig = $config->get($view);

        if (!$viewConfig) {
            return ViewDefinition::emptyViewDefinition();
        }

        $viewDefinition = new ViewDefinition($viewConfig->toArray());

        $this->viewDefinitionCache[$root][$className] = $viewDefinition;
        return $viewDefinition;
    }

    /**
     * @param string $name
     * @param string $view
     *
     * @return ViewDefinition|null
     */
    public function getViewDefinitionForObject(string $name, string $view = "default")
    {
        return $this->getViewDefinition("objects", $name, $view);
    }

    /**
     * @param string $name
     * @param string $view
     *
     * @return ViewDefinition|null
     */
    public function getViewDefinitionForFieldCollection(string $name, string $view = "default")
    {
        return $this->getViewDefinition("fieldCollections", $name, $view);
    }

    /**
     * @return ViewDefinition
     */
    public function getViewDefinitionForAssets()
    {
        if (array_key_exists("assets", $this->viewDefinitionCache)) {
            return $this->viewDefinitionCache["assets"];
        }

        $assets = $this->getConfigIfAvailable("assets");

        if (!$assets) {
            return null;
        }

        /**
         * @var \Pimcore\Config\Config $config
         */
        $config = $assets->get("fields");

        if (!$config) {
            return null;
        }

        $viewDefinition = new ViewDefinition($config->toArray());

        $this->viewDefinitionCache["assets"] = $viewDefinition;
        return $viewDefinition;
    }
}
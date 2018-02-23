<?php

namespace DavesWeblab\RestBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('daves_weblab_rest');

        $this->addDataType($rootNode);
        $this->addNormalization($rootNode);

        $this->addObjects($rootNode);
        $this->addFieldCollections($rootNode);
        // todo $this->addObjectBricks($rootNode);
        $this->addAssets($rootNode);
        // todo $this->addDocuments($rootNode);

        return $treeBuilder;
    }

    protected function addDataType(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode("dataTypes")
                    ->children()
                        ->arrayNode("relationTypes")
                            ->scalarPrototype()->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    protected function addNormalization(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode("normalization")
                    ->children()
                        // list of normalizers
                        ->arrayNode("normalizer")
                            ->scalarPrototype()->end()
                        ->end()

                        // list of normalization transformers
                        ->arrayNode("transformer")
                            ->scalarPrototype()->end()
                        ->end()

                        // list of context classes
                        ->arrayNode("context")
                            ->scalarPrototype()->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    protected function addObjects(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->variableNode("objects")->end()
            ->end();
    }

    protected function addFieldCollections(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->variableNode("fieldCollections")->end()
            ->end();
    }

    protected function addAssets(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode("assets")
                    ->children()
                        ->variableNode("fields")->end()
                    ->end()
                ->end()
            ->end();
    }
}

<?php

/*
 * This file is part of the Tadcka package.
 *
 * (c) Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tadcka\Bundle\SitemapBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tadcka\Component\Tree\DependencyInjection\AddNodeTypeConfigPass;
use Tadcka\Component\Tree\DependencyInjection\AddTreeConfigPass;
use Tadcka\Component\Tree\DependencyInjection\RegisterNodeTypeConfigPass;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 5/30/14 12:08 AM
 */
class TadckaSitemapBundle extends Bundle
{
    const SITEMAP_TREE = 'tadcka_sitemap';

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(
            new AddTreeConfigPass('tadcka_sitemap.tree.registry', 'tadcka_sitemap.tree.config')
        );
        $container->addCompilerPass(
            new AddNodeTypeConfigPass('tadcka_sitemap.node_type.registry', 'tadcka_sitemap.node_type.config')
        );
        $container->addCompilerPass(
            new RegisterNodeTypeConfigPass('tadcka_sitemap.node_type.registry', 'tadcka_sitemap.node_type')
        );

        $this->addRegisterMappingsPass($container);
        $this->enabledTreeExtension($container);
    }

    /**
     * Add register mappings pass.
     *
     * @param ContainerBuilder $container
     */
    private function addRegisterMappingsPass(ContainerBuilder $container)
    {
        $mappings = array(
            realpath(__DIR__ . '/Resources/config/doctrine/model') => 'Tadcka\Bundle\SitemapBundle\Model',
        );

        $ormCompilerClass = 'Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass';
        if (class_exists($ormCompilerClass)) {
            $container->addCompilerPass(DoctrineOrmMappingsPass::createXmlMappingDriver($mappings));
        }
    }

    /**
     * Enabled tree extension.
     *
     * @param ContainerBuilder $container
     */
    private function enabledTreeExtension(ContainerBuilder $container)
    {
        $container->prependExtensionConfig(
            'stof_doctrine_extensions',
            array(
                'default_locale' => '%locale%',
                'orm' => array('default' => array('tree' => true)),
            )
        );
    }
}

<?php

namespace Armetiz\FacebookBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class ArmetizFacebookExtension extends Extension {

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container) {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        if (false === $config["enabled"])
            return;

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        $session = new Reference("session");

        foreach ($config["sdk"] as $name => $sdk) {
            if (false === $sdk["enabled"]) {
                continue;
            }

            $facebookConfig = array(
                "appId" => $sdk["app_id"],
                "secret" => $sdk["secret"]
            );

            $isDefault = $sdk["default"];

            $facebookDef = new Definition("Armetiz\FacebookBundle\FacebookSessionPersistence", array($session));
            $facebookDef->addArgument($facebookConfig);

            $container->setDefinition("armetiz.facebook." . $name, $facebookDef);

            if ($isDefault) {
                $container->setAlias("armetiz.facebook", "armetiz.facebook." . $name);
            }
        }
    }

}

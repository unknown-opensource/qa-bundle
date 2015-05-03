<?php
namespace Unknown\Bundle\QABundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class UnknownQAExtension extends Extension
{
    /**
     * @inheritdoc
     */
    public function load(array $config, ContainerBuilder $container)
    {
        if ($container->getParameter("kernel.environment") == "test") {
            $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config/'));
            $loader->load("services_test.yml");
        }
    }
}

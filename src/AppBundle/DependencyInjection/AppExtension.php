<?php

namespace AppBundle\DependencyInjection;

use OldSound\RabbitMqBundle\DependencyInjection\Compiler\RegisterPartsPass;
use OldSound\RabbitMqBundle\DependencyInjection\OldSoundRabbitMqExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\FileLocator;

class AppExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $container->registerExtension(new OldSoundRabbitMqExtension());
        $container->addCompilerPass(new RegisterPartsPass());
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

    }

    public function getAlias()
    {
        return 'app';
    }
}

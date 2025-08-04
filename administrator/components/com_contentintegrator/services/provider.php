<?php

namespace Joomla\Component\ContentIntegrator\Administrator\Service;

\defined('_JEXEC') or die;

use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\Extension\Service\Provider\RouterFactory;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

class Provider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        $namespace = 'Joomla\\Component\\ContentIntegrator';

        $container->registerServiceProvider(new ComponentDispatcherFactory($namespace));
        $container->registerServiceProvider(new MVCFactory($namespace));
        $container->registerServiceProvider(new RouterFactory($namespace));
    }
}


<?php
namespace Joomla\Component\Contentintegrator\Administrator\Service;

\defined('_JEXEC') or die;

use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\Extension\Service\Provider\RouterFactory;
use Joomla\CMS\Extension\Service\Provider\CacheControllerFactory;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

class Provider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        $ns = 'Joomla\\Component\\Contentintegrator';

        $container->registerServiceProvider(new ComponentDispatcherFactory($ns));
        $container->registerServiceProvider(new MVCFactory($ns));
        $container->registerServiceProvider(new RouterFactory($ns));
        $container->registerServiceProvider(new CacheControllerFactory);
    }
}

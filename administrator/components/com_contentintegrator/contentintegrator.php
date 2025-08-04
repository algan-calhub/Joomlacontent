<?php
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Cache\CacheControllerFactoryInterface;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\Extension\Service\Provider\CacheControllerFactory;

$container = Factory::getContainer();
$ns        = 'Joomla\\Component\\Contentintegrator';

if (!$container->has(ComponentDispatcherFactoryInterface::class)) {
    $container->registerServiceProvider(new ComponentDispatcherFactory($ns));
}

if (!$container->has(MVCFactoryInterface::class)) {
    $container->registerServiceProvider(new MVCFactory($ns));
}

if (!$container->has(CacheControllerFactoryInterface::class)) {
    $container->registerServiceProvider(new CacheControllerFactory);
}

echo $container
    ->get(ComponentDispatcherFactoryInterface::class)
    ->createDispatcher(Factory::getApplication())
    ->dispatch();

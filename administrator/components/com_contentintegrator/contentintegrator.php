<?php
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;

$container = Factory::getContainer();
$ns        = 'Joomla\\Component\\ContentIntegrator';

if (!$container->has(ComponentDispatcherFactoryInterface::class)) {
    $container->registerServiceProvider(new ComponentDispatcherFactory($ns));
}

if (!$container->has(MVCFactoryInterface::class)) {
    $container->registerServiceProvider(new MVCFactory($ns));
}


echo $container
    ->get(ComponentDispatcherFactoryInterface::class)
    ->createDispatcher(Factory::getApplication())
    ->dispatch();

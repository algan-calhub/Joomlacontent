<?php
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;

$container = Factory::getContainer();

// Falls der Service noch nicht bekannt ist: jetzt registrieren
if (!$container->has(ComponentDispatcherFactoryInterface::class)) {
    $namespace = 'Joomla\\Component\\Contentintegrator';
    $container->registerServiceProvider(new ComponentDispatcherFactory($namespace));
}

echo $container
    ->get(ComponentDispatcherFactoryInterface::class)
    ->createDispatcher('com_contentintegrator')
    ->dispatch();

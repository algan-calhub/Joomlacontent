<?php

defined('_JEXEC') or die;

use Joomla\CMS\Extension\Service\Provider\MVCComponent;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

return new class implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container->registerServiceProvider(new MVCComponent('ContentImporter'));
    }
};

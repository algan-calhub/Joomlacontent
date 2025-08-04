<?php
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;

echo Factory::getContainer()
    ->get(ComponentDispatcherFactoryInterface::class)
    ->createDispatcher('com_contentintegrator')
    ->dispatch();


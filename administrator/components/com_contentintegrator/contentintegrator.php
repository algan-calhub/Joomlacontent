<?php
// Fallback-Dispatcher für Joomla 5 Backend-Komponenten
use Joomla\CMS\Factory;
use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;

defined('_JEXEC') or die;

/** @var ComponentDispatcherFactoryInterface $dispatcherFactory */
$dispatcherFactory = Factory::getContainer()->get(ComponentDispatcherFactoryInterface::class);
$app               = Factory::getApplication();
echo $dispatcherFactory->createDispatcher($app)->dispatch();

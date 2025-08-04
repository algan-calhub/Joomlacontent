<?php

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;

$controller = BaseController::getInstance('ContentIntegrator');
$controller->execute(Factory::getApplication()->input->getCmd('task'));
$controller->redirect();

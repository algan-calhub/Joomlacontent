<?php

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;

$controller = BaseController::getInstance('ContentImporter');
$controller->execute(Factory::getApplication()->input->getCmd('task'));
$controller->redirect();

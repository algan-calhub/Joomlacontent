<?php
namespace Joomla\Component\Contentintegrator\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;

class DisplayController extends BaseController
{
    protected $default_view = 'dashboard';

    public function display($cachable = false, $urlparams = [])
    {
        return parent::display($cachable, $urlparams);
    }

    protected function canView($view = null)
    {
        return Factory::getApplication()->getIdentity()->authorise('core.manage', 'com_contentintegrator');
    }
}

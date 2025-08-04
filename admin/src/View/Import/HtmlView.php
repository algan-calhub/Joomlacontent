<?php
namespace Joomla\Component\ContentIntegrator\Administrator\View\Import;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

class HtmlView extends BaseHtmlView
{
    protected array $legend;

    public function display($tpl = null): void
    {
        $this->legend = $this->getModel()->getLegend();
        parent::display($tpl);
    }
}

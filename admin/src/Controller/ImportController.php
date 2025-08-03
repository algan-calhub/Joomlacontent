<?php
namespace Joomla\Component\ContentImporter\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;

class ImportController extends BaseController
{
    protected $default_view = 'import';

    public function upload(): void
    {
        $app   = Factory::getApplication();
        $file  = $app->input->files->get('file');
        $model = $this->getModel('Import');
        $messages = $model->import($file);
        foreach ($messages as $message) {
            $app->enqueueMessage($message);
        }
        $this->setRedirect('index.php?option=com_contentimporter');
    }

    public function sample(): void
    {
        $app   = Factory::getApplication();
        $type  = $app->input->getCmd('format', 'json');
        $ids   = $app->input->get('ids', [], 'array');
        $model = $this->getModel('Import');
        $content = $model->generateSample($type, $ids);
        $app->clearHeaders();
        $app->setHeader('Content-Type', 'text/plain', true);
        $app->setHeader('Content-Disposition', 'attachment; filename="sample.' . $type . '"', true);
        echo $content;
        $app->close();
    }

    public function createMenu(): void
    {
        $app  = Factory::getApplication();
        $data = $app->input->getArray([
            'menutype' => 'string',
            'title'    => 'string',
            'language' => 'string',
        ]);
        $model = $this->getModel('Import');
        echo (int) $model->createMenu($data);
        $app->close();
    }

    public function createMenuItem(): void
    {
        $app  = Factory::getApplication();
        $data = $app->input->getArray([
            'parent_id' => 'int',
            'menutype'  => 'string',
            'title'     => 'string',
            'language'  => 'string',
        ]);
        $model = $this->getModel('Import');
        echo (int) $model->createMenuItem($data);
        $app->close();
    }
}

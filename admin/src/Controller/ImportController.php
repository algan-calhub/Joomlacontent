<?php
namespace Joomla\Component\ContentImporter\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Http\HttpFactory;
use Joomla\Component\Actionlogs\Administrator\Helper\ActionlogHelper;

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

    public function direct(): void
    {
        $app   = Factory::getApplication();
        $input = $app->input->getRaw('direct_input', '');
        $model = $this->getModel('Import');
        $messages = $model->importString($input);
        foreach ($messages as $message) {
            $app->enqueueMessage($message);
        }
        if (class_exists(ActionlogHelper::class)) {
            ActionlogHelper::addLog(
                Text::_('COM_CONTENTIMPORTER_DIRECT_IMPORT_LOG'),
                $app->getIdentity()->id,
                'com_contentimporter',
                $app->getIdentity()->username
            );
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

    public function chatgpt(): void
    {
        $app    = Factory::getApplication();
        $prompt = $app->input->getString('prompt', '');
        $params = ComponentHelper::getParams('com_contentimporter');
        $key    = $params->get('openai_api_key');
        $app->setHeader('Content-Type', 'text/plain', true);
        if (!$key || !$prompt) {
            echo '';
            $app->close();
        }
        $http = HttpFactory::getHttp();
        $body = json_encode([
            'model'    => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
        ]);
        $headers = [
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . $key,
        ];
        $response = $http->post('https://api.openai.com/v1/chat/completions', $body, $headers);
        $data     = json_decode($response->body, true);
        echo $data['choices'][0]['message']['content'] ?? '';
        $app->close();
    }
}

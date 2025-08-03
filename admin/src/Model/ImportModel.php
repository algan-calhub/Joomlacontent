<?php
namespace Joomla\Component\ContentImporter\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Table\Table;

class ImportModel extends BaseDatabaseModel
{
    public function getLegend(): array
    {
        $db = $this->getDatabase();

        $db->setQuery('SELECT id, alias, language FROM #__categories WHERE extension = "com_content"');
        $categories = $db->loadAssocList();

        $db->setQuery('SELECT id, menutype, title FROM #__menu_types');
        $menus = $db->loadAssocList();

        $db->setQuery('SELECT id, parent_id, title, menutype FROM #__menu WHERE client_id = 1');
        $menuitems = $db->loadAssocList();

        $languages = [];
        foreach (LanguageHelper::getContentLanguages() as $lang) {
            $code = $lang->lang_code;
            $dir  = JPATH_ADMINISTRATOR . '/language/' . $code;
            if (!is_dir($dir)) {
                Folder::create($dir);
            }
            $ini = $dir . '/' . $code . '.com_contentimporter.ini';
            $sys = $dir . '/' . $code . '.com_contentimporter.sys.ini';
            if (!file_exists($ini)) {
                File::copy(JPATH_ADMINISTRATOR . '/components/com_contentimporter/language/en-GB/en-GB.com_contentimporter.ini', $ini);
            }
            if (!file_exists($sys)) {
                File::copy(JPATH_ADMINISTRATOR . '/components/com_contentimporter/language/en-GB/en-GB.com_contentimporter.sys.ini', $sys);
            }
            Factory::getLanguage()->load('com_contentimporter', JPATH_ADMINISTRATOR, $code, true, false);
            $languages[] = $code;
        }

        return compact('categories', 'menus', 'menuitems', 'languages');
    }

    public function generateSample(string $type, array $ids): string
    {
        $legend = $this->getLegend();
        $sample = [
            'categories' => array_slice($legend['categories'], 0, 1),
            'menus'      => array_slice($legend['menus'], 0, 1),
            'menuitems'  => array_slice($legend['menuitems'], 0, 1),
            'articles'   => [
                [
                    'title'    => 'Sample Article',
                    'catid'    => $legend['categories'][0]['id'] ?? 0,
                    'language' => '*',
                    'text'     => 'Lorem ipsum',
                    'state'    => 1,
                ],
            ],
        ];
        switch ($type) {
            case 'csv':
                return "title,catid,language,text,state\nSample Article," . ($legend['categories'][0]['id'] ?? 0) . ",*,Lorem ipsum,1\n";
            case 'md':
            case 'txt':
                return "Sample Article\n=========\n\nLorem ipsum\n";
            default:
                return json_encode($sample, JSON_PRETTY_PRINT);
        }
    }

    public function import(array $file): array
    {
        $messages = [];
        $path = $file['tmp_name'] ?? '';
        if (!$path) {
            $messages[] = Text::_('COM_CONTENTIMPORTER_NO_FILE');
            return $messages;
        }
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $content = file_get_contents($path);
        switch ($ext) {
            case 'json':
                $data = json_decode($content, true);
                break;
            case 'csv':
                $rows = array_map('str_getcsv', explode("\n", trim($content)));
                $headers = array_shift($rows);
                $data = [];
                foreach ($rows as $row) {
                    if (!$row) {
                        continue;
                    }
                    $data[] = array_combine($headers, $row);
                }
                break;
            default:
                $data = [];
                break;
        }
        foreach (($data['articles'] ?? $data) as $article) {
            $table = Table::getInstance('Content', 'Joomla\\CMS\\Table\\', []);
            $table->title     = $article['title'] ?? '';
            $table->alias     = $article['alias'] ?? '';
            $table->catid     = $article['catid'] ?? 0;
            $table->language  = $article['language'] ?? '*';
            $table->introtext = $article['text'] ?? '';
            $table->state     = $article['state'] ?? 0;
            $table->check();
            $table->store();
            $messages[] = Text::sprintf('COM_CONTENTIMPORTER_ARTICLE_CREATED', $table->title);
        }
        return $messages;
    }

    public function createMenu(array $data): int
    {
        $table = Table::getInstance('MenuType', 'Joomla\\CMS\\Table\\', []);
        $table->menutype  = $data['menutype'] ?? '';
        $table->title     = $data['title'] ?? '';
        $table->client_id = 1;
        $table->language  = $data['language'] ?? '*';
        $table->check();
        $table->store();
        return (int) $table->id;
    }

    public function createMenuItem(array $data): int
    {
        $table = Table::getInstance('Menu', 'Joomla\\CMS\\Table\\', []);
        $table->title     = $data['title'] ?? '';
        $table->parent_id = $data['parent_id'] ?? 1;
        $table->menutype  = $data['menutype'] ?? '';
        $table->language  = $data['language'] ?? '*';
        $table->link      = 'index.php?option=com_content&view=article&id=1';
        $table->type      = 'component';
        $table->component_id = 22;
        $table->check();
        $table->store();
        return (int) $table->id;
    }
}

<?php
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;

class Com_ContentintegratorInstallerScript
{
    public function preflight($type, $parent)
    {
        $paths = [
            JPATH_ADMINISTRATOR . '/components/com_contentintegrator/services',
            JPATH_ADMINISTRATOR . '/components/com_contentintegrator/src',
            JPATH_ADMINISTRATOR . '/components/com_contentintegrator/tmpl',
            JPATH_ADMINISTRATOR . '/components/com_contentintegrator/admin/sql',
            JPATH_SITE . '/media/com_contentintegrator/css',
            JPATH_SITE . '/media/com_contentintegrator/js',
            JPATH_SITE . '/media/com_contentintegrator/images',
        ];

        foreach ($paths as $path)
        {
            if (!is_dir($path))
            {
                \Joomla\CMS\Filesystem\Folder::create($path, 0755);
            }
        }

        $missing = [];
        $base    = __DIR__;

        $xml = simplexml_load_file($base . '/com_contentintegrator.xml');

        foreach ($xml->administration->files->filename as $file)
        {
            $abs = $base . '/' . (string) $file;

            if (!is_file($abs))
            {
                $missing[] = $abs;
            }
        }

        if ($missing)
        {
            Factory::getApplication()->enqueueMessage(
                'Fehlende Dateien: ' . implode(', ', $missing),
                'error'
            );

            return false;
        }

        $tmpFile = JPATH_ADMINISTRATOR . '/components/com_contentintegrator/test.tmp';

        if (!@file_put_contents($tmpFile, 'test'))
        {
            Factory::getApplication()->enqueueMessage(
                'Das Verzeichnis ' . dirname($tmpFile) . ' ist nicht beschreibbar.',
                'error'
            );

            return false;
        }

        @unlink($tmpFile);

        $db = Factory::getDbo();
        $db->setQuery(
            'DELETE FROM #__extensions WHERE element = ' .
            $db->quote('com_contentintegrator') . ' AND type = ' .
            $db->quote('component')
        )->execute();
    }

    public function postflight($type, $parent)
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->update($db->quoteName('#__menu'))
            ->set($db->quoteName('published') . ' = 1')
            ->where($db->quoteName('link') . ' = ' . $db->quote('index.php?option=com_contentintegrator'));
        $db->setQuery($query)->execute();
    }
}

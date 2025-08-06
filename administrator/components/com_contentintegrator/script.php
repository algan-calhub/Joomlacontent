<?php
declare(strict_types=1);

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\Folder;

class Com_ContentintegratorInstallerScript
{
    public function preflight($type, $parent)
    {
        $app = Factory::getApplication();
        $root = __DIR__;
        $dirs = [
            $root . '/services',
            $root . '/src',
            $root . '/tmpl',
            $root . '/sql',
            JPATH_SITE . '/media/com_contentintegrator/css',
            JPATH_SITE . '/media/com_contentintegrator/js',
            JPATH_SITE . '/media/com_contentintegrator/images',
        ];
        foreach ($dirs as $dir) {
            if (!is_dir($dir) && !Folder::create($dir, 0755)) {
                $app->enqueueMessage("Verzeichnis nicht anlegbar: {$dir}", 'error');
                return false;
            }
        }
        $xml = simplexml_load_file($root . '/com_contentintegrator.xml');
        $missing = [];
        foreach ($xml->administration->files->filename as $file) {
            $abs = $root . '/' . (string) $file;
            if (!is_file($abs)) {
                $missing[] = $abs;
            }
        }
        if ($missing) {
            $app->enqueueMessage('Fehlende Dateien: ' . implode(', ', $missing), 'error');
            return false;
        }
        return true;
    }
}

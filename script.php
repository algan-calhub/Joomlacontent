<?php
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;

class com_contentintegratorInstallerScript
{
    public function postflight($type, $parent): void
    {
        $container = Factory::getContainer();

        $db = $container->get('DatabaseDriver');

        $db->setQuery("DELETE FROM #__extensions WHERE element = 'com_contentintegrator' AND type = 'component'");
        $db->execute();

        $lang = Factory::getApplication()->getLanguage();
        foreach (['en-GB', 'de-DE'] as $tag) {
            $lang->load('com_contentintegrator', JPATH_ADMINISTRATOR, $tag, true, true);
        }
    }
}

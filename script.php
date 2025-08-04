<?php
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\Service\Provider\Cache;

class com_contentintegratorInstallerScript
{
    public function postflight($type, $parent): void
    {
        $container = Factory::getContainer();

        if (! $container->has('cache.storage')) {
            $container->registerServiceProvider(new Cache());
        }

        $db = $container->get('DatabaseDriver');

        $db->setQuery("DELETE FROM #__extensions WHERE element = 'com_contentintegrator' AND type = 'component'");
        $db->execute();

        $container->get('cache.storage')->cleanAll();

        foreach (['en-GB', 'de-DE'] as $tag) {
            LanguageHelper::loadLanguageFromFilesystem($tag, JPATH_ADMINISTRATOR);
        }
    }
}

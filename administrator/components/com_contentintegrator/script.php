<?php
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;

class Com_ContentintegratorInstallerScript
{
    public function preflight($type, $parent)
    {
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

<?php
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
?>
<form action="<?php echo Route::_('index.php?option=com_contentimporter&task=import.upload'); ?>" method="post" enctype="multipart/form-data" id="import-form">
    <div id="drop-zone">
        <input type="file" name="file" accept=".csv,.json,.txt,.md">
        <p><?php echo Text::_('COM_CONTENTIMPORTER_UPLOAD'); ?></p>
    </div>
    <button type="submit" class="btn btn-primary"><?php echo Text::_('COM_CONTENTIMPORTER_UPLOAD'); ?></button>
</form>
<div class="sample-buttons">
    <a class="btn btn-secondary" href="<?php echo Route::_('index.php?option=com_contentimporter&task=import.sample&format=json'); ?>"><?php echo Text::_('COM_CONTENTIMPORTER_SAMPLE_JSON'); ?></a>
    <a class="btn btn-secondary" href="<?php echo Route::_('index.php?option=com_contentimporter&task=import.sample&format=csv'); ?>"><?php echo Text::_('COM_CONTENTIMPORTER_SAMPLE_CSV'); ?></a>
    <a class="btn btn-secondary" href="<?php echo Route::_('index.php?option=com_contentimporter&task=import.sample&format=txt'); ?>"><?php echo Text::_('COM_CONTENTIMPORTER_SAMPLE_TXT'); ?></a>
    <a class="btn btn-secondary" href="<?php echo Route::_('index.php?option=com_contentimporter&task=import.sample&format=md'); ?>"><?php echo Text::_('COM_CONTENTIMPORTER_SAMPLE_MD'); ?></a>
</div>
<h2><?php echo Text::_('COM_CONTENTIMPORTER_LEGEND'); ?></h2>
<div class="legend">
    <h3><?php echo Text::_('COM_CONTENTIMPORTER_CATEGORIES'); ?></h3>
    <table class="table">
        <thead><tr><th></th><th>ID</th><th>Alias</th><th><?php echo Text::_('JFIELD_LANGUAGE_LABEL'); ?></th></tr></thead>
        <tbody>
        <?php foreach ($this->legend['categories'] as $cat): ?>
            <tr>
                <td><input type="checkbox" class="legend-category" value="<?php echo (int) $cat['id']; ?>"></td>
                <td><?php echo (int) $cat['id']; ?></td>
                <td><?php echo htmlspecialchars($cat['alias'], ENT_QUOTES); ?></td>
                <td><?php echo $cat['language']; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <h3><?php echo Text::_('COM_CONTENTIMPORTER_MENUS'); ?></h3>
    <table class="table">
        <thead><tr><th></th><th>ID</th><th>menutype</th><th><?php echo Text::_('JGLOBAL_TITLE'); ?></th></tr></thead>
        <tbody>
        <?php foreach ($this->legend['menus'] as $menu): ?>
            <tr>
                <td><input type="checkbox" class="legend-menu" value="<?php echo (int) $menu['id']; ?>"></td>
                <td><?php echo (int) $menu['id']; ?></td>
                <td><?php echo htmlspecialchars($menu['menutype'], ENT_QUOTES); ?></td>
                <td><?php echo htmlspecialchars($menu['title'], ENT_QUOTES); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <h3><?php echo Text::_('COM_CONTENTIMPORTER_MENUITEMS'); ?></h3>
    <table class="table">
        <thead><tr><th></th><th>ID</th><th>Parent</th><th><?php echo Text::_('JGLOBAL_TITLE'); ?></th></tr></thead>
        <tbody>
        <?php foreach ($this->legend['menuitems'] as $item): ?>
            <tr>
                <td><input type="checkbox" class="legend-menuitem" value="<?php echo (int) $item['id']; ?>"></td>
                <td><?php echo (int) $item['id']; ?></td>
                <td><?php echo (int) $item['parent_id']; ?></td>
                <td><?php echo htmlspecialchars($item['title'], ENT_QUOTES); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <h3><?php echo Text::_('COM_CONTENTIMPORTER_LANGUAGES'); ?></h3>
    <table class="table">
        <thead><tr><th></th><th>ISO</th></tr></thead>
        <tbody>
        <?php foreach ($this->legend['languages'] as $lang): ?>
            <tr>
                <td><input type="checkbox" class="legend-language" value="<?php echo $lang; ?>"></td>
                <td><?php echo $lang; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<div class="new-menu">
    <h3><?php echo Text::_('COM_CONTENTIMPORTER_NEW_MENU'); ?></h3>
    <form action="<?php echo Route::_('index.php?option=com_contentimporter&task=import.createMenu'); ?>" method="post" id="form-menu">
        <input type="text" name="menutype" placeholder="menutype">
        <input type="text" name="title" placeholder="title">
        <input type="text" name="language" placeholder="language">
        <button class="btn btn-secondary" type="submit"><?php echo Text::_('JNEW'); ?></button>
    </form>
</div>
<div class="new-menuitem">
    <h3><?php echo Text::_('COM_CONTENTIMPORTER_NEW_MENUITEM'); ?></h3>
    <form action="<?php echo Route::_('index.php?option=com_contentimporter&task=import.createMenuItem'); ?>" method="post" id="form-menuitem">
        <input type="text" name="parent_id" placeholder="parent">
        <input type="text" name="menutype" placeholder="menutype">
        <input type="text" name="title" placeholder="title">
        <input type="text" name="language" placeholder="language">
        <button class="btn btn-secondary" type="submit"><?php echo Text::_('JNEW'); ?></button>
    </form>
</div>
<div class="prompt">
    <h3><?php echo Text::_('COM_CONTENTIMPORTER_PROMPT'); ?></h3>
    <textarea id="gpt-prompt" rows="8" class="form-control" readonly></textarea>
    <button id="copy-prompt" class="btn btn-secondary" type="button"><?php echo Text::_('COM_CONTENTIMPORTER_COPY'); ?></button>
</div>
<div class="chatgpt-widget">
    <textarea id="gpt-input" rows="8" class="form-control"></textarea>
    <button id="gpt-send" class="btn btn-primary mt-2" type="button"><?php echo Text::_('COM_CONTENTIMPORTER_SEND'); ?></button>
</div>
<link rel="stylesheet" href="media/com_contentimporter/css/style.css">
<script src="media/com_contentimporter/js/import.js"></script>

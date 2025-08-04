INSERT IGNORE INTO #__menu
(menutype, title, alias, path, link, type, published, parent_id, component_id, access, level, language, client_id)
VALUES ('menu', 'Content Integrator', 'content-integrator', 'content-integrator',
        'index.php?option=com_contentintegrator', 'component', 1,
        (SELECT id FROM #__menu WHERE title = 'Components'),
        (SELECT extension_id FROM #__extensions WHERE element = 'com_contentintegrator'),
        1, 2, '*', 1);

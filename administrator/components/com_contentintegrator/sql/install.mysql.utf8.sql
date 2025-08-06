-- Erstellt Menüeintrag unter "Komponenten"
INSERT IGNORE INTO #__menu
(menutype, title, alias, path, link, type, published,
 parent_id, component_id, access, level, language, client_id)
VALUES
(
'main',
'Content Integrator',
'content-integrator',
'content-integrator',
'index.php?option=com_contentintegrator',
'component',
1,
(SELECT id FROM #__menu WHERE menutype='main' AND parent_id=1 AND type='components'),
(SELECT extension_id FROM #__extensions WHERE element='com_contentintegrator' LIMIT 1),
1,
2,
'*',
1
);

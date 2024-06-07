<?php
# Shareleads module un-installer.

$name = 'Shareleads';

# Clean-up library icons
Siberian_Feature::removeIcons($name);

Siberian_Feature::removeIcons($name . '-flat');

# Clean-up Layouts
$layoutData = [1];
$slug = 'shareleads';
Siberian_Feature::removeLayouts($option->getId(), $slug, $layoutData);
# Clean-up Option(s)/Feature(s)
$code = 'shareleads';
Siberian_Feature::uninstallFeature($code);

Siberian_Feature::dropTables($tables);
# Clean-up module
Siberian_Feature::uninstallModule($name);
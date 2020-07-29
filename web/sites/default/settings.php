<?php

// #ddev-generated: Automatically generated Drupal settings file.
if (file_exists($app_root . '/' . $site_path . '/settings.ddev.php') && getenv('IS_DDEV_PROJECT') == 'true') {
  include $app_root . '/' . $site_path . '/settings.ddev.php';
}

$settings['container_yamls'][] = DRUPAL_ROOT . '/sites/development.services.yml';
$settings['config_sync_directory'] = DRUPAL_ROOT . '/../config/sync';

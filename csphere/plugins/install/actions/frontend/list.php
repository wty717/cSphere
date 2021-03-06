<?php

/**
 * List action
 *
 * PHP Version 5
 *
 * @category  Plugins
 * @package   Install
 * @author    Hans-Joachim Piepereit <contact@csphere.eu>
 * @copyright 2013 cSphere Team
 * @license   http://opensource.org/licenses/bsd-license Simplified BSD License
 * @link      http://www.csphere.eu
 **/

$loader = \csphere\core\service\Locator::get();

// Add breadcrumb navigation
$bread = new \csphere\core\template\Breadcrumb('install');

$bread->plugin('install', 'list');
$bread->trace();

// Get language data
$lang = \csphere\core\translation\Fetch::keys('install');

// Get list of required PHP extensions
$xml        = $loader->load('xml', 'plugin');
$default    = $xml->source('plugin', 'default');
$extensions = [];

if (isset($default['required']['extension'])) {

    $extensions = $default['required']['extension'];
}

// Check if these extensions are available
$error = '';
$continue = 'yes';
$missing = '';

foreach ($extensions AS $ext) {

    if (!extension_loaded($ext['value'])) {

        $missing .= "\n" . $ext['value'];
    }
}

if ($missing != '') {

    $error    .= $lang['no_php_ext'] . ': ' . $missing . "\n";
    $continue  = '';
}

// Check if directories are writable
$path  = \csphere\core\init\path();
$write = '';
$dirs  = ['config', 'storage', 'storage/cache', 'storage/database',
          'storage/logs', 'storage/logs/errors', 'storage/uploads'];

foreach ($dirs AS $dir) {

    $check = $path . 'csphere/' . $dir;

    if (!is_writable($check)) {

        $write .= "\n" . 'csphere/' . $dir;
    }
}

if ($write != '') {

    $error    .= $lang['no_write'] . ': ' . $write . "\n";
    $continue  = '';
}

$data = ['error' => $error, 'continue' => $continue];

// Send data to view
$view = $loader->load('view');

$view->template('install', 'list', $data);

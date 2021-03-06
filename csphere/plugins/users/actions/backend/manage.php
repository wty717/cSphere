<?php

/**
 * List action
 *
 * PHP Version 5
 *
 * @category  Plugins
 * @package   Users
 * @author    Hans-Joachim Piepereit <contact@csphere.eu>
 * @copyright 2013 cSphere Team
 * @license   http://opensource.org/licenses/bsd-license Simplified BSD License
 * @link      http://www.csphere.eu
 **/

// Add breadcrumb navigation
$bread = new \csphere\core\template\Breadcrumb('admin');
$bread->add('content');
$bread->plugin('users', 'manage');
$bread->trace();

// Get RAD class for this action
$rad = new \csphere\core\rad\Listed('users');

$rad->map('manage', 'manage');

// Define order columns
$order = ['user_name', 'user_since'];

$rad->search(['user_name'], true, true);
$rad->delegate('user_name', $order);

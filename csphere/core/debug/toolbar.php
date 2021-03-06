<?php

/**
 * Debug toolbar integration for template output
 *
 * PHP Version 5
 *
 * @category  Core
 * @package   Debug
 * @author    Hans-Joachim Piepereit <contact@csphere.eu>
 * @copyright 2013 cSphere Team
 * @license   http://opensource.org/licenses/bsd-license Simplified BSD License
 * @link      http://www.csphere.eu
 **/

namespace csphere\core\debug;

/**
 * Debug toolbar integration for template output
 *
 * @category  Core
 * @package   Debug
 * @author    Hans-Joachim Piepereit <contact@csphere.eu>
 * @copyright 2013 cSphere Team
 * @license   http://opensource.org/licenses/bsd-license Simplified BSD License
 * @link      http://www.csphere.eu
 **/

class Toolbar
{
    /**
     * Holds the loader object
     **/
    private $_loader = null;

    /**
     * Holds the view object
     **/
    private $_view = null;

    /**
     * These components are separated from logs
     **/
    private $_specials = ['database', 'errors', 'includes'];

    /**
     * Holds the most current data
     **/
    private $_content = '';

    /**
     * Create the string to attach to the template
     *
     * @return \csphere\core\debug\Toolbar
     **/

    public function __construct()
    {
        // Add stylesheet for debug
        \csphere\core\template\Hooks::stylesheet('debug', 'core_debug.css');

        // Create loader and view object
        $this->_loader = \csphere\core\service\Locator::get();
        $this->_view   = $this->_loader->load('view');

        // Get parsetime and memory usage
        $parsetime = $this->_view->getOption('parsetime');
        $memory    = memory_get_peak_usage();

        // Start and fill stats array
        $stats = ['parsetime' => $parsetime, 'memory' => $memory];

        // Prepare logs and specials
        $logs = $this->_logs();

        // Generate toolbar content
        $this->_content = $this->_format($stats, $logs);
    }

    /**
     * Return the string to attach to the template
     *
     * @return string
     **/

    public function content()
    {
        return $this->_content;
    }

    /**
     * Get and improve log array
     *
     * @return array
     **/

    private function _logs()
    {
        // Load log content
        $logs = $this->_loader->load('logs');
        $info = $logs->info();

        // Make sure that all needed log keys are set
        $info['database'] = empty($info['database']) ? [] : $info['database'];
        $info['errors']   = empty($info['errors']) ? [] : $info['errors'];

        ksort($info);

        // Add included files to logs
        $includes = get_included_files();
        $path     = \csphere\core\init\path();

        foreach ($includes AS $include) {

            $include            = str_replace('\\', '/', $include);
            $info['includes'][] = str_replace($path, '', $include);
        }

        return $info;
    }

    /**
     * Collect the overall debug content
     *
     * @param array $stats Data of statistics
     * @param array $logs  Content of log service
     *
     * @return string
     **/

    private function _format(array $stats, array $logs)
    {
        // Check for PHP settings that are problematic for developers
        $error = $this->_settings();

        if ($error != '') {

            $logs['errors'][] = $error;
        }

        // Details first to not overwrite something
        $data = $this->_formatDetails($logs);

        // Add PHP version and engine
        $data['php_full']   = phpversion();
        $data['php_engine'] = 'PHP';
        $sapi               = strtolower(php_sapi_name());

        if ($sapi == 'srv') {

            $data['php_engine'] = 'HHVM';
        }

        // Move parsetime and memory usage to data array
        $data['memory'] = \csphere\core\files\File::size($stats['memory']);

        $data['parsetime'] = \csphere\core\date\Microtime::untilNow(
            $stats['parsetime']
        );

        // Format logbar
        $data['logbar'] = $this->_formatLogbar($logs);

        // Send data to view and fetch box result
        $this->_view->template('debug', 'core_toolbar', $data, true);
        $result = $this->_view->box();

        return $result;
    }

    /**
     * Create the content for the logbar
     *
     * @param array $logs Content of log service
     *
     * @return array
     **/

    private function _formatLogbar(array $logs)
    {
        $data = [];

        foreach ($logs AS $name => $entries) {

            if (!in_array($name, $this->_specials)) {

                $data[] = ['component' => $name, 'count' => count($entries)];
            }
        }

        return $data;
    }

    /**
     * Create the content for the details
     *
     * @param array $logs Content of log service
     *
     * @return array
     **/

    private function _formatDetails(array $logs)
    {
        $data = ['count' => [], 'logs' => []];

        // Format log information for output
        foreach ($logs AS $name => $entries) {

            $sub = [];

            foreach ($entries AS $part) {

                $sub[] = ['text' => $part];
            }

            // Append to logs except for specials
            if (in_array($name, $this->_specials)) {

                $data[$name] = $sub;
                $data['count'][$name] = count($sub);

            } else {

                $data['logs'][] = ['name' => $name, 'entries' => $sub];
            }
        }

        $data['count']['logs'] = count($data['logs']);

        return $data;
    }

    /**
     * Check for development specific settings
     *
     * @return string
     **/

    private function _settings()
    {
        $error = '';

        // Check for OPcache validation
        if (extension_loaded('Zend OPcache')) {

            $val = ini_get('opcache.validate_timestamps');
            $frq = ini_get('opcache.revalidate_freq');

            if (empty($val)) {

                $error .= 'opcache.validate_timestamps=1' . "\n";
            }

            if (!empty($frq)) {

                $error .= 'opcache.revalidate_freq=0' . "\n";
            }
        }

        if ($error != '') {

            $lang  = \csphere\core\translation\Fetch::key('debug', 'php_ini_dev');
            $error = $lang . ':' . "\n" . $error;
        }

        return $error;
    }
}

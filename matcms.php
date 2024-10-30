<?php
/**
 * Plugin Name: MatCMS
 * Plugin URI: https://www.matriz.it/projects/matcms-wordpress/
 * Description: This plugin adds to WordPress some utilities for developers.
 * Version: 1.4.0
 * Requires at least: 4.0
 * Requires PHP: 7.3
 * Author: Mattia
 * Author URI: https://www.matriz.it
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

spl_autoload_register(function($class) {
	$res = false;
	if (strlen($class) >= 6 && substr($class, 0, 6) == 'MatCMS') {
		if ($class == 'MatCMS') {
			$arr = array('MatCMS');
		} else {
			$arr = explode('\\', substr($class, 7));
		}
		$path = __DIR__.'/inc';
		$counter = count($arr);
		if ($counter > 0) {
			for ($i = 0; $i < $counter; $i++) {
				$path .= '/'.preg_replace('/([^a-z0-9_]+)/', '', strtolower($arr[$i]));
			}
			$path .= '.class.php';
			if (is_file($path)) {
				require_once($path);
				$res = true;
			}
		}
	}
	return $res;
});

new MatCMS();
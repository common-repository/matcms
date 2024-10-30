<?php
/**
 * Classe base di MatCMS
 */
class MatCMS {
	/**
	 * Indirizzi da gestire in caso di errore 404 e verso cui fare dei redirect
	 * @access private
	 * @static
	 * @var array
	 */
	private static $redirect_404_urls = array();
	
	/**
	 * Costruttore della classe
	 * @access public
	 */
	public function __construct() {
		add_action('wp_enqueue_scripts', function() {
			wp_register_style('bootstrap-icons', 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11/font/bootstrap-icons.min.css', array(), null);
			wp_register_script('bootstrap-lightbox', 'https://cdn.jsdelivr.net/gh/matmattia/bootstrap-lightbox@1.1/bootstrap-lightbox.min.js', array(), null, true);
			wp_register_script('bootstrap-lightbox-init-images', plugins_url('/js/bootstrap-lightbox-init-images.js', __DIR__), array('bootstrap-lightbox'), null, true);
		});
	}
	
	/**
	 * Imposta alcuni indirizzi da gestire in caso di errore 404 e verso cui fare dei redirect
	 * @access public
	 * @static
	 * @param array $urls indirizzi con relativi redirect
	 * @return boolean
	 */
	public static function redirect404($urls) {
		static $added_filter = false;
		$res = false;
		if (is_array($urls) && !empty($urls)) {
			foreach ($urls as $k => $v) {
				if (is_string($k) && trim($k) !== '' && is_string($v) && trim($v) !== '') {
					self::$redirect_404_urls[$k] = $v;
					if (!$added_filter) {
						add_filter('status_header', function($status_header, $code) {
							if (is_numeric($code) && $code == 404 && !headers_sent() && isset($_SERVER['REQUEST_URI']) && is_string($_SERVER['REQUEST_URI']) && trim($_SERVER['REQUEST_URI']) !== '') {
								foreach (self::$redirect_404_urls as $k => $v) {
									if ($k == $_SERVER['REQUEST_URI']) {
										header(wp_get_server_protocol().' 301 '.get_status_header_desc(301));
										header('Location: '.$v);
										wp_die();
									}
								}
							}
						}, 10, 2);
						$added_filter = true;
					}
				}
			}
		}
		return $res;
	}
}
<?php
namespace MatCMS;

/**
 * Classe per gestire i temi
 */
class Theme {
	/**
	 * Include un file esterno al tema con la data di modifica come versione
	 * @access public
	 * @static
	 * @param string $type tipo di file (script o style)
	 * @param string $handle nome dello script
	 * @param string $src URL dello script
	 * @param array $deps elenco delle dipendenze
	 * @param array $more_params altri parametri di wp_enqueue_script e wp_enqueue_style
	 */
	public static function include_asset($type, $handle, $src = '', $deps = array(), $more_params = array()) {
		$ver = false;
		if (is_string($src) && trim($src) !== '') {
			$theme_uri = get_theme_root_uri();
			if (strpos($src, $theme_uri) === 0) {
				$path = get_theme_root().str_replace($theme_uri, '', $src);
				if (is_file($path)) {
					$ver = filemtime($path);
				}
			}
		}
		$params = array_merge(
			array($handle, $src, $deps, $ver),
			is_array($more_params) ? array_values($more_params) : array($more_params)
		);
		switch (is_string($type) ? $type : null) {
			case 'script':
				$function = 'wp_enqueue_script';
			break;
			case 'style':
				$function = 'wp_enqueue_style';
			break;
			default:
				$function = null;
			break;
		}
		if ($function) {
			call_user_func_array($function, $params);
		}
	}
	
	/**
	 * Stampa i meta tag per Open Graph e Twitter Cards
	 * @access public
	 * @static
	 * @param string $k nome del meta tag
	 * @param mixed $v valore del meta tag
	 */
	public static function printOpenGraph() {
		$meta = array(
			'og:site_name' => get_bloginfo('name', 'display'),
			'og:locale' => get_locale(),
			'twitter:card' => 'summary'
		);
		if (is_front_page()) {
			$meta['og:type'] = 'website';
			$meta['og:title'] = $meta['og:site_name'];
			$meta['og:description'] = get_bloginfo('description', 'display');
			if (trim($meta['og:description']) === '') {
				$meta['og:description'] = $meta['og:title'];
			}
			$meta['og:url'] = home_url('/');
			$meta['og:image'] = get_site_icon_url();
			if (!$meta['og:image']) {
				$meta['og:image'] = get_header_image();
			}
		} else if (is_single()) {
			$meta['og:type'] = 'article';
			$meta['og:title'] = get_the_title();
			$meta['og:description'] = get_the_excerpt();
			if (trim($meta['og:description']) === '') {
				$meta['og:description'] = wp_trim_words(get_the_content(null, false, get_the_ID()), 55);
				if (trim($meta['og:description']) === '') {
					$meta['og:description'] = $meta['og:title'];
				}
			}
			$meta['og:url'] = get_the_permalink();
			$meta['og:image'] = array();
			$images = Post::getImages();
			foreach ($images as $v) {
				$meta['og:image'][] = $v;
			}
		}
		foreach (array('title', 'description', 'url', 'image') as $v ) {
			if (isset($meta['og:'.$v]) && !isset($meta['twitter:'.$v])) {
				$meta['twitter:'.$v] = $meta['og:'.$v];
			}
		}
		foreach (array('og:description', 'twitter:description') as $v) {
			if (isset($meta[$v]) && is_string($meta[$v]) && trim($meta[$v]) != '') {
				$meta[$v] = wp_trim_words($meta[$v], 50, '...');
				if (!isset($meta['description'])) {
					$meta['description'] = wp_trim_words($meta[$v], 30, '...');
				}
			}
		}
		foreach ($meta as $k => $v) {
			self::printMetaTag($k, $v);
		}
	}
	
	/**
	 * Stampa un meta tag
	 * @access private
	 * @static
	 * @param string $k nome del meta tag
	 * @param mixed $v valore del meta tag
	 */
	private static function printMetaTag($k, $v) {
		if (is_string($k) && trim($k) != '' && is_scalar($v) && trim($v) != '') {
			$key = strlen($k) > 3 && substr($k, 0, 3) == 'og:' ? 'property' : 'name';
			echo '<meta '.$key.'="'.esc_attr($k).'" content="'.esc_attr(preg_replace('/\s+/', ' ', $v)).'" />';
		} else if (is_array($v) && !empty($v)) {
			foreach ($v as $k2 => $v2) {
				self::printMetaTag(is_string($k2) ? $k2 : $k, $v2);
			}
		}
	}
}
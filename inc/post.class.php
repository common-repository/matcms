<?php
namespace MatCMS;

/**
 * Classe per gestire i post
 */
class Post {
	/**
	 * Restituisce le immagini di un post
	 * @access public
	 * @static
	 * @param int|WP_Post ID o oggetto del post
	 * @param array $params altri parametri
	 * @return array
	 */
	public static function getImages($post = null, $params = array()) {
		global $wpdb;
		$images = array();
		$post = get_post($post);
		if ($post) {
			if (!is_array($params)) {
				$params = array();
			}
			$no_arr = array();
			
			// Immagini collegate al post
			$arr = get_attached_media('image', $post->ID);
			
			// Immagini incluse come blocchi del post
			$blocks = parse_blocks($post->post_content);
			foreach ($blocks as $block) {
				if ($block['blockName'] == 'core/image' && isset($block['attrs']) && is_array($block['attrs']) && isset($block['attrs']['id']) && is_numeric($block['attrs']['id'])) {
					$arr[] = $block['attrs']['id'];
					$no_arr = array_merge($no_arr, self::getHtmlImages($block['innerHTML']));
				}
				unset($block);
			}
			unset($blocks);
			
			// Immagini incluse nel post
			$post_images = self::getHtmlImages($post->post_content);
			foreach ($post_images as $v) {
				if (!in_array($v, $no_arr)) {
					$arr[] = $v;
				}
				unset($v);
			}
			unset($post_images);
			
			// Anteprime di YouTube
			if (has_block('core-embed/youtube')) {
				$m = array();
				if (preg_match_all('/\<\!\-\- wp\:core\-embed\/youtube \{([^}]+)\} -->/s', $post->post_content, $m, PREG_SET_ORDER)) {
					$counter = count($m);
					for ($i = 0; $i < $counter; $i++) {
						$json = @json_decode('{'.$m[$i][1].'}', true);
						if (is_array($json) && isset($json['url']) && is_string($json['url']) && trim($json['url']) !== '') {
							$q = parse_url($json['url'], PHP_URL_QUERY);
							if (is_string($q) && trim($q) !== '') {
								$qs = array();
								parse_str($q, $qs);
								if (is_array($qs) && isset($qs['v']) && is_string($qs['v']) && trim($qs['v']) !== '') {
									$arr[] = 'https://i.ytimg.com/vi/'.rawurlencode($qs['v']).'/maxresdefault.jpg';
								}
								unset($qs);
							}
							unset($q);
						}
						unset($json);
					}
					unset($i, $counter);
				}
				unset($m);
			}
			
			// Anteprime di inclusioni
			if (has_block('core/embed') && preg_match_all('/\<\!\-\- wp\:embed \{([^}]+)\} -->/s', $post->post_content, $m, PREG_SET_ORDER)) {
				$counter = count($m);
				for ($i = 0; $i < $counter; $i++) {
					$json = @json_decode('{'.$m[$i][1].'}', true);
					if (is_array($json)) {
						switch (isset($json['providerNameSlug']) ? $json['providerNameSlug'] : null) {
							case 'youtube':
								if (isset($json['url']) && is_string($json['url']) && trim($json['url']) !== '') {
									$q = parse_url($json['url'], PHP_URL_QUERY);
									if (is_string($q) && trim($q) !== '') {
										$qs = array();
										parse_str($q, $qs);
										if (is_array($qs) && isset($qs['v']) && is_string($qs['v']) && trim($qs['v']) !== '') {
											$arr[] = 'https://i.ytimg.com/vi/'.rawurlencode($qs['v']).'/maxresdefault.jpg';
										}
										unset($qs);
									}
									unset($q);
								}
							break;
						}
					}
					unset($json);
				}
				unset($i, $counter);
			}	
			unset($m);
			
			foreach ($arr as $v) {
				if (is_object($v) && isset($v->ID)) {
					$v = $v->ID;
				}
				if (is_numeric($v) && $v > 0) {
					$src = wp_get_attachment_image_src($v, isset($params['size']) ? $params['size'] : 'full');
					if ($src) {
						$images[] = $src[0];
					}
				} else if (is_string($v) && trim($v) !== '') {
					$images[] = $v;
				}
				unset($v);
			}
			unset($arr);
			$images = array_unique($images);
		}
		return $images;
	}
	
	/**
	 * Restituisce le immagini contenute in una pagina HTML
	 * @access public
	 * @static
	 * @param string $html codice HTML
	 * @return array
	 */
	public static function getHtmlImages($html) {
		$images = array();
		if (is_string($html)) {
			$m = array();
			if (preg_match_all('/\<img([^>]+)src\=(["\']{1})([^"\']+)(["\']{1})/s', $html, $m, PREG_SET_ORDER)) {
				$counter = count($m);
				for ($i = 0; $i < $counter; $i++) {
					$images[] = $m[$i][3];
				}
				unset($i, $counter);
			}
			unset($m);
		}
		return $images;
	}
	
	/**
	 * Modifica i blocchi in modo da aggiungere le classi di Bootstrap
	 * @access public
	 * @static
	 * @param string $block_content contenuto del blocco
	 * @param array $block dati del blocco
	 * @return string
	 */
	public static function checkBootstrapBlock($block_content = '', $block = array()) {
		if (isset($block['blockName'])) {
			$add_class_to_tag = $add_class_to_class = $add_style_to_class = $remove_tags_with_class = array();
			switch ($block['blockName']) {
				case 'core/embed':
					$add_class_to_class['wp-has-aspect-ratio'] = 'ratio';
					foreach (array(
						array(21, 9),
						array(16, 9),
						array(4, 3),
						array(1, 1)
					) as $v) {
						$add_class_to_class['wp-embed-aspect-'.$v[0].'-'.$v[1]] = 'ratio-'.$v[0].'x'.$v[1];
						unset($v);
					}
					foreach (array(
						array(18, 9),
						array(9, 16),
						array(1, 2)
					) as $v) {
						$add_style_to_class['wp-embed-aspect-'.$v[0].'-'.$v[1]] = '--bs-aspect-ratio: '.($v[1] / $v[0] * 100).'%;';
					}
					$remove_tags_with_class[] = 'wp-block-embed__wrapper';
				break;
				case 'core/file':
					$add_class_to_class['wp-block-file__button'] = 'btn btn-primary';
				break;
				case 'core/image':
					$add_class_to_tag['figure'] = 'figure';
					$add_class_to_tag['figcaption'] = 'figure-caption';
				break;
				case 'core/quote':
					$add_class_to_tag['blockquote'] = 'blockquote';
					$add_class_to_tag['footer'] = 'blockquote-footer';
				break;
			}
			
			// Aggiunta di una classe a un tag
			foreach ($add_class_to_tag as $tag => $class) {
				$m = array();
				if (preg_match_all('/\<'.$tag.'([^>]*?)\>/', $block_content, $m, PREG_SET_ORDER)) {
					if (is_array($class)) {
						$class = implode(' ', $class);
					}
					$class = esc_attr($class);
					$counter = count($m);
					for ($i = 0; $i < $counter; $i++) {
						$m2 = array();
						if (preg_match('/ class\="([^"]*?)\"/', $m[$i][1], $m2)) {
							$el = str_replace($m2[0], ' class="'.trim($m2[1].' '.$class).'"', $m[$i][0]);
						} else {
							$el = '<'.$tag.$m[$i][1].' class="'.$class.'">';
						}
						unset($m2);
						$block_content = str_replace($m[$i][0], $el, $block_content);
					}
					unset($i, $counter);
				}
				unset($m, $tag, $class);
			}
			unset($add_class_to_tag);
			
			// Aggiunta di una classe a un tag con una classe
			foreach ($add_class_to_class as $class => $new_class) {
				$block_content = preg_replace('/\<([^>]*?) class\=("|"([^"]*)\s)'.preg_quote($class).'("|\s([^"]*)")([^>]*?)\>/si', '<$1 class=$2'.$class.' '.$new_class.'$4$6>', $block_content);
				unset($class, $new_class);
			}
			unset($add_class_to_tag);
			
			// Aggiunta di uno stile a un tag con una classe
			foreach ($add_style_to_class as $class => $style) {
				$m = array();
				if (preg_match_all('/\<([^>]*?) class\=("|"([^"]*)\s)'.preg_quote($class).'("|\s([^"]*)")([^>]*?)\>/si', $block_content, $m, PREG_SET_ORDER)) {
					$counter = count($m);
					for ($i = 0; $i < $counter; $i++) {
						$m2 = array();
						if (preg_match('/ style\="([^"]*?)\"/', $m[$i][0], $m2)) {
							$el = str_replace($m2[0], ' style="'.rtrim($m2[1], ';').';'.esc_attr($style).'"', $m[$i][0]);
							var_dump($el);
						} else {
							$el = substr($m[$i][0], 0, -1).' style="'.esc_attr($style).'">';
						}
						unset($m2);
						$block_content = str_replace($m[$i][0], $el, $block_content);
					}
					unset($i, $counter);
				}
				unset($m, $class, $style);
			}
			
			// Rimozione di un tag con una classe (senza cancellare il contenuto)
			if (class_exists('\DOMDocument')) {
				foreach ($remove_tags_with_class as $class) {
					$dom = new \DOMDocument();
					$dom->loadHTML($block_content, LIBXML_NOERROR);
					$finder = new \DOMXPath($dom);
					$nodes = $finder->query('//*[contains(concat(\' \', normalize-space(@class), \' \'), \' '.$class.' \')]');
					foreach ($nodes as $node) {
						$parent = $node->parentNode;
						while ($node->hasChildNodes()) {
							$parent->insertBefore($node->lastChild, $node->nextSibling);
						}
						$parent->removeChild($node);
					}
					$block_content = $dom->saveHTML();
					unset($class);
				}
			}
			unset($remove_tags_with_class);
		}
		return $block_content;
	}
}
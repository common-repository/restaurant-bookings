<?php 
// Para evitar llamadas directas
defined("ABSPATH") or exit();

$rbkTinyMCE = null;
class RBKTinyMCE {
	const TMCE_AE_PLUGIN = "rbktmce";
	
	public static function init() {
		global $rbkTinyMCE;
		
		$rbkTinyMCE = new RBKTinyMCE();
		add_filter('mce_external_languages', array($rbkTinyMCE, 'mce_external_languages'));
		add_filter("mce_external_plugins", array($rbkTinyMCE, "mce_external_plugins"));
		add_filter("mce_buttons", array($rbkTinyMCE, "mce_buttons"));
		add_filter("mce_css", array($rbkTinyMCE, "mce_css"));
		
		add_action("after_wp_tiny_mce", array($rbkTinyMCE, "after_wp_tiny_mce"));
		add_filter("admin_enqueue_scripts", array($rbkTinyMCE, "mce_admin_styles"));
	}
	
	public function mce_external_languages($locales) {
		if ($this->is_tiny_mce_screen()) {
			$locales[self::TMCE_AE_PLUGIN] = RestaurantBooking::plugin_dir('rbktmce/languages.php');
		}
		return $locales;
	}
	
	public function mce_external_plugins($plugins) {
		if ($this->is_tiny_mce_screen()) {
			$plugins[self::TMCE_AE_PLUGIN] = RBKScripts::plugin_url("rbktmce/rbktmce.js");
		}
		return $plugins;
	}
	
	public function mce_buttons($buttons) {
		if ($this->is_tiny_mce_screen()) {
			if (count($buttons) > 1) {
				array_splice($buttons, count($buttons) - 1, 0, "rbktmce_button");
			} else {
				$buttons[] = "rbktmce_button";
			}
		}
		
		return $buttons;
	}
	
	public function mce_css( $mce_css ) {
		if ($this->is_tiny_mce_screen()) {
			if ( ! empty( $mce_css ) ) {
				$mce_css .= ',';
			}
			
			$mce_css .= RBKScripts::plugin_url("rbktmce/css/rbktmce.css");
		}
	
		return $mce_css;
	}
	
	/**
	 * Fires after any core TinyMCE editor instances are created.
	 *
	 * @param array $mce_settings TinyMCE settings array.
	 */
	public function after_wp_tiny_mce( $mce_settings ) {
		if ($this->is_tiny_mce_screen()) {
			include "rbktmce/dialog.php";
		}
	}
	
	public function mce_admin_styles() {
		if ($this->is_tiny_mce_screen()) {
			wp_enqueue_style("rbktmce-modal", RBKScripts::plugin_url("rbktmce/css/modal.css"));
		}
	}
	
	private function is_tiny_mce_screen() {
		$current_screen = null;
		
		if (function_exists("get_current_screen")) {
			$current_screen = get_current_screen();
		} elseif (isset($GLOBALS['current_screen'])) {
			$current_screen = $GLOBALS['current_screen'];
		}
		
		if ($current_screen != null) {
			// Filtro para ver en que pantallas permitimos el boton. 
			// por defecto en post y page
			return in_array($current_screen->id, apply_filters("rbk_tinymce_screen", Array("post", "page")));
		}
		
		return false;
	}
}
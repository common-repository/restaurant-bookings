<?php 
// Para evitar llamadas directas
defined("ABSPATH") or exit();

require_once 'listae-admin-page.php';

class ListaeOptionsPage extends ListaeAdminPage {
	public function __construct() {
		parent::__construct();
		add_action("admin_menu", array( $this, 'admin_menu' ));
	}
	
	public function admin_enqueue_scripts($hook) {
		parent::admin_enqueue_scripts($hook);
		
		wp_enqueue_script("rbk-option-page", RBKScripts::plugin_url("js/rbk-option-page.js"), array("jquery"));
		$this->localized_help_feedback("rbk-option-page");
	}
	
	public function admin_menu() {
		add_options_page("Restaurant Bookings", "Restaurant Bookings", "delete_others_pages", RestaurantBooking::PAGE_OPTIONS, array($this, 'option_page'));
	}
	
	public static function get_exclude_bootstrap_js() {
		return apply_filters("rbk_exclude_bootstrap_js", get_option("rb_exclude_bootstrap_js", "") == "1");
	}
	
	public static function get_exclude_bootstrap_css() {
		return apply_filters("rbk_exclude_bootstrap_css", get_option("rb_exclude_bootstrap_css", "") == "1");
	}
	
	public static function get_skin() {
		return get_option("rb_skin", "");
	}

	public function option_page() {
	    
		$this->admin_top();
		
		if (!empty($_POST) && wp_verify_nonce($_POST["rbk_save_options"], "rbk_save_options")) {
			if (!isset($_POST["chk_disconnect"])) {
				delete_option("ae_access_token");
			} else {
				update_option("rb_skin", $_POST["sel_rb_skin"]);

				update_option("rb_exclude_bootstrap_js", empty($_POST["chk_rb_exclude_bootstrap_js"]) ? "0" : "1");
				update_option("rb_exclude_bootstrap_css", empty($_POST["chk_rb_exclude_bootstrap_css"]) ? "0" : "1");
				
				do_action("rbk_option_page_update");
				
				if ($_POST["sel_sc_to_block_migration"] == "1") {
					$this->migrate_sc_to_block();
				}
			}
			
			if ( !RestaurantBooking::is_registered() ) {
				?>
				<div id="message" class="alert alert-success">
					<p><?php echo esc_html(__("Disconnection of your website from Listae.com completed.", 'restaurant-bookings')); ?></p>
					<p>
						<a href="<?php echo esc_attr(RestaurantBooking::admin_url( RestaurantBooking::PAGE_OPTIONS )); ?>"  class="btn btn-primary">
							<?php _e("New connection to Listae.com", 'restaurant-bookings'); ?>
						</a>
					</p>
				</div>
				<?php 
			}
        } 
        ?>
				
		<?php if ( RestaurantBooking::is_registered() ) { ?>
		
		<?php $this->admin_cta_visit(); ?>
		
		<form id="frm_rbk_options_page" method="post">
			<div class="card mb-5">
				<div class="card-header">
					<?php _e("Listae connection", 'restaurant-bookings'); ?>
				</div>
				<div class="card-body">
					<p class="card-text"><?php _e("Your website is connected to Listae.com.", 'restaurant-bookings'); ?></p>
					<?php $this->admin_tgl('chk_disconnect', 1, get_option("ae_access_token"),  __("Disconnect your website from Listae.com.", 'restaurant-bookings') ); ?>
				</div>
			</div>
					
			<div class="card mb-5">
                <div class="card-header">
					<?php _e("Skin", 'restaurant-bookings'); ?>
                </div>
				<div class="card-body">
					<p class="card-text"><?php _e("By default the Listae forms are shown with a clear color appearance, dark texts assuming clear backgrounds (black over white), if your website has dark backgrounds you can reverse this scheme by selecting a Dark appearance (white over black).", 'restaurant-bookings'); ?></p>
					<select name="sel_rb_skin" id="sel_rb_skin" class="custom-select col-md-2">
						<option value=""><?php _e("Light", 'restaurant-bookings'); ?></option>
						<option value="dark"<?php if (self::get_skin() == "dark") {?> selected<?php }?>><?php _e("Dark", 'restaurant-bookings'); ?></option>
					</select>
                </div>
			</div>
			
			<div class="card mb-5">
                <div class="card-header">
						<?php _e("Bootstrap", 'restaurant-bookings'); ?>
				</div>
				<div class="card-body">
                    <p class="card-text"><?php _e("We use the stylesheet and the Javascript libraries of Bootstrap 4, if your theme also uses them, errors may occur when they are added duplicated.", 'restaurant-bookings'); ?></p>
                    <p class="card-text"><?php $this->admin_tgl('chk_rb_exclude_bootstrap_css', 1, get_option("rb_exclude_bootstrap_css", "") == "1",  __("Do not include css style sheets from Bootstrap", 'restaurant-bookings') ); ?></p>
                    <p class="card-text"><?php $this->admin_tgl('chk_rb_exclude_bootstrap_js', 1, get_option("rb_exclude_bootstrap_js", "") == "1",  __("Do not include Bootstrap Javascript libraries", 'restaurant-bookings') ); ?></p>
                </div>
            </div>
            
            
			<div class="card mb-5">
                <div class="card-header">
						<?php _e("Convert shortcodes into blocks", 'restaurant-bookings'); ?>
				</div>
				<div class="card-body">
					<p class="card-text"><?php _e("Migrate old shortcodes to new gutenberg blocks. This process is irreversible and is only necessary if you ever used shortcodes.", 'restaurant-bookings'); ?></p>
					<select name="sel_sc_to_block_migration" id="sel_sc_to_block_migration" class="custom-select col-md-2">
						<option value=""><?php _e("No", 'restaurant-bookings'); ?></option>
						<option value="1"><?php _e("Yes", 'restaurant-bookings'); ?></option>
					</select>
				</div>
            </div>
		
			<?php do_action("rbk_option_page_options"); ?>
				
			<div class="mb-5">
				<?php wp_nonce_field("rbk_save_options", "rbk_save_options"); ?>
				<input type="submit" name="info_update" class="btn btn-terciary btn-lg btn-block" value="<?php _e("Update options", 'restaurant-bookings'); ?>" />
			</div>
				
		</form>
		
		<?php } ?>
		
		<?php $this->admin_cta_customize();?>	
		
		<?php $this->admin_support();?>
			
		<?php $this->admin_bottom();?>
			
		<?php
		
	}
	
	private function migrate_sc_to_block() {
		$pages = get_pages();
		
		foreach( $pages as $page ) {
			$this->migrate_content($page);
		}
		
		$posts = get_posts(array('numberposts' => -1));
		
		foreach( $posts as $post ) {
			$this->migrate_content($post);
		}
	}
	
	private function migrate_content($post) {
		$post_id = $post->ID;
		$content = $post->post_content;
		
		global $shortcode_tags;
		$original_shortcode_tags = $shortcode_tags;
		
		$shortcode_tags = RBKShortcodes::get_all_shortcodes();
		
		preg_match_all(  "/" . get_shortcode_regex() . "/", $content, $matches );
		if (is_array($matches) && count($matches) > 1 && is_array( $matches[2] ) ) {
			
			$content_change = false;
			
			$gut_block_tmp = '<!-- wp:bthemattic/rbk-gutenberg {ATTS} -->' . "\n" .
					'<div class="wp-block-bthemattic-rbk-gutenberg"></div>' . "\n" .
		 			'<!-- /wp:bthemattic/rbk-gutenberg -->';
			
			/*<!-- wp:bthemattic/rbk-gutenberg {"business_id":"babelia","shortcode_name":"ae-carte","content_id":"1937"} -->
			 <div class="wp-block-bthemattic-rbk-gutenberg"></div>
			 <!-- /wp:bthemattic/rbk-gutenberg -->*/
			
			foreach ($matches[2] as $index => $sc_tag) {
				$full_sc = $matches[0][$index];
				$new_content = false;
				$new_atts = array();
				
				$atts = shortcode_parse_atts($full_sc);
				
				if (count($atts) > 1) {
					if (isset($atts["id"])) {
						$new_atts["business_id"] = $atts["id"];
					}
				}
				
				$new_atts["shortcode_name"] = $sc_tag;
				
				switch ($sc_tag) {
					case RBKShortcodes::CARTE:
						if (isset($atts["carteid"])) {
							$new_atts["content_id"] = $atts["carteid"];
						}
					break;
					case RBKShortcodes::MENU:
						if (isset($atts["menuid"])) {
							$new_atts["content_id"] = $atts["menuid"];
						}
					break;					
					case RBKShortcodes::COUPON:
						if (isset($atts["couponid"])) {
							$new_atts["content_id"] = $atts["couponid"];
						}
					break;
					case RBKShortcodes::CATALOG_ITEM:
						if (isset($atts["catalogitemid"])) {
							$new_atts["content_id"] = $atts["catalogitemid"];
						}
					break;
					case RBKShortcodes::CARTE_GROUP:
					case RBKShortcodes::MENU_GROUP:
						if (isset($atts["groupid"])) {
							$new_atts["groupid"] = $atts["groupid"];
						}
					break;
				}
				
				if (isset($atts["booking"])) {
					$new_atts["booking"] = $atts["booking"] == "false" ? false : true;
				}
				
				if (isset($atts["takeaway"])) {
					$new_atts["takeaway"] = $atts["takeaway"] == "false" ? false : true;
				}
				
				if (isset($atts["delivery"])) {
					$new_atts["delivery"] = $atts["delivery"] == "false" ? false : true;
				}
				
				if (isset($atts["for_order"])) {
					$new_atts["for_order"] = $atts["for_order"] == "false" ? false : true;
				}
				
				if (isset($atts["allways_mobile"])) {
					$new_atts["allways_mobile"] = $atts["allways_mobile"] == "false" ? false : true;
				}
				
				$new_content = str_replace("{ATTS}", json_encode($new_atts), $gut_block_tmp);
				
				if (defined("WP_DEBUG") && WP_DEBUG) {
					echo "<b>Post/Page:$post_id - $full_sc</b><br/>";
					echo "<pre>";
					echo esc_html($new_content);
					echo "</pre>";
					echo "<hr/>";
				}
				
				if ($new_content !== false) {
					$content_change = true;
					$content = str_replace($full_sc, $new_content, $content);
				}
			}
			
			if ($content_change) {
				wp_update_post(array(
					"ID" => $post_id,
					"post_content" => $content
				));
			}
		}
		
		// Devolviendo shortcode_tags a su valor original
		$shortcode_tags = $original_shortcode_tags;
	}
}


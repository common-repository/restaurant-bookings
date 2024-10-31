<?php 
// Para evitar llamadas directas
defined("ABSPATH") or exit();

abstract class RBKWidget extends WP_Widget {
	private $cache = array();
	public $title;
	
	public function __construct( $id_base = false, $name, $widget_options = array(), $control_options = array() ) {
		parent::__construct($id_base, "æ: " . $name, $widget_options, $control_options);
		
		$this->title = $name;
		
		$current_cache = AECache::get_cache($this->get_cache_key());
		
		if (!is_array($current_cache)) {
			$this->cache = $current_cache;
			
			add_action( 'save_post', array($this, 'flush_widget_cache' ) );
			add_action( 'deleted_post', array($this, 'flush_widget_cache' ) );
			add_action( 'switch_theme', array($this, 'flush_widget_cache' ) );
		}
	}
	
	public function get_widget_title($instance) {
		if (isset($instance['title']) && !empty($instance['title'])) {
			$title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);
			if (!empty($title)) {
				return $title;
			}
		}
		
		return "";
	}
	
	public function flush_widget_cache() {
		AECache::remove_cache($this->get_cache_key());
	}
	
	public function get_cache($args) {
		if ( isset( $args['widget_id'] ) && isset( $this->cache[$args['widget_id']] ) ) {
			return $this->cache[$args['widget_id']];
		}
		
		return null;
	}
	
	public function set_cache($args, $html) {
		if ( isset( $args['widget_id'] ) ) {
			$this->cache[$args['widget_id']] = $html;
			
			AECache::add_cache($this->get_cache_key(), $this->cache);
		}
	}
	
	function update( $new_instance, $old_instance ) {
		$new_instance = parent::update($new_instance, $old_instance);
		
		$new_instance['business_id'] = strip_tags($new_instance['business_id']);
		$new_instance['title'] = strip_tags($new_instance['title']);
	
		return $new_instance;
	}
	
	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', "business_id" => '') );
	
		$this->field_select_business($instance['business_id']);
		$this->field_title($instance['title']);
	}
	
	protected function field_select_business($current_business_id = "") {
		$search = aeAPIS::search_my_restaurants();
		
		if (!aeAPIS::is_error()) {
			?>
			<p>
			<label for="<?php echo $this->get_field_id('business_id'); ?>"><?php esc_html_e("Restaurant", 'restaurant-bookings'); ?></label>
			<?php if ($search->getTotal() > 0) { ?>
				<select class="sel-business widefat" name="<?php echo $this->get_field_name('business_id'); ?>" id="<?php echo $this->get_field_id('business_id'); ?>" style="max-width : 100%;">
					<?php foreach ($search->getRestaurantInfo() as $r) { ?>
						<option value="<?php echo esc_attr($r->getUrl()); ?>"
							<?php selected($r->getUrl(), $current_business_id); ?>
							data-takeaway="<?php echo $r->getTakeaway() ? "true" : "false"; ?>"
							data-delivery="<?php echo $r->getDelivery() ? "true" : "false"; ?>"  
							data-bookings="<?php echo $r->getBookingsR2() ? "true" : "false"; ?>" 
							data-contact="<?php echo $r->getEmailContactEnabled() ? "true" : "false"; ?>" 
							data-opening="<?php echo $r->getOpening() == null ? "false" : "true"; ?>" 
							data-map="<?php echo $r->getMap() ? "true" : "false"; ?>" 
							data-cartes="<?php echo $r->getCartes(); ?>" 
							data-menus="<?php echo $r->getMenus(); ?>">
							<?php echo esc_html($r->getName() . " > " . $r->getAddress()); ?>
						</option>
					<?php } ?>
				</select>
			<?php } else { ?>
				<small><?php esc_html_e("It seems that you do not have any restaurant linked to your Listae account, add a restaurant to use this functionality.", 'restaurant-bookings'); ?></small>
			<?php } ?>
			</p>
			<?php 
		}
	}
	
	protected function field_title($current_title = "") {
		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php esc_html_e( 'Title:', 'restaurant-bookings' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($current_title); ?>" /></p>
		<?php 
	}
	
	protected function field_comments($current_comments = "") {
		?>
		<p><label for="<?php echo $this->get_field_id('comments'); ?>"><?php esc_html_e( 'Text:', 'restaurant-bookings' ); ?></label>
		<textarea class="widefat" rows="4" id="<?php echo $this->get_field_id('comments'); ?>" name="<?php echo $this->get_field_name('comments'); ?>"><?php echo esc_attr($current_comments); ?></textarea></p>
		<?php 
	}

	protected function field_checkbox($field_name, $current_value, $label) {
		$current_value = boolval( $current_value);
		?>
		<p>
			<input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id($field_name); ?>"
				name="<?php echo $this->get_field_name($field_name); ?>"
				value="1"<?php if ($current_value) {?> checked="checked"<?php } ?>>
			<label for="<?php echo $this->get_field_id($field_name); ?>">
				<?php echo esc_html($label);?>
			</label>
		</p>
		<?php 
	}
	
	private function get_cache_key() {
		return "wgt_" . $this->id_base . "[" . get_home_url() . "]";
	}
}

abstract class RBKWidgetWithPage extends RBKWidget {
	private $title_field_page = "";
	private $listae_field_page = false;
	private $google_field_page = false;
	
	public function __construct( $id_base = false, $name, $widget_options = array(), $control_options = array(), $rbk_options = array() ) {
		parent::__construct($id_base, $name, $widget_options, $control_options);
		
		$rbk_options = wp_parse_args($rbk_options, array(
			"title_field_page" => __( 'Target page:', 'restaurant-bookings' ),
			"listae_field_page" => false,
			"google_field_page" => false,
		));
		
		$this->title_field_page = $rbk_options["title_field_page"];
		$this->listae_field_page = $rbk_options["listae_field_page"];
		$this->google_field_page = $rbk_options["google_field_page"];
	}
	
	function update( $new_instance, $old_instance ) {
		$new_instance = parent::update($new_instance, $old_instance);
		
		$new_instance['page'] = strip_tags($new_instance['page']);
		
		return $new_instance;
	}
	
	protected function get_current_page_url($page, $restaurant_slug="") {
		if ( !empty($page) ) {
			return get_permalink($page);
		} elseif ($this->listae_field_page) {
			return "https://listae.me/informacion-restaurante/" . $restaurant_slug . "/?src=" . esc_url(get_bloginfo('url'));
		}
		
		return "";
	}
	
	protected function get_pages() {
		return get_pages();
	}
	
	protected static function get_pages_by_shortcode($shortcodes) {
		$content = Array();
		
		if (is_array($shortcodes)) {
			foreach ($shortcodes as $shortcode) {
				$content[] = "[" . $shortcode; // shortcode clasico
				$content[] = "\"shortcode_name\":\"$shortcode\""; // shortcode gutenberg
			}
		} else {
			$content[] = "[" . $shortcodes; // shortcode clasico
			$content[] = "\"shortcode_name\":\"$shortcodes\""; // shortcode gutenberg
		}
		
		return self::get_pages_by_content($content);
	}
	
	protected static function get_pages_by_content($content) {
		global $wpdb;
		// "shortcode_name":"ae-opening"
		$query = "SELECT ID, post_title FROM $wpdb->posts WHERE post_type = 'page' AND post_status = 'publish' AND ";
		
		if (is_array($content)) {
			$query .= "(";
			$i = 0;
			foreach ($content as $content_part) {
				if ($content_part) {
					if ($i++ > 0) $query .= " OR ";
					$query .= "post_content LIKE '%" . esc_sql($content_part) . "%'";
				}
			}
			$query .= ")";
		} else {
			$query .= "post_content LIKE '%" . esc_sql($content) . "%'";
		}
		// 
		
		$query .= ";";
		$pages = $wpdb->get_results($query);
	
		return !is_array($pages) || count($pages) == 0 ?
		array() : $pages;
	}
	
	protected function field_page($current_page = "", $field_id="page", $label="", $items=Array()) {
		$label = empty($label) ? $this->title_field_page : $label;
		$items = empty($items) ? $this->get_pages() : $items;
		
		?>
		<p><label for="<?php echo $this->get_field_id($field_id); ?>"><?php echo esc_html($label); ?></label>
		<select class="widefat" id="<?php echo $this->get_field_id($field_id); ?>" name="<?php echo $this->get_field_name($field_id); ?>">
			<?php 
			if ($this->listae_field_page) { 
				?><option value=""<?php if ( empty($current_page)) {?> selected="selected"<?php } ?>>Listae</option><?php 
			} else if ($this->google_field_page) {
				?><option value=""<?php if ( empty($current_page)) {?> selected="selected"<?php } ?>>Google</option><?php
			}
			
			foreach ($items as $page) {
				?><option value="<?php echo esc_attr($page->ID); ?>"<?php if ($page->ID == $current_page) {?> selected="selected"<?php } ?>><?php echo esc_html($page->post_title); ?></option><?php 
			} 
			?>
		</select></p>
		<?php 
	}
	
	function form( $instance ) {
		parent::form($instance);
		$instance = wp_parse_args( (array) $instance, array( 'page' => '') );
		
		$this->field_page($instance["page"]);
	}
}

abstract class RBKWidgetCatalog extends RBKWidgetWithPage {
	function __construct($id_base = false, $name, $widget_options = array(), $control_options = array(), $rbk_options = array()) {
		parent::__construct($id_base, $name, $widget_options, $control_options, $rbk_options);
	}
	
	function update( $new_instance, $old_instance ) {
		$new_instance = parent::update($new_instance, $old_instance);
		
		$new_instance['section'] = strip_tags($new_instance['section']);
		$new_instance['content'] = strip_tags($new_instance['content']);
		
		return $new_instance;
	}
	
	function form( $instance ) {
		// parent::form($instance);
		$instance = wp_parse_args( (array) $instance, array(
			'page' => '',
			'title' => '',
			'comments' => '',
			"business_id" => '',
			"section" => RBKShortcodes::CARTE_ALL,
			"content" => "",
		) );
		
		$this->field_title($instance['title']);
		$this->field_comments($instance['comments']);
		$this->field_ae_content_select($instance);
	}
	
	protected function field_ae_content_select($instance) {
		$section = $instance['section'];
		$content = $instance['content'];
		$business_id = $instance['business_id'];
		
		?>
		<div class="wrap-<?php echo esc_attr($this->id_base); ?>-admin">
			<?php 
			$this->field_page($instance["page"]);
			
			$this->field_select_business($instance['business_id']);
			
			$is_content_visible = !empty($business_id) && !empty($section) && 
				$section != RBKShortcodes::CARTE_ALL && 
				$section != RBKShortcodes::MENU_ALL;
			?>
			<p>
				<label for="<?php echo $this->get_field_id('section'); ?>">
					<?php esc_html_e("Content", 'restaurant-bookings'); ?>
				</label>
				<select class="sel-section widefat" name="<?php echo $this->get_field_name('section'); ?>" 
					id="<?php echo $this->get_field_id('section'); ?>">
					
					<option value="<?php echo RBKShortcodes::CARTE_ALL; ?>"
						<?php selected(RBKShortcodes::CARTE_ALL, $section); ?>>
							<?php esc_html_e("All the menus", 'restaurant-bookings'); ?></option>
					<option value="<?php echo RBKShortcodes::CARTE_GROUP; ?>"
						<?php selected(RBKShortcodes::CARTE_GROUP, $section); ?>>
							<?php esc_html_e("Carte group", 'restaurant-bookings'); ?></option>
					<option value="<?php echo RBKShortcodes::CARTE; ?>" 
						<?php selected(RBKShortcodes::CARTE, $section); ?>>
							<?php esc_html_e("Menu", 'restaurant-bookings'); ?></option>
					<option value="<?php echo RBKShortcodes::MENU_ALL; ?>"
						<?php selected(RBKShortcodes::MENU_ALL, $section); ?>>
							<?php esc_html_e("All the set menus", 'restaurant-bookings'); ?></option>
					<option value="<?php echo RBKShortcodes::MENU_GROUP; ?>"
						<?php selected(RBKShortcodes::MENU_GROUP, $section); ?>>
							<?php esc_html_e("Menu group", 'restaurant-bookings'); ?></option>
					<option value="<?php echo RBKShortcodes::MENU; ?>"
						<?php selected(RBKShortcodes::MENU, $section); ?>>
							<?php esc_html_e("Set Menu", 'restaurant-bookings'); ?></option>
					<option value="<?php echo RBKShortcodes::MENU_BOOKING; ?>"
						<?php selected(RBKShortcodes::MENU_BOOKING, $section); ?>>
							<?php esc_html_e("Set menus availables for reservation", 'restaurant-bookings'); ?></option>
				</select>
			</p>
			<p class="field-group field-group-1 wrap-content" style="display : <?php echo $is_content_visible ? "block" : "none"; ?>;">
				<label><span><?php _e("Element", 'restaurant-bookings'); ?></span></label>
				<select class="sel-content widefat" name="<?php echo $this->get_field_name('content'); ?>" id="<?php echo $this->get_field_id('content'); ?>">
					<?php 
					if ($is_content_visible) {
						foreach ($this->get_catalog_options($business_id, $section) as $content_id => $content_name) {
							if ($content_name instanceof stdClass) {
								$this->render_catalog_group_option($content_name, $content);
							} else {
								$this->render_catalog_option($content_id, $content_name, $content);
							}
						}
					}
					?>
				</select>
			</p>
		</div>
		<?php 
	}
	
	private function render_catalog_group_option($content_group, $current_value) {
		?>
		<optgroup label="<?php echo esc_attr($content_group->name); ?>" data-value="<?php echo esc_attr($content_group->id); ?>">
			<?php 
			foreach ($content_group->contents as $content_id => $content_label) {
				$this->render_catalog_option($content_id, $content_label, $current_value);
			}
			?>
		</optgroup>
		<?php 
	}
	
	private function render_catalog_option($value, $label, $current_value) {
		?>
		<option value="<?php echo esc_attr($value); ?>" <?php selected($value, $current_value); ?>>
			<?php echo esc_html($label); ?>
		</option>
		<?php 
	}
	
	private function get_catalog_options($business_id, $section) {
		$r = aeAPIS::get_restaurant(array("restaurant_id" => $business_id));
		if (aeAPIS::is_error()) {
			return [];
		}
		
		$contents = [];
		$content_with_groups = [];
		
		switch ($section) {
		case RBKShortcodes::CARTE_GROUP:
			foreach ($r->getCartes()->getCarte() as $carte) {
				$group_content = new stdClass();
				$group_content->id = $carte->getUrl();
				$group_content->name = AEI18n::__($carte->getName());
				$group_content->contents = [];
				foreach ($carte->getGroup() as $group) {
					$group_content->contents[$group->getUrl()] = AEI18n::__($group->getName());
				}
				$content_with_groups[] = $group_content;
			}
		break;
		case RBKShortcodes::CARTE:
			foreach ($r->getCartes()->getCarte() as $carte) {
				$contents[$carte->getUrl()] = AEI18n::__($carte->getName());
			}
		break;
		case RBKShortcodes::MENU_GROUP:
			foreach ($r->getMenus()->getMenu() as $menu_catalog) {
				foreach ($menu_catalog->getGroup() as $group) {
					$contents[$group->getUrl()] = AEI18n::__($group->getName());
				}
			}
		break;
		case RBKShortcodes::MENU:
			foreach ($r->getMenus()->getMenu() as $menu_catalog) {
				foreach ($menu_catalog->getGroup() as $group) {
					$group_content = new stdClass();
					$group_content->id = $group->getUrl();
					$group_content->name = AEI18n::__($group->getName());
					$group_content->contents = [];
					
					foreach ($group->getMenu() as $menu) {
						$group_content->contents[$menu->getUrl()] = AEI18n::__($menu->getName());
					}
					$content_with_groups[] = $group_content;
				}
			}
		break;
		case RBKShortcodes::MENU_BOOKING:
			foreach ($r->getMenus()->getMenu() as $menu_catalog) {
				foreach ($menu_catalog->getGroup() as $group) {
					$group_content = new stdClass();
					$group_content->id = $group->getUrl();
					$group_content->name = AEI18n::__($group->getName());
					$group_content->contents = [];
					
					foreach ($group->getMenu() as $menu) {
						if ($menu->getBooking()) {
							$group_content->contents[$menu->getUrl()] = AEI18n::__($menu->getName());
						}
					}
					
					if (!empty($group_content->contents)) {
						$content_with_groups[] = $group_content;
					}
				}
			}
		break;
		}
		
		return !empty($contents) ? $contents : $content_with_groups;
	}
	
	protected function get_pages() {
		return self::get_pages_by_shortcode(array(
			RBKShortcodes::CARTE_ALL,
			RBKShortcodes::CARTE,
			RBKShortcodes::CARTE_GROUP,
			RBKShortcodes::CATALOG_ITEM,
			RBKShortcodes::MENU_ALL,
			RBKShortcodes::MENU,
			RBKShortcodes::MENU_GROUP,
			RBKShortcodes::MENU_BOOKING,
		));
	}
}

class Widget_Listae_Catalog_Resume extends RBKWidgetCatalog {
	function __construct() {
		parent::__construct("restaurant_catalog_resume", __( 'Catalog summary', 'restaurant-bookings' ),  array(
			'classname' => 'aewidget widget_catalog_resume',
			'description' => __( "Mini-Widget for a catalog summary, a group or even a catalog item", 'restaurant-bookings' )
		), array(), array(
			"title_field_page" => __( 'Location of item detail:', 'restaurant-bookings' ),
			"listae_field_page" => false,
		));
	}
	
	function update( $new_instance, $old_instance ) {
		$new_instance = parent::update($new_instance, $old_instance);
		
		$new_instance['max_imgs'] = intval($new_instance['max_imgs']);
		
		return $new_instance;
	}
	
	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array(
			'page' => '',
			'title' => '',
			'max_imgs' => 4,
			'content_height' 		=> '5625',
			'comments' => '',
			"business_id" => '',
			"section" => RBKShortcodes::CARTE_ALL,
			"content" => "",
		) );
		
		$this->field_title($instance['title']);
		$this->field_comments($instance['comments']);
		?>
		<p>
			<label for="<?php echo $this->get_field_id('max_imgs'); ?>"><?php esc_html_e( 'Max. No. images:', 'restaurant-bookings' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('max_imgs'); ?>" name="<?php echo $this->get_field_name('max_imgs'); ?>" type="text" value="<?php echo intval($instance['max_imgs']); ?>" />
		</p>
		
		<p>
			<label for="<?php echo esc_attr($this->get_field_id("content_height")); ?>">
				<?php echo esc_html_e("Content height", 'restaurant-bookings'); ?>
			</label>
			<select id="<?php echo $this->get_field_id('content_height'); ?>" name="<?php echo $this->get_field_name('content_height'); ?>">	
				<option value="25" <?php selected('25', $instance["content_height"], true); ?>>25 %</option>
				<option value="333" <?php selected('333', $instance["content_height"], true); ?>>33 %</option>
				<option value="5625" <?php selected('5625', $instance["content_height"], true); ?>>56.25 %</option>
				<option value="75" <?php selected('75', $instance["content_height"], true); ?>>75 %</option>
				<option value="1" <?php selected('1', $instance["content_height"], true); ?>>100%</option>
			</select>
		</p>
		
		<?php 
		$this->field_ae_content_select($instance);
	}
	
	function widget( $args, $instance ) {
		if (!isset($instance["business_id"])) {
			return;
		}
		
		$cache = $this->get_cache($args);
		
		if ($cache != null) {
			echo $cache;
			return;
		}
		
		$s = $args["before_widget"];
		$max_img = isset($instance["max_imgs"]) && is_numeric($instance["max_imgs"]) ? intval($instance["max_imgs"]) : 4;
		
		$s .= '<div class="layout-widget-catalog-resume container-fluid media-left">';
		
			
			$s .= '<div class="layout-catalog-resume-content row justify-content-center">';
			
				$images = self::get_images(
					$instance["section"], $instance["business_id"], $instance["content"]
				);
				
				if (!empty($images)) {
					$s .= '<div class="col-10 col-sm-6 catalog-resume-media">';
						
					$s .= '<div class="catalog-resume-gallery e-h' . $instance["content_height"] . '">';
						
						foreach ($images as $i => $image) {
							if ($i + 1 > $max_img) {
								break;
							}
					
							$s .= '<div class="catalog-resume-image">';
								$s .= '<a href="' . esc_attr($image->url) . '" title="' . esc_attr($image->title) . '">';
									$s .= '<img  src="' . esc_attr($image->thumbnail) . '" alt="' . esc_attr($image->title) . '" />';
								$s .= '</a>';
							$s .= '</div>';
					
						}
						
						$s .= '</div>';
					$s .= '</div>';
				}
			
				$s .= '<div class="col-10 col-sm-6 col-lg-5 offset-lg-1 catalog-resume-txt">';
				$title = $this->get_widget_title($instance);
				if (!empty($title)) {
					$s .= '<h2 class="catalog-resume-title mb-4">' . wp_kses_post($title) . '</h2>';
				}
				
				$comments = $instance["comments"];
				if (!empty($comments)) {
					$s .= '<div class="catalog-resume-content mb-4 clear">';
					$s .= wpautop($comments);
					$s .= '</div>';
				}
				
				if ( isset($instance['page']) && !empty($instance['page']) ) {
					$page_url = get_permalink($instance['page']);
					$s .= '<a href="' . $page_url . '" class="btn btn-lg btn-primary">';
					$s .= esc_html(get_the_title($instance['page']));
					$s .= '</a>';
				}
				
				$s .= '</div>';
				
				
			$s .= '</div>';
			
		$s .= '</div>';
		
		
		$s .= $args["after_widget"];
		
		$this->set_cache($args, $s);
		echo $s;
	}
	
	/**
	 * @param \Listae\Client\Model\Catalog[] $catalogs
	 * @param stdClass[] &$images
	 * @return stdClass[]
	 */
	private static function add_images_from_catalogs($catalogs, &$images) {
		foreach ($catalogs as $catalog) {
			self::add_images_from_catalog($catalog, $images);
		}
		
		return $images;
	}
	
	/**
	 * @param \Listae\Client\Model\Catalog $catalog
	 * @param stdClass[] &$images
	 * @return stdClass[]
	 */
	private static function add_images_from_catalog($catalog, &$images) {
		foreach ($catalog->getGroup() as $group) {
			self::add_images_from_group($group, $images);
		}
		
		return $images;
	}
	
	/**
	 * @param \Listae\Client\Model\CatalogItemGroup $group
	 * @param stdClass[] &$images
	 * @return stdClass[]
	 */
	private static function add_images_from_group($group, &$images) {
		foreach ($group->getItem() as $item) {
			self::add_images_from_catalog_item($item, $images);
		}
		
		return $images;
	}
	
	/**
	 * @param \Listae\Client\Model\CatalogItem $catalog_item
	 * @param stdClass[] &$images
	 * @return stdClass[]
	 */
	private static function add_images_from_catalog_item($catalog_item, &$images) {
		if (!empty($catalog_item) && !empty($catalog_item->getThumbnailUrl()) && !empty($catalog_item->getImageUrl())) {
			$std = new stdClass();
			$std->thumbnail = $catalog_item->getThumbnailUrl();
			$std->url = $catalog_item->getImageUrl();
			$std->title = sprintf( __('Full size image of %s', 'restaurant-bookings', "restaurant-bookings"), AEI18n::__($catalog_item->getName()) );
			$images[] = $std;
		}
		
		return $images;
	}
	
	private static function get_images($section, $business_id, $content_id) {
		$images = array();
		switch ($section) {
		case RBKShortcodes::CARTE:
			$carte = aeAPIS::get_carte(array(
				"restaurant_id" => $business_id,
				"carte_id" => $content_id,
			));
			
			if (!aeAPIS::is_error()) {
				self::add_images_from_catalog($carte, $images);
			}
		break;
		case RBKShortcodes::CARTE_ALL:
		case RBKShortcodes::MENU_ALL:
			$restaurant = aeAPIS::get_restaurant(array(
				"restaurant_id" => $business_id
			));
			
			if (!aeAPIS::is_error()) {
				if ($section == RBKShortcodes::MENU_ALL) {
					self::add_images_from_catalogs($restaurant->getMenus()->getMenu(), $images);
				} else {
					self::add_images_from_catalogs($restaurant->getCartes()->getCarte(), $images);
				}
			}
		break;
		case RBKShortcodes::CARTE_GROUP:
		case RBKShortcodes::MENU_GROUP:
			$group = false;
			
			if ($section == RBKShortcodes::CARTE_GROUP) {
				$group = aeAPIS::get_carte_group(array(
					"restaurant_id" => $business_id,
					"group_id" => $content_id,
				));
			} else {
				$group = aeAPIS::get_menu_group(array(
					"restaurant_id" => $business_id,
					"group_id" => $content_id,
				));
			}
			
			if (!aeAPIS::is_error() && $group) {
				self::add_images_from_group($group, $images);
			}
		break;
		case RBKShortcodes::MENU:
			$menu = aeAPIS::get_menu(array(
				"restaurant_id" => $business_id,
				"menu_id" => $content_id,
			));
			if (!aeAPIS::is_error()) {
				self::add_images_from_catalog_item($menu, $images);
			}
		break;
		}
		
		return $images;
	}
}




class Widget_Online_Booking extends RBKWidgetWithPage {
	function __construct() {
		parent::__construct("restaurant_bookings", __( 'Bookings', 'restaurant-bookings' ),  array( 
			'classname' => 'aewidget widget_restaurant_bookings', 
			'description' => __( "Listae online booking widget", 'restaurant-bookings' )
		), array(), array(
			"title_field_page" => __( 'Booking form link:', 'restaurant-bookings' ),
			"listae_field_page" => true,
		));
	}
	
	function widget( $args, $instance ) {
		if (!isset($instance["business_id"])) {
			return;
		}
		
		wp_enqueue_script("jquery-ui-datepicker");

		$cache = $this->get_cache($args);
		
		if ($cache != null) {
			echo $cache;
			return;
		}
		
		$sc_args = array("id" => $instance["business_id"]);
		
		if ( isset($instance['page']) && !empty($instance['page']) ) {
			$sc_args["booking_form_url"] = get_permalink($instance['page']);
		}
		
		$title = $this->get_widget_title($instance);
		
		$sc_args["title"] = "";
		$sc_args["before_title"] = "";
		$sc_args["after_title"] = "";
		
		if (!empty($title)) {
			$sc_args["title"] = $title;
			
			if (isset($args["before_title"])) {
				$sc_args["before_title"] = $args["before_title"];
			}
			
			if (isset($args["after_title"])) {
				$sc_args["after_title"] = $args["after_title"];
			}
		}
		
		$sc_args["btn_label"] = isset($instance["btn_label"]) && $instance["btn_label"] != "" ? $instance["btn_label"] :  __("Reserve now", 'restaurant-bookings');

		$s = $args["before_widget"];
		
		$s .= RBKShortcodes::get_instance()->booking_widget($sc_args);
		
		$s .= $args["after_widget"];
		
		$this->set_cache($args, $s);
		echo $s;
	}
	
	function update( $new_instance, $old_instance ) {
		$new_instance = parent::update($new_instance, $old_instance);
		
		$new_instance['btn_label'] = strip_tags($new_instance['btn_label']);
		
		return $new_instance;
	}
	
	function form( $instance ) {
		parent::form($instance);
		$instance = wp_parse_args( (array) $instance, array( 'btn_label' => '') );
		
		$btn_label = isset($instance["btn_label"]) ? $instance["btn_label"] :  __("Reserve now", 'restaurant-bookings');

		?>
		<p><label for="<?php echo $this->get_field_id("btn_label"); ?>"><?php _e("Label button", 'restaurant-bookings'); ?></label>
		<input class="widefat" type="text" value="<?php echo esc_attr($btn_label); ?>" id="<?php echo $this->get_field_id('btn_label'); ?>" name="<?php echo $this->get_field_name('btn_label'); ?>" />
		<?php
	}
}

class Widget_Listae_Opening extends RBKWidgetWithPage {
	function __construct() {
		parent::__construct("restaurant_opening", __( 'Opening', 'restaurant-bookings' ), array( 
			'classname' => 'aewidget widget_restaurant_opening', 
			'description' => __( "Opening information widget", 'restaurant-bookings' ) 
		), array(), array(
			"title_field_page" => __( 'Link to Opening detail:', 'restaurant-bookings' ),
			"listae_field_page" => true,
		));
	}
	
	function widget( $args, $instance ) {
		if (!isset($instance["business_id"])) {
			return;
		}
		
		$instance = wp_parse_args($instance, array(
			"page" => ""
		));
		
		$cache = $this->get_cache($args);
		
		if ($cache != null) {
			echo $cache;
			return;
		}
		
		$r = aeAPIS::get_restaurant(array("restaurant_id" => $instance["business_id"]));
		
		if (!aeAPIS::is_error()) {
			$agenda = $r->getAgendas()->getOpening();
			
			$opening_detail_url = "";
			
			if (isset($instance["show_link"]) && boolval($instance["show_link"])) {
				$opening_detail_url = $this->get_current_page_url($instance['page'], $r->getUrl());
			}
			
			$s = $args["before_widget"];
			
			$title = $this->get_widget_title($instance);
			if (!empty($title)) {
				$s .= $args["before_title"];
				$s .= esc_html($title);
				$s .= $args["after_title"];
			}
			
			$s .= '<div class="biz-opening">';
			
			if ( $agenda->getTurns() != null) {
				$html_turns = RBKTemplateTags::get_instance()->get_turns_html(
					$r->getAgendas()->getOpening(),
					_x("D:", "Title for the days of the week in the time widget", 'restaurant-bookings')
				);
			}
			
			$s .= apply_filters("ae_widget_opening_turns_html", $html_turns, $r);
			
			$s .= '<p class="opening-toggle"><a href="JavaScript:void(0);" onclick="JavaScript:jQuery(this).parent().parent().toggleClass(\'aria-expanded\');jQuery(this).blur();"><span>' . __("Show days", 'restaurant-bookings') .'</span><span>' . __("Hide days", 'restaurant-bookings') . '</span> </a></p>';
			
			if ( !empty($opening_detail_url) ) {
				$s .= '<p class="opening-more"><a class="button btn btn-primary" href="' . esc_attr($opening_detail_url) . '">';
				$s .= esc_html__("Detailed timetable", 'restaurant-bookings');
				$s .= '</a></p>';
			}
			
			$s .= '</div>';
			
			$s .= $args["after_widget"];
			
			$this->set_cache($args, $s);
			
			echo $s;
		}
	}
	
	function update( $new_instance, $old_instance ) {
		$new_instance = parent::update($new_instance, $old_instance);
	
		$new_instance['show_link'] = boolval($new_instance['show_link']);
	
		return $new_instance;
	}
	
	protected function get_pages() {
		return self::get_pages_by_shortcode(RBKShortcodes::OPENING);
	}
	
	function form( $instance ) {
		parent::form($instance);
	
		$instance = wp_parse_args( (array) $instance, array(
			'show_link' => false,
		));
	
		$show_link = boolval( $instance['show_link'] );
	
		?>
		<p><input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('show_link'); ?>" 
			name="<?php echo $this->get_field_name('show_link'); ?>" 
			value="1"<?php if ($show_link) {?> checked="checked"<?php } ?>>
		<label for="<?php echo $this->get_field_id('show_link'); ?>"><?php esc_html_e("Show link to opening detail", 'restaurant-bookings');?></label></p>
		<?php 
	}
}

class Widget_Listae_Map extends RBKWidgetWithPage {
	
	function __construct() {
		parent::__construct("restaurant_map", __('Map', 'restaurant-bookings'), array( 
			'classname' => 'aewidget widget_restaurant_map', 
			'description' => __("Map, address, telephone and links of the business", 'restaurant-bookings') 
		), array(), array(
			"title_field_page" => __( 'Link to map detail:', 'restaurant-bookings' ),
			"listae_field_page" => false,
			"google_field_page" => true,
		));
	}
	
	function widget( $args, $instance ) {
		if (!isset($instance["business_id"])) {
			return;
		}
		
		$instance = wp_parse_args($instance, array(
			"page" => "",
			"show_social" => false,
			"show_group_btn" => false,
		));
		
		$cache = $this->get_cache($args);
		
		if ($cache != null) {
			echo $cache;
			return;
		}
		
		$sc_args = array("id" => $instance["business_id"]);
		
		$sc_args["map_url"] = $this->get_current_page_url($instance['page']);
		
		$sc_args['show_map'] = $instance["show_map"];
		$sc_args['show_address'] = $instance["show_address"];
		$sc_args['show_phones'] = $instance["show_phones"];
		$sc_args['show_social'] = $instance["show_social"];
		
		$sc_args['show_contact_btn'] = isset($instance["show_contact_btn"]) ? boolval($instance['show_contact_btn']) : false;
		if (isset($instance["contact_page"]) && !empty($instance["contact_page"]) && !wp_is_mobile()) {
			$sc_args['contact_url'] = get_permalink($instance["contact_page"]);
		} else {
			$sc_args['contact_url'] = AEUrl::get_listae_url(AE_URLS::FORM_CONTACT, array(
				"slug" => $instance["business_id"],
				"origin" => get_bloginfo("url"),
				"back" => get_bloginfo("url"),
			));
		}
		
		$sc_args['show_group_btn'] = $instance["show_group_btn"];
		if (isset($instance["group_page"]) && !empty($instance["group_page"]) && !wp_is_mobile()) {
			$sc_args['group_url'] = get_permalink($instance["group_page"]);
		} else {
			$sc_args['group_url'] = AEUrl::get_listae_url(AE_URLS::FORM_GROUP, array(
				"slug" => $instance["business_id"],
				"origin" => get_bloginfo("url"),
				"back" => get_bloginfo("url"),
			));
		}
		
		$s = $args["before_widget"];
		
		$title = $this->get_widget_title($instance);
		if (!empty($title)) {
			$s .= $args["before_title"];
			$s .= esc_html($title);
			$s .= $args["after_title"];
		}
		
		$s .= RBKShortcodes::get_instance()->map_widget($sc_args);
		$s .= $args["after_widget"];
		
		$this->set_cache($args, $s);
		
		echo $s;
	}
	
	function update( $new_instance, $old_instance ) {
		$new_instance = parent::update($new_instance, $old_instance);
		
		$new_instance['show_map'] = boolval($new_instance['show_map']);
		$new_instance['show_address'] = boolval($new_instance['show_address']);
		$new_instance['show_phones'] = boolval($new_instance['show_phones']);
		$new_instance['show_social'] = boolval($new_instance['show_social']);
		
		$new_instance['show_contact_btn'] = boolval($new_instance['show_contact_btn']);
		$new_instance['contact_page'] = strip_tags($new_instance['contact_page']);
		$new_instance['show_group_btn'] = boolval($new_instance['show_group_btn']);
		$new_instance['group_page'] = strip_tags($new_instance['group_page']);
		
		return $new_instance;
	}

	protected function get_pages() {
		return self::get_pages_by_shortcode(RBKShortcodes::MAP);
	}
	
	private function get_contactform_pages() {
		return self::get_pages_by_shortcode(RBKShortcodes::CONTACT_FORM);
	}

	private function get_groupform_pages() {
		return self::get_pages_by_shortcode(RBKShortcodes::GROUP_FORM);
	}
		
	function form( $instance ) {
		parent::form($instance);
		
		$instance = wp_parse_args( (array) $instance, array(
			'show_map' => true,
			'show_address' => false,
			'show_phones' => false,
			'show_social' => false,
			"show_contact_btn" => false,
			"contact_page" => "",
			"show_group_btn" => false,
			"group_page" => ""
		));
		
		$show_map = boolval( $instance['show_map'] );
		$show_address = boolval( $instance['show_address'] );
		$show_phones = boolval( $instance['show_phones'] );
		$show_social = boolval( $instance['show_social'] );
		
		$show_contact_btn = boolval( $instance['show_contact_btn'] );
		$show_group_btn = boolval( $instance['show_group_btn'] );
		
		?>
		<p><input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('show_map'); ?>" 
			name="<?php echo $this->get_field_name('show_map'); ?>" 
			value="1"<?php if ($show_map) {?> checked="checked"<?php } ?>>
		<label for="<?php echo $this->get_field_id('show_map'); ?>"><?php esc_html_e("Show map", 'restaurant-bookings');?></label></p>

		<p><input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('show_address'); ?>" 
			name="<?php echo $this->get_field_name('show_address'); ?>" 
			value="1"<?php if ($show_address) {?> checked="checked"<?php } ?>>
		<label for="<?php echo $this->get_field_id('show_address'); ?>"><?php esc_html_e("Show address", 'restaurant-bookings');?></label></p>
		
		<p><input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('show_phones'); ?>" 
			name="<?php echo $this->get_field_name('show_phones'); ?>" 
			value="1"<?php if ($show_phones) {?> checked="checked"<?php } ?>>
		<label for="<?php echo $this->get_field_id('show_phones'); ?>"><?php esc_html_e("Shot telephones", 'restaurant-bookings');?></label></p>
		 
		<p><input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('show_social'); ?>" 
			name="<?php echo $this->get_field_name('show_social'); ?>" 
			value="1"<?php if ($show_social) {?> checked="checked"<?php } ?>>
		<label for="<?php echo $this->get_field_id('show_social'); ?>"><?php esc_html_e("Show social links", 'restaurant-bookings');?></label></p>
		 
		 
		<p><input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('show_contact_btn'); ?>" 
			name="<?php echo $this->get_field_name('show_contact_btn'); ?>" 
			value="1"<?php if ($show_contact_btn) {?> checked="checked"<?php } ?>>
		<label for="<?php echo $this->get_field_id('show_contact_btn'); ?>"><?php esc_html_e("Show contact button", 'restaurant-bookings');?></label></p>
		
		<p><label for="<?php echo $this->get_field_id('contact_page'); ?>"><?php esc_html_e( 'Contact form link', 'restaurant-bookings' ); ?></label>
		<select class="widefat" id="<?php echo $this->get_field_id('contact_page'); ?>" name="<?php echo $this->get_field_name('contact_page'); ?>">
			<option value=""<?php if ("" == $instance["contact_page"]) {?> selected="selected"<?php } ?>>Listae</option>
			<?php 
			foreach ($this->get_contactform_pages() as $page) {
				?><option value="<?php echo esc_attr($page->ID); ?>"<?php if ($page->ID == $instance["contact_page"]) {?> selected="selected"<?php } ?>><?php echo esc_html($page->post_title); ?></option><?php 
			} 
			?>
		</select></p>
		
		<p><input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('show_group_btn'); ?>" 
			name="<?php echo $this->get_field_name('show_group_btn'); ?>" 
			value="1"<?php if ($show_group_btn) {?> checked="checked"<?php } ?>>
		<label for="<?php echo $this->get_field_id('show_group_btn'); ?>"><?php esc_html_e("Show group menu form link", 'restaurant-bookings');?></label></p>
		
		<p><label for="<?php echo $this->get_field_id('group_page'); ?>"><?php esc_html_e( 'Group menu form link:', 'restaurant-bookings' ); ?></label>
		<select class="widefat" id="<?php echo $this->get_field_id('group_page'); ?>" name="<?php echo $this->get_field_name('group_page'); ?>">
			<option value=""<?php if ("" == $instance["group_page"]) {?> selected="selected"<?php } ?>>Listae</option>
			<?php 
			foreach ($this->get_groupform_pages() as $page) {
				?><option value="<?php echo esc_attr($page->ID); ?>"<?php if ($page->ID == $instance["group_page"]) {?> selected="selected"<?php } ?>><?php echo esc_html($page->post_title); ?></option><?php 
			} 
			?>
		</select></p>
		<?php 
	}
}

class Widget_Listae_Reviews extends RBKWidgetWithPage {
	function __construct() {
		parent::__construct("restaurant_reviews", __( 'Reviews', 'restaurant-bookings' ),  array( 
			'classname' => 'aewidget widget_restaurant_reviews', 
			'description' => __( "Reviews of the business", 'restaurant-bookings' ) 
		), array(), array(
			"title_field_page" => __( 'Link to reviews page:', 'restaurant-bookings' ),
			"listae_field_page" => true,
		));
	}
	
	function widget( $args, $instance ) {
		if (!isset($instance["business_id"])) {
			return;
		}
		
		$cache = $this->get_cache($args);
		
		if ($cache != null) {
			echo $cache;
			return;
		}
		
		$r = aeAPIS::get_restaurant(array("restaurant_id" => $instance["business_id"]));
		
		if (!aeAPIS::is_error()) {
			$show_more_reviews = isset($instance["show_link_more_reviews"]) ?
				boolval($instance["show_link_more_reviews"]) : false;
			
			$show_add_review = isset($instance["show_link_add_review"]) ?
				boolval($instance["show_link_add_review"]) : false;
			
			$show_first_review = isset($instance["show_first_review"]) && $r->getReviews() != null ? 
				boolval($instance["show_first_review"]) : false;
			
			$s = $args["before_widget"];
			
			$title = $this->get_widget_title($instance);
			if (!empty($title)) {
				$s .= $args["before_title"];
				$s .= esc_html($title);
				$s .= $args["after_title"];
			}
			
			$s .= '<div class="reviews-summary">';
			if ($r->getReviews() != null && $r->getReviews()->getCount() > 0) {
				$s .= RBKTemplateTags::get_instance()->get_html_rating_aggregate($r->getReviews()->getStats()->getGeneral(), $r, __("General", 'restaurant-bookings'));
				$s .= RBKTemplateTags::get_instance()->get_html_rating_aggregate_no_meta($r->getReviews()->getStats()->getService(), __("Service", 'restaurant-bookings'));
				$s .= RBKTemplateTags::get_instance()->get_html_rating_aggregate_no_meta($r->getReviews()->getStats()->getFood(), __("Food", 'restaurant-bookings'));
				$s .= RBKTemplateTags::get_instance()->get_html_rating_aggregate_no_meta($r->getReviews()->getStats()->getLocal(), __("Others", 'restaurant-bookings'));
			} else {
				$s .= '<p>' . esc_html__("At this time we have no reviews. If you know us, we encourage you to tell us your experience.", 'restaurant-bookings') . '</p>';
			}
			$s .= '</div>';
			
			if ($show_first_review && $r->getReviews() && count($r->getReviews()->getReview()) > 0) {
				$s .= '<div class="reviews-content">';
				$s .= '<h2>' . esc_html__("Last review", 'restaurant-bookings') . '</h2>';
				$s .= '<div class="reviews-list">';
				
				foreach ($r->getReviews()->getReview() as $review) {
					$s .= RBKTemplateTags::get_instance()->get_html_review($review, $r);
					break;
				}
				$s .= '</div>';
				$s .= '</div>';
			}
			
			if ($show_more_reviews && $r->getReviews() != null && $r->getReviews()->getCount() > 0) {
				$more_reviews_url = $this->get_current_page_url($instance["page"], $instance["business_id"]);
				$s .= '<p class="reviews-more"><a class="button btn btn-primary" href="' . esc_attr($more_reviews_url) . '">' . esc_html__("See reviews", 'restaurant-bookings') . '</a></p>';
			}
			
			if ($show_add_review) { 
				$add_review_url = isset($instance["review_page"]) && !empty($instance["review_page"]) ? 
					get_permalink($instance["review_page"]) : 
					AEUrl::get_listae_url(AE_URLS::FORM_REVIEW, array(
						"slug" => $instance["business_id"], 
						"origin" => AEUrl::get_full_url(),
						"back" => AEUrl::get_full_url(),
					)
				);
				
				$s .= '<p class="add-review">';
				$s .= '<a class="button btn btn-primary" href="' . esc_attr($add_review_url) . '">' . esc_html__("Review", 'restaurant-bookings') . '</a>';
				$s .= '</p>';
			}
			
			$s .= $args["after_widget"];
			
			$this->set_cache($args, $s);
			
			echo $s;
		}
	}

	function update( $new_instance, $old_instance ) {
		$new_instance = parent::update($new_instance, $old_instance);
		
		$new_instance['review_page'] = strip_tags($new_instance['review_page']);
		$new_instance['filter'] = strip_tags($new_instance['filter']);
		$new_instance['show_first_review'] = strip_tags($new_instance['show_first_review']);
		$new_instance['show_link_more_reviews'] = strip_tags($new_instance['show_link_more_reviews']);
		$new_instance['show_link_add_review'] = strip_tags($new_instance['show_link_add_review']);
		
		return $new_instance;
	}
	
	protected function get_pages() {
		return self::get_pages_by_shortcode(RBKShortcodes::REVIEWS);
	}
	
	private function get_reviewform_pages() {
		return self::get_pages_by_shortcode(RBKShortcodes::REVIEW_FORM);
	}
	
	
	function form( $instance ) {
		parent::form($instance);
		
		$instance = wp_parse_args( (array) $instance, array( "review_page" => null, 'show_first_review' => true, 'show_link_more_reviews' => false, 'show_link_add_review' => false ) );
		
		$show_first_review = boolval( $instance['show_first_review'] );
		$show_link_more_reviews = boolval( $instance['show_link_more_reviews'] );
		$show_link_add_review = boolval( $instance['show_link_add_review'] );
		
		?>
		<p><label for="<?php echo $this->get_field_id('review_page'); ?>"><?php esc_html_e( 'Link to review form:', 'restaurant-bookings' ); ?></label>
		<select class="widefat" id="<?php echo $this->get_field_id('review_page'); ?>" name="<?php echo $this->get_field_name('review_page'); ?>">
			<option value=""<?php if ( empty($instance["review_page"])) {?> selected="selected"<?php } ?>>Listae</option>
			<?php 
			foreach ($this->get_reviewform_pages() as $page) {
				?><option value="<?php echo esc_attr($page->ID); ?>"<?php if ($page->ID == $instance["review_page"]) {?> selected="selected"<?php } ?>><?php echo esc_html($page->post_title); ?></option><?php 
			} 
			?>
		</select></p>
		
		<p><label for="<?php echo $this->get_field_id('filter'); ?>"><?php esc_html_e( 'Filter:', 'restaurant-bookings' ); ?></label>
		<select class="widefat" id="<?php echo $this->get_field_id('filter'); ?>" name="<?php echo $this->get_field_name('filter'); ?>">
			<option value="ALL"<?php if (isset($instance["filter"]) && "ALL" == $instance["filter"]) {?> selected="selected"<?php } ?>><?php esc_html_e("All",  'restaurant-bookings'); ?></option>
			<option value="BOOKING"<?php if (isset($instance["filter"]) && "BOOKING" == $instance["filter"]) {?> selected="selected"<?php } ?>><?php esc_html_e("Bookings",  'restaurant-bookings'); ?></option>
			<option value="OTHER"<?php if (isset($instance["filter"]) && "OTHER" == $instance["filter"]) {?> selected="selected"<?php } ?>><?php esc_html_e("Web",  'restaurant-bookings'); ?></option>
		</select></p>
		
		<p><input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('show_first_review'); ?>" 
			name="<?php echo $this->get_field_name('show_first_review'); ?>" 
			value="1"<?php if ($show_first_review) {?> checked="checked"<?php } ?>>
		<label for="<?php echo $this->get_field_id('show_first_review'); ?>"><?php esc_html_e("Show last review", 'restaurant-bookings');?></label></p>
		
		<p><input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('show_link_more_reviews'); ?>" 
			name="<?php echo $this->get_field_name('show_link_more_reviews'); ?>" 
			value="1"<?php if ($show_link_more_reviews) {?> checked="checked"<?php } ?>>
		<label for="<?php echo $this->get_field_id('show_link_more_reviews'); ?>"><?php esc_html_e("Show link to reviews", 'restaurant-bookings' );?></label></p>
		
		<p><input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('show_link_add_review'); ?>" 
			name="<?php echo $this->get_field_name('show_link_add_review'); ?>" 
			value="1"<?php if ($show_link_add_review) {?> checked="checked"<?php } ?>>
		<label for="<?php echo $this->get_field_id('show_link_add_review'); ?>"><?php esc_html_e("Show lint to review", 'restaurant-bookings');?></label></p>
		<?php 
	}
}

class Widget_Order_Cart extends RBKWidgetWithPage {
	function __construct() {
		parent::__construct("widget_rbk_order", __( 'Orders: shopping cart', 'restaurant-bookings' ),  array(
			'classname' => 'aewidget widget_rbk_order',
			'description' => __( "Shopping cart for orders", 'restaurant-bookings' )
		), array(), array(
			"title_field_page" => __( 'Link to order form:', 'restaurant-bookings' ),
			"listae_field_page" => true,
		));
	}
	
	function widget( $args, $instance ) {
		$instance = wp_parse_args( $instance, apply_filters("ae_get_order_cfg_defaults", array(
			"restaurant_id"	=> null
		)) );
		
		if ($instance["business_id"] == null) {
			return;
		}
		
		$order_cfg = aeAPIS::get_order_cfg(array(
			"restaurant_id" => $instance["business_id"]
		));
		
		if (!aeAPIS::is_error()) {
			if (empty($order_cfg) || (empty($order_cfg->getDelivery()) || !$order_cfg->getDelivery()->getEnabled()) &&
				(empty($order_cfg->getTakeaway()) || !$order_cfg->getTakeaway()->getEnabled()) && 
				empty($order_cfg->getBooking())) {
					
				echo $args["before_widget"];
				echo '<div class="alert alert-danger" role="alert">';
				_e("The online order system is not activated.", 'restaurant-bookings');
				echo '</div>';
				echo $args["after_widget"];
				
				return;
			}
			
			$s = $args["before_widget"];
			
			$s .= RBKShortcodes::get_instance()->order_cart(array(
				"id" => $instance["business_id"],
				"booking" => isset($instance["show_booking"]) && $instance["show_booking"],
				"takeaway" => isset($instance["show_takeaway"]) && $instance["show_takeaway"],
				"delivery" => isset($instance["show_delivery"]) && $instance["show_delivery"],
				"allways_mobile" => isset($instance["allways_mobile"]) && $instance["allways_mobile"],
				"action_page" => isset($instance['page']) ? $instance['page'] : false,
				"title" => isset($instance['title']) ? $instance['title'] : "",
				"before_title" => isset($args['before_title']) ? $args['before_title'] : "",
				"after_title" => isset($args['after_title']) ? $args['after_title'] : "",
				"no_google_places" => isset($instance["no_google_places"]) && $instance["no_google_places"],
				"_data" => $order_cfg,
			));
			
			$s .= $args["after_widget"];
			
			echo $s;
		}
	}
	
	public function update( $new_instance, $old_instance ) {
		$new_instance = parent::update($new_instance, $old_instance);
		
		$new_instance['show_booking'] = boolval($new_instance['show_booking']);
		$new_instance['show_takeaway'] = boolval($new_instance['show_takeaway']);
		$new_instance['show_delivery'] = boolval($new_instance['show_delivery']);
		$new_instance['allways_mobile'] = boolval($new_instance['allways_mobile']);
		$new_instance['opening_page'] = strip_tags($new_instance['opening_page']);
		$new_instance['no_google_places'] = boolval($new_instance['no_google_places']);
		
		return $new_instance;
	}
	
	public function form( $instance ) {
		parent::form($instance);
		
		$instance = wp_parse_args( (array) $instance, array(
			'show_booking' => false,
			'show_takeaway' => false,
			'show_delivery' => false,
			'allways_mobile' => false,
			'no_google_places' => false,
		));
		
		$this->field_checkbox('show_booking', $instance['show_booking'], __("Show bookings options", 'restaurant-bookings'));
		$this->field_checkbox('show_takeaway', $instance['show_takeaway'], __("Show takeaway options", 'restaurant-bookings'));
		$this->field_checkbox('show_delivery', $instance['show_delivery'], __("Show delivery options", 'restaurant-bookings'));
		$this->field_checkbox('allways_mobile', $instance['allways_mobile'], __("Always fixed on the bottom", 'restaurant-bookings'));
		$this->field_checkbox('no_google_places', $instance['no_google_places'], __("Disable place search", 'restaurant-bookings'));
	}

	protected function get_pages() {
		return self::get_pages_by_shortcode(RBKShortcodes::ORDER_FORM);
	}
	
	private function get_modal_html($id, $title="", $label_ok="Ok", $label_cancel="Cancel", $body="", $extra_css="") {
		$s  = '<!-- Modal -->';
		
		$s .= '<div class="modal fade" id="' . $id . '" tabindex="-1" role="dialog" aria-labelledby="' . $id . 'Title" aria-hidden="true"';
		if ($label_cancel == "") {
			$s .= ' data-backdrop="static" data-keyboard="false"';
		}
		$s .= '>';
		
		$s .= '<div class="modal-dialog ' . esc_attr($extra_css) . '" role="document">';
		$s .= '<div class="modal-content">';
		$s .= '<div class="modal-header">';
		$s .= '<h5 class="modal-title" id="' . $id . 'Title">' . esc_html($title) . '</h5>';
		if ($label_cancel != "") {
			$s .= '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
			$s .= '<span aria-hidden="true">&times;</span>';
			$s .= '</button>';
		}
		$s .= '</div>';
		$s .= '<div class="modal-body">' . $body . '</div>';
		$s .= '<div class="modal-footer">';
		
		if ($label_cancel != "") {
			$s .= '<button type="button" class="btn btn-secondary" data-dismiss="modal">' . esc_html($label_cancel) . '</button>';
		}
		
		if ($label_ok != "") {
			$s .= '<button type="button" class="btn btn-primary">' . esc_html($label_ok) . '</button>';
		}
		
		$s .= '</div>';
		$s .= '</div>';
		$s .= '</div>';
		$s .= '</div>';
		
		return $s;
	}
}

class Widget_Order_Nav extends RBKWidget {
	function __construct() {
		parent::__construct("widget_rbk_order_nav", __( 'Orders: group navigation', 'restaurant-bookings' ),  array(
			'classname' => 'aewidget order-widget-nav',
			'description' => __( "Navigation for orders", 'restaurant-bookings' )
		), array());
		
		$widget_ops = array('classname' => 'widget_rbk_order_nav', 'description' => __( 'Orders navigation menu', 'restaurant-bookings') );
		// Instantiate the parent object
		parent::__construct( false, __('Orders: group navigation', 'restaurant-bookings'), $widget_ops);
	}
	
	function widget( $args, $instance ) {
		if ($instance["business_id"] == null) {
			return;
		}
		
		$order_cfg = aeAPIS::get_order_cfg(array(
			"restaurant_id" => $instance["business_id"]
		));
		
		if (!aeAPIS::is_error()) {
			if (empty($order_cfg) || (empty($order_cfg->getDelivery()) || !$order_cfg->getDelivery()->getEnabled()) &&
				(empty($order_cfg->getTakeaway()) || !$order_cfg->getTakeaway()->getEnabled()) &&
				empty($order_cfg->getBooking())) {
				
				echo $args["before_widget"];
				echo '<div class="alert alert-danger" role="alert">';
				_e("The online order system is not activated.", 'restaurant-bookings');
				echo '</div>';
				echo $args["after_widget"];
				
				return;
			}
					
			$s = $args["before_widget"];
			
			$s .= RBKShortcodes::get_instance()->order_nav(array(
				"id" => $instance["business_id"],
				"title" => isset($instance['title']) ? $instance['title'] : "",
				"before_title" => isset($args['before_title']) ? $args['before_title'] : "",
				"after_title" => isset($args['after_title']) ? $args['after_title'] : "",
				"_data" => $order_cfg,
			));
			
			$s .= $args["after_widget"];
			
			echo $s;
		}
	}
}

class Widget_Order_Header extends RBKWidget {
	function __construct() {
		parent::__construct("widget_rbk_order_header", __( 'Orders: header', 'restaurant-bookings' ),  array(
			'classname' => 'aewidget order-widget-nav',
			'description' => __( "Header for orders", 'restaurant-bookings' )
		), array());
		
		$widget_ops = array('classname' => 'widget_rbk_order_header', 'description' => __( 'Orders header info', 'restaurant-bookings') );
		// Instantiate the parent object
		parent::__construct( false, __('Orders: header', 'restaurant-bookings'), $widget_ops);
	}
	
	function widget( $args, $instance ) {
	    $args = apply_filters('ae_widget_order_header_args', $args);
	    
		if ($instance["business_id"] == null) {
			return;
		}
		
		$order_cfg = aeAPIS::get_order_cfg(array(
			"restaurant_id" => $instance["business_id"]
		));
		
		if (!aeAPIS::is_error()) {
			if (empty($order_cfg) || (empty($order_cfg->getDelivery()) || !$order_cfg->getDelivery()->getEnabled()) &&
				(empty($order_cfg->getTakeaway()) || !$order_cfg->getTakeaway()->getEnabled()) &&
				empty($order_cfg->getBooking())) {
				
				echo $args["before_widget"];
				echo '<div class="alert alert-danger" role="alert">';
				_e("The online order system is not activated.", 'restaurant-bookings');
				echo '</div>';
				echo $args["after_widget"];
				
				return;
			}
					
			$s = $args["before_widget"];
			
			$s .= RBKShortcodes::get_instance()->order_header(array(
				"id" => $instance["business_id"],
				"booking" => isset($instance["show_booking"]) && $instance["show_booking"],
				"takeaway" => isset($instance["show_takeaway"]) && $instance["show_takeaway"],
				"delivery" => isset($instance["show_delivery"]) && $instance["show_delivery"],
				"title" => isset($instance['title']) ? $instance['title'] : "",
				"before_title" => isset($args['before_title']) ? $args['before_title'] : "",
				"after_title" => isset($args['after_title']) ? $args['after_title'] : "",
				"_data" => $order_cfg,
			));
			
			$s .= $args["after_widget"];
			
			echo $s;
		}
	}
	
	public function update( $new_instance, $old_instance ) {
		$new_instance = parent::update($new_instance, $old_instance);
		
		$new_instance['show_booking'] = boolval($new_instance['show_booking']);
		$new_instance['show_takeaway'] = boolval($new_instance['show_takeaway']);
		$new_instance['show_delivery'] = boolval($new_instance['show_delivery']);
		
		return $new_instance;
	}
	
	public function form( $instance ) {
		parent::form($instance);
		
		$instance = wp_parse_args( (array) $instance, array(
			'show_booking' => false,
			'show_takeaway' => false,
			'show_delivery' => false,
		));
		
		$this->field_checkbox('show_booking', $instance['show_booking'], __("Show bookings options", 'restaurant-bookings'));
		$this->field_checkbox('show_takeaway', $instance['show_takeaway'], __("Show takeaway options", 'restaurant-bookings'));
		$this->field_checkbox('show_delivery', $instance['show_delivery'], __("Show delivery options", 'restaurant-bookings'));
	}
}



class Widget_Online_Booking_Slots extends RBKWidgetWithPage {
	private static $widget_index = 1;
	
	function __construct() {
		parent::__construct("restaurant_booking_slots", __( 'Bookings with Slots', 'restaurant-bookings' ),  array(
			'classname' => 'aewidget widget_restaurant_booking_slots',
			'description' => __( "Listae online booking with slots widget", 'restaurant-bookings' )
		), array(), array(
			"title_field_page" => __( 'Booking form link:', 'restaurant-bookings' ),
			"listae_field_page" => true,
		));
	}
	
	function widget( $args, $instance ) {
		if (!isset($instance["business_id"])) {
			return;
		}
		
		wp_enqueue_script("rbk-widgets");
		wp_enqueue_script("jquery-ui-datepicker");
		
		$cache = $this->get_cache($args);
		
		if ($cache != null) {
			echo $cache;
			return;
		}
		
		$sc_args = array("id" => $instance["business_id"]);
		
		if ( isset($instance['page']) && !empty($instance['page']) ) {
			$sc_args["booking_form_url"] = get_permalink($instance['page']);
		}
		
		$title = $this->get_widget_title($instance);
		
		$sc_args["title"] = "";
		$sc_args["before_title"] = "";
		$sc_args["after_title"] = "";
		
		if (!empty($title)) {
			$sc_args["title"] = $title;
			
			if (isset($args["before_title"])) {
				$sc_args["before_title"] = $args["before_title"];
			}
			
			if (isset($args["after_title"])) {
				$sc_args["after_title"] = $args["after_title"];
			}
		}
		
		$s = $args["before_widget"];
		$s .= RBKShortcodes::get_instance()->booking_slot_widget($sc_args);
		$s .= $args["after_widget"];
		
		$this->set_cache($args, $s);
		echo $s;
	}
	
	protected function get_pages() {
		return self::get_pages_by_shortcode(RBKShortcodes::BOOKING_FORM);
	}
}

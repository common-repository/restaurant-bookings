<?php 
// Para evitar llamadas directas
defined("ABSPATH") or exit();

$aeapi = aeAPIS::get_instance();

$search = aeAPIS::search_my_restaurants();

?>
<div id="rbktmce-backdrop" style="display: none"></div>
<div id="rbktmce-wrap" class="wp-core-ui" style="display: none">
<form id="rbktmce">
	<div id="rbktmce-modal-title">
		<?php _e('Insert / edit contents from Listae', 'restaurant-bookings'); ?>
		<button type="button" id="rbktmce-close"><span class="screen-reader-text"><?php _e('Close', 'restaurant-bookings'); ?></span></button>
 	</div>
	<?php if (!aeAPIS::is_error()) { ?>
		<?php wp_nonce_field( 'internal-linking', '_ajax_linking_nonce', false ); ?>
		
		<div id="rbktmce-selector">
			<div id="rbktmce-options">
				<p class="howto"><?php esc_html_e("Select business and content to publish and clic on insert.", 'restaurant-bookings'); ?></p>
				
				<?php if ($search->getTotal() > 0) { ?>
					<div class="field-group">
						<label><span><?php _e("Business", 'restaurant-bookings'); ?></span></label>
						<select name="rbktmce-business" id="rbktmce-business">
							<?php foreach ($search->getRestaurantInfo() as $r) { ?>
								<option value="<?php echo esc_attr($r->getUrl()); ?>"
									data-booking="<?php echo $r->getBookingsR2() ? "true" : "false"; ?>"
									data-takeaway="<?php echo $r->getTakeaway() ? "true" : "false"; ?>"
									data-delivery="<?php echo $r->getDelivery() ? "true" : "false"; ?>"
									data-coupon="<?php echo $r->getCoupon() ? "true" : "false"; ?>"    
									data-contact="<?php echo $r->getEmailContactEnabled() ? "true" : "false"; ?>" 
									data-opening="<?php echo $r->getOpening() == null ? "false" : "true"; ?>" 
									data-map="<?php echo $r->getMap() ? "true" : "false"; ?>" 
									data-cartes="<?php echo $r->getCartes(); ?>" 
									data-menus="<?php echo $r->getMenus(); ?>">
									<?php echo esc_html($r->getName() . " > " . $r->getAddress()); ?>
								</option>
							<?php } ?>
						</select>
					</div>
					
					<div class="field-group">
						<label><span><?php _e("Content", 'restaurant-bookings'); ?></span></label>
						<select name="rbktmce-section" id="rbktmce-section">
							<option value="<?php echo RBKShortcodes::BOOKING_FORM; ?>" selected="selected"><?php esc_html_e("Booking form", 'restaurant-bookings'); ?></option>
							<option value="<?php echo RBKShortcodes::CONTACT_FORM; ?>"><?php esc_html_e("Contact form", 'restaurant-bookings'); ?></option>
							<option value="<?php echo RBKShortcodes::GROUP_FORM; ?>"><?php esc_html_e("Group menu request form", 'restaurant-bookings'); ?></option>
							<option value="<?php echo RBKShortcodes::ORDER_CATALOG_FORM; ?>"><?php esc_html_e("Order catalog form", 'restaurant-bookings'); ?></option>
							<option value="<?php echo RBKShortcodes::ORDER_FORM; ?>"><?php esc_html_e("Order form", 'restaurant-bookings'); ?></option>
							<option value="<?php echo RBKShortcodes::ORDER_ALL; ?>"><?php esc_html_e("All the items available for orders", 'restaurant-bookings'); ?></option>
							<option value="<?php echo RBKShortcodes::ORDER_CART; ?>"><?php esc_html_e("Cart for orders", 'restaurant-bookings'); ?></option>
							<option value="<?php echo RBKShortcodes::ORDER_NAV; ?>"><?php esc_html_e("Orders category navigation", 'restaurant-bookings'); ?></option>
							<option value="<?php echo RBKShortcodes::REVIEW_FORM; ?>"><?php esc_html_e("Reviews form", 'restaurant-bookings'); ?></option>
							<option value="<?php echo RBKShortcodes::OPENING; ?>"><?php esc_html_e("Schedulle / Opening", 'restaurant-bookings'); ?></option>
							<option value="<?php echo RBKShortcodes::MAP; ?>"><?php esc_html_e("Map / Address", 'restaurant-bookings'); ?></option>
							<option value="<?php echo RBKShortcodes::CARTE; ?>"><?php esc_html_e("Menu", 'restaurant-bookings'); ?></option>
							<option value="<?php echo RBKShortcodes::CARTE_GROUP; ?>"><?php esc_html_e("Menu group", 'restaurant-bookings'); ?></option>
							<option value="<?php echo RBKShortcodes::CARTE_ALL; ?>"><?php esc_html_e("All the menus", 'restaurant-bookings'); ?></option>
							<option value="<?php echo RBKShortcodes::MENU; ?>"><?php esc_html_e("Set Menu", 'restaurant-bookings'); ?></option>
							<option value="<?php echo RBKShortcodes::MENU_GROUP; ?>"><?php esc_html_e("Menu group", 'restaurant-bookings'); ?></option>
							<option value="<?php echo RBKShortcodes::MENU_ALL; ?>"><?php esc_html_e("All the set menus", 'restaurant-bookings'); ?></option>
							<option value="<?php echo RBKShortcodes::MENU_BOOKING; ?>"><?php esc_html_e("Set menus availables for reservation", 'restaurant-bookings'); ?></option>
							<option value="<?php echo RBKShortcodes::COUPON; ?>"><?php esc_html_e("Coupons", 'restaurant-bookings'); ?></option>
							<option value="<?php echo RBKShortcodes::SERVICES; ?>"><?php esc_html_e("Services", 'restaurant-bookings'); ?></option>
							<option value="<?php echo RBKShortcodes::REVIEWS; ?>"><?php esc_html_e("Reviews", 'restaurant-bookings'); ?></option>
						</select>
					</div>
					
					<div class="field-group field-group-for-order" style="display : none; margin: 2px 0;">
						<label for="rbktmce-for-order"><span><?php _e("For orders", 'restaurant-bookings'); ?></span></label>
						<input type="checkbox" name="rbktmce-for-order" id="rbktmce-for-order" value="1" />
					</div>
					
					<div class="field-group field-group-booking" style="display : none; margin: 2px 0;">
						<label for="rbktmce-booking"><span><?php _e("Booking", 'restaurant-bookings'); ?></span></label>
						<input type="checkbox" name="rbktmce-booking" id="rbktmce-booking" value="1" />
					</div>
					
					<div class="field-group field-group-takeaway" style="display : none; margin-bottom: 2px;">
						<label for="rbktmce-takeaway"><span><?php _e("Takeaway", 'restaurant-bookings'); ?></span></label>
						<input type="checkbox" name="rbktmce-takeaway" id="rbktmce-takeaway" value="1" />
					</div>
					
					<div class="field-group field-group-delivery" style="display : none; margin-bottom: 2px;">
						<label for="rbktmce-delivery"><span><?php _e("Delivery", 'restaurant-bookings'); ?></span></label>
						<input type="checkbox" name="rbktmce-delivery" id="rbktmce-delivery" value="1" />
					</div>
					
					<div class="field-group field-group-allways-mobile" style="display : none; margin-bottom: 2px;">
						<label for="rbktmce-allways-mobile"><span><?php _e("Always fixed on the bottom", 'restaurant-bookings'); ?></span></label>
						<input type="checkbox" name="rbktmce-allways-mobile" id="rbktmce-allways-mobile" value="1" />
					</div>
					
					<div class="field-group field-group-1" id="wrap_rbktmce_content" style="display : none;">
						<label><span><?php _e("Element", 'restaurant-bookings'); ?></span></label>
						<select name="rbktmce-content" id="rbktmce-content"></select>
					</div>
				<?php } else { ?>
					<p><?php esc_html_e("It seems that you do not have any restaurant linked to your Listae account, add a restaurant to use this functionality.", 'restaurant-bookings'); ?></p>
				<?php } ?>
			</div>
		</div>
		
		<div class="submitbox">
			<div id="rbktmce-cancel">
				<a class="submitdelete deletion" href="#"><?php _e("Cancel", 'restaurant-bookings'); ?></a>
			</div>
			<div id="rbktmce-update">
				<input id="rbktmce-insert" type="button" value="<?php _e("Accept", 'restaurant-bookings'); ?>" class="button media-button button-primary button-large media-button-insert" />
			</div>
		</div>
	<?php } else {  ?>
		<div id="rbktmce-cancel" class="updated fade">
			<?php if (aeAPIS::is_fobidden()) { ?>
				<p><?php _e("403: Forbidden access to Listae.com.", 'restaurant-bookings'); ?></p>
			<?php } elseif (aeAPIS::is_not_found()) { ?>
				<p><?php _e("404: request not found", 'restaurant-bookings'); ?></p>
			<?php } ?>
			<p><?php _e("Oops, it seems we have some problem and connection to listae failed. Check your server connection as well as your login credentials and try again.", 'restaurant-bookings'); ?></p>
			
			<a class="submitdelete deletion" href="#"><?php _e("Cancel", 'restaurant-bookings'); ?></a>
		</div>
	<?php } ?>
</form>
</div>
<?php if ($search !== false) { ?>
<script type="text/javascript">
<!--
var RBKTMCE = {
	"URL" : "<?php echo RBKScripts::plugin_url("rbktmce/"); ?>",
	"AJAX_URL" 	: "<?php echo get_admin_url() . "/admin-ajax.php"; ?>",
	"ERROR_GET_MY_RESTAURANTS" : "<?php echo esc_js(__("It seems that we have not been able to get your business list", "restaurant-bookings"))?>",
	"ERROR_GET_CONTENT" : "<?php echo esc_js(__("Ops! We were unable to retrieve the data linked to the busines", "restaurant-bookings"))?>",
	"SC_MENU":	 		"<?php echo RBKShortcodes::MENU; ?>",
	"SC_MENU_GROUP": 	"<?php echo RBKShortcodes::MENU_GROUP; ?>",
	"SC_MENU_BOOKING": 	"<?php echo RBKShortcodes::MENU_BOOKING; ?>",
	"SC_MENU_ALL": 		"<?php echo RBKShortcodes::MENU_ALL; ?>",
	"SC_CARTE": 		"<?php echo RBKShortcodes::CARTE; ?>",
	"SC_CARTE_GROUP": 	"<?php echo RBKShortcodes::CARTE_GROUP; ?>",
	"SC_CARTE_ALL": 	"<?php echo RBKShortcodes::CARTE_ALL; ?>",
	"SC_COUPON": 		"<?php echo RBKShortcodes::COUPON; ?>",
	"SC_BOOKING_FORM": 	"<?php echo RBKShortcodes::BOOKING_FORM; ?>",
	"SC_BOOKING_WIDGET": "<?php echo RBKShortcodes::BOOKING_WIDGET; ?>",
	"SC_ORDER_CATALOG_FORM": 	"<?php echo RBKShortcodes::ORDER_CATALOG_FORM; ?>",
	"SC_ORDER_FORM": 	"<?php echo RBKShortcodes::ORDER_FORM; ?>",
	"SC_ORDER_ALL": 	"<?php echo RBKShortcodes::ORDER_ALL; ?>",
	"SC_ORDER_CART": 	"<?php echo RBKShortcodes::ORDER_CART; ?>",
	"SC_ORDER_NAV": 	"<?php echo RBKShortcodes::ORDER_NAV; ?>",
	"SC_CONTACT_FORM": 	"<?php echo RBKShortcodes::CONTACT_FORM; ?>",
	"SC_GROUP_FORM": 	"<?php echo RBKShortcodes::GROUP_FORM; ?>",
	"SC_REVIEW_FORM": 	"<?php echo RBKShortcodes::REVIEW_FORM; ?>",
	"SC_REVIEWS": 		"<?php echo RBKShortcodes::REVIEWS; ?>",
	"SC_OPENING": 		"<?php echo RBKShortcodes::OPENING; ?>",
	"SC_MAP": 			"<?php echo RBKShortcodes::MAP; ?>",
	"SC_MAP_WIDGET": 	"<?php echo RBKShortcodes::MAP_WIDGET; ?>",
	"SC_SERVICES": 		"<?php echo RBKShortcodes::SERVICES; ?>"
};
//-->
</script>
<?php } ?>

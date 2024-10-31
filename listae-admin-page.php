<?php 
// Para evitar llamadas directas
defined("ABSPATH") or exit();

class ListaeAdminPage {
	public function __construct() {
		if ( is_admin() ) {
			add_action( 'admin_enqueue_scripts', array ($this, "admin_enqueue_scripts") );
			add_filter( 'admin_body_class', array( $this, 'add_ae_pagestyles' ) );
			add_filter( 'admin_body_class', array( $this, 'admin_body_class' ) );
			
			add_action('wp_ajax_rbk_submit_help_message_ajax', array($this, 'submit_help_message_ajax'));
		}
	}
	
	
	public function admin_top(){
		?>
		<div class="layout-plugin">
		
				<nav class="navbar navbar-top navbar-light">
						<div class="container">
			
							<a class="navbar-brand" href="https://listae.com">
								<img class="icon-ae" src="<?php echo RBKScripts::plugin_url("img/ae-icon.png")?>">
								<span>Listae</span>
						</a>
						<span class="ae-register">
								<a href="https://listae.com/r2console" target="_blank"><?php _e("Registry", 'restaurant-bookings'); ?></a>
						</span>
				   
					</div><!-- .container -->
			</nav>
				
				<div class="container layout-main">
				<div class="row">
					<div class="col-12">
					
					
		<?php 
	}
	
	public function admin_bottom(){
		?>
		
					</div><!-- .col -->
				</div><!-- .row -->
			</div><!-- .container -->	   
				
			<div id="footer" class="footer">
				<span>&copy; <?php echo date("Y");?> Bthemattic BL</span><span class="sep"> | </span><a href="https://listae.com/r2console/users/eula.html">Aviso Legal</a>
			</div>
				
		</div><!-- .ae-admin-plugin -->			
		<?php 
	}
	
	public function admin_cta_visit(){
		?>
	
		<div class="card mb-5">
			<img class="card-img-top" src="<?php echo RBKScripts::plugin_url("img/ae-banner-buildings.png")?>" alt="Restaurant Bookings by Listae">
			<div class="card-body">
				<p class="card-text"><?php _e("Discover everything you can do with Listae to make your website an effective source of income.", 'restaurant-bookings'); ?></p>
				<p class="card-text"><a href="https://listae.com/?utm_source=rbkadm&utm_medium=ctavisit&utm_term=<?php echo get_bloginfo('url'); ?>" class="btn btn-primary btn-lg btn-block" target="_blank"><?php _e("Visit Listae", 'restaurant-bookings'); ?></a></p>
			</div>
		</div>	
		<?php 
	}
	
	public function admin_cta_customize(){
		?>
	
		<div class="card mb-5">
			<img class="card-img-top" src="<?php echo RBKScripts::plugin_url("img/ae-banner-services-success.jpg")?>" alt="">
			<div class="card-body">
				<p class="card-text"><?php _e("Make your website great by incorporating the advanced features of Listae, let us help you adjust the design of your theme so that they shine on any device.", 'restaurant-bookings'); ?></p>
				<p class="card-text"><a href="mailto:sales@listae.com?subject=<?php echo __("Information to improve my website ", 'restaurant-bookings'). '&body='. __("I want help to incorporate the advanced features of Listae into my website ", 'restaurant-bookings') . get_bloginfo('url'); ?>" class="btn btn-primary btn-lg btn-block"><?php _e("Ask for information about our success packages ", 'restaurant-bookings'); ?></a></p>
			</div>
		</div>	
		<?php 
	}
	
	public function admin_support(){
		?>
		<div class="card mb-5">
			<div class="card-header">
				<?php _e("Technical support request", 'restaurant-bookings'); ?>
			</div>
			<div class="card-body">
				<p><?php _e("Something is not working well? Do you have problems connecting to Listae? Contact our support team, they will try to answer you as soon as possible.", 'restaurant-bookings'); ?></p>
				
				<form id="frm_rbk_help" data-admin-ajax-url="<?php echo admin_url('admin-ajax.php'); ?>" data-ajax-nonce="<?php echo wp_create_nonce('rbk_submit_help_message_ajax'); ?>">
		   			<div class="form-group">
						<label for="help_role"><?php _e("I manage the website as", 'restaurant-bookings'); ?></label>
			   			<select name="help_role" id="help_role" class="custom-select">
								<option value=""><?php _e("Select an option", 'restaurant-bookings'); ?></option>
								<option value="Owner"><?php _e("Owner of the bussiness", 'restaurant-bookings'); ?></option>
								<option value="Agent"><?php _e("Web agent", 'restaurant-bookings'); ?></option>
								<option value="Other"><?php _e("Others", 'restaurant-bookings'); ?></option>
							</select>
						</div>	
		   			
					<div class="form-group">
						<label for="help_email"><?php _e("Your e-mail", 'restaurant-bookings'); ?></label>
						<input type="email" class="form-control" id="help_email" aria-describedby="helpEmail" value="<?php esc_html_e(get_bloginfo("admin_email")); ?>" placeholder="<?php _e("name@domain.com", 'restaurant-bookings'); ?>">
						<small id="emailHelp" class="form-text text-muted"><?php _e("We will answer to this e-mail, verify carefully that it is correct", 'restaurant-bookings'); ?></small>
					</div>
		   			
					<div class="form-group">
						<label for="help_txt"><?php _e("Message", 'restaurant-bookings'); ?></label>
						<textarea class="form-control" id="help_txt" rows="3" placeholder="<?php _e("Hi, I wanted to request information for...", 'restaurant-bookings'); ?>"></textarea>
					</div>
					
					<?php $this->admin_tgl('help_xtra_i', 1, 1,  __("Send additional information", 'restaurant-bookings') ); ?>
					<small class="form-text text-muted"><?php _e("The support team will need this information to help you efficiently.", 'restaurant-bookings'); ?>.</small>
					<p class="m-3">
					   	<?php 
					   	echo $this->get_site_details() .
							'<em>' . __("The options values ​​of the Restaurant Bookings will also be sent", 'restaurant-bookings') . '</em>';
						?>
					</p>
					
					<button type="submit" class="btn btn-primary btn-lg btn-block"><?php _e("Submit support request", 'restaurant-bookings'); ?></button>
				</form>
			</div>
		</div>			
		<?php
	}
	
	public function submit_help_message_ajax() {
		check_ajax_referer('rbk_submit_help_message_ajax');
		
		$email = sanitize_text_field($_POST['help_email']);
		
		if (!is_email($email)) {
			wp_send_json_error(__('Check that the email address is correct.', 'restaurant-bookings'));
		}
		
		$message = stripslashes(sanitize_text_field($_POST['help_txt']));
		$subject = 'RBK: Solicitud de soporte de ' . $email;
		$body = "<h3>Mensaje:</h3>";
		$body .= esc_html($message) . "<br/>";
		
		$body .= "<strong>Email:</strong> <code>" . esc_html($email) . "</code><br/>";
		$body .= "<strong>Rol:</strong> <code>" . esc_html(empty($_POST["help_role"]) ? 'Other' : $_POST["help_role"]) . "</code><br/>";
		
		if (!empty($_POST['help_info'])) {
			$body .= "<h3>Información del sitio web:</h3>";
			$body .= $this->get_site_details(false);
		}
		
		$headers = 'From: ' . $email . "\r\n" . 'Reply-To: ' . $email;
		$headers .= "MIME-Version: 1.0\n";
		$headers .= "Content-Type: " . get_bloginfo('html_type') . "; charset=\"". get_bloginfo('charset') . "\"\n";
		
		if (true === wp_mail('rbk@listae.com', $subject, $body, $headers)) {
			wp_send_json_success();
		} else {
			wp_send_json_error(__('Something is not working with your Wordpress mail sent . You will have to send us an email manually to rbk@listae.com', 'restaurant-bookings'));
		}
	}
	
	private function get_site_details($hide_token = true) {
		
		$theme = wp_get_theme();
		
		$help_curl = function_exists("curl_init") ? 'OK':'NO';
		$details = __("Wordpress version: ", 'restaurant-bookings') . '<code>' . get_bloginfo('version') . '</code><br>';
		$details .= __("RBK plugin version: ", 'restaurant-bookings'). '<code>' . RestaurantBooking::VERSION . '</code><br>';
		$details .= __("Listae Key: ", 'restaurant-bookings') . '<code>' . ($hide_token ? __("Hidden", 'restaurant-bookings') : get_option("ae_access_token")) . '</code><br>';
		$details .= __("Skin RBK: ", 'restaurant-bookings') . '<code>' . (empty(get_option("rb_skin")) ? "light" : get_option("rb_skin"))  . '</code><br>';
		$details .= __("Exclude Bootstrap JS: ", 'restaurant-bookings') . '<code>' . get_option("rb_exclude_bootstrap_js", "0") . '</code><br>';
		$details .= __("Exclude Bootstrap CSS: ", 'restaurant-bookings') . '<code>' . get_option("rb_exclude_bootstrap_css", "0") . '</code><br>';
		$details .= __("PHP version: ", 'restaurant-bookings'). '<code>' . PHP_VERSION . '</code><br>';
		$details .= __("CURL library: ", 'restaurant-bookings') . '<code>';
		if (function_exists("curl_init") && function_exists("curl_version")) {
			$curl_ver = curl_version();
			$details .= "OK (" . $curl_ver["version"] . ")";
			if (isset($curl_ver["ssl_version"])) {
				$details .= ', ssl_version (' . $curl_ver["ssl_version"] . ")";
			} else {
				$details .= ', NO SSL';
			}
			
		} else {
			$details .= "NO";
		}
		$details .= '</code><br>';
		$details .= __("HTTP extension: ", 'restaurant-bookings'). '<code>' . (function_exists("http_build_url") ? 'OK' : 'NO') . '</code><br>';
		
		$details .= __("Web site url: ", 'restaurant-bookings'). '<code>' .  get_bloginfo('url') . '</code><br>';
		$details .= __("Wordpress URL: ", 'restaurant-bookings'). '<code>' . get_bloginfo('wpurl') . '</code><br>';
		$details .= __("Theme: ", 'restaurant-bookings'). '<code>' . $theme->get('Name') . ' v' . $theme->get('Version') . '</code><br>';
		
		return $details;
	}
	
	public function admin_tgl($iname, $ival, $ichecked='', $xlabel=''){
		?>
		<div class="form-check form-check-toggle">
			<label class="tgl">
				<input type="checkbox" name="<?php echo $iname;?>" id="<?php echo $iname;?>" onclick="" onchange="" value="<?php echo $ival;?>" <?php if('' != $ichecked) echo 'checked'; ?> class="" data-id="" data-target="">
				<span class="tgl_body">
					<span class="tgl_switch"></span>
					<span class="tgl_track">
						<span class="tgl_bgd">
							<i class="mdi-check mdi " title="" aria-hidden="true"></i>
						</span>
						<span class="tgl_bgd tgl_bgd-negative">
							<i class="mdi-close mdi " title="" aria-hidden="true"></i>
						</span>
					</span>
				</span>
			</label>
			<?php 
			if('' != $xlabel) {
				echo '<label class="tgl-xtra" for="'. $iname .'">'. $xlabel .'</label>';
			}
			?>
		</div>
	   	<?php 		
	}
	
	public function help_html() {
		?>
		<?php 
	}
	
	public function admin_body_class( $admin_body_class = '' ) {
		$classes = explode( ' ', trim( $admin_body_class ) );
		
		$classes[] = RestaurantBooking::is_registered() ? 'ae-connected' : 'ae-disconnected';
		
		$admin_body_class = implode( ' ', array_unique( $classes ) );
		return " $admin_body_class ";
	}
	
	public function add_ae_pagestyles( $admin_body_class = '' ) {
		return $admin_body_class . ' ae-pagestyles ';
	}
	
	public function admin_enqueue_scripts($hook) {
		$min = (!defined("WP_DEBUG") || !WP_DEBUG) ? ".min" : "";
		
		if($hook == 'settings_page_rbk-options') {
		    wp_enqueue_style( 'bootstrap', RBKScripts::plugin_url('css/bootstrap.css'));
			wp_enqueue_style( 'rbk_admin_css', RBKScripts::plugin_url('css/restaurant-booking-admin.css'));
			wp_enqueue_script("popper", RBKScripts::plugin_url("js/third-party/popper.js"), array(), "1.12.9" );
			wp_enqueue_script("bootstrap", RBKScripts::plugin_url("js/third-party/bootstrap$min.js"), array("jquery", "popper"), "4.0.0");
		}
	}
	
	protected function localized_help_feedback($handle) {
		wp_localize_script( $handle, 'RBK_HELP_FEEDBACK', array(
			'MSG_VALIDATION_EMPTY' => __("All fields of the technical support request form are required.", 'restaurant-bookings'),
			'MSG_SUCCESSFUL' => __("Message sent!. Our agents will try to answer you as soon as possible.", 'restaurant-bookings'),
			'MSG_ERROR' => __("Oops! Something went wrong, please refresh the page and try again.", 'restaurant-bookings'),
		));
	} 
}
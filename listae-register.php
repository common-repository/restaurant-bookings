<?php
// Silence
defined("ABSPATH") or exit();

require_once 'listae-admin-page.php';

class ListaeRegister extends ListaeAdminPage {
	public function __construct() {
		parent::__construct();
		add_action("admin_menu", array( $this, 'add_registration_page' ));
		
		// Solo sacamos el mensaje si NO estamos en la pagina de registro
		if (!self::is_registration_page()) {
			add_action("admin_notices", array($this, "admin_notices"));
		}
	}
	
	public function admin_enqueue_scripts($hook) {
		parent::admin_enqueue_scripts($hook);
		
		wp_enqueue_script("rbk-register", RBKScripts::plugin_url("js/rbk-registration.js"), array("jquery"));
		
		wp_localize_script( "rbk-register", 'RBKREG', array(
			'AE_URL' => $url = AE_URL,
			'AE_FROM_URL' => AEAPI::get_host_url(),
			'TITLE_AUTH_WIN' => __("Log in to Listae", 'restaurant-bookings'),
		    'AUTH_ERROR' => __("Oops! We could not connect with Listae.com. Try the connection again. It is recommended that you are previously logged in at Listae.com with your username and password.", "restaurant-bookings"),
			"OK_LABEL" 	=> __("Accept and Connect", 'restaurant-bookings'),
			"CLOSE_LABEL" 	=> __("Close", 'restaurant-bookings'),
		));
		
		$this->localized_help_feedback("rbk-register");
	}
	
	public function admin_notices() {
	    ?>
		<div class="updated fade">
			<p><?php esc_html_e("We are almost finished.", 'restaurant-bookings'); ?></p>
			<p>
				<a href="<?php echo esc_attr(RestaurantBooking::admin_url( RestaurantBooking::PAGE_OPTIONS )); ?>">
					<?php esc_html_e("Connect to listae.com to complete the activation of Restaurant Bookings.", 'restaurant-bookings'); ?>
				</a>
			</p>
		</div>
		<?php 
	}
	
	public function add_registration_page() {
		add_options_page("Restaurant Bookings", "Restaurant Bookings", "delete_others_pages", RestaurantBooking::PAGE_OPTIONS, array($this, 'registration_page'));
	}
	
	private static function is_registration_page() {
		return isset($_GET["page"]) && $_GET["page"] == RestaurantBooking::PAGE_OPTIONS;
	}
	
	
	/**
	 * Pagina de registro
	 */
	public function registration_page() {
		?>
		
		<?php $this->admin_top();?>
			
		<?php 
			if (!empty($_POST) && wp_verify_nonce($_POST["rbk_save_register"], "rbk_save_register")) {
				update_option("ae_access_token", $_POST["txt_ae_access_token"]);
									
				$search = aeAPIS::search_my_restaurants();
					
				if (!aeAPIS::is_error()) { 
					?>
					<div class="alert alert-success" role="alert">
						<p><?php _e("!! Congratulations!! you are already connected with listae.com", 'restaurant-bookings'); ?></p>
						<p>
							<a href="<?php echo esc_attr(RestaurantBooking::admin_url( RestaurantBooking::PAGE_OPTIONS )); ?>"  class="btn btn-primary">
								<?php _e("Listae configuration options", 'restaurant-bookings'); ?>
							</a>
						</p>
					</div>
					<?php 
					if (!$search->getTotal() > 0) { ?>
					<div class="alert alert-danger" role="alert">
						<p><?php esc_html_e("It seems that there is no business linked to your Listae account, you must add one if you want that this plugin provides any functionality to your website.", 'restaurant-bookings'); ?></p>
					</div>
					<?php 
					}
				} else { 
					?>
					<div class="alert alert-danger" role="alert">
						<p><?php _e("Oops! We could not connect with Listae.com. Try the connection again. It is recommended that you are previously logged in at Listae.com with your username and password.", 'restaurant-bookings'); ?></p>
					</div>
					<?php 
					delete_option("ae_access_token");
				}
			}				
			
			if (false == get_option("ae_access_token", false)) { ?>
        			<form id="frm_ae_register" method="post">
        			<?php wp_nonce_field("rbk_save_register", "rbk_save_register"); ?>
        			
        			<div class="accordion" id="accordionRegister">
        			
        				<div class="card">
                        
                        <div class="card-header" id="headingConect">
                          <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseRegister" aria-expanded="true" aria-controls="collapseRegister">
                         	<?php _e("Connect Restaurant Bookings to Listae", 'restaurant-bookings'); ?>
                          </button>
    						</div>
    						
                         <div id="collapseRegister" class="collapse show" aria-labelledby="headingConect" data-parent="#accordionRegister">
                        	   <div class="card-body">
                        	   <img class="float-left mr-2 mb-4" src="<?php echo RBKScripts::plugin_url("img/icon-128x128.png")?>" alt="Restaurant Bookings by Listae" />
            					<h6><?php _e("Restaurant Bookings plugin requires an active account at Listae.", 'restaurant-bookings'); ?></h6>
            					<p class="card-text">
            						<?php _e("Your business have to be registered at Listae to be able to connect and get the information of your opening hours, catalog, contact information, etc.", 'restaurant-bookings'); ?>
            					</p>
    							<p class="card-text">
    								<?php 
    								    printf(
        								    esc_html__("%s before proceed to connection.", 'restaurant-bookings'),
        								    '<a href="https://listae.com/r2console/users/register.html" target="_blank">' . esc_html__("Complete the registration of your business", 'restaurant-bookings') . '</a>'
    								    );
    								?>
    							</p>
    							<input type="button" name="btn_ae_register" id="btn_ae_register" class="btn btn-primary btn-lg btn-block" value="<?php _e("Connect to Listae", 'restaurant-bookings'); ?>" />
                            </div>
                        </div>
                        
					</div>
					
					<div class="card mb-5">
						
						<div class="card-header" id="headingKey">
							<button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseKey" aria-expanded="false" aria-controls="collapseKey">
    								<?php _e("Api Key", 'restaurant-bookings'); ?>
    							</button>
                        </div>
						
						<div id="collapseKey" class="collapse" aria-labelledby="headingKey" data-parent="#accordionRegister">
							<div class="card-body">
                                <p class="card-text"><?php _e("In case you have not been able to complete the connection of your website to Listae, please include here the access code provided by our Technical Support.", 'restaurant-bookings'); ?></p>
                                <div class="input-group ">
    	                            <input type="text" class="form-control" id="txt_ae_access_token" name="txt_ae_access_token" placeholder="<?php _e("Listae access code", 'restaurant-bookings'); ?>">
                                    <div class="input-group-append">
                                    		<button class="btn btn-primary" type="button" id="button-addon2"><?php _e("Submit", 'restaurant-bookings'); ?></button>
    								</div>
                                </div>
    							</div>
    						</div>
    						
					</div>	
				
				</div>
				
        			</form>
				<?php 
			}
			?>
			
			<?php $this->admin_cta_visit();?>
			
			<?php $this->admin_support();?>
			
			<?php $this->admin_bottom();?>
			
		<?php 
		 
	}
	
}

global $listaeRegister;
$listaeRegister = new ListaeRegister();
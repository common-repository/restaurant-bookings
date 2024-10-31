<?php 
// Para evitar llamadas directas
defined("ABSPATH") or exit();

class RBKAdminAjax {
	public static function init() {
		add_action("wp_ajax_ae-get-my-restaurants", "RBKAdminAjax::wp_ajax_ae_get_my_restaurants");
		add_action("wp_ajax_ae-get-cartes", "RBKAdminAjax::wp_ajax_ae_get_cartes");
		add_action("wp_ajax_ae-get-carte-groups", "RBKAdminAjax::wp_ajax_ae_get_carte_groups");
		add_action("wp_ajax_ae-get-menus", "RBKAdminAjax::wp_ajax_ae_get_menus");
		add_action("wp_ajax_ae-get-menu-groups", "RBKAdminAjax::wp_ajax_ae_get_menu_groups");
		add_action("wp_ajax_ae-get-coupons", "RBKAdminAjax::wp_ajax_ae_get_coupons");
	}
	
	public static function wp_ajax_ae_get_my_restaurants() {
		if ( ! current_user_can( "edit_posts" ) ) {
			wp_die( -1 );
		}
		
		$search = aeAPIS::search_my_restaurants();
		
		if (!aeAPIS::is_error() && $search->getTotal() > 0) {
			$items = array();
			foreach ( $search->getRestaurantInfo() as $r ) {
				$item = new stdClass();
				$item->name = $r->getName();
				$item->address = $r->getAddress();
				$item->contact = $r->getEmailContactEnabled();
				$item->bookings = $r->getBookingsR2();
				$item->cartes = $r->getCartes();
				$item->menus = $r->getMenus();
				$item->map = $r->getMap();
				$item->opening = $r->getOpening() != null;
				
				$items[$r->getUrl()] = $item;
			}
			
			echo json_encode($items);
		} else {
			echo "[]";
		}
		wp_die();
	}
	
	public static function wp_ajax_ae_get_cartes() {
		if ( ! current_user_can( "edit_posts" ) ) {
			wp_die( -1 );
		}
		
		if ( !isset( $_GET['term'] ) ) {
			wp_die();
		}
		
		$s = wp_unslash( $_GET['term'] );
		$s = trim( $s );
		
		if ( strlen( $s ) < 1 ){
			wp_die();
		}
		
		$cartes = aeAPIS::get_cartes(array("restaurant_id" => $s));
		
		if (!aeAPIS::is_error() && !empty($cartes->getCarte())) {
			$items = array();
			foreach ( $cartes->getCarte() as $c ) {
				$item = new stdClass();
				$item->id = $c->getUrl();
				$item->value = AEI18n::__($c->getName());
				$item->label = AEI18n::__($c->getDescription());
				
				$items[] = $item;
			}
			
			echo json_encode($items);
		} else {
			echo "[]";
		}
		wp_die();
	}
	
	public static function wp_ajax_ae_get_carte_groups() {
		if ( ! current_user_can( "edit_posts" ) ) {
			wp_die( -1 );
		}
		
		if ( !isset( $_GET['term'] ) ) {
			wp_die();
		}
		
		$s = wp_unslash( $_GET['term'] );
		$s = trim( $s );
		
		if ( strlen( $s ) < 1 ){
			wp_die();
		}
		
		$cartes = aeAPIS::get_cartes(array("restaurant_id" => $s));
		
		if (!aeAPIS::is_error() && !empty($cartes->getCarte())) {
			$catalogs = array();
			foreach ( $cartes->getCarte() as $c ) {
				$catalog = new stdClass();
				$catalog->id = $c->getUrl();
				$catalog->value = AEI18n::__($c->getName());
				$catalog->label = AEI18n::__($c->getDescription());
				$catalog->groups = array();
				
				foreach ( $c->getGroup() as $g ) {
					$group = new stdClass();
					$group->id = $g->getUrl();
					$group->value = AEI18n::__($g->getName());
					$group->label = AEI18n::__($g->getDescription());
					
					$catalog->groups[] = $group;
				}
				
				$catalogs[] = $catalog;
			}
			
			echo json_encode($catalogs);
		} else {
			echo "[]";
		}
		wp_die();
	}
	
	public static function wp_ajax_ae_get_menus() {
		if ( ! current_user_can( "edit_posts" ) ) {
			wp_die( -1 );
		}
		
		if ( !isset( $_GET['term'] ) ) {
			wp_die();
		}
		
		$s = wp_unslash( $_GET['term'] );
		$s = trim( $s );
		
		if ( strlen( $s ) < 1 ){
			wp_die();
		}
		
		$menus = aeAPIS::get_menus(array("restaurant_id" => $s));
		
		if (!aeAPIS::is_error() && !empty($menus->getMenu())) {
			$groups = array();
			foreach ( $menus->getMenu() as $catalog ) {
				foreach ($catalog->getGroup() as $g) {
					$group = new stdClass();
					$group->id = $g->getUrl();
					$group->value = AEI18n::__($g->getName());
					$group->label = AEI18n::__($g->getDescription());
					$group->items = array();
					
					foreach ($g->getMenu() as $m) {
						$item = new stdClass();
						$item->id = $m->getUrl();
						$item->value = AEI18n::__($m->getName());
						$item->label = AEI18n::__($m->getComment());
						$group->items[] = $item;
					}
					$groups[] = $group;
				}
			}
			
			echo json_encode($groups);
		} else {
			echo "[]";
		}
		wp_die();
	}
	
	public static function wp_ajax_ae_get_menu_groups() {
		if ( ! current_user_can( "edit_posts" ) ) {
			wp_die( -1 );
		}
		
		if ( !isset( $_GET['term'] ) ) {
			wp_die();
		}
		
		$s = wp_unslash( $_GET['term'] );
		$s = trim( $s );
		
		if ( strlen( $s ) < 1 ){
			wp_die();
		}
		
		$menus = aeAPIS::get_menus(array("restaurant_id" => $s));
		
		if (!aeAPIS::is_error() && !empty($menus->getMenu())) {
			$groups = array();
			
			foreach ( $menus->getMenu() as $catalog ) {
				foreach ($catalog->getGroup() as $g) {
					$group = new stdClass();
					$group->id = $g->getUrl();
					$group->value = AEI18n::__($g->getName());
					$group->label = AEI18n::__($g->getDescription());
					
					$groups[] = $group;
				}
			}
			
			echo json_encode($groups);
		} else {
			echo "[]";
		}
		wp_die();
	}
	
	public static function wp_ajax_ae_get_coupons() {
		if ( ! current_user_can( "edit_posts" ) ) {
			wp_die( -1 );
		}
		
		if ( !isset( $_GET['term'] ) ) {
			wp_die();
		}
		
		$s = wp_unslash( $_GET['term'] );
		$s = trim( $s );
		
		if ( strlen( $s ) < 1 ){
			wp_die();
		}
		
        $coupons = aeAPIS::get_coupons(array("restaurant_id" => $s));
		
		if (!aeAPIS::is_error() && !empty($coupons->getCoupon())) {
			$cc = array();
			
			foreach ( $coupons->getCoupon() as $c ) {
				$coupon = new stdClass();
				$coupon->id = $c->getUrl();
				$coupon->value = AEI18n::__($c->getName());
				$coupon->label = AEI18n::__($c->getDescription());
				
				$cc[] = $coupon;
			}
			
			echo json_encode($cc);
		} else {
			echo "[]";
		}
		wp_die();
	}
}
<?php 

function rbk_wp_footer() {
    require_once( plugin_dir_path(__FILE__) .'/img/ae-svg-icons.svg' );
    ?><!-- Powered by Listae.com --><?php 
}
add_action("wp_footer", "rbk_wp_footer");
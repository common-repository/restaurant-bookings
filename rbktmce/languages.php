<?php 
// This file is based on wp-includes/js/tinymce/langs/wp-langs.php

if ( ! defined( 'ABSPATH' ) || ! class_exists("RBKTinyMCE") )
    exit;

if ( ! class_exists( '_WP_Editors' ) )
    require( ABSPATH . WPINC . '/class-wp-editor.php' );

function rbktmce_translation() {
	$strings = array(
		'desc' => __('Contents of Listae', 'restaurant-bookings'),
		'delete.btn' => __('Delete', 'restaurant-bookings'),
		'tooltip' => __("Manage Listae content", 'restaurant-bookings'),
	);
	
	$translated = 'tinyMCE.addI18n("' . _WP_Editors::$mce_locale . '.' . RBKTinyMCE::TMCE_AE_PLUGIN . '", ' . json_encode( $strings ) . ");\n";
	
	return $translated;
}

?>
<script type="text/javascript">
<!--
(function($) {
	$(document).ready(function () {
		<?php echo rbktmce_translation(); ?>
	});
})(jQuery);
//-->
</script>

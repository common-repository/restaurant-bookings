<?php 
/**
 * Plantilla para pintar el widget para cabecera personalizada que va dentro de .layout-header-extra-inside
 * 
 * Variables disponibles en la plantilla
 * 
 * @var \Listae\Client\Model\OrderCfg $order_cfg
 * @var \Listae\Client\Model\Restaurant $restaurant
 * @var string[]string[] $args
 * @var string[]string[] $order_types
 * @var string $order_type
 */

$current_title = empty($args["title"]) ? 
	sprintf(__('Orders in %s', 'restaurant-bookings'), $restaurant->getName()) : 
	$args["title"];

?>
<header class="entry-header">
	<?php 
		echo $args["before_title"];
		echo esc_html($current_title);
		echo $args["after_title"];
	?>
</header>

<div id="rbkor_order_type_out_modal_wrap">
	<div id="rbkor_order_type_wrap" class="text-center content-order-type">
		<?php if (count($order_types) > 1) { ?>
			<div class="btn-group btn-group-toggle mb-4" data-toggle="buttons">
				<?php foreach ($order_types as $ot_key => $ot_label) { ?>
					<label class="btn btn-primary<?php echo $ot_key == $order_type ? ' active' : ''; ?>" for="rbkor_order_type<?php echo $ot_key; ?>">
						<input type="radio" class="btn-check" name="rbkor_order_type" id="rbkor_order_type<?php echo $ot_key; ?>" 
							autocomplete="off" value="<?php echo $ot_key; ?>"<?php echo $ot_key == $order_type ? ' checked' : ''; ?>>
						<?php echo esc_html($ot_label); ?>
					</label>
				<?php } ?>
			</div>
		<?php } else { ?>
			<input type="hidden" name="rbkor_order_type" id="rbkor_order_type" value="<?php echo $order_type; ?>" />
		<?php } ?>
	</div><!-- div#rbkor_order_type_wrap -->
</div><!-- div#rbkor_order_type_out_modal_wrap -->

<div class="rbkor_available_order rbkor_available_delivery" style="display: none;">
	<?php 
	if (!empty($order_cfg->getDelivery()) && $order_cfg->getDelivery()->getEnabled() && null !== $order_cfg->getDelivery()->getMinOrderDate() ) { 
		$number_of_days = $order_cfg->getDelivery()->getDaysToElaborate();
		$order_time = $order_cfg->getDelivery()->getMinOrderTime();
		?>
		<p class="info rbkor_default_date_time">
			<span class="rbkor_default_date_time_msg <?php echo $number_of_days == 0 ? 'is-today' : 'not-is-today'; ?>">
				<?php 
				if ($number_of_days == 0) {
					if ($order_cfg->getDelivery()->getElaborateRightNow()) {
						echo sprintf(
							__("Closest delivery: <b>today</b> on %1s-%2s min.", 'restaurant-bookings'),
							$order_cfg->getDelivery()->getMinTimeRightNow(),
							$order_cfg->getDelivery()->getMaxTimeRightNow()
						);
					} else {
						echo sprintf(__("Closest delivery: <b>today</b> at %1s.", 'restaurant-bookings'), $order_time );
					}
				} else if ($number_of_days == 1) {
					echo sprintf(__("Closest delivery: <b>tomorrow</b> at %1s.", 'restaurant-bookings'), $order_time );
				} else {
					$order_date = date_i18n("d/m/Y", $order_cfg->getDelivery()->getMinOrderDate()->getTimestamp());
					echo sprintf(__("Closest delivery: <b>next</b> %1s at %2s.", 'restaurant-bookings'), $order_date, $order_time );
				} ?>
			</span>
		</p>
	<?php } ?>
</div><!-- div.rbkor_available_delivery -->

<div class="rbkor_available_order rbkor_available_takeaway" style="display: none;">
	<?php 
	if (!empty($order_cfg->getTakeaway()) && $order_cfg->getTakeaway()->getEnabled() && null !== $order_cfg->getTakeaway()->getMinOrderDate() ) { 
		$number_of_days = $order_cfg->getTakeaway()->getDaysToElaborate();
		$order_time = $order_cfg->getTakeaway()->getMinOrderTime();
		?>
		<p class="info rbkor_default_date_time">
			<span class="rbkor_default_date_time_msg <?php echo $number_of_days == 0 ? 'is-today' : 'not-is-today'; ?>">
				<?php 
				if ($number_of_days == 0) {
					if ($order_cfg->getTakeaway()->getElaborateRightNow()) {
						echo sprintf(
							__("Closest takeaway: <b>today</b> on %1s-%2s min.", 'restaurant-bookings'),
							$order_cfg->getTakeaway()->getMinTimeRightNow(),
							$order_cfg->getTakeaway()->getMaxTimeRightNow()
						);
					} else {
						echo sprintf(__("Closest takeaway: <b>today</b> at %1s.", 'restaurant-bookings'), $order_time );
					}
				} else if ($number_of_days == 1) {
					echo sprintf(__("Closest takeaway: <b>tomorrow</b> at %1s.", 'restaurant-bookings'), $order_time );
				} else {
					$order_date = date_i18n("d/m/Y", $order_cfg->getTakeaway()->getMinOrderDate()->getTimestamp());
					echo sprintf(__("Closest takeaway: <b>next</b> %1s at %2s.", 'restaurant-bookings'), $order_date, $order_time );
				} ?>
			</span>
		</p>
	<?php } ?>
</div><!-- div.rbkor_available_takeaway -->

<div class="rbkor_msgs alert alert-info" role="alert" style="display: none;"></div><!-- div.rbkor_msgs -->

<p id="rbkor_delivery_address" style="display: none;">
	<a class="rbkor_show_address" href="javascript:void(0)">
		<span class="wrap-icon"><svg class="icon icon-cancel-outline" aria-hidden="true" role="img"><use href="#icon-cancel-outline" xlink:href="#icon-cancel-outline"></use></svg></span>
		<span class="wrap-text"></span>
	</a>
</p><!-- p#rbkor_delivery_address -->

<div class="row justify-content-center rbkor_order_type_msg"></div>
<?php 
/**
 * Plantilla para pintar el carrito del formulario de pedidos
 * 
 * Variables disponibles en la plantilla
 */
?>
<div class="rbkor_shooping_cart_widget_wrap">
	<div id="rbkor_mobile_panel">
 		
 		<div class="rbkor_mini_cart">
			
			<div>
				<button id="rbkor_show_info" class="btn btn-outline-primary btn-block btn-show" type="button">
					<span class="rbkor_mini_cart_items">
						<span class="rbkor_mini_cart_oqty">0</span>
						<span><?php esc_html_e('Items', 'restaurant-bookings'); ?></span>
					</span>
					<span class="rbkor_mini_cart_otl">0 &euro;</span>						
				</button>
			</div>
			
			<div>
				<button id="rbkor_mini_cart_ordernow" class="btn btn-primary btn-order btn-block" type="button" disabled="disabled">
					<span class="btn-label">
						<?php esc_html_e('Confirm', 'restaurant-bookings'); ?>
						<span class="wrap-icon">
            				<svg class="icon icon-chevron-right" aria-hidden="true" role="img"><use href="#icon-chevron-right" xlink:href="#icon-chevron-right"></use></svg>
            			</span>
					</span>
					<small class="disabled-info" style="display: none;">
						<span class="min-order-msg"></span>
						<span class="plus-dlv" style="display: none;">(+ <?php _ex('Delivery', 'small text for delivery costs', 'restaurant-bookings'); ?>)</span>
					</small>
				</button>
			</div>
			
		</div><!-- .rbkor_mini_cart -->

	</div><!-- #rbkor_mobile_panel -->
	
	<div id="rbkor_shooping_cart_widget" class="rbkor_shooping_cart_widget">
		<h2 class="widget-title"><?php esc_html_e('Your order', 'restaurant-bookings'); ?></h2>	
		
		<div id="rbkor_order_header" class="rbkor_order_header">
			<div class="rbkor_offer_search">
				<div class="input-group input-group-sm">
					<input id="txt_rbkor_offer_search" type="text" class="form-control form-control-sm" 
						placeholder="<?php esc_attr_e('Offer code', 'restaurant-bookings'); ?>" 
						aria-label="<?php esc_html_e('Offer code', 'restaurant-bookings'); ?>" 
						aria-describedby="esc_attr_e">
					<div class="input-group-prepend">
						<div class="input-group-text" id="btnGroupAddon">
							<a class="rbkor_btn" id="rbkor_btn_search_offer" href="javascript:void(0);">
								<svg class="icon icon-local-offer" aria-hidden="true" role="img"><use href="#icon-local-offer" xlink:href="#icon-local-offer"></use></svg>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div id="rbkor_order_detail" class="rbkor_order_detail">
			<table>
			<tbody id="rbkor_oitems">
			<tr>
				<td class="rbkor_oitems_initxt">
					<span><?php esc_html_e('Add items to order', 'restaurant-bookings'); ?></span>
					<button type="button" class="btn-show btn btn-block btn-danger">
						<?php esc_html_e('Add items to order', 'restaurant-bookings'); ?>
					</button>
				</td>
			</tr>
			</tbody>
			<tfoot>
			<tr class="rbkor_discount_otl rbkor_oitem_line" style="display: none;">
				<td>
					<div class="rbkor_ovalue">
						<span class="rbkor_odesc">
							<svg class="icon icon-local-offer-outline" aria-hidden="true" role="img">
								<use href="#icon-local-offer-outline" xlink:href="#icon-local-offer-outline"></use>
							</svg>
							<span>
								<?php esc_html_e('Discounts', 'restaurant-bookings'); ?>
							</span>
						</span>
						<span id="rbkor_discount_otl_value" class="rbkor_oprice">0 &euro;</span>
					</div>
				</td>
			</tr>
			</tfoot>
			</table>
		</div>

		<div id="rbkor_order_footer" class="rbkor_order_footer">
			<div class="rbkor_ototal">
				<table>
				<tbody>
				<tr class="rbkor_otl">
					<td>
						<?php esc_html_e('Total', 'restaurant-bookings'); ?>
					</td>
					<td>
						<span id="rbkor_otl_value">0 &euro;</span>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<button id="rbkor_ordernow" disabled="disabled" class="button btn btn-order btn-primary">
							<span class="btn-label"><?php esc_attr_e('Order now', 'restaurant-bookings')?></span>
							<small class="disabled-info" style="display: none;">
								<span class="min-order-msg"></span>
								<span class="plus-dlv" style="display: none;">(+ <?php _ex('Delivery', 'small text for delivery costs', 'restaurant-bookings'); ?>)</span>
							</small>
						</button>
					</td>
				</tr>
				</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

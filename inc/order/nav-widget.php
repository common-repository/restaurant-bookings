<?php 
/**
 * Plantilla para pintar el nuevo widget de navegacion de del formulario de pedidos.
 * 
 * Variables disponibles en la plantilla
 * 
 * @var \Listae\Client\Model\OrderCfg $order_cfg
 * @var string[]string[] $args
 */
?>
<?php // Este div de 1px de altura se utiliza para saber cuando se ha puesto como fixed el contenedor siguiente... NO BORRAR ?>
<div id="nav-container-top" style="height: 1px;"></div>

<div id="layout-catalog-navigation" class="layout-catalog-navigation container-fluid">
	<div class="row">
		<div class="content-area content-catalog-navigation">
			<nav id="order-navbar" class="order-navbar navbar" data-delivery="<?php echo $args["delivery"] ? '1' : '0'; ?>" 
				data-takeaway="<?php echo $args["takeaway"] ? '1' : '0'; ?>"
				data-booking="<?php echo $args["booking"] ? '1' : '0'; ?>">
				
				<div id="navbarOrder<?php echo esc_attr($args["id"]); ?>">
					<ul class="navbar-nav mr-auto nav nav-pills">
						<?php foreach ($order_cfg->getCartes()->getCarte() as $carte) { ?>
							<?php foreach ($carte->getGroup() as $group) { ?>
								<li class="nav-item">
									<a class="nav-link" href="#catalog-group-<?php echo $group->getUrl(); ?>" <?php echo AECatalog::get_data_item_properties_on_group($group); ?>>
										<?php echo esc_html(AEI18n::__($group->getName())); ?>
									</a>
								</li>
							<?php } ?>
						<?php } ?>
					</ul>
				</div>
			</nav>
		</div><!-- div.content-area content-catalog-navigation -->
		
		<?php /*
		TODO: Esto ya no se usa y se puede borrar
		<div class="widget-area content-widget-area">
			<div class="rbkor_msgs alert alert-info" role="alert" style="display: none;"></div>
		</div><!-- div.widget-area content-widget-area -->
		*/ ?>
	</div><!-- div#layout-catalog-navigation .row -->
</div><!-- div#layout-catalog-navigation -->
<?php 
/**
 * Plantilla para pintar el nuevo diseno del formulario de pedidos con el catalogo.
 * 
 * Variables disponibles en la plantilla
 * 
 * @var \Listae\Client\Model\Restaurant $restaurant
 * @var \Listae\Client\Model\OrderCfg $order_cfg
 * @var string[]|mixed[] $config_js
 * @var string[]string[] $args
 * @var string[]string[] $order_types
 * @var string $order_type
 * @var string $action_url
 * @var string $html_catalogs
 */

// En jsp aqui hay un if restaurant.someItemOrderable pero vamos a asumir que siempre que llega aqui hay al menos algun item disponible...
?>
<div class="listae-order">
<form id="rbkor_form" method="post" class="rbkor_form " action="<?php echo esc_attr($action_url); ?>" data-action-no-iframe="">
	<input name="origin" id="origin" value="<?php echo esc_attr(get_permalink()); ?>" type="hidden" />
	
	<?php /* TODO: PArece que esto NO es necesario... si vemos que rula todo guay borrar este comentario
	<?php // TODO: Revisar... Los campos de ot y slug los tiene el jsp... pero desconozco si son necesarios ?>
	<input name="ot" id="ot" value="<?php echo esc_attr($order_type); ?>" type="hidden" />
	<input name="slug" id="slug" value="<?php echo esc_attr($restaurant->getUrl()); ?>" type="hidden" />
	<?php /* */ ?>
	
	<div id="precontent" class="layout-custom-header">
        <div class="custom-header-media">
        	<div class="custom-header-extra">
        		<div class="layout-header-extra-inside container">
        			<?php require_once 'header-widget.php'; ?>
        		</div><!-- div.layout-header-extra-inside -->
        	</div><!-- div.custom-header-extra -->
        	
        	<div class="custom-thumbnail-header wp-custom-header">
        		<?php if ($restaurant->getFeaturedImage()) { ?>
        			<img src="<?php echo esc_attr($restaurant->getFeaturedImage()->getHref()); ?>" alt="<?php echo esc_attr($restaurant->getName()); ?>" />
        		 <?php } ?>
        	</div>
        	
        </div><!-- div.custom-header-media -->
	</div><!-- div#precontent -->
	
	<div id="breadcrumb"></div><!-- div#breadcrumb -->
	
	<?php require_once 'nav-widget.php';?>
	
	<div id="content" class="site-content container-fluid">
		<div class="row">
			<div id="primary" class="content-area">
				<main id="main" class="site-main" role="main">
					<article class="page type-page status-publish">
						
						<?php if (!empty($args["title"])) { ?>
							<header class="entry-header">
								<?php 
									echo $args["before_title"];
									echo esc_html($args["title"]);
									echo $args["after_title"];
								?>
							</header>
						<?php } ?>
						
						<div class="page-content">
							<?php echo $html_catalogs; ?>
						</div><!-- div.page-content -->
					</article><!-- main#main article.status-publish -->
				</main><!-- main#main -->
			</div><!-- div#primary -->
			
			<aside id="secondary" class="widget-area content-widget-area" role="complementary">
				<section id="widget_rbk_order-2" class="widget aewidget widget_rbk_order">
					<?php require_once 'cart-widget.php';?>
				</section><!-- section#widget_rbk_order-2 -->
			</aside><!-- aside#secondary -->
		</div><!-- div#content .row -->
	</div><!-- div#content -->
</form>
</div>

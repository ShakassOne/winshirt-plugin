<?php
// WINSHIRT CUSTOMIZER PAGE TEMPLATE

// Récupère les produits phares (ex : les 4 premiers, ou une sélection)
$featured_products = get_posts([
  'post_type'      => 'product',
  'posts_per_page' => 4,
  'orderby'        => 'menu_order',
  'order'          => 'ASC',
  'meta_query'     => [['key' => '_visibility', 'value' => 'visible']],
]);

$page_id = absint( get_option( 'winshirt_custom_page' ) );
$customizer_page_url = $page_id ? get_permalink( $page_id ) : '/personnalisez/';

// Layout principal
?>
<div class="winshirt-customizer-container">
  <?php if ( empty($product) ) : ?>
    <div class="winshirt-no-product">
      <h2>Choisissez un produit à personnaliser</h2>
      <p>Veuillez sélectionner un produit dans la boutique pour commencer.</p>
      <a href="<?php echo esc_url( get_permalink( wc_get_page_id('shop') ) ); ?>" class="winshirt-btn winshirt-btn-main">Voir la boutique</a>
      <?php if ( !empty($featured_products) ) : ?>
        <div class="winshirt-featured-products">
          <?php foreach ($featured_products as $fp) : ?>
            <div class="winshirt-featured-product">
              <a href="<?php echo esc_url( add_query_arg( 'product_id', $fp->ID, $customizer_page_url ) ); ?>">
                <?php echo get_the_post_thumbnail($fp->ID, 'medium'); ?>
                <div class="winshirt-featured-title"><?php echo esc_html(get_the_title($fp->ID)); ?></div>
              </a>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  <?php else : ?>
    <main class="winshirt-customizer-main">
      <header class="winshirt-customizer-header">
        <h1>Personnalisez votre T-shirt : <?php echo esc_html($product->get_name()); ?></h1>
        <a href="<?php echo esc_url( get_permalink( wc_get_page_id('shop') ) ); ?>" class="winshirt-btn winshirt-btn-small">Changer de produit</a>
      </header>
      <section class="winshirt-customizer-workspace">
        <!-- Ici, le code principal de ton customizer (mockup, panels, options) -->
        <?php
        // Par exemple :
        // winshirt_render_customizer($product, $colors, $zones, ...);
        ?>
      </section>
    </main>
  <?php endif; ?>
</div>

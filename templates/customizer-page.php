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

// Liste complète des produits personnalisables
$available_products = wc_get_products([
  'limit'     => -1,
  'status'    => 'publish',
  'orderby'   => 'title',
  'order'     => 'ASC',
  'meta_query' => [
    [
      'key'   => '_winshirt_enabled',
      'value' => 'yes',
    ],
  ],
]);
$selectable_products = [];
foreach ( $available_products as $ap ) {
  if ( winshirt_get_customizer_vars( $ap->get_id() ) ) {
    $selectable_products[] = $ap;
  }
}

$page_id = absint( get_option( 'winshirt_custom_page' ) );
$customizer_page_url = $page_id ? get_permalink( $page_id ) : '/personnalisez/';

// Layout principal
?>
<div class="winshirt-customizer-container">
  <div class="winshirt-product-selector">
    <label for="ws-product-select">Produit :</label>
    <select id="ws-product-select" class="ws-select">
      <option value="">-- <?php esc_html_e( 'Sélectionner', 'winshirt' ); ?> --</option>
      <?php foreach ( $selectable_products as $sp ) : ?>
        <option value="<?php echo esc_attr( $sp->get_id() ); ?>" <?php selected( $product && $product->get_id() === $sp->get_id() ); ?>><?php echo esc_html( $sp->get_name() ); ?></option>
      <?php endforeach; ?>
    </select>
  </div>
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
        <?php include WINSHIRT_PATH . 'templates/personalizer-modal.php'; ?>
      </section>
      <script type="text/javascript">
      document.addEventListener('DOMContentLoaded', function(){
        if (typeof window.openWinShirtModal === 'function') {
          window.openWinShirtModal(<?php echo intval( $product->get_id() ); ?>);
        }
      });
      </script>
    </main>
  <?php endif; ?>
</div>
<script type="text/javascript">
document.getElementById('ws-product-select').addEventListener('change', function(){
  if(this.value){
    window.location.href = '<?php echo esc_url( $customizer_page_url ); ?>?product_id=' + this.value;
  }
});
</script>

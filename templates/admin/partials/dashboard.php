<?php
/**
 * Admin dashboard for WinShirt.
 * Variables expected: $mockup_count, $visual_count, $product_count, $lottery_count, $lottery_progress
 */
?>
<h1><?php esc_html_e('Bienvenue sur le tableau de bord WinShirt', 'winshirt'); ?></h1>
<div class="winshirt-dashboard-cards" style="display:flex;flex-wrap:wrap;gap:20px;margin-top:20px;">
  <div class="winshirt-dashboard-card" style="flex:1;min-width:150px;padding:20px;background:#fff;border:1px solid #ddd;border-radius:8px;text-align:center;">
    <h2 style="margin:0;font-size:2em;"><?php echo esc_html($mockup_count); ?></h2>
    <p style="margin:5px 0 0;">Mockups</p>
  </div>
  <div class="winshirt-dashboard-card" style="flex:1;min-width:150px;padding:20px;background:#fff;border:1px solid #ddd;border-radius:8px;text-align:center;">
    <h2 style="margin:0;font-size:2em;"><?php echo esc_html($visual_count); ?></h2>
    <p style="margin:5px 0 0;">Visuels</p>
  </div>
  <div class="winshirt-dashboard-card" style="flex:1;min-width:150px;padding:20px;background:#fff;border:1px solid #ddd;border-radius:8px;text-align:center;">
    <h2 style="margin:0;font-size:2em;"><?php echo esc_html($product_count); ?></h2>
    <p style="margin:5px 0 0;">Produits personnalisables</p>
  </div>
  <div class="winshirt-dashboard-card" style="flex:1;min-width:150px;padding:20px;background:#fff;border:1px solid #ddd;border-radius:8px;text-align:center;">
    <h2 style="margin:0;font-size:2em;"><?php echo esc_html($lottery_count); ?></h2>
    <p style="margin:5px 0 0;">Loteries</p>
  </div>
</div>

<div class="winshirt-dashboard-progress" style="margin-top:30px;max-width:400px;">
  <h3 style="margin-bottom:10px;">Participation aux loteries</h3>
  <div style="background:#eee;border-radius:4px;height:20px;position:relative;">
    <div style="background:#4caf50;height:100%;width:<?php echo esc_attr($lottery_progress); ?>%;border-radius:4px;"></div>
  </div>
  <p style="margin-top:5px;"><?php echo esc_html(round($lottery_progress)); ?>% des produits contiennent une loterie.</p>
</div>

<div class="winshirt-dashboard-links" style="margin-top:30px;">
  <h3>Liens rapides</h3>
  <ul style="list-style:disc;padding-left:20px;">
    <li><a href="<?php echo esc_url(admin_url('admin.php?page=winshirt-mockups')); ?>">Ajouter un mockup</a></li>
    <li><a href="<?php echo esc_url(admin_url('admin.php?page=winshirt-designs')); ?>">Voir les visuels</a></li>
    <li><a href="<?php echo esc_url(admin_url('admin.php?page=winshirt-products')); ?>">Configurer les produits</a></li>
    <li><a href="<?php echo esc_url(admin_url('admin.php?page=winshirt-lotteries')); ?>">Gérer les loteries</a></li>
    <li><a href="<?php echo esc_url(admin_url('admin.php?page=winshirt-settings')); ?>">Paramètres du plugin</a></li>
  </ul>
</div>

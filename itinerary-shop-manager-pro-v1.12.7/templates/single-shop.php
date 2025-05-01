<?php get_header(); ?>
<style>
.shop-tabs { margin-top: 20px; }
.shop-tabs nav { display: flex; gap: 10px; margin-bottom: 10px; }
.shop-tabs nav button {
    padding: 8px 15px;
    border: 1px solid #ccc;
    background: #f2f2f2;
    cursor: pointer;
}
.shop-tabs nav button.active {
    background: #0073aa;
    color: white;
}
.shop-tabs .tab-content { display: none; }
.shop-tabs .tab-content.active { display: block; }
.gallery img { max-width: 150px; margin: 5px; border: 1px solid #ccc; cursor: pointer; }
.shop-tabs .gallery { display: flex; flex-wrap: wrap; gap: 10px; }
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const buttons = document.querySelectorAll('.shop-tabs nav button');
    const contents = document.querySelectorAll('.shop-tabs .tab-content');
    buttons.forEach(btn => btn.addEventListener('click', function () {
        buttons.forEach(b => b.classList.remove('active'));
        contents.forEach(c => c.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById(btn.dataset.target).classList.add('active');
    }));
    if (buttons.length) buttons[0].click(); // Activate the first tab by default
});
</script>



<div class="shop-details">
    <h1><?php the_title(); ?></h1>
    
<div class="shop-sections">
    <h2>Info</h2>
    <div class="tab-info">
        <p><strong>Address:</strong> <?php echo esc_html(get_post_meta(get_the_ID(), 'address', true)); ?></p>
        <p><strong>City:</strong>
            <?php $terms = get_the_terms(get_the_ID(), 'shop_city');
        </p>
        <p><strong>Categories:</strong>
            <?php $terms = get_the_terms(get_the_ID(), 'shop_category');
        </p>
        <p><strong>Mobiles:</strong>
            <?php echo esc_html(get_post_meta(get_the_ID(), 'mobile_1', true)); ?>,
            <?php echo esc_html(get_post_meta(get_the_ID(), 'mobile_2', true)); ?>,
            <?php echo esc_html(get_post_meta(get_the_ID(), 'mobile_3', true)); ?>
        </p>
        <p><strong>WhatsApps:</strong>
            <?php echo esc_html(get_post_meta(get_the_ID(), 'whatsapp_1', true)); ?>,
            <?php echo esc_html(get_post_meta(get_the_ID(), 'whatsapp_2', true)); ?>,
            <?php echo esc_html(get_post_meta(get_the_ID(), 'whatsapp_3', true)); ?>
        </p>
        <p><strong>Open Timings:</strong> <?php echo esc_html(get_post_meta(get_the_ID(), 'shop_open_timings', true)); ?></p>
        <p><strong>Social Links:</strong><br><?php echo nl2br(esc_html(get_post_meta(get_the_ID(), 'shop_social_links', true))); ?></p>
        <p><strong>Services:</strong><br><?php echo nl2br(esc_html(get_post_meta(get_the_ID(), 'shop_services', true))); ?></p>
        <p><strong>Offers/Coupons:</strong><br><?php echo nl2br(esc_html(get_post_meta(get_the_ID(), 'shop_offers', true))); ?></p>
        <p><strong>Our Website Coupon:</strong> <?php echo esc_html(get_post_meta(get_the_ID(), 'shop_site_coupon', true)); ?></p>
        <p><strong>Notes:</strong><br><?php echo nl2br(esc_html(get_post_meta(get_the_ID(), 'shop_custom_notes', true))); ?></p>
        <p><strong>Description:</strong> <?php the_content(); ?></p>
    </div>

    <h2>Map</h2>
    <div class="tab-map">
        <?php $coords = get_post_meta(get_the_ID(), 'map_coords', true); ?>
        <?php if ($coords): ?>
            <iframe width="100%" height="300" frameborder="0"
                src="https://maps.google.com/maps?q=<?php echo urlencode($coords); ?>&output=embed">
            </iframe>
        <?php else: ?>
            <p><em>No map coordinates provided.</em></p>
        <?php endif; ?>
    </div>

    <h2>Gallery</h2>
    <div class="tab-gallery gallery">
        <?php
        $images = get_post_meta(get_the_ID(), 'shop_gallery', true);
        if (!empty($images) && is_array($images)) {
            foreach ($images as $img_id) {
                $full = wp_get_attachment_image_src($img_id, 'full')[0] ?? '';
                $thumb = wp_get_attachment_image_src($img_id, 'medium')[0] ?? '';
            }
        } else {
        }
        ?>
    </div>

    <h2>Shop Items</h2>
    
<div class="tab-items">
    <div class="item-grid">
        <?php
        $items = get_post_meta(get_the_ID(), '_shop_items', true);
        if (!empty($items)) {
            foreach ($items as $item) {
                $img_html = '';
                if (!empty($item['image'])) {
                    $img = wp_get_attachment_image_src($item['image'], 'medium');
                    if ($img) {
                        $img_html = '<img src="' . esc_url($img[0]) . '" width="256" height="256" style="object-fit:cover;border:1px solid #ccc;margin-bottom:8px;" />';
                    }
                }
            }
        } else {
        }
        ?>
    </div>
</div>

</div>
</div>
<?php get_footer(); ?>

</div>
<?php get_footer();
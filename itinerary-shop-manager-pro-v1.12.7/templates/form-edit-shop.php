<?php
$shop_id = intval($_GET['edit_shop']);
$meta = get_post_meta($shop_id);
$title = get_the_title($shop_id);
$content = get_post_field('post_content', $shop_id);
$address = $meta['address'][0] ?? '';
$coords = $meta['map_coords'][0] ?? '';
$city_terms = wp_get_post_terms($shop_id, 'shop_city', ['fields' => 'names']);
$cat_terms = wp_get_post_terms($shop_id, 'shop_category', ['fields' => 'ids']);
?>
<form method="post" enctype="multipart/form-data">
    <?php wp_nonce_field('edit_shop', 'shop_edit_nonce'); ?>
    <input type="hidden" name="edit_shop_id" value="<?php echo esc_attr($shop_id); ?>" />
    <p><label>Shop Title</label><input type="text" name="shop_title" value="<?php echo esc_attr($title); ?>" required></p>
    <p><label>Address</label><input type="text" name="shop_address" value="<?php echo esc_attr($address); ?>" required></p>
    <p><label>City</label>
        <select name="shop_city" class="select2">
            <?php
            $cities = get_terms('shop_city', ['hide_empty' => false]);
            foreach($cities as $term):
                $selected = in_array($term->name, $city_terms) ? 'selected' : '';
            endforeach;
            ?>
        </select>
    </p>
    <p><label>Categories</label>
        <select name="shop_categories[]" class="select2" multiple="multiple">
            <?php
            $categories = get_terms('shop_category', ['hide_empty' => false]);
            foreach($categories as $term):
                $selected = in_array($term->term_id, $cat_terms) ? 'selected' : '';
            endforeach;
            ?>
        </select>
    </p>
    <p><label>Description</label><textarea name="shop_description"><?php echo esc_textarea($content); ?></textarea></p>
    <p><label>Map Coordinates</label><input type="text" name="map_coords" value="<?php echo esc_attr($coords); ?>" /></p>
    <p><button type="submit" name="submit_shop_edit">Save Changes</button></p>
</form>
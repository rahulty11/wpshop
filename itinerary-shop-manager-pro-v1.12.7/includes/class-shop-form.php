<?php
class Shop_Form {
    public function __construct() {
        
        add_shortcode('edit_shop_form', [$this, 'render_edit_form']);

        add_shortcode('register_shop_form', [$this, 'render_form']);
        add_action('template_redirect', [$this, 'handle_submission']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
    }

    public function enqueue_scripts() {
        wp_enqueue_media();
        wp_enqueue_style('select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css');
        wp_enqueue_script('select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', ['jquery'], null, true);
        wp_add_inline_script('select2', "jQuery(document).ready(function($){ $('.select2').select2(); });");
    }

    public function render_form() {
        ob_start();
        $categories = get_terms('shop_category', ['hide_empty' => false]);
        $cities = get_terms('shop_city', ['hide_empty' => false]);
        ?>
        <form method="post" enctype="multipart/form-data" class="shop-registration-form">
            <?php wp_nonce_field('submit_shop', 'shop_nonce'); ?>
            <p><label>Shop Title</label><input type="text" name="shop_title" required></p>
            <p><label>Address</label><input type="text" name="shop_address" required></p>
            <p><label>City</label>
                <select name="shop_city" class="select2">
                    <?php foreach($cities as $term): ?>
                        <option value="<?php echo esc_attr($term->name); ?>"><?php echo esc_html($term->name); ?></option>
                    <?php endforeach; ?>
                </select>
            </p>
            <p><label>Mobile 1</label><input type="text" name="mobile_1" /></p>
            <p><label>Mobile 2</label><input type="text" name="mobile_2" /></p>
            <p><label>Mobile 3</label><input type="text" name="mobile_3" /></p>
            <p><label>WhatsApp 1</label><input type="text" name="whatsapp_1" /></p>
            <p><label>WhatsApp 2</label><input type="text" name="whatsapp_2" /></p>
            <p><label>WhatsApp 3</label><input type="text" name="whatsapp_3" /></p>
            <p><label>Map Coordinates</label><input type="text" name="map_coords" placeholder="e.g., 19.0760,72.8777" /></p>
            <p><label>Categories</label>
                <select name="shop_categories[]" class="select2" multiple="multiple">
                    <?php foreach($categories as $cat): ?>
                        <option value="<?php echo $cat->term_id; ?>"><?php echo $cat->name; ?></option>
                    <?php endforeach; ?>
                </select>
            </p>
            <p><label>Description</label><textarea name="shop_description"></textarea></p>
            <p><label>Open Timings</label><input type="text" name="shop_open_timings" /></p>
            <p><label>Social Media Links</label><textarea name="shop_social_links"></textarea></p>
            <p><label>Services Provided</label><textarea name="shop_services"></textarea></p>
            <p><label>Offers/Coupons</label><textarea name="shop_offers"></textarea></p>
            <p><label>Our Website Coupon</label><input type="text" name="shop_site_coupon" /></p>
            <p><label>Write What You Want</label><textarea name="shop_custom_notes"></textarea></p>

            <h4>Gallery</h4>
            <p>
                <button type="button" class="button" id="upload_gallery_btn">Select Images</button>
                <input type="hidden" name="shop_gallery" id="shop_gallery_input" value="" />
                <div id="gallery_preview"></div>
            </p>

            <h4>Shop Items</h4>
            <table class="shop-items-table">
                <thead><tr><th>Item</th><th>Type</th><th>Qty</th><th>Unit</th><th>Price</th><th>Remove</th></tr></thead>
                <tbody id="shop-items-body"></tbody>
            </table>
            <p><a href="#" class="button" id="add-shop-item">+ Add Item</a></p>

            <p><button type="submit" name="submit_shop">Submit Shop</button></p>
        </form>

        <script>
        document.addEventListener("DOMContentLoaded", function () {
            const frame = wp.media({ multiple: true });
            document.getElementById("upload_gallery_btn").addEventListener("click", function (e) {
                e.preventDefault();
                frame.open();
            });
            frame.on("select", function () {
                const attachments = frame.state().get("selection").toJSON();
                const ids = attachments.map(img => img.id);
                const preview = document.getElementById("gallery_preview");
                preview.innerHTML = attachments.map(img => `<img src="${img.sizes.thumbnail.url}" style="max-width:100px;margin:5px;" />`).join('');
                document.getElementById("shop_gallery_input").value = ids.join(",");
            });
        });
        </script>
        <?php
        return ob_get_clean();
    }

    public function handle_submission() {
        if (
            isset($_POST['submit_shop']) &&
            wp_verify_nonce($_POST['shop_nonce'], 'submit_shop') &&
            is_user_logged_in()
        ) {
            $shop_id = wp_insert_post([
                'post_type' => 'shop',
                'post_title' => sanitize_text_field($_POST['shop_title']),
                'post_content' => sanitize_textarea_field($_POST['shop_description']),
                'post_status' => 'pending',
                'post_author' => get_current_user_id(),
            ]);

            if ($shop_id && !is_wp_error($shop_id)) {
                update_post_meta($shop_id, 'address', sanitize_text_field($_POST['shop_address']));
                wp_set_post_terms($shop_id, [sanitize_text_field($_POST['shop_city'])], 'shop_city');
                update_post_meta($shop_id, 'mobile_1', sanitize_text_field($_POST['mobile_1']));
                update_post_meta($shop_id, 'mobile_2', sanitize_text_field($_POST['mobile_2']));
                update_post_meta($shop_id, 'mobile_3', sanitize_text_field($_POST['mobile_3']));
                update_post_meta($shop_id, 'whatsapp_1', sanitize_text_field($_POST['whatsapp_1']));
                update_post_meta($shop_id, 'whatsapp_2', sanitize_text_field($_POST['whatsapp_2']));
                update_post_meta($shop_id, 'whatsapp_3', sanitize_text_field($_POST['whatsapp_3']));
                update_post_meta($shop_id, 'map_coords', sanitize_text_field($_POST['map_coords']));
                
                $items = $_POST['shop_items'] ?? [];
                $images = $_FILES['shop_items_images'] ?? [];

                foreach ($items as $i => &$item) {
                    if (!empty($images['name'][$i]) && $images['error'][$i] === UPLOAD_ERR_OK) {
                        $upload = wp_handle_upload([
                            'name'     => $images['name'][$i],
                            'type'     => $images['type'][$i],
                            'tmp_name' => $images['tmp_name'][$i],
                            'error'    => $images['error'][$i],
                            'size'     => $images['size'][$i]
                        ], ['test_form' => false]);

                        if (!isset($upload['error']) && isset($upload['file'])) {
                            $filetype = wp_check_filetype($upload['file']);
                            $attachment = [
                                'post_mime_type' => $filetype['type'],
                                'post_title'     => sanitize_file_name($images['name'][$i]),
                                'post_status'    => 'inherit'
                            ];
                            $attach_id = wp_insert_attachment($attachment, $upload['file']);
                            require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
                            $attach_data = wp_generate_attachment_metadata($attach_id, $upload['file']);
                            wp_update_attachment_metadata($attach_id, $attach_data);
                            $item['image'] = $attach_id;
                        }
                    }
                }
                update_post_meta($shop_id, '_shop_items', $items);


                update_post_meta($shop_id, 'shop_open_timings', sanitize_text_field($_POST['shop_open_timings']));
                update_post_meta($shop_id, 'shop_social_links', sanitize_textarea_field($_POST['shop_social_links']));
                update_post_meta($shop_id, 'shop_services', sanitize_textarea_field($_POST['shop_services']));
                update_post_meta($shop_id, 'shop_offers', sanitize_textarea_field($_POST['shop_offers']));
                update_post_meta($shop_id, 'shop_site_coupon', sanitize_text_field($_POST['shop_site_coupon']));
                update_post_meta($shop_id, 'shop_custom_notes', sanitize_textarea_field($_POST['shop_custom_notes']));

                $gallery_ids = array_filter(array_map('intval', explode(',', $_POST['shop_gallery'] ?? '')));
                update_post_meta($shop_id, 'shop_gallery', $gallery_ids);

                wp_set_post_terms($shop_id, array_map('intval', $_POST['shop_categories'] ?? []), 'shop_category');
                wp_redirect(add_query_arg('shop_submitted', '1', get_permalink($shop_id)));
                exit;
            }
        }
    }
}


    /*public function render_edit_form() {
        if (!is_user_logged_in()) return '<p>You must be logged in to edit a shop.</p>';
        if (empty($_GET['edit_shop'])) return '<p>No shop selected.</p>';

        $shop_id = intval($_GET['edit_shop']);
        $post = get_post($shop_id);
        if (!$post || $post->post_type !== 'shop') return '<p>Invalid shop ID.</p>';

        if (!current_user_can('edit_post', $shop_id) && get_current_user_id() !== $post->post_author)
            return '<p>You do not have permission to edit this shop.</p>';

        ob_start();
        include plugin_dir_path(__FILE__) . '../templates/form-edit-shop.php';
        return ob_get_clean();
    }*/
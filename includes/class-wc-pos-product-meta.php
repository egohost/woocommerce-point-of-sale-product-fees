<?php
/**
 * WoocommercePointOfSale Product Meta Class
 *
 * @author    Mike Barkemeyer (Original plugin by Jason Judge)
 * @package   WoocommercePointOfSale/Classes/ProductMeta
 * @category	Class
 * @since     3.1.4.4
 */
 
class WC_Pos_ProductMeta {

    protected static $protected_fields = array(
        // A few WP internal fields should not be exposed.
        '_edit_lock',
        '_edit_last',
        // All these meta fields are already present in the product_data in some form.
        '_visibility',
        '_stock_status',
        'total_sales',
        '_downloadable',
        '_virtual',
        '_regular_price',
        '_sale_price',
        '_purchase_note',
        '_featured',
        '_weight',
        '_length',
        '_width',
        '_height',
        '_sku',
        '_product_attributes',
        '_price',
        '_sold_individually',
        '_manage_stock',
        '_backorders',
        '_stock',
        '_upsell_ids',
        '_crosssell_ids',
        '_product_image_gallery',
        '_sale_price_dates_from',
        '_sale_price_dates_to',
    );

    public function initialize() {		
        add_filter('woocommerce_api_product_response', array('WC_Pos_ProductMeta', 'fetchCustomMeta'), 10, 4);
    }
    public function fetchCustomMeta($product_data, $product, $fields, $server) {
        if (current_user_can( 'view_register' )) {
            $product_id = $product->id;
            $all_meta = get_post_meta($product_id);
            $all_meta = array_diff_key($all_meta, array_flip(static::$protected_fields));

            foreach($all_meta as $key => &$value) {
                $value = maybe_unserialize(reset($value));
            }
            unset($value);
            $meta = $all_meta;
            $product_data['meta'] = $meta;

            if(isset($product_data['variations'])) {
                foreach($product_data['variations'] as $k => &$variation) {
                    $variation_id = $variation['id'];
                    $all_meta = get_post_meta($variation_id);
                    $all_meta = array_diff_key($all_meta, array_flip(static::$protected_fields));
                    foreach($all_meta as $key => &$value) {
                        $value = maybe_unserialize(reset($value));
                    }
                    unset($value);
                    $meta = $all_meta;
                    $variation['meta'] = $meta;
                }
            }
        }
        return $product_data;
    }
}
WC_Pos_ProductMeta::initialize();

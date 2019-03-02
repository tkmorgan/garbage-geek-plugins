<?php
class tipCategory {
    public static $categories = [
        'trash' => 'Trash',
        'street-violations' => 'Street Violations',
        'recyclable' => 'Recycling',
        'non-recyclable' => 'Non Recyclables',
        'general' => 'General'
    ];

    public static function get_human_readble_by_cat_name($category) {
        return self::$categories[$category];
    }
    public static function get_human_readable_by_post_id($post_id) {
        $category = get_post_meta( $post_id, 'tip_category', true );
        return self::get_human_readble_by_cat_name( $category );
    }
}
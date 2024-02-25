<?php
/*
Plugin Name: Custom Open Graph Image Plugin
Description: Dynamically generates Cloudinary URL and sets it as the og:image meta tag based on post data.
Version: 1.0
Author: aomi borah
*/

function custom_replace_og_image($og_image) {
    if (is_singular('job')) { // Check if it's a single job post
        global $post;

        // Get post title (heading)
        $heading = get_the_title($post->ID);

        // Get post subtitle
        $opdrachtgever = esc_html(get_post_meta($post->ID, 'job_opdrachtgever', true));
        $subtitle = get_post($opdrachtgever)->post_title;

        // Get Uren per week
        $level = wp_get_post_terms($post->ID, 'job_level');
        $uren_per_week = '';
        if ($level) {
            $uren_per_week = '';
            foreach ($level as $l) {
                $uren_per_week .= $l->name . ', ';
            }
            // Remove trailing comma and space
            $uren_per_week = rtrim($uren_per_week, ', ');
        }

        // Characters to be replaced and their respective replacements
        $characters_to_replace = array(' ', '/', '&', '+', '|', '@', ',', '-');
        $replacements = array('%20', '%2F', '%26', '%2B', '%7C', '%40', '%2C', '-');

        // Replace characters with their respective replacements
        $heading_encoded = str_replace($characters_to_replace, $replacements, $heading);
        $subtitle_encoded = str_replace($characters_to_replace, $replacements, $subtitle);
        $uren_encoded = str_replace($characters_to_replace, $replacements, $uren_per_week);

        // Construct Cloudinary URL
        $cloudinary_url = 'https://res.cloudinary.com/dsq4b5hag/image/upload/';
        $cloudinary_url .= 'c_scale,w_1200/';
        $cloudinary_url .= 'c_fit,co_rgb:000,l_text:Arial_90_bold:' . urlencode($heading_encoded) . '/';
        $cloudinary_url .= 'l_text:Arial_90:' . urlencode($subtitle_encoded) . '/';
        $cloudinary_url .= 'l_text:Arial_90:' . urlencode($uren_encoded . '%20Uren%20Per%20Week') . '/';
        $cloudinary_url .= 'fl_layer_apply,g_north_west,x_100,y_100/';
        $cloudinary_url .= 'image-og_jobhob.png';

        // Return Cloudinary URL
        return esc_url($cloudinary_url);
    }

    // For other posts, return the default og:image
    return $og_image;
}

add_filter('wpseo_opengraph_image', 'custom_replace_og_image');

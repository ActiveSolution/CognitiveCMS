<?php

class Cognitive_CMS_Helper {
    public static function get_items_for_upload_ids() {
        $blob_names = Storage_Account_Helper::get_blob_names();
        $cognitive_cms_posts = self::get_cognitive_cms_attachments();

        $items_for_upload_ids = array();

        foreach ($cognitive_cms_posts as $key=>$post) {
            if (!in_array(basename($post->guid), $blob_names)) {
                array_push($items_for_upload_ids, $post->ID);
            }
        }

        return $items_for_upload_ids;
    }

    public static function get_posts_for_upload_ids() {
        $blob_names = Storage_Account_Helper::get_blob_names();
        $cognitive_cms_posts = self::get_cognitive_cms_posts();

        $items_for_upload_ids = array();

        foreach ($cognitive_cms_posts as $key=>$post) {
            if (!in_array($post->post_title.'.txt', $blob_names)) {
                array_push($items_for_upload_ids, $post->ID);
            }
        }

        return $items_for_upload_ids;
    }

    public static function get_items_for_enrichment_filenames() {
        $azure_search_filenames = Azure_Search_Helper::get_azure_search_filenames();
        $unenriched_cognitive_cms_attachments = self::get_unenriched_cognitive_cms_attachments();
        $unenriched_cognitive_cms_posts = self::get_unenriched_cognitive_cms_posts();

        $filenames = array();

        if (!empty($azure_search_filenames)) {
            foreach ($unenriched_cognitive_cms_attachments as $key=>$attachment) {
                if (in_array(basename($attachment->guid), $azure_search_filenames)) {
                    array_push($filenames, basename($attachment->guid));
                }
            }
            foreach ($unenriched_cognitive_cms_posts as $key=>$post) {
                if (in_array($post->post_title.'.txt', $azure_search_filenames)) {
                    array_push($filenames, $post->post_title);
                }
            }
        }

        return $filenames;
    }

    private static function get_cognitive_cms_attachments() {
        $args = array(
            'posts_per_page'   => -1,
            'post_type'   => 'attachment',
            'post_status' => 'inherit',
            'meta_query'  => array(
                array(
                    'key'     => 'asccms_include_in_cognitive_cms', // TODO: Add const value
                    'value'   => '1'
                )
            )
        );
        $query = new WP_Query($args);

        return $query->posts;
    }

    private static function get_cognitive_cms_posts() {
        $posts = get_posts(
                    array(
                        'post_type' => 'post',
                        'numberposts' => -1,
                        'meta_query'  => array(
                            array(
                                'key'     => 'asccms_include_in_cognitive_cms', // TODO: Add const value
                                'value'   => '1'
                            )
                        )
                    )
                );

        return $posts;
    }

    private static function get_unenriched_cognitive_cms_attachments() {
        $args = array(
            'posts_per_page'   => -1,
            'post_type'   => 'attachment',
            'post_status' => 'inherit',
            'meta_query'  => array(
                array(
                    'key'     => 'asccms_include_in_cognitive_cms',
                    'value'   => '1'
                ),
                array(
                    'key'     => 'asccms_tags',
                    'value'   => '', 
                    'compare' => '='
                ),
                array(
                    'key'     => 'asccms_caption',
                    'value'   => '', 
                    'compare' => '='
                ),
                array(
                    'key'     => 'asccms_dominant_colors',
                    'value'   => '', 
                    'compare' => '='
                )
            )
        );
        $query = new WP_Query($args);
        return $query->posts;
    }

    private static function get_unenriched_cognitive_cms_posts() {
        $posts = get_posts(
            array(
                'post_type' => 'post',
                'numberposts' => -1,
                'meta_query'  => array(
                    array(
                        'key'     => 'asccms_include_in_cognitive_cms',
                        'value'   => '1'
                    ),
                    array(
                        'key'     => 'asccms_persons',
                        'value'   => '', 
                        'compare' => '='
                    ),
                    array(
                        'key'     => 'asccms_locations',
                        'value'   => '', 
                        'compare' => '='
                    ),
                    array(
                        'key'     => 'asccms_organizations',
                        'value'   => '', 
                        'compare' => '='
                    ),
                    array(
                        'key'     => 'asccms_key_phrases',
                        'value'   => '', 
                        'compare' => '='
                    )
                )
            )
        );

        return $posts;
    }

    public static function enrich_data_for_filename($filename) {
        $post = self::get_attachment_by_filename($filename);
        if (!$post) {
            $post = self::get_post_by_filename($filename);
        }
        if ($post) {
            $post_id = $post->ID;
            if ($post->post_type === 'attachment') {
                $asccms_tags_value = get_post_meta($post_id, 'asccms_tags', true);
                if (!$asccms_tags_value) {
                    $tags = implode(", ", Azure_Search_Helper::get_tags_for_filename($filename));
                    self::set_meta_tags_for_post_id($tags, $post_id);
                }

                $asccms_caption_value = get_post_meta($post_id, 'asccms_caption', true);
                if (!$asccms_caption_value) {
                    $captions = implode(", ", Azure_Search_Helper::get_captions_for_filename($filename));
                    self::set_meta_captions_for_post_id($captions, $post_id);
                }

                $asccms_dominant_colors_value = get_post_meta($post_id, 'asccms_dominant_colors', true);
                if (!$asccms_dominant_colors_value) {
                    $dominant_colors = implode(", ", Azure_Search_Helper::get_dominant_colors_for_filename($filename));
                    self::set_meta_dominant_colors_for_post_id($dominant_colors, $post_id);
                }
            } else if ($post->post_type === 'post') {
                $asccms_persons_value = get_post_meta($post_id, 'asccms_persons', true);
                if (!$asccms_persons_value) {
                    $persons = implode(", ", Azure_Search_Helper::get_persons_for_filename($filename.'.txt'));
                    self::set_meta_persons_for_post_id($persons, $post_id);
                }

                $asccms_locations_value = get_post_meta($post_id, 'asccms_locations', true);
                if (!$asccms_locations_value) {
                    $locations = implode(", ", Azure_Search_Helper::get_locations_for_filename($filename.'.txt'));
                    self::set_meta_locations_for_post_id($locations, $post_id);
                }

                $asccms_organizations_value = get_post_meta($post_id, 'asccms_organizations', true);
                if (!$asccms_organizations_value) {
                    $organizations = implode(", ", Azure_Search_Helper::get_organizations_for_filename($filename.'.txt'));
                    self::set_meta_organizations_for_post_id($organizations, $post_id);
                }

                $asccms_key_phrases_value = get_post_meta($post_id, 'asccms_key_phrases', true);
                if (!$asccms_key_phrases_value) {
                    $key_phrases = implode(", ", Azure_Search_Helper::get_key_phrases_for_filename($filename.'.txt'));
                    self::set_meta_key_phrases_for_post_id($key_phrases, $post_id);
                }
            }
        }
    }

    private static function get_attachment_by_filename($filename) {
        $args = array(
            'posts_per_page' => 1,
            'post_type'      => 'attachment',
            'name'           => trim(pathinfo($filename)['filename']),
        );

        $query = new WP_Query($args);

        if (!$query || !isset($query->posts, $query->posts[0])) {
            return false;
        }

        return $query->posts[0];
    }

    private static function get_post_by_filename($filename) {
        $post = get_page_by_title($filename, OBJECT, 'post');

        if (!$post || !isset($post)) {
            return false;
        }

        return $post;
    }

    public static function set_meta_include_in_cognitive_cms_for_post_id($include_in_cognitive_cms, $post_id) {
        if (!add_post_meta($post_id, 'asccms_include_in_cognitive_cms', $include_in_cognitive_cms, true)) { 
            update_post_meta($post_id, 'asccms_include_in_cognitive_cms', $include_in_cognitive_cms);
        }
    }

    public static function set_meta_tags_for_post_id($tags, $post_id) {
        if (!add_post_meta($post_id, 'asccms_tags', $tags, true)) { 
            update_post_meta($post_id, 'asccms_tags', $tags);
        }
    }

    public static function set_meta_captions_for_post_id($captions, $post_id) {
        if (!add_post_meta($post_id, 'asccms_caption', $captions, true)) { 
            update_post_meta($post_id, 'asccms_caption', $captions);
        }
    }

    public static function set_meta_dominant_colors_for_post_id($dominant_colors, $post_id) {
        if (!add_post_meta($post_id, 'asccms_dominant_colors', $dominant_colors, true)) { 
            update_post_meta($post_id, 'asccms_dominant_colors', $dominant_colors);
        }
    }

    public static function set_meta_persons_for_post_id($persons, $post_id) {
        if (!add_post_meta($post_id, 'asccms_persons', $persons, true)) { 
            update_post_meta($post_id, 'asccms_persons', $persons);
        }
    }

    public static function set_meta_locations_for_post_id($locations, $post_id) {
        if (!add_post_meta($post_id, 'asccms_locations', $locations, true)) { 
            update_post_meta($post_id, 'asccms_locations', $locations);
        }
    }

    public static function set_meta_organizations_for_post_id($organizations, $post_id) {
        if (!add_post_meta($post_id, 'asccms_organizations', $organizations, true)) { 
            update_post_meta($post_id, 'asccms_organizations', $organizations);
        }
    }

    public static function set_meta_key_phrases_for_post_id($key_phrases, $post_id) {
        if (!add_post_meta($post_id, 'asccms_key_phrases', $key_phrases, true)) { 
            update_post_meta($post_id, 'asccms_key_phrases', $key_phrases);
        }
    }
}
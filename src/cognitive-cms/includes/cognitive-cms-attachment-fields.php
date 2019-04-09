<?php
if (!class_exists('ASCCMS_Attachment_Fields')) {
    class ASCCMS_Attachment_Fields {
        public function __construct() {
            add_filter('attachment_fields_to_edit', array($this, 'asccms_include_in_cognitive_cms_fields_to_edit'), 10, 2);
            add_filter('attachment_fields_to_save', array($this, 'asccms_include_in_cognitive_cms_fields_to_save'), 10, 2);

            add_filter('attachment_fields_to_edit', array($this, 'asccms_keywords_fields_to_edit'), 10, 2);
            add_filter('attachment_fields_to_save', array($this, 'asccms_keywords_fields_to_save'), 10, 2);

            add_filter('attachment_fields_to_edit', array($this, 'asccms_tags_fields_to_edit'), 10, 2);
            add_filter('attachment_fields_to_save', array($this, 'asccms_tags_fields_to_save'), 10, 2);

            add_filter('attachment_fields_to_edit', array($this, 'asccms_caption_fields_to_edit'), 10, 2);
            add_filter('attachment_fields_to_save', array($this, 'asccms_caption_fields_to_save'), 10, 2);

            add_filter('attachment_fields_to_edit', array($this, 'asccms_dominant_colors_fields_to_edit'), 10, 2);
            add_filter('attachment_fields_to_save', array($this, 'asccms_dominant_colors_fields_to_save'), 10, 2);

            add_filter('posts_join', array($this, 'cf_search_join'));
            add_filter('posts_where', array($this, 'cf_search_where'));
            add_filter('posts_distinct', array($this, 'cf_search_distinct'));
        }

        function cf_search_join($join) {
            global $wpdb;

            if (isset($_REQUEST['query']['s']) || isset($_REQUEST['s'])) {
                $join .=' LEFT JOIN '.$wpdb->postmeta. ' ON '. $wpdb->posts . '.ID = ' . $wpdb->postmeta . '.post_id ';
            }

            return $join;
        }

        function cf_search_where($where) {
            global $wpdb;

            if (isset($_REQUEST['query']['s']) || isset($_REQUEST['s'])) {
                $where = preg_replace(
                    "/\(\s*".$wpdb->posts.".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/",
                    "(".$wpdb->posts.".post_title LIKE $1) OR (".$wpdb->postmeta.".meta_value LIKE $1)", $where );
            }

            return $where;
        }

        function cf_search_distinct($where) {
            global $wpdb;

            if (isset($_REQUEST['query']['s']) || isset($_REQUEST['s'])) {
                return "DISTINCT";
            }

            return $where;
        }

        function asccms_include_in_cognitive_cms_fields_to_edit($form_fields, $post) {
            $asccms_include_in_cognitive_cms_value = get_post_meta($post->ID, 'asccms_include_in_cognitive_cms', true);
            $form_fields['asccms-include-in-cognitive-cms'] = array(
                'label' => 'Include?',
                'input' => 'html',
                'html' => '<label for="attachments-'.$post->ID.'-asccms-include-in-cognitive-cms"><input type="checkbox" id="attachments-'.$post->ID.'-asccms-include-in-cognitive-cms" name="attachments['.$post->ID.'][asccms-include-in-cognitive-cms]" value="1"'.($asccms_include_in_cognitive_cms_value ? ' checked="checked"' : '').' /></label>',
                'value' => $asccms_include_in_cognitive_cms_value,
                'helps' => 'Include in Cognitive CMS?'
            );

            return $form_fields;
        }

        function asccms_include_in_cognitive_cms_fields_to_save($post, $attachment) {
            if (isset($attachment['asccms-include-in-cognitive-cms'])) {
                update_post_meta($post['ID'], 'asccms_include_in_cognitive_cms', $attachment['asccms-include-in-cognitive-cms']);
            } else {
                delete_post_meta($post['ID'], 'asccms_include_in_cognitive_cms');
            }

            return $post;
        }

        function asccms_keywords_fields_to_edit($form_fields, $post) {
            $asccms_keywords_value = get_post_meta($post->ID, 'asccms_keywords', true);
            $form_fields['asccms-keywords'] = array(
                'label' => 'Keywords',
                'input' => 'textarea',
                'value' => $asccms_keywords_value,
                'helps' => 'Keywords for Cognitive CMS'
            );

            return $form_fields;
        }

        function asccms_keywords_fields_to_save($post, $attachment) {
            if (isset($attachment['asccms-keywords'])) {
                update_post_meta($post['ID'], 'asccms_keywords', $attachment['asccms-keywords']);
            } else {
                delete_post_meta($post['ID'], 'asccms_keywords');
            }

            return $post;
        }

        function asccms_tags_fields_to_edit($form_fields, $post) {
            $asccms_tags_value = get_post_meta($post->ID, 'asccms_tags', true);
            $form_fields['asccms-tags'] = array(
                'label' => 'Tags',
                'input' => 'textarea',
                'value' => $asccms_tags_value,
                'helps' => 'Tags from Cognitive CMS'
            );

            return $form_fields;
        }

        function asccms_tags_fields_to_save($post, $attachment) {
            if (isset($attachment['asccms-tags'])) {
                update_post_meta($post['ID'], 'asccms_tags', $attachment['asccms-tags']);
            } else {
                delete_post_meta($post['ID'], 'asccms_tags');
            }

            return $post;
        }

        function asccms_caption_fields_to_edit($form_fields, $post) {
            $asccms_caption_value = get_post_meta($post->ID, 'asccms_caption', true);
            $form_fields['asccms-caption'] = array(
                'label' => 'Caption',
                'input' => 'textarea',
                'value' => $asccms_caption_value,
                'helps' => 'Caption from Cognitive CMS'
            );

            return $form_fields;
        }

        function asccms_caption_fields_to_save($post, $attachment) {
            if (isset($attachment['asccms-caption'])) {
                update_post_meta($post['ID'], 'asccms_caption', $attachment['asccms-caption']);
            } else {
                delete_post_meta($post['ID'], 'asccms_caption');
            }

            return $post;
        }

        function asccms_dominant_colors_fields_to_edit($form_fields, $post) {
            $asccms_dominant_colors_value = get_post_meta($post->ID, 'asccms_dominant_colors', true);
            $form_fields['asccms-dominant-colors'] = array(
                'label' => 'Dominant Colors',
                'input' => 'textarea',
                'value' => $asccms_dominant_colors_value,
                'helps' => 'Dominant Colors from Cognitive CMS'
            );

            return $form_fields;
        }

        function asccms_dominant_colors_fields_to_save($post, $attachment) {
            if (isset($attachment['asccms-dominant-colors'])) {
                update_post_meta($post['ID'], 'asccms_dominant_colors', $attachment['asccms-dominant-colors']);
            } else {
                delete_post_meta($post['ID'], 'asccms_dominant_colors');
            }

            return $post;
        }
    }
}

$asccms_Attachment_Fields = new ASCCMS_Attachment_Fields();
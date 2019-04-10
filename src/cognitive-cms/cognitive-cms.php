<?php
/**
 * @package Cognitive CMS
 */
/*
Plugin Name: Cognitive CMS
Plugin URI: https://www.activesolution.se/cognitive-cms
Description: Cognitive CMS
Version: 0.0.1
Author: Active Solution
Author URI: https://www.activesolution.se/
*/

/* ASCCMS - Active Solution Cognitive CMS */

//  Exit if accessed directly.
defined('ABSPATH') || exit;

// Only load if Gutenberg is available.
if (!function_exists('register_block_type')) {
    return;
}

define('ASCCMS_PLUGIN_PATH', plugin_dir_path( __FILE__ ));
define('ASCCMS_NAME', 'Cognitive CMS');
define('ASCCMS_ABBREVIATION', 'asccms');
define('ASCCMS_STORAGE_ACCOUNT_CONTAINER_NAME_DOCUMENT_IMAGE', ASCCMS_ABBREVIATION.'-document-image');

define('ASCCMS_PAGE_SLUG_DASHBOARD_PAGE', 'cognitive-cms-dashboard-page.php');
define('ASCCMS_PAGE_SLUG_DETAILS_PAGE', 'cognitive-cms-details-page.php');
define('ASCCMS_PAGE_SLUG_SETTINGS_PAGE', 'cognitive-cms-settings-page.php');
define('ASCCMS_SEARCH_INDEX_NAME', 'index-'.ASCCMS_ABBREVIATION);
define('ASCCMS_SEARCH_INDEXER_NAME', 'indexer-'.ASCCMS_ABBREVIATION);

define('ASCCMS_PAGE_NAME_SETTINGS_PAGE', 'Settings');

define('ASCCMS_ICON', 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhLS0gR2VuZXJhdG9yOiBBZG9iZSBJbGx1c3RyYXRvciAxOS4xLjAsIFNWRyBFeHBvcnQgUGx1Zy1JbiAuIFNWRyBWZXJzaW9uOiA2LjAwIEJ1aWxkIDApICAtLT4NCjxzdmcgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiDQoJIHdpZHRoPSI1MHB4IiBoZWlnaHQ9IjUwcHgiIHZpZXdCb3g9IjAgMCA1MCA1MCIgZW5hYmxlLWJhY2tncm91bmQ9Im5ldyAwIDAgNTAgNTAiIHhtbDpzcGFjZT0icHJlc2VydmUiPg0KPHBhdGggZmlsbD0iIzAwOTU4MCIgZD0iTTQ5LjM4MywyNC44NzNjLTAuOTA3LTIuMTUtMi43ODUtMy45Ny01LjIxNC01LjA4M2MtMS4xMi0zLjc2NS00LjI1NC03LjUzLTguOTI4LTcuNTMNCgljLTAuMTQ5LDAtMC4yOTksMC4wMDQtMC40NTEsMC4wMTFjLTEuOTQ5LTEuMzk4LTMuODQzLTIuMDc5LTUuNzcxLTIuMDc5Yy0xLjE2MiwwLTIuMzUsMC4yNTctMy41NDEsMC43NjQNCgljLTAuODgyLTAuNDYyLTEuODkzLTAuNzAzLTIuOTc1LTAuNzAzYy0yLjM5MiwwLTQuNjYxLDEuMTA1LTYuMDYxLDIuMzk5Yy0wLjQ0MS0wLjEwNC0wLjktMC4xNTYtMS4zNzQtMC4xNTYNCgljLTMuNTY4LDAtNy4xMDQsMi45NDgtOC4wNiw2LjUyMmMtMy4wNzYsMS4xMTYtNS4zMjIsMy4xNDMtNi4zNzcsNS43ODFjLTEuMDIzLDIuNTU5LTAuNzk4LDUuNDIsMC42MTcsNy44NQ0KCWMxLjQwNiwyLjQxMywzLjg2NiwzLjg1NCw2LjU4LDMuODU0YzEuMDQxLDAsMi4wNDUtMC4yMTMsMi45Ni0wLjYyMmMxLjY1LDEuNSwzLjc3MywyLjM5Nyw1Ljc4NCwyLjM5Nw0KCWMwLjcwMSwwLDEuMzcyLTAuMTA0LDIuMDA3LTAuMzA5YzEuNjI4LDEuMzM5LDQuMTIzLDEuODQxLDYuMTQ4LDEuODQxYzIuMzg5LDAsNC40NjUtMC42MzEsNS44NDEtMS43MzMNCgljMC44OTIsMC40MTMsMS44NzQsMC42MjcsMi44OTksMC42MjdoMC4wMDFjMi41NjMsMCw1LjIxMi0xLjM2Niw2LjY1OS0zLjI2NWMwLjgwNywwLjMzMSwxLjY1NiwwLjUwMiwyLjUxNSwwLjUwMg0KCWMwLDAsMC4wMDEsMCwwLjAwMSwwYzIuODAxLTAuMDAxLDUuNDAyLTEuODI5LDYuNjI2LTQuNjU4QzUwLjIwMywyOS4xMjQsNTAuMjQzLDI2LjkwOCw0OS4zODMsMjQuODczeiIvPg0KPHBhdGggZmlsbD0iI0ZGRkZGRiIgZD0iTTE2LjA3LDM4LjI1NnYtOC44MDRjMC0xLjA5My0wLjg4OS0xLjk4MS0xLjk4MS0xLjk4MUgxMC43OXYxLjRoMy4yOTljMC4zMiwwLDAuNTgxLDAuMjYxLDAuNTgxLDAuNTgxDQoJdjguNTY5QzE1LjEzNCwzOC4xNDQsMTUuNjA0LDM4LjIyMiwxNi4wNywzOC4yNTZ6Ii8+DQo8cGF0aCBmaWxsPSIjRkZGRkZGIiBkPSJNMjIuMTU4LDIzLjE5NmgtNC40MzJjLTAuMzIsMC0wLjU4MS0wLjI2MS0wLjU4MS0wLjU4MVYxMi4wNzZjLTAuMjUyLDAuMTg2LTAuNDg4LDAuMzc4LTAuNzAyLDAuNTc2DQoJYy0wLjIyOC0wLjA1NC0wLjQ2MS0wLjA5My0wLjY5OC0wLjExOXYxMC4wODJjMCwxLjA5MywwLjg4OSwxLjk4MSwxLjk4MSwxLjk4MWg0LjQzMmMwLjMyLDAsMC41OCwwLjI2LDAuNTgsMC41OHYzLjI4MmgxLjR2LTMuMjgyDQoJQzI0LjEzOSwyNC4wODQsMjMuMjUsMjMuMTk2LDIyLjE1OCwyMy4xOTZ6Ii8+DQo8cGF0aCBmaWxsPSIjRkZGRkZGIiBkPSJNNDAuODAzLDI4Ljc5NHYtMS4yNTZoLTEuNDAxdjEuMjU2YzAsMC4zMi0wLjI2LDAuNTgxLTAuNTgsMC41ODFIMzMuMjFWMTkuMzM0DQoJYzAtMS4wOTItMC44ODgtMS45ODEtMS45ODEtMS45ODFoLTMuODE3djEuNGgzLjgxN2MwLjMyLDAsMC41OCwwLjI2LDAuNTgsMC41ODF2MTkuMTcxYzAuNDUzLDAuMTEsMC45MjIsMC4xNzQsMS40LDAuMTkxdi03LjkyDQoJaDUuNjExQzM5LjkxNCwzMC43NzUsNDAuODAzLDI5Ljg4Nyw0MC44MDMsMjguNzk0eiIvPg0KPHBhdGggZmlsbD0iI0ZGRkZGRiIgZD0iTTIzLjQzOSwzNC4yNWMtMS43MiwwLTMuMTItMS40LTMuMTItMy4xMmMwLTEuNzIxLDEuNC0zLjEyMSwzLjEyLTMuMTIxYzEuNzIxLDAsMy4xMiwxLjQsMy4xMiwzLjEyMQ0KCUMyNi41NTksMzIuODUsMjUuMTU5LDM0LjI1LDIzLjQzOSwzNC4yNXogTTIzLjQzOSwyOS4zMDRjLTEuMDA2LDAtMS44MjUsMC44MTktMS44MjUsMS44MjZjMCwxLjAwNywwLjgxOSwxLjgyNiwxLjgyNSwxLjgyNg0KCWMxLjAwNywwLDEuODI1LTAuODE5LDEuODI1LTEuODI2QzI1LjI2NCwzMC4xMjMsMjQuNDQ1LDI5LjMwNCwyMy40MzksMjkuMzA0eiIvPg0KPHBhdGggZmlsbD0iI0ZGRkZGRiIgZD0iTTguNTI1LDMxLjI4Yy0xLjcyMSwwLTMuMTItMS40LTMuMTItMy4xMjFjMC0xLjcyMSwxLjQtMy4xMiwzLjEyLTMuMTJzMy4xMiwxLjQsMy4xMiwzLjEyDQoJQzExLjY0NSwyOS44OCwxMC4yNDYsMzEuMjgsOC41MjUsMzEuMjh6IE04LjUyNSwyNi4zMzNjLTEuMDA3LDAtMS44MjUsMC44MTktMS44MjUsMS44MjZjMCwxLjAwNywwLjgxOSwxLjgyNiwxLjgyNSwxLjgyNg0KCXMxLjgyNS0wLjgxOSwxLjgyNS0xLjgyNkMxMC4zNTEsMjcuMTUyLDkuNTMyLDI2LjMzMyw4LjUyNSwyNi4zMzN6Ii8+DQo8cGF0aCBmaWxsPSIjRkZGRkZGIiBkPSJNNDAuMTE0LDI4LjMwMWMtMS43MjEsMC0zLjEyLTEuNC0zLjEyLTMuMTJjMC0xLjcyMSwxLjQtMy4xMiwzLjEyLTMuMTJjMS43MiwwLDMuMTIsMS40LDMuMTIsMy4xMg0KCUM0My4yMzQsMjYuOTAyLDQxLjgzNCwyOC4zMDEsNDAuMTE0LDI4LjMwMXogTTQwLjExNCwyMy4zNTZjLTEuMDA3LDAtMS44MjUsMC44MTktMS44MjUsMS44MjVzMC44MTksMS44MjYsMS44MjUsMS44MjYNCgljMS4wMDYsMCwxLjgyNS0wLjgxOSwxLjgyNS0xLjgyNlM0MS4xMiwyMy4zNTYsNDAuMTE0LDIzLjM1NnoiLz4NCjxwYXRoIGZpbGw9IiNGRkZGRkYiIGQ9Ik0yNS4xNDcsMjEuMTYxYy0xLjcyMSwwLTMuMTItMS40LTMuMTItMy4xMmMwLTEuNzIxLDEuNC0zLjEyLDMuMTItMy4xMnMzLjEyLDEuNCwzLjEyLDMuMTINCglDMjguMjY4LDE5Ljc2MSwyNi44NjgsMjEuMTYxLDI1LjE0NywyMS4xNjF6IE0yNS4xNDcsMTYuMjE1Yy0xLjAwNywwLTEuODI1LDAuODE5LTEuODI1LDEuODI2YzAsMS4wMDYsMC44MTksMS44MjUsMS44MjUsMS44MjUNCglzMS44MjUtMC44MTksMS44MjUtMS44MjVDMjYuOTczLDE3LjAzNCwyNi4xNTQsMTYuMjE1LDI1LjE0NywxNi4yMTV6Ii8+DQo8L3N2Zz4NCg==');

require_once ASCCMS_PLUGIN_PATH . 'helpers/azure-search-helper.php';
require_once ASCCMS_PLUGIN_PATH . 'helpers/storage-account-helper.php';
require_once ASCCMS_PLUGIN_PATH . 'helpers/cognitive-cms-helper.php';

require_once ASCCMS_PLUGIN_PATH . 'includes/cognitive-cms-dashboard-page.php';
require_once ASCCMS_PLUGIN_PATH . 'includes/cognitive-cms-settings-page.php';

require_once ASCCMS_PLUGIN_PATH . 'includes/cognitive-cms-attachment-fields.php';

require_once "vendor-azure-storage-blob/autoload.php";
require_once "vendor-php-azure-search/autoload.php";

if (!class_exists('ASCCMS_Plugin')) {
    class ASCCMS_Plugin {
        public function __construct() {
            add_filter('bulk_actions-upload', array($this, 'register_include_attachment_in_cognitive_cms_bulk_action'));
            add_filter('handle_bulk_actions-upload', array($this, 'include_attachment_in_cognitive_cms_bulk_action_handler'), 10, 3);

            add_filter('bulk_actions-upload', array($this, 'register_remove_attachment_enriched_data_bulk_action'));
            add_filter('handle_bulk_actions-upload', array($this, 'remove_attachment_enriched_data_bulk_action_handler'), 10, 3);

            add_filter('bulk_actions-edit-post', array($this, 'register_include_post_in_cognitive_cms_bulk_action'));
            add_filter('handle_bulk_actions-edit-post', array($this, 'include_post_in_cognitive_cms_bulk_action_handler'), 10, 3);

            add_filter('bulk_actions-edit-post', array($this, 'register_remove_post_enriched_data_bulk_action'));
            add_filter('handle_bulk_actions-edit-post', array($this, 'remove_post_enriched_data_bulk_action_handler'), 10, 3);

            add_filter('manage_media_columns', array($this, 'attachment_column_id'));
            add_filter('manage_media_custom_column', array($this, 'attachment_column_id_row'), 10, 2);

            add_filter('manage_posts_columns', array($this, 'post_column_id'));
            add_filter('manage_posts_custom_column', array($this, 'post_column_id_row'), 10, 2);

            add_action( 'init', array($this, 'sidebar_plugin_register' ));
            add_action( 'enqueue_block_editor_assets', array($this, 'sidebar_plugin_script_enqueue' ));
            add_action( 'enqueue_block_assets', array($this, 'sidebar_plugin_style_enqueue' ));
        }

        function sidebar_plugin_register() {
            wp_register_script(
                'plugin-sidebar-js',
                plugins_url( 'plugin-sidebar.js', __FILE__ ),
                array(
                    'wp-plugins',
                    'wp-edit-post',
                    'wp-element',
                    'wp-components'
                )
            );
            wp_register_style(
                'plugin-sidebar-css',
                plugins_url( 'plugin-sidebar.css', __FILE__ )
            );
            register_meta('post', 'asccms_include_in_cognitive_cms', array(
                'show_in_rest' => true,
                'single' => true,
                'type' => 'boolean',
            ));
            register_meta('post', 'asccms_persons', array(
                'show_in_rest' => true,
                'single' => true,
                'type' => 'string',
            ));
            register_meta('post', 'asccms_locations', array(
                'show_in_rest' => true,
                'single' => true,
                'type' => 'string',
            ));
            register_meta('post', 'asccms_organizations', array(
                'show_in_rest' => true,
                'single' => true,
                'type' => 'string',
            ));
            register_meta('post', 'asccms_key_phrases', array(
                'show_in_rest' => true,
                'single' => true,
                'type' => 'string',
            ));
        }

        function sidebar_plugin_script_enqueue() {
            wp_enqueue_script( 'plugin-sidebar-js' );
        }
        
        function sidebar_plugin_style_enqueue() {
            wp_enqueue_style( 'plugin-sidebar-css' );
        }
 
        function register_include_attachment_in_cognitive_cms_bulk_action($bulk_actions) {
            $bulk_actions['include_in_cognitive_cms'] = __( 'Include in Cognitive CMS', 'include_in_cognitive_cms');
            return $bulk_actions;
        }

        function include_attachment_in_cognitive_cms_bulk_action_handler($redirect_to, $doaction, $post_ids) {
            if ($doaction === 'include_in_cognitive_cms') {
                foreach ($post_ids as $post_id) {
                    Cognitive_CMS_Helper::set_meta_include_in_cognitive_cms_for_post_id(1, $post_id);
                    Cognitive_CMS_Helper::set_meta_tags_for_post_id('', $post_id);
                    Cognitive_CMS_Helper::set_meta_captions_for_post_id('', $post_id);
                    Cognitive_CMS_Helper::set_meta_dominant_colors_for_post_id('', $post_id);
                }
            }
            return $redirect_to;
        }

        function register_remove_attachment_enriched_data_bulk_action($bulk_actions) {
            $bulk_actions['remove_enriched_data'] = __( 'Remove enriched data', 'remove_enriched_data');
            return $bulk_actions;
        }

        function remove_attachment_enriched_data_bulk_action_handler($redirect_to, $doaction, $post_ids) {
            if ($doaction === 'remove_enriched_data') {
                foreach ($post_ids as $post_id) {
                    Cognitive_CMS_Helper::set_meta_tags_for_post_id('', $post_id);
                    Cognitive_CMS_Helper::set_meta_captions_for_post_id('', $post_id);
                    Cognitive_CMS_Helper::set_meta_dominant_colors_for_post_id('', $post_id);
                }
            }
            return $redirect_to;
        }
 
        function register_include_post_in_cognitive_cms_bulk_action($bulk_actions) {
            $bulk_actions['include_in_cognitive_cms'] = __( 'Include in Cognitive CMS', 'include_in_cognitive_cms');
            return $bulk_actions;
        }

        function include_post_in_cognitive_cms_bulk_action_handler($redirect_to, $doaction, $post_ids) {
            if ($doaction === 'include_in_cognitive_cms') {
                foreach ($post_ids as $post_id) {
                    Cognitive_CMS_Helper::set_meta_include_in_cognitive_cms_for_post_id(1, $post_id);
                    Cognitive_CMS_Helper::set_meta_persons_for_post_id('', $post_id);
                    Cognitive_CMS_Helper::set_meta_locations_for_post_id('', $post_id);
                    Cognitive_CMS_Helper::set_meta_organizations_for_post_id('', $post_id);
                    Cognitive_CMS_Helper::set_meta_key_phrases_for_post_id('', $post_id);
                }
            }
            return $redirect_to;
        }

        function register_remove_post_enriched_data_bulk_action($bulk_actions) {
            $bulk_actions['remove_enriched_data'] = __( 'Remove enriched data', 'remove_enriched_data');
            return $bulk_actions;
        }

        function remove_post_enriched_data_bulk_action_handler($redirect_to, $doaction, $post_ids) {
            if ($doaction === 'remove_enriched_data') {
                foreach ($post_ids as $post_id) {
                    Cognitive_CMS_Helper::set_meta_persons_for_post_id('', $post_id);
                    Cognitive_CMS_Helper::set_meta_locations_for_post_id('', $post_id);
                    Cognitive_CMS_Helper::set_meta_organizations_for_post_id('', $post_id);
                    Cognitive_CMS_Helper::set_meta_key_phrases_for_post_id('', $post_id);
                }
            }
            return $redirect_to;
        }

        function attachment_column_id($columns) {
            $columns['cognitiveCMS'] = __('Cognitive CMS');
            return $columns;
        }

        function attachment_column_id_row($columnName, $columnID){
            if($columnName == 'cognitiveCMS') {
                $meta = get_post_meta($columnID, 'asccms_include_in_cognitive_cms', true);
                echo $meta ? 'Included' : null;
            }
        }

        function post_column_id($columns) {
            $columns['cognitiveCMS'] = __('Cognitive CMS');
            return $columns;
        }

        function post_column_id_row($columnName, $columnID){
            if($columnName == 'cognitiveCMS') {
                $meta = get_post_meta($columnID, 'asccms_include_in_cognitive_cms', true);
                echo $meta ? 'Included' : null;
            }
        }

        public static function deactivate() {

        }

        public static function activate() {

        }
    }

    register_activation_hook(__FILE__, array('ASCCMS_Plugin', 'activate'));
    register_deactivation_hook(__FILE__, array('ASCCMS_Plugin', 'deactivate'));
}

$asccms_Plugin = new ASCCMS_Plugin();
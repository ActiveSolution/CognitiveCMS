<?php

use MicrosoftAzure\Storage\Blob\BlobRestProxy;

if (!class_exists('ASCCMS_Plugin_Settings')) {
    class ASCCMS_Plugin_Settings {
        public function __construct() {
            add_action('admin_menu', array($this, 'asccms_plugin_settings_menu'));
            add_action('admin_init', array($this, 'register_asccms_plugin_settings'));
            add_action('admin_footer', array($this, 'asccms_storage_account_test_connection_javascript'));
            add_action('wp_ajax_post_storage_account_test_connection', array($this, 'post_storage_account_test_connection'));
            add_action('admin_footer', array($this, 'asccms_azure_search_test_connection_javascript'));
            add_action('wp_ajax_post_azure_search_test_connection', array($this, 'post_azure_search_test_connection'));
        }

        function asccms_plugin_settings_menu() {
            add_submenu_page(ASCCMS_PAGE_SLUG_DASHBOARD_PAGE, ASCCMS_PAGE_NAME_SETTINGS_PAGE, ASCCMS_PAGE_NAME_SETTINGS_PAGE, 'manage_options', ASCCMS_PAGE_SLUG_SETTINGS_PAGE, array($this, 'asccms_show_screen'));
        }

        function register_asccms_plugin_settings() {
            register_setting('asccms-settings-group', 'asccms_storage_account_name');
            register_setting('asccms-settings-group', 'asccms_storage_account_key');
            register_setting('asccms-settings-group', 'asccms_azure_search_name');
            register_setting('asccms-settings-group', 'asccms_azure_search_admin_key');
        }

        function asccms_show_screen() {
            $storage_account_name = get_option('asccms_storage_account_name');
            $storage_account_key = get_option('asccms_storage_account_key');
            $azure_search_name = Azure_Search_Helper::get_azure_search_name();
            $azure_search_admin_key = Azure_Search_Helper::get_azure_search_admin_key();

            ?>
            <div class="wrap">
                <h1><?php echo ASCCMS_NAME; ?> / Settings</h1>
                <form method="post" name="asccms-options-form" id="asccms-options-form" action="options.php">
                <?php
                    settings_fields('asccms-settings-group');
                    do_settings_sections('asccms-settings-group');
                ?>
                    <h2>Storage Account</h2>
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="asccms_storage_account_name">
                                    Storage Account Name
                                </label>
                            </th>
                            <td>
                                <input type="text" id="asccms_storage_account_name" name="asccms_storage_account_name" class="regular-text" title="Store Account Name" value="<?php echo isset($storage_account_name) ? esc_attr($storage_account_name) : ''; ?>"/>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="asccms_storage_account_key">
                                    Store Account Key
                                </label>
                            </th>
                            <td>
                                <input type="text" id="asccms_storage_account_key" name="asccms_storage_account_key" class="regular-text" title="Store Account Key" value="<?php echo isset($storage_account_key) ? esc_attr($storage_account_key) : ''; ?>"/>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <button type="button" id="asccms_storage_account_test_connection" class="button button-secondary" title="Test Connection">Test Connection</button>
                            </th>
                            <td>
                                <span id="asccms_storage_account_test_connection_wrapper"></span>
                            </td>
                        </tr>
                    </table>
                    <h2>Azure Search</h2>
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="asccms_azure_search_name">
                                    Search Service Name
                                </label>
                            </th>
                            <td>
                                <input type="text" id="asccms_azure_search_name" name="asccms_azure_search_name" class="regular-text" title="Search Service Name" value="<?php echo isset($azure_search_name) ? esc_attr($azure_search_name) : ''; ?>"/>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="asccms_azure_search_admin_key">
                                    Admin Key
                                </label>
                            </th>
                            <td>
                                <input type="text" id="asccms_azure_search_admin_key" name="asccms_azure_search_admin_key" class="regular-text" title="Admin Key" value="<?php echo isset($azure_search_admin_key) ? esc_attr($azure_search_admin_key) : ''; ?>"/>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <button type="button" id="asccms_azure_search_test_connection" class="button button-secondary" title="Test Connection">Test Connection</button>
                            </th>
                            <td>
                                <span id="asccms_azure_search_test_connection_wrapper"></span>
                            </td>
                        </tr>
                    </table>
                <?php
                    submit_button();
                ?>
                </form>
            </div>
            <?php
        }

        function asccms_storage_account_test_connection_javascript() { ?>
            <script type="text/javascript" >
                jQuery(document).ready(function($) {
                    jQuery("#asccms_storage_account_test_connection").click(function () {
                        let data = {
                            'action': 'post_storage_account_test_connection',
                            'asccms_storage_account_name': jQuery("#asccms_storage_account_name").val(),
                            'asccms_storage_account_key': jQuery("#asccms_storage_account_key").val()
                        };
                        jQuery.post(ajaxurl, data, function (response) {
                            jQuery("#asccms_storage_account_test_connection_wrapper").html(response);
                        });
                    });
                });
            </script><?php
        }

        function post_storage_account_test_connection() {
            $storage_account_name = $_POST['asccms_storage_account_name'];
            $storage_account_key = $_POST['asccms_storage_account_key'];

            if (Storage_Account_Helper::is_storage_account_connection_working($storage_account_name, $storage_account_key)) {
                echo '<span class="dashicons dashicons-yes"></span>';
            } else {
                echo '<span class="dashicons dashicons-no-alt"></span>';
            }

            wp_die();
        }

        function asccms_azure_search_test_connection_javascript() { ?>
            <script type="text/javascript" >
                jQuery(document).ready(function($) {
                    jQuery("#asccms_azure_search_test_connection").click(function () {
                        let data = {
                            'action': 'post_azure_search_test_connection',
                            'asccms_azure_search_name': jQuery("#asccms_azure_search_name").val(),
                            'asccms_azure_search_admin_key': jQuery("#asccms_azure_search_admin_key").val()
                        };
                        jQuery.post(ajaxurl, data, function (response) {
                            jQuery("#asccms_azure_search_test_connection_wrapper").html(response);
                        });
                    });
                });
            </script><?php
        }

        function post_azure_search_test_connection() {
            $azure_search_name = $_POST['asccms_azure_search_name'];
            $azure_search_admin_key = $_POST['asccms_azure_search_admin_key'];

            if (Azure_Search_Helper::is_azure_search_connection_working($azure_search_name, $azure_search_admin_key)) {
                echo '<span class="dashicons dashicons-yes"></span>';
            } else {
                echo '<span class="dashicons dashicons-no-alt"></span>';
            }

            wp_die();
        }

        public static function deactivate() {

        }

        public static function activate() {

        }
    }

    register_activation_hook(__FILE__, array('ASCCMS_Plugin_Settings', 'activate'));
    register_deactivation_hook(__FILE__, array('ASCCMS_Plugin_Settings', 'deactivate'));
}

$asccms_Plugin_Settings = new ASCCMS_Plugin_Settings();
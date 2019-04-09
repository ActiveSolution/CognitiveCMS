<?php

if (!class_exists('ASCCMS_Plugin_Details')) {
    class ASCCMS_Plugin_Details {
        public function __construct() {
            add_action('admin_menu', array($this, 'asccms_plugin_details_page'));
        }

        function asccms_plugin_details_page() {
            add_submenu_page(null, ASCCMS_NAME, ASCCMS_NAME, 'manage_options', ASCCMS_PAGE_SLUG_DETAILS_PAGE, array($this, 'asccms_show_screen'));
        }

        function asccms_show_screen() {
            echo '  <div class="wrap">
                        <h1><a href="'.menu_page_url(ASCCMS_PAGE_SLUG_DASHBOARD_PAGE, false).'">'.ASCCMS_NAME.'</a> / Details</h1>';

            if (isset($_GET['filename'])) {
                $filename = $_GET['filename'];

                print_r($this->get_enriched_data_for_filename($filename));
            }

            echo '</div>';
        }

        function get_enriched_data_for_filename($filename) {
            $azure_url = 'https://ss-cognitivecms-daliboras.search.windows.net/';
            $azure_admin_key = '1F2058942A6C1667C364BC58156FE7E7';
            $azure_version = '2017-11-11';
            $azuresearch = new BenjaminHirsch\Azure\Search\Service($azure_url, $azure_admin_key, $azure_version);
            $results = $azuresearch->search('index-cognitivecms-daliboras', null, ['filter' => "metadata_storage_name eq '".$filename."'"]);

            if ($results && sizeof($results) > 0) {
                return (reset($results[value]));
            }
        }
    }

    register_activation_hook(__FILE__, array('ASCCMS_Plugin_Details', 'activate'));
    register_deactivation_hook(__FILE__, array('ASCCMS_Plugin_Details', 'deactivate'));
}

$asccms_Plugin_Details = new ASCCMS_Plugin_Details();
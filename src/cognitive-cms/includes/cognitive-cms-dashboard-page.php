<?php

use MicrosoftAzure\Storage\Blob\BlobRestProxy;

if (!class_exists('ASCCMS_Plugin_Dashboard')) {
    class ASCCMS_Plugin_Dashboard {
        public function __construct() {
            add_action('admin_menu', array($this, 'asccms_plugin_dashboard_page'));
            add_action('admin_footer', array($this, 'asccms_dashboard_javascript'));
            add_action('wp_ajax_asccms_post_upload', array($this, 'asccms_post_upload'));
            add_action('wp_ajax_asccms_post_enrich', array($this, 'asccms_post_enrich'));
        }

        function asccms_plugin_dashboard_page() {
            add_menu_page(ASCCMS_NAME, ASCCMS_NAME, 'manage_options', ASCCMS_PAGE_SLUG_DASHBOARD_PAGE, array($this, 'asccms_show_screen'), ASCCMS_ICON);
        }

        function asccms_show_screen() {
            echo '  <div class="wrap">
                        <h1>'.ASCCMS_NAME.'</h1>';

            if (Storage_Account_Helper::is_storage_account_setup_completed()) {
                echo $this->get_items_to_upload_card();
                echo $this->get_items_to_enrich_card();
            } else {
                echo '<div class="card"><p><a href="'.admin_url('admin.php?page='.ASCCMS_PAGE_SLUG_SETTINGS_PAGE.'').'">Please update Settings ...</a></p></div>';
            }

            echo '</div>';
        }

        function get_items_to_upload_card() {
            $items_for_upload_ids = Cognitive_CMS_Helper::get_items_for_upload_ids();
            $posts_for_upload_ids = Cognitive_CMS_Helper::get_posts_for_upload_ids();
            $number_of_items_for_upload = count($items_for_upload_ids) + count($posts_for_upload_ids);

            $result = '';
            if ($number_of_items_for_upload > 0) {
                $result .= '<div class="card asccms-upload-card">';
                $result .= '<div class="asccms-upload-default">';
                $result .= '<p>There are '.$number_of_items_for_upload.' assets waiting for upload!</p>';
                $result .= '<button class="button asccms-upload-button">Upload to Azure</button>';
                $result .= '</div>';
                $result .= '<div class="asccms-upload-uploading" style="display: none;">';
                $result .= '<p>Uploading to Azure ...</p>';
                $result .= '<progress value="0" max="'.$number_of_items_for_upload.'" class="asccms-upload-progress"></progress>';
                $result .= '</div>';
                foreach  ($items_for_upload_ids as $key=>$id) {
                    $result .= '<input type="hidden" class="asccms-upload-post" data-post-id="'.$id.'" data-post-type="attachment" />';
                }
                foreach  ($posts_for_upload_ids as $key=>$id) {
                    $result .= '<input type="hidden" class="asccms-upload-post" data-post-id="'.$id.'" data-post-type="post" />';
                }
                $result .= '</div>';
            }
            $uploaded_card_style = $number_of_items_for_upload > 0 ? ' style="display: none;"' : '';
            $result .= '<div class="card asccms-uploaded-card"'.$uploaded_card_style.'>';
            $result .= '<p>All marked assets are uploaded to Azure!</p>';
            $result .= '</div>';

            return $result;
        }

        function get_items_to_enrich_card() {
            $enriched_items = Cognitive_CMS_Helper::get_items_for_enrichment_filenames();
            $number_of_enriched_items = count($enriched_items);

            $result = '';
            if ($number_of_enriched_items > 0) {
                $result .= '<div class="card asccms-enrich-card">';
                $result .= '<div class="asccms-enrich-default">';
                $result .= '<p>There are '.$number_of_enriched_items.' WordPress assets waiting to be enriched!</p>';
                $result .= '<button class="button asccms-enrich-button">Enrich WordPress Data</button>';
                $result .= '</div>';
                $result .= '<div class="asccms-enrich-enriching" style="display: none;">';
                $result .= '<p>Enriching WordPress Data ...</p>';
                $result .= '<progress value="0" max="'.$number_of_enriched_items.'" class="asccms-enrich-progress"></progress>';
                $result .= '</div>';
                foreach  ($enriched_items as $key=>$filename) {
                    $result .= '<input type="hidden" class="asccms-enrich-post" data-post-filename="'.$filename.'" />';
                }
                $result .= '</div>';
            }
            $enriched_card_style = $number_of_enriched_items > 0 ? ' style="display: none;"' : '';
            $result .= '<div class="card asccms-enriched-card"'.$enriched_card_style.'>';
            $result .= '<p>All marked assets are enriched!</p>';
            $result .= '</div>';

            return $result;
        }

        function asccms_dashboard_javascript() { ?>
            <script type="text/javascript" >
                jQuery(document).ready(function($) {
                    var upload_progress = 0;
                    var upload_progress_max = jQuery(".asccms-upload-progress").attr("max");
                    jQuery(".asccms-upload-button").click(function () {
                        jQuery(".asccms-upload-default").hide();
                        jQuery(".asccms-upload-uploading").show();
                        jQuery(".asccms-upload-post").each(function (index) {
                            let upload_post = $(this);
                            let attachment_id = upload_post.data("post-id");
                            let post_type = upload_post.data("post-type");
                            let data = {
                                'action': 'asccms_post_upload',
                                'asccms_attachment_id': attachment_id,
                                'asccms_post_type': post_type
                            };
                            jQuery.post(ajaxurl, data, function (response) {
                                $(".asccms-upload-progress").val(upload_progress++);
                                if (upload_progress == upload_progress_max) {
                                    jQuery(".asccms-upload-card").hide();
                                    jQuery(".asccms-uploaded-card").show();
                                }
                            });
                        });
                    });
                    var enrich_progress = 0;
                    var enrich_progress_max = jQuery(".asccms-enrich-progress").attr("max");
                    jQuery(".asccms-enrich-button").click(function () {
                        jQuery(".asccms-enrich-default").hide();
                        jQuery(".asccms-enrich-enriching").show();
                        jQuery(".asccms-enrich-post").each(function (index) {
                            let enrich_post = $(this);
                            let post_filename = enrich_post.data("post-filename");
                            let data = {
                                'action': 'asccms_post_enrich',
                                'asccms_post_filename': post_filename
                            };
                            jQuery.post(ajaxurl, data, function (response) {
                                $(".asccms-enrich-progress").val(enrich_progress++);
                                if (enrich_progress == enrich_progress_max) {
                                    jQuery(".asccms-enrich-card").hide();
                                    jQuery(".asccms-enriched-card").show();
                                }
                            });
                        });
                    });
                });
            </script><?php
        }

        function asccms_post_upload() {
            $attachment_id = $_POST['asccms_attachment_id'];
            $post_type = $_POST['asccms_post_type'];
            $storage_account_name = get_option('asccms_storage_account_name');
            $storage_account_key = get_option('asccms_storage_account_key');
            $blob_container = ASCCMS_STORAGE_ACCOUNT_CONTAINER_NAME_DOCUMENT_IMAGE;
            $connectionString = "DefaultEndpointsProtocol=https;AccountName=$storage_account_name;AccountKey=$storage_account_key;";
            $blobClient = BlobRestProxy::createBlobService($connectionString);

            if ($post_type === 'post') {
                $post = get_post($attachment_id);
                $filename = $post->post_title.'.txt';
                $content = $post->post_title.' '.strip_tags((get_post($attachment_id)->post_content));
                $blobClient->createBlockBlob($blob_container, $filename, $content);

                $post_tags = get_the_tags($attachment_id);
                if ($post_tags) {
                    $post_metadata = array_map(function($post_tag) { return $post_tag->name; }, $post_tags);
                    $metadata = array('MetadataKeywords' => base64_encode(join(', ', $post_metadata)));
                    $blobClient->setBlobMetadata($blob_container, $filename, $metadata);
                }
            } else if ($post_type === 'attachment') {
                $fullsize_path = get_attached_file($attachment_id);
                $filename = basename($fullsize_path);

                $content = fopen($fullsize_path, "r");

                $uploadResult = $blobClient->createBlockBlob($blob_container, $filename, $content);

                $attachment_metadata = get_post_meta($attachment_id);
                if (array_key_exists('asccms_keywords', $attachment_metadata)) {
                    $metadata = array('MetadataKeywords' => base64_encode($attachment_metadata['asccms_keywords'][0]));
                    $blobClient->setBlobMetadata($blob_container, $filename, $metadata);
                }
            }

            wp_die();
        }

        function asccms_post_enrich() {
            $post_filename = $_POST['asccms_post_filename'];

            Cognitive_CMS_Helper::enrich_data_for_filename($post_filename);

            wp_die();
        }

        public static function deactivate() {

        }

        public static function activate() {

        }
    }

    register_activation_hook(__FILE__, array('ASCCMS_Plugin_Dashboard', 'activate'));
    register_deactivation_hook(__FILE__, array('ASCCMS_Plugin_Dashboard', 'deactivate'));
}

$asccms_Plugin_Dashboard = new ASCCMS_Plugin_Dashboard();
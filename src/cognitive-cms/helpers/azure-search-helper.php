<?php

class Azure_Search_Helper {
    public static function get_azure_search_name() {
        return get_option('asccms_azure_search_name');
    }

    public static function get_azure_search_admin_key() {
        return get_option('asccms_azure_search_admin_key');
    }

    public static function get_azure_search_index_name() {
        return ASCCMS_SEARCH_INDEX_NAME;
    }

    public static function get_azure_search_url($azure_search_name = null) {
        if (!$azure_search_name) {
            $azure_search_name = self::get_azure_search_name();
        }

        if (!$azure_search_name) {
            return null;
        } else {
            return 'https://'.$azure_search_name.'.search.windows.net';
        }
    }

    public static function get_azure_search_api_version() {
        return '2017-11-11';
    }

    public static function is_azure_search_setup_completed() {
        return !empty(self::get_azure_search_name()) && !empty(self::get_azure_search_admin_key()) && !empty(self::get_azure_search_index_name())
            && self::is_azure_search_connection_working(self::get_azure_search_name(), self::get_azure_search_admin_key(), self::get_azure_search_index_name());
    }

    public static function is_azure_search_connection_working($azure_search_name, $azure_search_admin_key) {
        try {
            $azure_search_url = self::get_azure_search_url($azure_search_name);
            $azuresearch = new BenjaminHirsch\Azure\Search\Service($azure_search_url, $azure_search_admin_key, self::get_azure_search_api_version());
            $azure_search_index = $azuresearch->getIndex(self::get_azure_search_index_name());

            if (!$azure_search_index) {
                throw new Exception("Error Processing Request", 1);
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function get_azure_search_filenames() {
        $azuresearch = new BenjaminHirsch\Azure\Search\Service(self::get_azure_search_url(), self::get_azure_search_admin_key(), self::get_azure_search_api_version());
        $results = $azuresearch->search(self::get_azure_search_index_name(), null, ['select' => "metadata_storage_name"]);
        return array_map(function($filename) { return $filename['metadata_storage_name']; }, $results['value']);
    }

    private static $azure_search_results;
    private static $azure_search_results_last_filename;
    private static function get_azure_search_results_for_filename($filename) {
        if (self::$azure_search_results_last_filename !== $filename) {
            self::$azure_search_results_last_filename = $filename;
            $azuresearch = new BenjaminHirsch\Azure\Search\Service(self::get_azure_search_url(), self::get_azure_search_admin_key(), self::get_azure_search_api_version());
            self::$azure_search_results = $azuresearch->search(self::get_azure_search_index_name(), null, ['filter' => "metadata_storage_name eq '".$filename."'"]);
        }

        if (self::$azure_search_results && sizeof(self::$azure_search_results) > 0) {
            return self::$azure_search_results;
        } else {
            return null;
        }
    }

    private static function get_azure_search_results_value_array($filename, $array_name) {
        $results = self::get_azure_search_results_for_filename($filename);

        if ($results && sizeof($results) > 0) {
            return (reset($results['value']))[$array_name];
        } else {
            return array();
        }
    }

    public static function get_persons_for_filename($filename) {
        return self::get_azure_search_results_value_array($filename, 'persons');
    }

    public static function get_locations_for_filename($filename) {
        return self::get_azure_search_results_value_array($filename, 'locations');
    }

    public static function get_organizations_for_filename($filename) {
        return self::get_azure_search_results_value_array($filename, 'organizations');
    }

    public static function get_key_phrases_for_filename($filename) {
        return self::get_azure_search_results_value_array($filename, 'keyPhrases');
    }

    public static function get_celebrities_for_filename($filename) {
        return self::get_azure_search_results_value_array($filename, 'celebrities');
    }

    public static function get_landmarks_for_filename($filename) {
        return self::get_azure_search_results_value_array($filename, 'landmarks');
    }

    public static function get_tags_for_filename($filename) {
        return self::get_azure_search_results_value_array($filename, 'tags');
    }

    public static function get_captions_for_filename($filename) {
        return self::get_azure_search_results_value_array($filename, 'captions');
    }

    public static function get_dominant_colors_for_filename($filename) {
        return self::get_azure_search_results_value_array($filename, 'dominantColors');
    }

    public static function run_indexer() {
        $url = self::get_azure_search_url() . '/indexers/' . ASCCMS_SEARCH_INDEXER_NAME . '/run?api-version=' . self::get_azure_search_api_version();

        // use key 'http' even if you send the request to https://...
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\nContent-length: 0\r\napi-key: ".self::get_azure_search_admin_key()."\r\n",
                'method'  => 'POST'
            )
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        if ($result === FALSE) { /* Handle error */ }
    }
}
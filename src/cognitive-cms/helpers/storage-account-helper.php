<?php

use MicrosoftAzure\Storage\Blob\BlobRestProxy;

class Storage_Account_Helper {
    public static function get_storage_account_name() {
        return get_option('asccms_storage_account_name');
    }

    public static function get_storage_account_key() {
        return get_option('asccms_storage_account_key');
    }

    public static function get_blob_container() {
        return ASCCMS_STORAGE_ACCOUNT_CONTAINER_NAME_DOCUMENT_IMAGE;
    }

    public static function get_storage_account_conection_string() {
        return "DefaultEndpointsProtocol=https;AccountName=".self::get_storage_account_name().";AccountKey=".self::get_storage_account_key().";";
    }

    public static function is_storage_account_setup_completed() {
        return !empty(self::get_storage_account_name()) && !empty(self::get_storage_account_key()) && !empty(self::get_blob_container())
            && Storage_Account_Helper::is_storage_account_connection_working(self::get_storage_account_name(), self::get_storage_account_key());
    }

    public static function is_storage_account_connection_working($storage_account_name, $storage_account_key) {
        $blob_containers = self::get_blob_containers($storage_account_name, $storage_account_key);
        $blob_container_names = array_map(function($blob_container) { return basename($blob_container->getName()); }, $blob_containers);

        return in_array(self::get_blob_container(), $blob_container_names);
    }

    private static function get_blob_containers($storage_account_name, $storage_account_key) {
        try {
            $connectionString = "DefaultEndpointsProtocol=https;AccountName=$storage_account_name;AccountKey=$storage_account_key;";
            $blobClient = BlobRestProxy::createBlobService($connectionString);
            $containerlist = $blobClient->listContainers();
            $containers = $containerlist->getcontainers();
            return $containers;
        } catch (Exception $e) {
            $code = $e->getCode();
            $error_message = $e->getMessage();
            return array();
        }
    }

    public static function get_blobs() {
        try {
            $blobClient = BlobRestProxy::createBlobService(self::get_storage_account_conection_string());
            $blob_list = $blobClient->listBlobs(self::get_blob_container());
            $blobs = $blob_list->getBlobs();
            return $blobs;
        } catch (Exception $e) {
            $code = $e->getCode();
            $error_message = $e->getMessage();
            return array();
        }
    }

    public static function get_blob_names() {
        return array_map(function($blob) { return $blob->getName(); }, self::get_blobs());
    }
}
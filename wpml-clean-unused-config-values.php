<?php

	// your require may need a different way to get the path to wp-load.php
	//	require_once( 'wordpress/wp-load.php' );

	// this value worked on WPEngine
	require_once( $_SERVER['DOCUMENT_ROOT'] . 'wp-load.php' );
	global $wpdb;

	/**
	 * @param $arrayKeys
	 * @param $dbKeys
	 * @param $optionValue
	 * @param $translationType
	 *
	 * @return array
	 */
	function removeMissingKeys( $arrayKeys, $dbKeys, &$optionValue, $translationType, $found_missing_keys = [] ): array {
		$missingKeys = array_diff( $arrayKeys, $dbKeys );

		if ( count( $missingKeys ) > 0 && ! isset( $found_missing_keys[ $translationType ] ) ) {
			$found_missing_keys[ $translationType ]   = [];
			$found_missing_keys[ $translationType ][] = $missingKeys;
			echo "Found missing keys for " . $translationType . "\n";
		} else {
			echo "No missing keys for " . $translationType . "\n";
		}

		foreach ( $missingKeys as $key ) {
			unset( $optionValue['translation-management'][ $translationType ][ $key ] );
		}

		return $found_missing_keys;
	}

	$query   = "SELECT DISTINCT meta_key FROM wp_postmeta";
	$results = $wpdb->get_results( $query, ARRAY_A );

	if ( count( $results ) === 0 ) {
		echo "0 results";
		exit;
	}

	$dbKeys             = array_column( $results, 'meta_key' );
	$currentOptions     = get_option( 'icl_sitepress_settings' );
	$found_missing_keys = [];

	$found_missing_keys = removeMissingKeys( array_keys( $currentOptions['translation-management']['custom_term_fields_translation'] ), $dbKeys, $currentOptions, 'custom_term_fields_translation', $found_missing_keys );
	$found_missing_keys = removeMissingKeys( array_keys( $currentOptions['translation-management']['custom_fields_translation'] ), $dbKeys, $currentOptions, 'custom_fields_translation', $found_missing_keys );

	try {
		file_put_contents( __DIR__ . '/' . time() . '-missing-keys.json', json_encode( $found_missing_keys, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT ) );
	} catch ( JsonException $e ) {
		echo $e->getMessage();
	}

	update_option( 'icl_sitepress_settings', $currentOptions );

<?php

// Bỏ qua cảnh báo biến toàn cục
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
   exit;
}

$option_name = 'err-custom-login-options';

delete_option( $option_name );
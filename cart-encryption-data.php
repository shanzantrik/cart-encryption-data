<?php
/*
Plugin Name: Cart Encryption Data Plugin
Description: Save cart information, generate encrypted links, and allow for checkout.
Version: 1.0
Author: Shantanu Goswami
*/
if (!defined('ABSPATH')) {
    exit;
}

require_once(plugin_dir_path(__FILE__) . 'includes/class-cart-encryption.php');

$cart_encryption = new CartEncryptionData();
return $cart_encryption;

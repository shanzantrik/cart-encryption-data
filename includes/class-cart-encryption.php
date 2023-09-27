<?php

class CartEncryptionData{

private $secret_key;

   public function __construct() {
        $this->secret_key = bin2hex(random_bytes(32));
        add_action('woocommerce_cart_updated', array($this, 'save_cart_information'));
        add_action('woocommerce_email', array($this, 'add_custom_email_action'));
        add_action('init', array($this, 'handle_checkout_from_link'));
    }

public function save_cart_information() {

    if (is_user_logged_in()) {
        $user_id = get_current_user_id();

          global $woocommerce;
          $items = $woocommerce->cart->get_cart();
          $cart_contents = $items;

        // Serialize and saving the cart data to the user's meta.
        update_user_meta($user_id, 'saved_cart', serialize($cart_contents));
    } else {
        $cart_contents = WC()->cart->get_cart();
        //  using a transient to store guest cart data temporarily.
        set_transient('guest_cart', serialize($cart_contents), 3600); // Store for 1 hour

    }
}

private function generate_encrypted_link($cart_contents) {

    $random_bytes = random_bytes(32);
    $secret_key = bin2hex($random_bytes);
    $encryption_key = $secret_key;

    // Serializing the cart data.
    $cart_data = serialize($cart_contents);

    //Cart Data Encryption Link
    $encrypted_data = openssl_encrypt($cart_data, 'aes-256-cbc', $encryption_key, 0, $encryption_key);
    $hash = md5($encrypted_data);
    $encrypted_link = home_url('/checkout/?cart=' . urlencode(base64_encode($encrypted_data)) . '&hash=' . $hash);
    return $encrypted_link;
}


public function add_custom_email_action($email_class) {

    if (isset($email_class->id) === 'customer_processing_order') {

        $user_email = $email_class->object->billing_email;

        $cart_contents = get_cart_contents();

        $encrypted_link = generate_encrypted_link($cart_contents);

        $subject = 'Your Encrypted Cart Link';
        $message = 'Click the following link to access your cart: ' . $encrypted_link;

        // Sending the email.
        $sent = wp_mail($user_email, $subject, $message);

        if ($sent) {
            log_successful_email_sending($user_email, $subject);
        } else {
            handle_email_sending_error();
        }
    }
}

public function log_successful_email_sending($user_email, $subject) {
    $log_message = "Email sent successfully to $user_email with subject: $subject\n";
    error_log($log_message, 3, plugin_dir_path( __FILE__ ).'/mail-logs/mail-log.log');
}

public function handle_email_sending_error() {
    $error_message = 'Email sending failed at ' . date('Y-m-d H:i:s') . "\n";
    error_log($error_message, 3, plugin_dir_path( __FILE__ ).'/mail-logs/error.log');

    $admin_email = get_option('admin_email');
    $subject = 'Email Sending Error Notification';
    $message = 'There was an error sending an email from your website. Please check the error log for details.';

    wp_mail($admin_email, $subject, $message);
}


public function handle_checkout_from_link() {
    if (isset($_GET['cart']) && isset($_GET['hash'])) {
        $encrypted_data = base64_decode($_GET['cart']);
        $hash = $_GET['hash'];

        $random_bytes = random_bytes(32);
        $secret_key = bin2hex($random_bytes);
        $encryption_key = $secret_key;

        $computed_hash = md5($encrypted_data);

        if ($computed_hash === $hash) {
            $decrypted_data = openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $encryption_key);

            if ($decrypted_data !== false) {
                $cart_contents = unserialize($decrypted_data);

                global $woocommerce;
                $woocommerce->cart->empty_cart();

                // Adding items from the decrypted cart to the WooCommerce cart.
                foreach ($cart_contents as $cart_item_key => $cart_item_data) {
                    WC()->cart->add_to_cart(
                        $cart_item_data['product_id'],
                        $cart_item_data['quantity'],
                        $cart_item_data['variation_id'],
                        $cart_item_data['variation'],
                        $cart_item_data
                    );
                }

                $checkout_url = wc_get_checkout_url();
                wp_redirect($checkout_url);
                exit;
            } else {
                wp_die('Error: Unable to decrypt cart data.');
            }
        } else {
            wp_die('Error: Hash verification failed.');
        }
    }
}

}

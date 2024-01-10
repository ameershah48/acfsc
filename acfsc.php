<?php

/**
 * Plugin Name: ACFSC - Smart Coupon
 * Description: Automatically create free shipping coupon based on WooCommerce order
 * Version: 1.0
 * Author: Ameer Shah
 * Author URI: https://ameershah.my/
 **/

add_action('woocommerce_before_checkout_form', 'auto_apply_coupon');
add_action('woocommerce_thankyou', 'auto_create_coupon');


function auto_create_coupon()
{
    global $wp;

    $order_id = $wp->query_vars['order-received'];
    $order = wc_get_order($order_id);
    $email = $order->get_billing_email();

    $coupon = new WC_Coupon($email);

    $coupon->set_date_expires(date('Y-m-d', strtotime('+1 day')));
    $coupon->set_free_shipping(true);
    $coupon->set_email_restrictions(array($email));
    $coupon->save();
}

function auto_apply_coupon()
{
    $email = WC()->session->get('customer')['email'];
    $coupons = WC()->cart->get_coupons();

    if (empty($email) || empty($coupons)) {
        return;
    }

    $coupon = new WC_Coupon($email);

    if (!$coupon->exists) {
        return;
    }

    WC()->cart->apply_coupon($email);
}

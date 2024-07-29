<?php 


/****Checkout Addional Info****/

/*
* 1. Add the Date & Time Picker Fields
*/

add_filter( 'woocommerce_checkout_fields', 'add_additional_fields' );

function add_additional_fields( $fields ) {
    $fields['additional_info']['delivery_date'] = array(
        'type'        => 'text',
        'label'       => __('Delivery Date & Time', 'woodmart'),
        'placeholder' => _x('Select date', 'placeholder', 'woodmart'),
        'required'    => true,
        'class'       => array('delivery_date_row'),
        'priority'    => 30,
    );
    $fields['additional_info']['delivery_time'] = array(
        'type'        => 'text',
        'label'       => __(' ', 'woodmart'),
        'placeholder' => _x('Select time', 'placeholder', 'woodmart'),
        'required'    => true,
        'class'       => array('delivery_time_row'),
        'priority'    => 40,
    );
    $fields['additional_info']['egg_preference'] = array(
        'type'        => 'select',
        'label'       => __('Egg Preference', 'woodmart'),
        'placeholder' => _x('Select One', 'placeholder', 'woodmart'),
        'required'    => true,
        'class'       => array('form-row-wide'),
        'priority'    => 50,
        'options'     => array(
            ''         => __('Select One', 'woodmart'),
            'egg'      => __('Egg', 'woodmart'),
            'egg_free' => __('Egg Free', 'woodmart'),
        ),
    );
    $fields['additional_info']['cake_message'] = array(
        'type'        => 'textarea',
        'label'       => __('Message on the cake', 'woodmart'),
        'placeholder' => _x('Write your message', 'placeholder', 'woodmart'),
        'required'    => false,
        'class'       => array('form-row-wide'),
        'priority'    => 60,
    );
    return $fields;
}

/*
* 2. Display the Fields on the Checkout Page
*/

add_action( 'woocommerce_before_order_notes', 'display_additional_fields' );
function display_additional_fields( $checkout ) {

    echo '<div id="date_time_picker_checkout_fields"><h3 class="mb-shipping-hd">' . __('Additional Info') . '</h3>';

    woocommerce_form_field( 'delivery_date', $checkout->get_checkout_fields()['additional_info']['delivery_date'], $checkout->get_value( 'delivery_date' ) );
    woocommerce_form_field( 'delivery_time', $checkout->get_checkout_fields()['additional_info']['delivery_time'], $checkout->get_value( 'delivery_time' ) );
    woocommerce_form_field( 'egg_preference', $checkout->get_checkout_fields()['additional_info']['egg_preference'], $checkout->get_value( 'egg_preference' ) );
    woocommerce_form_field( 'cake_message', $checkout->get_checkout_fields()['additional_info']['cake_message'], $checkout->get_value( 'cake_message' ) );

    echo '</div>';
}

/*
* 3. Enqueue the Date & Time Picker Scripts
*/

add_action( 'wp_enqueue_scripts', 'enqueue_additional_scripts' );
function enqueue_additional_scripts() {
    if ( is_checkout() ) {
        // Enqueue jQuery UI Datepicker
        wp_enqueue_script( 'jquery-ui-datepicker' );
        wp_enqueue_style( 'jquery-ui-datepicker-style', 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css' );

        // Enqueue jQuery Timepicker Addon
        wp_enqueue_script( 'jquery-timepicker-addon', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.js', array( 'jquery', 'jquery-ui-datepicker' ), null, true );
        wp_enqueue_style( 'jquery-timepicker-addon-style', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.css' );

        // Enqueue custom script to initialize the date and time pickers
        wp_enqueue_script( 'custom-date-time-picker', get_stylesheet_directory_uri() . '/js/custom-date-time-picker.js', array( 'jquery', 'jquery-ui-datepicker', 'jquery-timepicker-addon' ), null, true );
    }
}

/*
* 4. Save the Custom Fields
*/

add_action( 'woocommerce_checkout_update_order_meta', 'save_additional_fields' );
function save_additional_fields( $order_id ) {
    if ( ! empty( $_POST['delivery_date'] ) ) {
        update_post_meta( $order_id, '_delivery_date', sanitize_text_field( $_POST['delivery_date'] ) );
    }
    if ( ! empty( $_POST['delivery_time'] ) ) {
        update_post_meta( $order_id, '_delivery_time', sanitize_text_field( $_POST['delivery_time'] ) );
    }
    if ( ! empty( $_POST['egg_preference'] ) ) {
        update_post_meta( $order_id, '_egg_preference', sanitize_text_field( $_POST['egg_preference'] ) );
    }
    if ( ! empty( $_POST['cake_message'] ) ) {
        update_post_meta( $order_id, '_cake_message', sanitize_textarea_field( $_POST['cake_message'] ) );
    }
}

/*
* 5. Display the Fields in the Order Admin Panel
*/

add_action( 'woocommerce_admin_order_data_after_billing_address', 'display_additional_in_admin', 10, 1 );
function display_additional_in_admin( $order ) {
    echo '<h3>' . __('Additional Info', 'woodmart') . '</h3>';
    echo '<p><strong>' . __('Delivery Date', 'woodmart') . ':</strong> ' . get_post_meta( $order->get_id(), '_delivery_date', true ) . '</p>';
    echo '<p><strong>' . __('Delivery Time', 'woodmart') . ':</strong> ' . get_post_meta( $order->get_id(), '_delivery_time', true ) . '</p>';
    echo '<p><strong>' . __('Egg Preference', 'woodmart') . ':</strong> ' . get_post_meta( $order->get_id(), '_egg_preference', true ) . '</p>';
    echo '<p><strong>' . __('Message on the cake', 'woodmart') . ':</strong> ' . get_post_meta( $order->get_id(), '_cake_message', true ) . '</p>';
}

/*
* 6. Add Custom Fields to Order Emails
*/

add_action( 'woocommerce_email_order_meta', 'add_aditional_fields_to_email', 20, 3 );
function add_aditional_fields_to_email( $order, $sent_to_admin, $plain_text ) {
    $delivery_date = get_post_meta( $order->get_id(), '_delivery_date', true );
    $delivery_time = get_post_meta( $order->get_id(), '_delivery_time', true );
    $egg_preference = get_post_meta( $order->get_id(), '_egg_preference', true );
    $cake_message = get_post_meta( $order->get_id(), '_cake_message', true );

    if ( $plain_text ) {
        echo "Delivery Date: " . $delivery_date . "\n";
        echo "Delivery Time: " . $delivery_time . "\n";
        echo "Egg Preference: " . $egg_preference . "\n";
        echo "Message on the cake: " . $cake_message . "\n";
    } else {
        echo '<h3>' . __('Additional Info', 'woodmart') . '</h3>';
        echo '<p><strong>' . __('Delivery Date', 'woodmart') . ':</strong> ' . $delivery_date . '</p>';
        echo '<p><strong>' . __('Delivery Time', 'woodmart') . ':</strong> ' . $delivery_time . '</p>';
        echo '<p><strong>' . __('Egg Preference', 'woodmart') . ':</strong> ' . $egg_preference . '</p>';
        echo '<p><strong>' . __('Message on the cake', 'woodmart') . ':</strong> ' . $cake_message . '</p>';
    }
}

/*Whats app Field*/

// Add WhatsApp field to the checkout
add_filter( 'woocommerce_billing_fields', 'add_whatsapp_billing_field' );
function add_whatsapp_billing_field( $fields ) {
    $fields['billing_whatsapp'] = array(
        'label'       => __('WhatsApp', 'woodmart'),
        'required'    => false,
        'class'       => array('form-row-wide'),
        'clear'       => true
    );

    // Reorder fields to place WhatsApp after phone
    $billing_fields = array();
    foreach ($fields as $key => $field) {
        $billing_fields[$key] = $field;
        if ($key == 'billing_phone') {
            $billing_fields['billing_whatsapp'] = $fields['billing_whatsapp'];
        }
    }

    return $billing_fields;
}

// Save WhatsApp field data
add_action('woocommerce_checkout_update_order_meta', 'save_whatsapp_number_checkout');
function save_whatsapp_number_checkout( $order_id ) {
    if ( ! empty( $_POST['billing_whatsapp'] ) ) {
        update_post_meta( $order_id, '_billing_whatsapp', sanitize_text_field( $_POST['billing_whatsapp'] ) );
    }
}

// Display WhatsApp field in the order admin page
add_action( 'woocommerce_admin_order_data_after_billing_address', 'display_whatsapp_number_admin_order', 10, 1 );
function display_whatsapp_number_admin_order($order){
    $order_id = $order->get_id();
    if ($whatsapp_number = get_post_meta($order_id, '_billing_whatsapp', true)) {
        echo '<p><strong>'.__('WhatsApp Number').':</strong> ' . $whatsapp_number . '</p>';
    }
}
// Add WhatsApp number to order emails
add_filter( 'woocommerce_email_order_meta_fields', 'add_whatsapp_to_order_email', 10, 3 );
function add_whatsapp_to_order_email( $fields, $sent_to_admin, $order ) {
    $whatsapp_number = get_post_meta( $order->get_id(), '_billing_whatsapp', true );
    if ( ! empty( $whatsapp_number ) ) {
        $fields['whatsapp_number'] = array(
            'label' => __( 'WhatsApp', 'woodmart' ),
            'value' => $whatsapp_number,
        );
    }
    return $fields;
}
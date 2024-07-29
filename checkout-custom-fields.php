<?php 

/*Add Custom Fields to the Checkout Fields*/

add_filter( 'woocommerce_checkout_fields', 'add_additional_info_checkout_fields' );
function add_additional_info_checkout_fields( $fields ) {
    $fields['additional_info'] = array(
        'custom_field_one' => array(
            'type'        => 'text',
            'label'       => __('Custom Field One', 'woocommerce'),
            'placeholder' => _x('Enter custom field one', 'placeholder', 'woocommerce'),
            'required'    => true,
            'class'       => array('form-row-wide'),
            'priority'    => 10,
        ),
        'custom_field_two' => array(
            'type'        => 'textarea',
            'label'       => __('Custom Field Two', 'woocommerce'),
            'placeholder' => _x('Enter custom field two', 'placeholder', 'woocommerce'),
            'required'    => false,
            'class'       => array('form-row-wide'),
            'priority'    => 20,
        ),
    );

    return $fields;
}

/*Display the Fields on the Checkout Page*/

add_action( 'woocommerce_before_order_notes', 'display_additional_info_fields' );
function display_additional_info_fields( $checkout ) {
    echo '<div id="additional_info_checkout_fields"><h3>' . __('Additional Info') . '</h3>';

    woocommerce_form_field( 'custom_field_one', $checkout->get_checkout_fields()['additional_info']['custom_field_one'], $checkout->get_value( 'custom_field_one' ) );
    woocommerce_form_field( 'custom_field_two', $checkout->get_checkout_fields()['additional_info']['custom_field_two'], $checkout->get_value( 'custom_field_two' ) );

    echo '</div>';
}

/*Save the Custom Fields*/

add_action( 'woocommerce_checkout_update_order_meta', 'save_additional_info_fields' );
function save_additional_info_fields( $order_id ) {
    if ( ! empty( $_POST['custom_field_one'] ) ) {
        update_post_meta( $order_id, '_custom_field_one', sanitize_text_field( $_POST['custom_field_one'] ) );
    }
    if ( ! empty( $_POST['custom_field_two'] ) ) {
        update_post_meta( $order_id, '_custom_field_two', sanitize_textarea_field( $_POST['custom_field_two'] ) );
    }
}

/*Display Custom Fields in the Order Admin Panel*/

add_action( 'woocommerce_admin_order_data_after_billing_address', 'display_additional_info_in_admin', 10, 1 );
function display_additional_info_in_admin( $order ) {
    echo '<p><strong>' . __('Custom Field One') . ':</strong> ' . get_post_meta( $order->get_id(), '_custom_field_one', true ) . '</p>';
    echo '<p><strong>' . __('Custom Field Two') . ':</strong> ' . get_post_meta( $order->get_id(), '_custom_field_two', true ) . '</p>';
}

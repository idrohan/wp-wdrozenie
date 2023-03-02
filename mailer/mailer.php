<?php
function contact_form_shortcode() {

    // Handle form submission
    if ( isset( $_POST['contact_form_submit'] ) ) {

        // Verify nonce to prevent unauthorized access
        if ( ! wp_verify_nonce( $_POST['contact_form_nonce'], 'contact_form_submit' ) ) {
            wp_die( 'Nonce verification failed', 'Error', array( 'response' => 403 ) );
        }

        // Process form data and send email
        $name = sanitize_text_field( $_POST['contact_form_name'] );
        $email = sanitize_email( $_POST['contact_form_email'] );
        $message = sanitize_textarea_field( $_POST['contact_form_message'] );

        // Perform form validation
        if ( empty( $name ) || empty( $email ) || empty( $message ) ) {
            wp_die( 'Please fill in all required fields', 'Error', array( 'response' => 400 ) );
        }

        // Build email headers and message
        $to = get_option( 'admin_email' );
        $subject = 'New Contact Form Submission';
        $headers = array(
            'From: ' . $name . ' <' . $email . '>',
            'Reply-To: ' . $name . ' <' . $email . '>',
            'Content-Type: text/html; charset=UTF-8',
        );
        $message = '<p><strong>Name:</strong> ' . $name . '</p>' .
            '<p><strong>Email:</strong> ' . $email . '</p>' .
            '<p><strong>Message:</strong> ' . $message . '</p>';

        // Send email
        wp_mail( $to, $subject, $message, $headers );

        // Display success message
        return '<p class="contact-form-success">Thank you for contacting us!</p>';

    }

    // Generate contact form HTML
    $form = '<form method="post" class="contact-form">' .
        wp_nonce_field( 'contact_form_submit', 'contact_form_nonce', true, false ) .
        '<div class="form-group">' .
        '<label for="contact-form-name">Name*</label>' .
        '<input type="text" name="contact_form_name" id="contact-form-name" class="form-control" required>' .
        '</div>' .
        '<div class="form-group">' .
        '<label for="contact-form-email">Email*</label>' .
        '<input type="email" name="contact_form_email" id="contact-form-email" class="form-control" required>' .
        '</div>' .
        '<div class="form-group">' .
        '<label for="contact-form-message">Message*</label>' .
        '<textarea name="contact_form_message" id="contact-form-message" class="form-control" rows="5" required></textarea>' .
        '</div>' .
        '<button type="submit" name="contact_form_submit" class="btn btn-primary">Send Message</button>' .
        '</form>';

    return $form;

}
add_shortcode( 'contact_form', 'contact_form_shortcode' );

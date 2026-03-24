<?php
/**
 * Gravity Forms: submission handling, admin email summary, CRM webhook stub.
 *
 * @package ARCOPAN_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * After Gravity Forms submission: optional admin email + CRM webhook.
 *
 * @param array $entry Entry data.
 * @param array $form  Form definition.
 * @return void
 */
function arcopan_form_handler( $entry, $form ) {
	if ( ! is_array( $entry ) || ! is_array( $form ) ) {
		return;
	}

	if ( apply_filters( 'arcopan_gf_send_admin_notification', true, $entry, $form ) ) {
		arcopan_form_send_admin_email( $entry, $form );
	}

	$webhook_url = (string) apply_filters( 'arcopan_crm_webhook_url', '', $entry, $form );
	if ( '' === $webhook_url ) {
		return;
	}

	$form_id   = isset( $form['id'] ) ? (int) $form['id'] : 0;
	$entry_id  = isset( $entry['id'] ) ? (int) $entry['id'] : 0;
	$site_name = get_bloginfo( 'name', 'display' );

	$payload = array(
		'source'     => 'arcopan-wordpress',
		'site_url'   => home_url( '/' ),
		'site_name'  => $site_name,
		'form_id'    => $form_id,
		'form_title' => isset( $form['title'] ) ? (string) $form['title'] : '',
		'entry_id'   => $entry_id,
		'entry'      => $entry,
		'submitted'  => time(),
	);

	$payload = apply_filters( 'arcopan_crm_webhook_payload', $payload, $entry, $form );

	wp_remote_post(
		$webhook_url,
		array(
			'timeout'  => 8,
			'headers'  => array(
				'Content-Type' => 'application/json; charset=utf-8',
			),
			'body'     => wp_json_encode( $payload ),
			'blocking' => false,
		)
	);
}

/**
 * Send plain-text admin summary of submitted fields.
 *
 * @param array $entry Entry data.
 * @param array $form  Form definition.
 * @return void
 */
function arcopan_form_send_admin_email( $entry, $form ) {
	$to = (string) apply_filters( 'arcopan_gf_admin_notify_email', get_option( 'admin_email' ), $entry, $form );
	if ( '' === $to || ! is_email( $to ) ) {
		return;
	}

	$site       = get_bloginfo( 'name', 'display' );
	$form_title = isset( $form['title'] ) ? (string) $form['title'] : '';

	$subject = apply_filters(
		'arcopan_gf_notify_subject',
		sprintf(
			/* translators: 1: site name, 2: form title */
			__( '[%1$s] Form received: %2$s', 'arcopan-child' ),
			$site,
			$form_title
		),
		$entry,
		$form
	);

	$lines = array();

	if ( class_exists( 'GFAPI' ) && ! empty( $form['fields'] ) && is_array( $form['fields'] ) ) {
		foreach ( $form['fields'] as $field ) {
			if ( ! is_object( $field ) || ! isset( $field->id ) ) {
				continue;
			}
			$fid   = (string) $field->id;
			$label = isset( $field->label ) ? (string) $field->label : $fid;
			if ( ! isset( $entry[ $fid ] ) || '' === (string) $entry[ $fid ] ) {
				continue;
			}
			$lines[] = $label . ': ' . (string) $entry[ $fid ];
		}
	}

	if ( array() === $lines ) {
		$lines[] = __( '(No field values in summary.)', 'arcopan-child' );
	}

	$body = implode( "\n", $lines );
	$body = apply_filters( 'arcopan_gf_notify_body', $body, $entry, $form );

	wp_mail(
		$to,
		$subject,
		$body,
		array( 'Content-Type: text/plain; charset=UTF-8' )
	);
}

if ( class_exists( 'GFForms' ) ) {
	add_action( 'gform_after_submission', 'arcopan_form_handler', 10, 2 );
}

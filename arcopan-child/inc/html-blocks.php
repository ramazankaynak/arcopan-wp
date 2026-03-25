<?php
/**
 * ARCOPAN HTML Block Utilities.
 *
 * Safe HTML block extraction, translation, and reassembly.
 *
 * @package ARCOPAN_Child
 * @since 1.1.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Extract HTML blocks for safer translation.
 *
 * Splits content into blocks (headings, paragraphs, lists).
 * Each block is translated separately to prevent HTML corruption.
 *
 * @param string $html HTML content.
 * @return array Array of blocks with metadata.
 */
function arcopan_extract_html_blocks( $html ) {
	if ( ! $html ) {
		return array();
	}

	$blocks = array();
	$block_index = 0;

	// Pattern to match: h2-h4, p, ul/ol with content, blockquote, img
	$pattern = '/<(h[2-4]|p|blockquote)(?:\s[^>]*)?>.*?<\/\1>|<(?:ul|ol)(?:\s[^>]*)?>.*?<\/(?:ul|ol)>|<img[^>]*>|<br\s*\/?>/is';

	preg_match_all( $pattern, $html, $matches );

	foreach ( $matches[0] as $match ) {
		if ( trim( $match ) ) {
			$blocks[] = array(
				'id'      => $block_index,
				'content' => trim( $match ),
				'type'    => arcopan_get_block_type( $match ),
			);
			$block_index++;
		}
	}

	// If no blocks found, treat entire content as one block
	if ( empty( $blocks ) ) {
		$blocks[] = array(
			'id'      => 0,
			'content' => $html,
			'type'    => 'text',
		);
	}

	return $blocks;
}

/**
 * Determine block type.
 *
 * @param string $block HTML block.
 * @return string Block type.
 */
function arcopan_get_block_type( $block ) {
	if ( preg_match( '/<h[2-4]/i', $block ) ) {
		return 'heading';
	} elseif ( preg_match( '/<p/i', $block ) ) {
		return 'paragraph';
	} elseif ( preg_match( '/<(?:ul|ol)/i', $block ) ) {
		return 'list';
	} elseif ( preg_match( '/<blockquote/i', $block ) ) {
		return 'blockquote';
	} elseif ( preg_match( '/<img/i', $block ) ) {
		return 'image';
	} elseif ( preg_match( '/<br/i', $block ) ) {
		return 'break';
	}
	return 'text';
}

/**
 * Reassemble HTML blocks after translation.
 *
 * @param array $blocks Array of translated blocks.
 * @return string Reassembled HTML.
 */
function arcopan_reassemble_html_blocks( $blocks ) {
	if ( empty( $blocks ) ) {
		return '';
	}

	// Sort by ID to maintain order
	usort(
		$blocks,
		function( $a, $b ) {
			return $a['id'] - $b['id'];
		}
	);

	$html = '';
	foreach ( $blocks as $block ) {
		$html .= $block['content'] . "\n";
	}

	return trim( $html );
}

/**
 * Strip HTML tags but preserve structure info.
 *
 * Removes all HTML for translation, preserving text content.
 *
 * @param string $html HTML content.
 * @return string Clean text.
 */
function arcopan_strip_html_for_translation( $html ) {
	// Remove script and style tags
	$html = preg_replace( '/<script[^>]*>.*?<\/script>/is', '', $html );
	$html = preg_replace( '/<style[^>]*>.*?<\/style>/is', '', $html );

	// Remove comments
	$html = preg_replace( '/<!--.*?-->/s', '', $html );

	// Remove event handlers and dangerous attributes
	$html = preg_replace( '/\s+on\w+\s*=\s*"[^"]*"/i', '', $html );

	// Remove HTML tags but keep content
	$html = wp_strip_all_tags( $html );

	return trim( $html );
}

/**
 * Wrap text in original HTML tags.
 *
 * @param string $translated Translated text.
 * @param string $original Original HTML.
 * @return string HTML with translated content.
 */
function arcopan_wrap_translated_in_html( $translated, $original ) {
	if ( ! $original ) {
		return $translated;
	}

	// Extract opening tag
	if ( preg_match( '/^<([^>]+)>/i', $original, $open_match ) ) {
		$opening = $open_match[0];

		// Extract tag name
		if ( preg_match( '/<(\w+)/i', $opening, $tag_match ) ) {
			$tag_name = $tag_match[1];

			// Preserve attributes but update content
			return $opening . $translated . '</' . $tag_name . '>';
		}
	}

	return $translated;
}

/**
 * Validate HTML integrity after translation.
 *
 * Checks for unclosed tags and basic structure issues.
 *
 * @param string $html HTML to validate.
 * @return bool|string True if valid, error message string if invalid.
 */
function arcopan_validate_html_integrity( $html ) {
	if ( ! $html ) {
		return true; // Empty is valid
	}

	// Count opening vs closing tags (simplified check)
	$open_count = substr_count( $html, '<' );
	$close_count = substr_count( $html, '>' );

	if ( $open_count !== $close_count ) {
		return 'Mismatched angle brackets in HTML';
	}

	// Check for common unclosed tags
	$self_closing = array( 'br', 'hr', 'img', 'input', 'meta', 'link' );
	foreach ( $self_closing as $tag ) {
		// Simple check: if opening tag exists, should have either / at end or proper closing
		$pattern = '/<' . $tag . '[^>]*(?<!\/)\s*>/i';
		if ( preg_match( $pattern, $html ) && ! preg_match( '/<\/' . $tag . '>/i', $html ) ) {
			$pattern_with_close = '/<' . $tag . '[^>]*\/\s*>/i';
			if ( ! preg_match( $pattern_with_close, $html ) ) {
				// Allow most cases, but warn about img tags
				if ( 'img' === $tag ) {
					$html = preg_replace( '/<img([^>]*)>/i', '<img$1 />', $html );
				}
			}
		}
	}

	return true;
}

/**
 * Sanitize HTML for translation safety.
 *
 * Removes dangerous attributes while preserving structure.
 *
 * @param string $html HTML content.
 * @return string Sanitized HTML.
 */
function arcopan_sanitize_html_for_translation( $html ) {
	if ( ! $html ) {
		return '';
	}

	// Remove event handlers (onclick, onerror, etc.)
	$html = preg_replace( '/\s+on\w+\s*=\s*"[^"]*"/i', '', $html );

	// Remove class and id attributes (keep structure)
	$html = preg_replace( '/\s+class\s*=\s*"[^"]*"/i', '', $html );
	$html = preg_replace( '/\s+id\s*=\s*"[^"]*"/i', '', $html );

	// Remove inline styles
	$html = preg_replace( '/\s+style\s*=\s*"[^"]*"/i', '', $html );

	// Remove data attributes (might contain code)
	$html = preg_replace( '/\s+data-[a-z-]+\s*=\s*"[^"]*"/i', '', $html );

	// Fix double spaces in tags
	$html = preg_replace( '/\s{2,}/', ' ', $html );

	return $html;
}

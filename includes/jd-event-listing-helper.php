<?php
/**
 * This file is used for define common helper function which we can use
 * in plugin.
 *
 * @package JD_Event_Listing
 */

/**
 * Load a template part into a template
 *
 * You may include the same template part multiple times.
 *
 * @uses  jd_event_locate_template()
 * @since 1.0.0
 *
 * @param string $slug The slug name for the generic template.
 * @param string $name The name of the specialised template.
 */
function jd_event_get_template_part( $slug, $name = null ) {

	$templates = array();
	if ( isset( $name ) ) {
		$templates[] = "{$slug}-{$name}.php";
	}

	$templates[] = "{$slug}.php";

	jd_event_locate_template( $templates, true, false );
}

/**
 * Get template.
 *
 * Behaves almost identically to `{@see locate_template()}`
 *
 * @since 1.0.0
 *
 * @param string|array $template_names Template file(s) to search for, in order.
 * @param bool         $load           If true the template file will be loaded if it is found.
 * @param bool         $require_once   Whether to require_once or require. Default true. Has no effect if $load is false.
 *
 * @return string The template filename if one is located.
 */
function jd_event_locate_template( $template_names, $load = false, $require_once = true ) {
	$located = '';

	$template_dir        = get_stylesheet_directory();
	$parent_template_dir = get_template_directory();

	$stack = array( $template_dir, $parent_template_dir, JD_EVENT_LISTING_PLUGIN_DIR . '/templates' );

	/**
	 * Filters the template stack: an array of directories the plug-in looks for
	 * for templates.
	 *
	 * @param array $stack Array of directories (absolute path).
	 */
	$stack = apply_filters( 'jd_event_template_stack', $stack );
	$stack = array_unique( $stack );

	foreach ( (array) $template_names as $template_name ) {
		if ( ! $template_name ) {
			continue;
		}
		foreach ( $stack as $template_stack ) {
			if ( file_exists( trailingslashit( $template_stack ) . $template_name ) ) {
				$located = trailingslashit( $template_stack ) . $template_name;
				break 2;
			}
		}
	}

	if ( $load && '' !== $located ) {
		load_template( $located, $require_once );
	}

	return $located;
}

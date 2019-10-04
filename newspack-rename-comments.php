<?php
/**
 * Plugin Name:       Newspack Rename Comments
 * Description:       Provides the Newspack theme with the ability to allow users to rename comments.
 * Version:           0.1.0
 * Author:            Philip John, Automattic
 * Author URI:        https://automattic.com
 * Text Domain:       rename-comments
 * Domain Path:       /languages
 */

namespace Rename_Comments;

/**
 * Sets up our settings fields.
 * @return void
 */
function admin_init() {

	add_settings_section(
		'rename_comments',
		esc_html__( 'Rename Comments', 'rename-comments' ),
		__NAMESPACE__ . '\settings_section_output',
		'discussion'
	);

	// Comment name
	add_settings_field(
		'comments_name',
		esc_html__( 'Name', 'rename-comments' ),
		__NAMESPACE__ . '\field_comments_name',
		'discussion',
		'rename_comments'
	);
	register_setting(
		'discussion',
		'rc_comments_name',
		[
			'type' => 'string',
			'sanitise_callback' => 'esc_html',
		]
	);

	// Comment name (plural)
	add_settings_field(
		'comments_name_plural',
		esc_html__( 'Name (plural)', 'rename-comments' ),
		__NAMESPACE__ . '\field_comments_name_plural',
		'discussion',
		'rename_comments'
	);
	register_setting(
		'discussion',
		'rc_comments_name_plural',
		[
			'type' => 'string',
			'sanitise_callback' => 'esc_html',
		]
	);

	// "Leave a comment"
	add_settings_field(
		'comments_leave_comment',
		esc_html__( 'Leave a comment', 'rename-comments' ),
		__NAMESPACE__ . '\field_comments_leave_comment',
		'discussion',
		'rename_comments'
	);
	register_setting(
		'discussion',
		'rc_comments_leave_comment',
		[
			'type' => 'string',
			'sanitise_callback' => 'esc_html',
		]
	);

	// "Comments are closed"
	add_settings_field(
		'comments_comments_closed',
		esc_html__( 'Comments are closed', 'rename-comments' ),
		__NAMESPACE__ . '\field_comments_comments_closed',
		'discussion',
		'rename_comments'
	);
	register_setting(
		'discussion',
		'rc_comments_comments_closed',
		[
			'type' => 'string',
			'sanitise_callback' => 'esc_html',
		]
	);

	// "No comments"
	add_settings_field(
		'comments_no_comments',
		esc_html__( 'No comments', 'rename-comments' ),
		__NAMESPACE__ . '\field_comments_no_comments',
		'discussion',
		'rename_comments'
	);
	register_setting(
		'discussion',
		'rc_comments_no_comments',
		[
			'type' => 'string',
			'sanitise_callback' => 'esc_html',
		]
	);

	// Heading (no comments)
	add_settings_field(
		'no_comments_title',
		esc_html__( 'Heading (no comments)', 'rename-comments' ),
		__NAMESPACE__ . '\field_no_comments_title',
		'discussion',
		'rename_comments'
	);
	register_setting(
		'discussion',
		'rc_no_comments_title',
		[
			'type' => 'string',
			'sanitise_callback' => 'esc_html',
		]
	);

	// Heading (with comments)
	add_settings_field(
		'comments_title',
		esc_html__( 'Heading (with comments)', 'rename-comments' ),
		__NAMESPACE__ . '\field_comments_title',
		'discussion',
		'rename_comments'
	);
	register_setting(
		'discussion',
		'rc_comments_title',
		[
			'type' => 'string',
			'sanitise_callback' => 'esc_html',
		]
	);

}
add_action( 'admin_init', __NAMESPACE__ . '\admin_init' );

/**
 * Removes the meta from the comment text on the front end.
 *
 * It's added by the WP Comment Meta plugin we're using, but we don't want it on the front.
 * @return void
 */
function remove_meta_from_comment() {
	global $nmwpcomment;
	remove_filter( 'comment_text', array( $nmwpcomment, 'render_comment_meta_front' ), 100 );
}
add_action( 'init', __NAMESPACE__ . '\remove_meta_from_comment' );

/**
 * Filters translatable strings to rename core strings.
 *
 * Some of the strings we can target specifically in the theme but others are core
 * strings so we need to do them dynamically. This function targets them explicitly
 * and pulls in the user-defined alternative.
 *
 * @param  string $translated_text The translated text
 * @param  string $text            Original text
 * @param  string $context         What context (if any) if this is the 'gettext' filter
 * @param  string $domain          Text domain for the string.
 * @return string                  Our transformed text
 */
function filter_gettext( $translated_text, $text = '', $context = '', $domain = '' ) {

	// Only replace on the frontend.
	if ( \is_admin() ) {
		return $translated_text;
	}

	// A specific list of strings we want to change.
	$strings_to_translate = [
		'Comment' => get_text( 'rc_comments_name' ),
		'Post Comment' => 'Post ' . get_text( 'rc_comments_name' ),
		'Comments' => get_text( 'rc_comments_name_plural' ),
		'%1$s %2$s Comments Feed' => '%1$s %2$s ' . get_text( 'rc_comments_name_plural' ) . ' Feed',
		'%1$s %2$s %3$s Comments Feed' => '%1$s %2$s %3$s ' . get_text( 'rc_comments_name_plural' ) . ' Feed',
		'Comments (%s)' => get_text( 'rc_comments_name_plural' ) . ' (%s)',
		'Comments Template' => get_text( 'rc_comments_name_plural' ) . '  Template',
		'Comments are closed' => get_text( 'rc_comments_name_plural' ) . '  are closed',
		'Popup Comments Template' => 'Popup ' . get_text( 'rc_comments_name_plural' ) . '  Template',
		'Rename Comments' => 'Rename ' . get_text( 'rc_comments_name_plural' ),
		'You must be <a href="%s">logged in</a> to post a comment' => 'You must be <a href="%s">logged in</a> to post a ' . strtolower( get_text( 'rc_comments_name' ) ),
		'Post Comment' => 'Post ' . get_text( 'rc_comments_name' ),
		'Recent Comments' => 'Recent ' . get_text( 'rc_comments_name_plural' ),
		'Comments <abbr title="Really Simple Syndication">RSS</abbr>' => get_text( 'rc_comments_name_plural' ) . ' <abbr title="Really Simple Syndication">RSS</abbr>',
	];

	// Let's explicitly match our strings.
	if ( in_array( $translated_text, array_keys( $strings_to_translate ) ) ) {
		if ( ! empty( $strings_to_translate[ $translated_text ] ) ) {
			return $strings_to_translate[ $translated_text ];
		}
	}

	return $translated_text;

}
add_filter( 'gettext', __NAMESPACE__ . '\filter_gettext', 1, 3 );
add_filter( 'gettext_with_context', __NAMESPACE__ . '\filter_gettext', 1, 4 );

/**
 * Output for our settings section.
 * @return void
 */
function settings_section_output() {
	echo esc_html__( 'Provide your own text to rename the comments section as you desire.', 'rename-comments' );
}

/**
 * Output for the Comment Name field.
 * @return void
 */
function field_comments_name() {
	$value = get_option( 'rc_comments_name' );
	?>
	<p><label for="comments_name"><?php esc_html_e( 'This is the new name for a "Comment". E.g. you might want to call them "Letters".', 'rename-comments' ); ?></label></p>
	<p><input id="comments_name" name="rc_comments_name" value="<?php echo esc_attr( $value ) ?>" /></p>
	<?php
}

/**
 * Output for the Plural Comment Name field.
 * @return void
 */
function field_comments_name_plural() {
	$value = get_option( 'rc_comments_name_plural' );
	?>
	<p><label for="comments_name_plural"><?php esc_html_e( 'This is the new name for a "Comment" in it\'s plural form.', 'rename-comments' ); ?></label></p>
	<p><input id="comments_name_plural" name="rc_comments_name_plural" value="<?php echo esc_attr( $value ) ?>" /></p>
	<?php
}

/**
 * Output for the "Leave Comment" field
 * @return void
 */
function field_comments_leave_comment() {
	$value = get_option( 'rc_comments_leave_comment' );
	?>
	<p><label for="comments_leave_comment"><?php esc_html_e( 'This text will replace the "Leave a comment" message.', 'rename-comments' ); ?></label></p>
	<p><input id="comments_leave_comment" name="rc_comments_leave_comment" value="<?php echo esc_attr( $value ) ?>" /></p>
	<?php
}

/**
 * Output for the "comments closed" field
 * @return void
 */
function field_comments_comments_closed() {
	$value = get_option( 'rc_comments_comments_closed' );
	?>
	<p><label for="comments_comments_closed"><?php esc_html_e( 'This text will replace the "Comments are closed" message.', 'rename-comments' ); ?></label></p>
	<p><input id="comments_comments_closed" name="rc_comments_comments_closed" value="<?php echo esc_attr( $value ) ?>" /></p>
	<?php
}

/**
 * Output for the "No comments" field
 * @return void
 */
function field_comments_no_comments() {
	$value = get_option( 'rc_comments_no_comments' );
	?>
	<p><label for="comments_no_comments"><?php esc_html_e( 'This text will replace the "No comments" message.', 'rename-comments' ); ?></label></p>
	<p><input id="comments_no_comments" name="rc_comments_no_comments" value="<?php echo esc_attr( $value ) ?>" /></p>
	<?php
}

/**
 * Output for the No Comments Title field
 * @return void
 */
function field_no_comments_title() {
	$value = get_option( 'rc_no_comments_title' );
	?>
	<p><label for="no_comments_title"><?php esc_html_e( 'This will be the heading for the section when there are no comments yet.', 'rename-comments' ); ?></label></p>
	<p><input id="no_comments_title" name="rc_no_comments_title" value="<?php echo esc_attr( $value ) ?>" /></p>
	<?php
}

/**
 * Output for the Comments Title field
 * @return void
 */
function field_comments_title() {
	$value = get_option( 'rc_comments_title' );
	?>
	<p><label for="comments_title"><?php esc_html_e( 'This will be the heading for the section when there are existing comments.', 'rename-comments' ); ?></label></p>
	<p><input id="comments_title" name="rc_comments_title" value="<?php echo esc_attr( $value ) ?>" /></p>
	<?php
}

/**
 * Grabs the relevant text from the option and returns it for output
 * @param  strong $id Name of the option
 * @return string     The text to use in place of the default
 */
function get_text( $id ) {
	$value = get_option( $id );
	if ( $value ) {
		return $value;
	}
	return false;
}

add_filter( 'newspack_comment_section_title_nocomments', function( $text ) {
	return ( get_text( 'rc_no_comments_title' ) ) ? get_text( 'rc_no_comments_title' ) : $text;
} );

add_filter( 'newspack_comment_section_title', function( $text ) {
	return ( get_text( 'rc_comments_title' ) ) ? get_text( 'rc_comments_title' ) : $text;
} );

add_filter( 'newspack_comments_name_plural', function( $text ) {
	return ( get_text( 'rc_comments_name_plural' ) ) ? get_text( 'rc_comments_name_plural' ) : $text;
} );

add_filter( 'newspack_comments_leave_comment', function( $text ) {
	return ( get_text( 'rc_comments_leave_comment' ) ) ? get_text( 'rc_comments_leave_comment' ) : $text;
} );

add_filter( 'newspack_comments_closed', function( $text ) {
	return ( get_text( 'rc_comments_comments_closed' ) ) ? get_text( 'rc_comments_comments_closed' ) : $text;
} );

add_filter( 'newspack_no_comments', function( $text ) {
	return ( get_text( 'rc_comments_no_comments' ) ) ? get_text( 'rc_comments_no_comments' ) : $text;
} );

add_filter( 'newspack_number_comments', function ( $text ) {
	$replacements = 0;

	$new_text = str_replace( 'Comments', get_text( 'rc_comments_name_plural' ), $text, $replacements );
	if ( 0 < $replacements ) {
		return $new_text;
	}

	$new_text = str_replace( 'Comment', get_text( 'rc_comments_name' ), $text, $replacements );
	if ( 0 < $replacements ) {
		return $new_text;
	}

	return $text;
} );

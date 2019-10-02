=== Plugin Name ===
Contributors: automattic, philipjohn
Tags: comments
Requires at least: 5.2
Tested up to: 5.2
Requires PHP: 7.2
Stable tag: 0.1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Provides an ability to rename the comments section of a WordPress site.

== Description ==

With some tweaks to the theme's comments templates, this plugin allows for the renaming of the comments section. It is primarily built to work with the Newspack theme.

Extra fields are added to the Discussion settings screen to allow site owners to choose what naming they'd like to use. The plugin then exposes them through a helper function (`Rename_Comments\get_text( $id )`) which can be used within the comments template to output the relevant texts.

== Installation ==

Just like any other WP plugin.

== Changelog ==

= 0.1.0 =
* First version

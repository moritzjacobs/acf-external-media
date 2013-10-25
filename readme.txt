=== Advanced Custom Fields: External Media Field ===
Contributors: Moritz Jacobs
Tags: acf, vimeo, youtube, media
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add YouTube, Vimeo, Soundcloud or external images as embedded assets via ACF.

== Description ==

This enables a custom field (ACF) for embedding YouTube, Vimeo, SoundCloud or external image content by URL. Just paste the URL and valid embedding code will be generated.

= Compatibility =

This add-on will work with:

* version 4 and up

== Installation ==

This add-on can be treated as both a WP plugin and a theme include.

= Plugin =
1. Copy the 'acf-embed' folder into your plugins folder
2. Activate the plugin via the Plugins admin page

= Include =
1.	Copy the 'acf-embed' folder into your theme folder (can use sub folders). You can place the folder anywhere inside the 'wp-content' directory
2.	Edit your functions.php file and add the code below (Make sure the path is correct to include the acf-embed.php file)

`
add_action('acf/register_fields', 'my_register_fields');

function my_register_fields()
{
	include_once('acf-embed/acf-embed.php');
}
`

== Changelog ==

= 0.0.1 =
* Initial Release.

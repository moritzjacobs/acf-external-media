acf-external-media
==================

## YouTube/Vimeo/Souncloud/img field add-on for Advanced Custom Fields

![Screenshot](http://i.imgur.com/WOoWHn6.png)

### Description

This enables a custom field (ACF) for embedding YouTube, Vimeo, SoundCloud or external image content by URL. Just paste the URL and valid embedding code will be generated.

### Compatibility

This add-on will work with ACF version 4 and up

### Installation

This add-on can be treated as both a WP plugin and a theme include.

#### Plugin 
1. Copy the 'acf-embed' folder into your plugins folder
2. Activate the plugin via the Plugins admin page

#### Include
1.	Copy the 'acf-embed' folder into your theme folder (can use sub folders). You can place the folder anywhere inside the 'wp-content' directory
2.	Edit your functions.php file and add the code below (Make sure the path is correct to include the acf-embed.php file)
    
		add_action('acf/register_fields', 'my_register_fields');
	
		function my_register_fields()
		{
			include_once('acf-embed/acf-embed.php');
		}
	
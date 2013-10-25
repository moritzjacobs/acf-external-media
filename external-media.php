<?php

class acf_field_external_media extends acf_field
{
	// vars
	var $settings, // will hold info such as dir / path
	$defaults; // will hold default field options


	/*
	*  __construct
	*
	*  Set name / label needed for actions / filters
	*
	*  @since	3.6
	*  @date	23/01/13
	*/

	function __construct()
	{
		// vars
		$this->name = 'external_media';
		$this->label = __('External Media');
		$this->category = __("Basic",'acf'); // Basic, Content, Choice, etc
		$this->defaults = array(
			"output_type" => "code"
		);


		// do not delete!
		parent::__construct();


		// settings
		$this->settings = array(
			'path' => apply_filters('acf/helpers/get_path', __FILE__),
			'dir' => apply_filters('acf/helpers/get_dir', __FILE__),
			'version' => '1.0.0'
		);


		add_action('wp_ajax_acf_external_media_get_link_data', function() {
				echo json_encode($this->get_link_data($_POST['url']));
				die();
			});

	}


	/*
	*  create_options()
	*
	*  Create extra options for your field. This is rendered when editing a field.
	*  The value of $field['name'] can be used (like bellow) to save extra data to the $field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field	- an array holding all the field's data
	*/

	function create_options($field)
	{

		// $field = array_merge($this->defaults, $field);
		// key is needed in the field names to correctly save the data
		$key = $field['name'];


		// Create Field Options HTML
?>
<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label><?php _e("Output", 'acf'); ?></label>
	</td>
	<td>
		<?php

		do_action('acf/create_field', array(
				'type'    =>  'select',
				'name'    =>  'fields[' . $key . '][output_type]',
				'value'   =>  $field['output_type'],
				'choices' =>  array(
					'code' => __('Display Code'),
					'data' => __('Array Data'),
					'URL' => __('URL')
				)
			));

?>
	</td>
</tr>
		<?php

	}


	/*
	*  create_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field - an array holding all the field's data
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/



	function get_link_data($link, $w=640, $h=390) {
		$url = parse_url(trim($link));
		$embed = "";
		$type = "error";
		$title = __("Error: invalid media URL!");
		$thumb = $this->settings['dir'] . 'images/error.png';
		$normalized = $link;

		if (empty($url['host'])) {
			return array("type"=>$type, "title"=>$title, "thumb"=>$thumb, "embed"=>$embed, "url"=>$normalized);
		}

		// youtube.com
		if (strpos($url["host"], "youtube.com") !== false) {
			parse_str($url["query"], $query);
			$id = $query["v"];
			$thumb = "http://img.youtube.com/vi/".$id."/mqdefault.jpg";
			$xmlData = simplexml_load_string(@file_get_contents("http://gdata.youtube.com/feeds/api/videos/{$id}?fields=title"));
			$title = (string)$xmlData->title;
			$embed = '<iframe width="'.$w.'" height="'.$h.'" class="youtube-player" type="text/html" src="http://www.youtube.com/embed/'.$id.'" allowfullscreen frameborder="0"></iframe>';
			$type = "youtube";
			$normalized = "http://www.youtube.com/watch?v=".$id;


			
		// youtu.be
		} elseif (strpos($url["host"], "youtu.be") !== false) {
			$id = substr($url["path"], 1);
			$thumb = "http://img.youtube.com/vi/".$id."/mqdefault.jpg";
			$xmlData = simplexml_load_string(@file_get_contents("http://gdata.youtube.com/feeds/api/videos/{$id}?fields=title"));
			$title = (string)$xmlData->title;
			$type = "youtube";
			$embed = '<iframe width="'.$w.'" height="'.$h.'" class="youtube-player" type="text/html" src="http://www.youtube.com/embed/'.$id.'" allowfullscreen frameborder="0"></iframe>';
			$normalized = "http://www.youtube.com/watch?v=".$id;



			
		// vimeo.com
		} elseif (strpos($url["host"], "vimeo.com") !== false) {
			$id = substr($url["path"], 1);
			$hash = unserialize(@file_get_contents("http://vimeo.com/api/v2/video/".$id.".php"));
			$thumb = $hash[0]['thumbnail_medium'];
			$title = $hash[0]['title'];
			$embed = '<iframe width="'.$w.'" height="'.$h.'" src="http://player.vimeo.com/video/'.$id.'" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
			$type = "vimeo";
			$normalized = "https://vimeo.com/".$id;



			
		// soundcloud
		} elseif (strpos($url["host"], "soundcloud.com") !== false) {
			$esc = urlencode($link);
			$title = substr($url["path"], 1);
			$thumb = $this->settings['dir'] . 'images/sc.png';
			$type = "soundcloud";
			$embed = '<iframe width="'.$w.'" height="'.$h.'" scrolling="no" frameborder="no"src="http://w.soundcloud.com/player/?url='.$esc.'&auto_play=false&color=915f33&theme_color=00FF00"></iframe>';
			

		// gif
		} elseif (strpos($url["path"], ".png") !== false || strpos($url["path"], ".jpg") !== false || strpos($url["path"], ".jpeg") !== false || strpos($url["path"], ".gif") !== false) {
			$thumb = $link;
			$title = substr($url["path"], 1);
			$type = "img";
			$embed = '<img src="'.$link.'" alt="'.$title.'"></a>';
			
		}

		$ret = array("type"=>$type, "title"=>$title, "thumb"=>$thumb, "embed"=>$embed, "url"=>$normalized);
		return $ret;
	}











	function create_field( $field )
	{
		if (!empty($field['value'])) {
			$ld = $this->get_link_data($field['value']);
			$thumb = $ld['thumb'];
			$title = $ld['title'];
			$url = $ld['url'];
		}

?>






		<img class="acf-external-media-image" src="<?php echo @$thumb?>" alt=""/>

		<div class="hover">
			<ul class="bl">
				<li><a class="acf-button-delete ir" href="#"><?php echo __('Delete')?></a></li>
				<li><a class="acf-button-edit ir" href="#"><?php echo __('Edit')?></a></li>
			</ul>
		</div>

		
		<div class="has-external-media meta">
			<p>
				<strong><a href="<?php echo @$url?>" alt="<?php echo @$title?>" target="_blank"><?php echo @$title?></a></strong><br>
				<code><?php echo @$url?></code>
			</p>
		</div>


		
		
		<input class="text acf-field-external-media" id="<?php echo $field['id']?>" data-key="<?php echo $field['key']; ?>" type="hidden" name="<?php echo $field['name']; ?>" value="<?php echo $field['value']; ?>" />



		<?php


	}


	/*
	*  input_admin_enqueue_scripts()
	*
	*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
	*  Use this action to add css + javascript to assist your create_field() action.
	*
	*  $info	http://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/

	function input_admin_enqueue_scripts()
	{
		// Note: This function can be removed if not used


		// register acf scripts
		wp_register_script('acf-input-external-media', $this->settings['dir'] . 'js/input.js', array('acf-input'), $this->settings['version']);
		wp_register_style('acf-input-external-media', $this->settings['dir'] . 'css/input.css', array('acf-input'), $this->settings['version']);


		// scripts
		wp_enqueue_script(array(
				'acf-input-external-media',
			));

		// styles
		wp_enqueue_style(array(
				'acf-input-external-media',
			));

	}


	/*
	*  input_admin_head()
	*
	*  This action is called in the admin_head action on the edit screen where your field is created.
	*  Use this action to add css and javascript to assist your create_field() action.
	*
	*  @info	http://codex.wordpress.org/Plugin_API/Action_Reference/admin_head
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/

	function input_admin_head()
	{
		// Note: This function can be removed if not used
	}


	/*
	*  field_group_admin_enqueue_scripts()
	*
	*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is edited.
	*  Use this action to add css + javascript to assist your create_field_options() action.
	*
	*  $info	http://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/

	function field_group_admin_enqueue_scripts()
	{
		// Note: This function can be removed if not used
	}


	/*
	*  field_group_admin_head()
	*
	*  This action is called in the admin_head action on the edit screen where your field is edited.
	*  Use this action to add css and javascript to assist your create_field_options() action.
	*
	*  @info	http://codex.wordpress.org/Plugin_API/Action_Reference/admin_head
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/

	function field_group_admin_head()
	{
		// Note: This function can be removed if not used
	}


	/*
	*  load_value()
	*
	*  This filter is appied to the $value after it is loaded from the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value - the value found in the database
	*  @param	$post_id - the $post_id from which the value was loaded from
	*  @param	$field - the field array holding all the field options
	*
	*  @return	$value - the value to be saved in te database
	*/

	function load_value($value, $post_id, $field)
	{
		// Note: This function can be removed if not used
		return $value;
	}


	/*
	*  update_value()
	*
	*  This filter is appied to the $value before it is updated in the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value - the value which will be saved in the database
	*  @param	$post_id - the $post_id of which the value will be saved
	*  @param	$field - the field array holding all the field options
	*
	*  @return	$value - the modified value
	*/

	function update_value($value, $post_id, $field)
	{
		// Note: This function can be removed if not used
		return $value;
	}


	/*
	*  format_value()
	*
	*  This filter is appied to the $value after it is loaded from the db and before it is passed to the create_field action
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value	- the value which was loaded from the database
	*  @param	$post_id - the $post_id from which the value was loaded
	*  @param	$field	- the field array holding all the field options
	*
	*  @return	$value	- the modified value
	*/

	function format_value($value, $post_id, $field)
	{
		// defaults?
		/*
		$field = array_merge($this->defaults, $field);
		*/

		// perhaps use $field['preview_size'] to alter the $value?


		// Note: This function can be removed if not used
		return $value;
	}


	/*
	*  format_value_for_api()
	*
	*  This filter is appied to the $value after it is loaded from the db and before it is passed back to the api functions such as the_field
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value	- the value which was loaded from the database
	*  @param	$post_id - the $post_id from which the value was loaded
	*  @param	$field	- the field array holding all the field options
	*
	*  @return	$value	- the modified value
	*/

	function format_value_for_api($value, $post_id, $field)
	{
	
		if($field['output_type'] == "code") {
			return $this->get_link_data($value)['embed'];
		} else if($field['output_type'] == "data") {
			return $this->get_link_data($value);
		} else {
			return $value;	
		}
	}


	/*
	*  load_field()
	*
	*  This filter is appied to the $field after it is loaded from the database
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field - the field array holding all the field options
	*
	*  @return	$field - the field array holding all the field options
	*/

	function load_field($field)
	{
		// Note: This function can be removed if not used
		return $field;
	}


	/*
	*  update_field()
	*
	*  This filter is appied to the $field before it is saved to the database
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field - the field array holding all the field options
	*  @param	$post_id - the field group ID (post_type = acf)
	*
	*  @return	$field - the modified field
	*/

	function update_field($field, $post_id)
	{
		// Note: This function can be removed if not used
		return $field;
	}


}


// create field
new acf_field_external_media();

?>

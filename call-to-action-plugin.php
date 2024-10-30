<?php
/*
Plugin Name: Call To Action Plugin
Plugin URI: http://easycalltoactionplugin.com/
Description: Easily add a Call To Action Box to your WordPress blog!
Author: Taylor Marek
Version: 3.1.3
Author URI: http://taylormarek.com//call-to-action-author-bio-wordpress-plugin-professional-bloggers/
*/

global $cbox_db_version;
$cbox_db_version = "2.0"; // Don't change this!!

function cbox_admin_url( $query = array() ) {
	if ( ! isset( $query['page'] ) )
		$query['page'] = 'custombox';

	$path = 'admin.php';

	if ( $query = http_build_query( $query ) )
		$path .= '?' . $query;

	$url = admin_url( $path );

	return esc_url_raw( $url );
}

function cbox_options()
{
	add_menu_page('Call To Action', 'Call To Action', 'level_0', 'custombox', 'cbox_options_page');
}

function cbox_options_page()
{ ?>
<?php
	global $wpdb;

	global $user_ID; get_currentuserinfo();
	$current_name = (isset($_REQUEST['name']) && $_REQUEST['name'] != '') ? $_REQUEST['name'] : 'Default';

	if ( isset($_REQUEST['option_save']) && $_REQUEST['option_save'] ) {

	    $options = array(
	    	'cbox_mini'      => isset($_REQUEST['cbox_mini']) ? $_REQUEST['cbox_mini'] : null,
			'cbox_imgurl'    => isset($_REQUEST['cbox_imgurl']) ? $_REQUEST['cbox_imgurl'] : null,
			'cbox_title'     => isset($_REQUEST['cbox_title']) ? $_REQUEST['cbox_title'] : null,
			'cbox_content'   => isset($_REQUEST['cbox_content']) ? $_REQUEST['cbox_content'] : null,
			'cbox_linkurl'   => isset($_REQUEST['cbox_linkurl']) ? $_REQUEST['cbox_linkurl'] : null,
			'cbox_linktext'  => isset($_REQUEST['cbox_linktext']) ? $_REQUEST['cbox_linktext'] : null,
			'cbox_color'     => isset($_REQUEST['cbox_color']) ? $_REQUEST['cbox_color'] : null,
			'cbox_font'      => isset($_REQUEST['cbox_font']) ? $_REQUEST['cbox_font'] : null,
			'cbox_fontcolor' => isset($_REQUEST['cbox_fontcolor']) ? $_REQUEST['cbox_fontcolor'] : null
	    );

		$rows_affected = $wpdb->update( $wpdb->prefix . "custombox", array('options' => serialize($options)), array('name' => $current_name ));

	} else if ( isset($_REQUEST['option_new']) && $_REQUEST['option_new'] ) {
		if ( $_REQUEST['cbox_newname'] != '' ) {
			$def_options = array ( 'cbox_mini' => '',
								'cbox_imgurl' => 'http://www.taylormarek.com/cta-logo.jpg',
								'cbox_title' => 'Put Your Title Here',
								'cbox_content' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
								'cbox_linkurl' => 'http://www.mywebsite.com/',
								'cbox_linktext' => 'My Website',
								'cbox_color' => 'ffffcc',
								'cbox_font' => 'Verdana',
								'cbox_fontcolor' => '#000000' );

			$rows_affected = $wpdb->insert( $wpdb->prefix . "custombox", array( 'name' => $_REQUEST['cbox_newname'], 'userid' => $user_ID, 'options' => serialize($def_options) ) );

			echo '<meta http-equiv="Refresh" content="0; url=' . cbox_admin_url(array('name' => $_REQUEST['cbox_newname'])) . '">';
			return;
		}

	} else if ( isset($_REQUEST['option_delete']) && $_REQUEST['option_delete'] ) {
		$rows_affected = $wpdb->get_results( $wpdb->prepare("DELETE FROM `" . $wpdb->prefix . "custombox` WHERE `name`='$current_name'") );

		echo '<meta http-equiv="Refresh" content="0; url=' . cbox_admin_url() . '">';
		return;
	}

	$cbox_names = $wpdb->get_results( $wpdb->prepare("SELECT `name` FROM `" . $wpdb->prefix . "custombox` WHERE `userid`=0 OR `userid`=$user_ID") );
	$cbox_options = $wpdb->get_results( $wpdb->prepare("SELECT `options` FROM `" . $wpdb->prefix . "custombox` WHERE `name`='$current_name'") );
?>
<div class="wrap">
<div class="icon32" id="icon-options-general"><br /></div>
<h2 style="margin-bottom: 20px;">Call To Action Plugin Options</h2>

<div class="dlm" id="poststuff">
<form style="border: medium none; background: none repeat scroll 0% 0% transparent;" method="post">

	<ul class="subsubsub">
	<?php foreach ($cbox_names as $cbox_name) { ?>
		<li><a <?php if($cbox_name->name == $current_name) echo 'class="current"'; ?> href="<?php echo cbox_admin_url(array('name' => $cbox_name->name)); ?>"><?php echo $cbox_name->name; ?></a></li>
	<?php } ?>
		<li><a class="thickbox" href="#TB_inline?height=200&width=400&inlineId=cbox-addnew">Add New</a></li>
	</ul>
	<div class="clear"></div>

	<?php foreach ($cbox_options as $cbox_option) {
		$the_options = unserialize($cbox_option->options);
		foreach ($the_options as $k => $v) {
			$the_options[$k] = stripslashes($v);
		}?>

	<div class="postbox">
		<h3 class="hndle"><span>General Options</span></h3>
		<div class="inside"><table class="form-table"><tbody>
                <b>Support:</b> <a href="http://calltoactionwordpressplugin.com/support" target="_blank">http://calltoactionwordpressplugin.com/support</a>
		<br><br>
		<b>Suggestions:</b> <a href="http://calltoactionwordpressplugin.com/suggestions" target="_blank">http://calltoactionwordpressplugin.com/suggestions</a>
		<br><br>
		<b>Refer a Friend:</b> <a href="http://calltoactionwordpressplugin.com/affiliates/" target="_blank">http://calltoactionwordpressplugin.com/affiliates/</a>
		<br>
		<h4>Customize Your Box Below</h4>
		<tr>
			<th><label for="cbox_mini">Mini Mode</label></th>
			<?php if($the_options['cbox_mini'] == 'true') { $checked = " checked=\"checked\""; } else { $checked = ""; } ?>
			<td><input id="cbox_mini" name="cbox_mini" type="checkbox" value="true"<?php echo $checked; ?> />
			<label for="cbox_mini">Use mini mode.</label><br />
			Shows only the <strong>Logo/Image</strong> and the <strong>Content Text</strong>. (The image will be shrunk to 40x40px to fit.)
		</tr>
		<tr>
			<th><label for="cbox_imgurl">Logo/Image URL (Direct link to any image url, all formats accepted.)(Can be left blank.)</label></th>
			<td><input class="code" type="text" name="cbox_imgurl" id="cbox_imgurl" size="73" value="<?php echo(htmlentities($the_options['cbox_imgurl'])); ?>" /></td>
		</tr>
		<tr>
			<th><label for="cbox_title">Title Text(NO HTML)</label></th>
			<td><input class="code" type="text" name="cbox_title" id="cbox_title" size="73" value="<?php echo(htmlentities($the_options['cbox_title'])); ?>" /></td>
		</tr>
		<tr>
			<th><label for="cbox_content">Content Text(HTML can be used here.)</label></th>
			<td><textarea rows="7" cols="60" name="cbox_content" id="cbox_content"><?php echo(htmlentities($the_options['cbox_content'])); ?>
							</textarea></td>
		</tr>
<tr>
			<th><label for="cbox_font">What Font Should The Entire Text Be? (Can be changed at anytime.)</label></th>
			<td><select name="cbox_font" id="cbox_font">
				<option<?php if ( $the_options['cbox_font'] == 'Arial') { echo ' selected="selected"'; } ?> value="Arial">Arial</option>
				<option<?php if ( $the_options['cbox_font'] == 'Comic Sans MS') { echo ' selected="selected"'; } ?> value="Comic Sans MS">Comic Sans MS</option>
				<option<?php if ( $the_options['cbox_font'] == 'Courier New') { echo ' selected="selected"'; } ?> value="Courier New">Courier New</option>
				<option<?php if ( $the_options['cbox_font'] == 'Georgia') { echo ' selected="selected"'; } ?> value="Georgia">Georgia</option>
				<option<?php if ( $the_options['cbox_font'] == 'Helvetica') { echo ' selected="selected"'; } ?> value="Helvetica">Helvetica</option>
				<option<?php if ( $the_options['cbox_font'] == 'impact') { echo ' selected="selected"'; } ?> value="impact">Impact</option>
				<option<?php if ( $the_options['cbox_font'] == 'sans-serif') { echo ' selected="selected"'; } ?> value="sans-serif">Sans-Serif</option>
				<option<?php if ( $the_options['cbox_font'] == 'Tahoma') { echo ' selected="selected"'; } ?> value="Tahoma">Tahoma</option>
				<option<?php if ( $the_options['cbox_font'] == 'Times New Roman') { echo ' selected="selected"'; } ?> value="Times New Roman">Times New Roman</option>
				<option<?php if ( $the_options['cbox_font'] == 'Verdana') { echo ' selected="selected"'; } ?> value="Verdana">Verdana</option>
			</select></td>
		</tr>
		<tr>
			<th><label for="cbox_fontcolor">What Color Should The Content Text Be? (Can be changed at anytime.)</label></th>
			<td><select name="cbox_fontcolor" id="cbox_fontcolor">
				<option<?php if ( $the_options['cbox_fontcolor'] == '000000') { echo ' selected="selected"'; } ?> value="000000">Black</option>
				<option<?php if ( $the_options['cbox_fontcolor'] == 'ffffff') { echo ' selected="selected"'; } ?> value="ffffff">White</option>
				<option<?php if ( $the_options['cbox_fontcolor'] == 'C0C0C0') { echo ' selected="selected"'; } ?> value="C0C0C0">Grey</option>
				<option<?php if ( $the_options['cbox_fontcolor'] == '000033') { echo ' selected="selected"'; } ?> value="000033">Navy Blue</option>
			</select></td>
		</tr>
		<tr>
			<th><label for="cbox_linktext">Link Text (NO HTML)</label></th>
			<td><input class="code" type="text" name="cbox_linktext" id="cbox_linktext" size="73" value="<?php echo(htmlentities($the_options['cbox_linktext'])); ?>" /></td>
		</tr>
		<tr>
			<th><label for="cbox_linkurl">Link URL</label></th>
			<td><input class="code" type="text" name="cbox_linkurl" id="cbox_linkurl" size="73" value="<?php echo(htmlentities($the_options['cbox_linkurl'])); ?>" /></td>
		</tr>
		<tr>
			<th><label for="cbox_color">What Color Should The Box Be? (Can be changed at any time.)</label></th>
			<td><select name="cbox_color" id="cbox_color">
				<option<?php if ( $the_options['cbox_color'] == '#ffcccc') { echo ' selected="selected"'; } ?> value="#ffcccc">Red</option>
				<option<?php if ( $the_options['cbox_color'] == '#ffffcc') { echo ' selected="selected"'; } ?> value="#ffffcc">Yellow</option>
				<option<?php if ( $the_options['cbox_color'] == '#ccffcc') { echo ' selected="selected"'; } ?> value="#ccffcc">Light Green</option>
				<option<?php if ( $the_options['cbox_color'] == '#ccffff') { echo ' selected="selected"'; } ?> value="#ccffff">Light Blue</option>
				<option<?php if ( $the_options['cbox_color'] == '#ffffff') { echo ' selected="selected"'; } ?> value="#ffffff">White</option>
				<option<?php if ( $the_options['cbox_color'] == 'transparent') { echo ' selected="selected"'; } ?> value="transparent">Transparent</option>

			</select></td>
		</tr>

		</tbody></table></div>
	</div>
	<?php } ?>
	<p class="submit">
		<input type="submit" class="button-primary" name="option_save" value="Save Changes" />
		<?php if ($current_name != 'Default') { ?><input type="submit" class="button" name="option_delete" value="Delete" onclick="if (confirm('You are about to delete this box.\n  \'Cancel\' to stop, \'OK\' to delete.')) {return true;} return false;" /><?php } ?>
	</p>
</form>
</div>
</div><!-- /.postbox -->
<div id="cbox-addnew" class="hidden">
	<h4>Add New Box</h4>
	<form action="" method="GET">
		<input type="hidden" name="page" value="custombox" />
		<input class="code" type="text" name="cbox_newname" id="cbox_newname" size="43" value="" /></td>
		<input type="submit" class="button" name="option_new" value="Add New" />
	</form>
</div>
<?php }

function cbox_style()
{ ?>
<style type="text/css">
#custom-box {
border:1px solid #ddd;
margin:auto;
padding:10px;
}

#custom-box img {
border:none;
padding:0;
margin:0;
}
#custom-box h4 {
color: red;
font-size: 1em;
}
</style>
<?php }

function cbox_show($content)
{
	global $post, $wpdb;
	$box = '';
	if (is_single() || is_page()) {
	    $option = get_post_meta($post->ID, 'cbox_options', true);
	    if ($option == 'none') {
	        return $content;
	    }
		$cbox_options = $wpdb->get_results( $wpdb->prepare("SELECT `options` FROM `" . $wpdb->prefix . "custombox` WHERE `name`='$option'") );
		if ( count($cbox_options) < 1 ) $cbox_options = $wpdb->get_results( $wpdb->prepare("SELECT `options` FROM `" . $wpdb->prefix . "custombox` WHERE `name`='Default'") );

		foreach ($cbox_options as $cbox_option) {
			$the_options = unserialize($cbox_option->options);
    		foreach ($the_options as $k => $v) {
    			$the_options[$k] = stripslashes($v);
    		}

			$img = ($the_options['cbox_imgurl'] != '') ? '<img src="' . $the_options['cbox_imgurl'] . '" style="float:left; width:' . (($the_options['cbox_mini'] != 'true') ? '120' : '40') . 'px;" />' : '';

			$box = '<div id="custom-box" style="background:' . (($the_options['cbox_color'] != '' ? $the_options['cbox_color'] : 'ffffcc')) . '; width:' . (($the_options['cbox_mini'] != 'true' ? 'auto' : '380')) . 'px; font-family:' . (($the_options['cbox_font'] != '' ? $the_options['cbox_font'] : 'Verdana')) . ', Arial;">';

			$box .= $img . "\n"
			. '<div style="float:left; text-align:center; padding-left:10px; ' . (($img != '') ? 'width:330px;' : '') . '">' . "\n";
			if ($the_options['cbox_mini'] != 'true') { $box .= '<h4>' . $the_options['cbox_title'] . '</h4>'  . "\n"; }
			$box .= '<p style="color:#' . (($the_options['cbox_fontcolor'] != '' ? $the_options['cbox_fontcolor'] : '000000')) . ';">' . $the_options['cbox_content'] . '</p>' . "\n";
			if ($the_options['cbox_mini'] != 'true') { $box .= '<a href="' . $the_options['cbox_linkurl'] . '">' . $the_options['cbox_linktext'] . '</a>' . "\n"; }
			$box .= '</div>';

			$box .= '<div style="clear:both"></div></div>';
		}
	}

	return $content . $box;
}

add_action('admin_menu', 'cbox_options');

// Set the default options when the plugin is activated
function cbox_activation()
{
	global $wpdb,$cbox_db_version;
	$table_name = $wpdb->prefix . "custombox";

	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		$sql = "CREATE TABLE " . $table_name . " (
			  name varchar(20) NOT NULL,
			  userid bigint(20) NOT NULL,
			  options longtext NOT NULL,
			  UNIQUE KEY name (name)
			);";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);

		$def_name = "Default";
		$def_options = array ( 'cbox_mini' => '',
							'cbox_imgurl' => 'http://www.taylormarek.com/cta-logo.jpg',
							'cbox_title' => 'Put Your Title Here',
							'cbox_content' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
							'cbox_linkurl' => 'http://www.mywebsite.com/',
							'cbox_linktext' => 'My Website',
							'cbox_color' => '#ffffcc',
							'cbox_font' => 'Verdana',
							'cbox_fontcolor' => '000000' );

		$rows_affected = $wpdb->insert( $table_name, array( 'name' => $def_name, 'userid' => '0', 'options' => serialize($def_options) ) );

		add_option("cbox_db_version", $cbox_db_version);
	}
}

// Create custom meta box
function create_cbox_meta() {
	global $key;
	add_meta_box( 'cbox_options', 'Choose Which Call To Action Box To Use', 'meta_options', 'post', 'normal', 'high' );
	add_meta_box( 'cbox_options', 'Choose Which Call To Action Box To Use', 'meta_options', 'page', 'normal', 'high' );
}
function meta_options() {
	global $post, $wpdb;
	$option = get_post_meta($post->ID, 'cbox_options', true); ?>

	<p><label for="cbox_options">Select Box Name:</label><br />
	<?php $names = $wpdb->get_results( $wpdb->prepare("SELECT `name` FROM `" . $wpdb->prefix . "custombox`") ); ?>
	<select name="cbox_options" id="cbox_options" style="width:99%;">
        <option value="none"<?php if ('none' === $option) { echo ' selected="selected"'; } ?>>None</option>
        <option value="Default"<?php if ('' === $option || 'Default' === $option) { echo ' selected="selected"'; } ?>>Default</option>
    	<?php foreach ($names as $name): ?>
            <?php if ('Default' == $name->name) { continue; } ?>
    	    <option<?php if ( $option == $name->name ) { echo ' selected="selected"'; } ?>><?php echo $name->name; ?></option>
    	<?php endforeach; ?>
    </select></p>

<?php }
function save_cbox_meta( $post_id ) {
	if (isset($_POST['cbox_options'])) {
	    update_post_meta($post_id, "cbox_options", $_POST["cbox_options"]);
	}
}
add_action( 'admin_init', 'create_cbox_meta' );
add_action( 'save_post', 'save_cbox_meta' );

add_action( 'admin_print_styles', 'cbox_admin_enqueue_styles' );
function cbox_admin_enqueue_styles() {
	global $plugin_page;

	if ( ! isset( $plugin_page ) || 'custombox' != $plugin_page )
		return;

	wp_enqueue_style( 'thickbox' );
}

add_action( 'admin_print_scripts', 'cbox_admin_enqueue_scripts' );
function cbox_admin_enqueue_scripts() {
	global $plugin_page;

	if ( ! isset( $plugin_page ) || 'custombox' != $plugin_page )
		return;

	wp_enqueue_script( 'thickbox' );
}

register_activation_hook( __FILE__, 'cbox_activation');

add_filter('the_content', 'cbox_show');
add_filter('wp_head', 'cbox_style');

?>
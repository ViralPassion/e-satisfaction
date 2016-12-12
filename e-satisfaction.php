<?php
/*
  Plugin Name: E-satisfaction
  Description: e-satisfaction.
  Version: 3.1
  Author: Viral Passion
  Text Domain: esat
  Domain Path: /esat
*/


defined( 'ABSPATH' ) or die();

/* footer script */
add_action('wp_footer', 'echo_esatisfaction_code');
function echo_esatisfaction_code(){
	if(esc_attr( get_option('es_site_id') )!=""){
	echo ("<script>
				var _esatisf = _esatisf || [];
				_esatisf.push(['_site','".get_option('es_site_id')."']);

				(function() {
				var ef = document.createElement('script'); ef.type = 'text/javascript'; ef.async = true;
				ef.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'www.e-satisfaction.gr/min/f=e-satisfaction.js';
				var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ef, s);
				})();
			</script>
				");
	}
}


add_action( 'woocommerce_thankyou', 'esatisfaction_thankyou_form' );
function esatisfaction_thankyou_form( $order_id ) {

	if(esc_attr( get_option('es_auth_key') )!=""){

		// Lets grab the order
		$url = 'https://www.e-satisfaction.gr/miniquestionnaire/genkey.php?site_auth='.get_option('es_auth_key');
		$order = wc_get_order( $order_id );
		$ch = curl_init();

	    curl_setopt($ch,CURLOPT_URL,$url);
	    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	//  curl_setopt($ch,CURLOPT_HEADER, false);

	    $output=curl_exec($ch);

	    curl_close($ch);
		$token = $output;

		echo ("<div id='esatisf-form'></div>
		<script>
			var _esatisf = _esatisf || [];
			_esatisf.push(['_responder', '".$order_id."']);
			_esatisf.push(['_token', '".$token."']);
			_esatisf.push(['_email', '".$order->billing_email."']);
			_esatisf.push(['_showQuestionnaire', '#esatisf-form']);
		</script>");
	}
}







// create custom plugin settings menu
add_action('admin_menu', 'esatisfaction');

function esatisfaction() {

	//create new top-level menu
	add_menu_page('E-Satisfaction', 'e-Satisfaction', 'administrator', __FILE__, 'esatisfaction_plugin_settings_page','dashicons-share'  );

	//call register settings function
	add_action( 'admin_init', 'register_esatisfaction_settings' );
}


function register_esatisfaction_settings() {
	//register our settings
	register_setting( 'esatisfaction_settings-group', 'es_site_id' );
	register_setting( 'esatisfaction_settings-group', 'es_auth_key' );
}

function esatisfaction_plugin_settings_page() {
?>
<div class="wrap">
<h2>E-satisfaction</h2>


<form method="post" action="options.php">
    <?php settings_fields( 'esatisfaction_settings-group' ); ?>
    <?php do_settings_sections( 'esatisfaction_settings-group' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">YOUR_SITE_ID</th>
        <td><input type="text" name="es_site_id" value="<?php echo esc_attr( get_option('es_site_id') ); ?>" /></td>
        </tr>

        <tr valign="top">
        <th scope="row">YOUR_AUTH_KEY</th>
        <td><input type="text" name="es_auth_key" value="<?php echo esc_attr( get_option('es_auth_key') ); ?>" /></td>
        </tr>


    </table>

    <?php submit_button(); ?>

</form>
</div>
<?php } ?>

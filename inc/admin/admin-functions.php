<?php
/**
 * Functions.
 *
 */

 function partnify_menu() {
	$assets_path = plugin_dir_url( PARTNIFY_PLUGIN_FILE ) . 'assets/';
    add_menu_page( 'Partnify', 'Partnify', 'manage_options', 'partnify-settings', 'partnify_page', $assets_path . 'images/logo.png' );
 }

 function partnify_page() {
     $settings = partnify_get_settings();
	 $partnify_status = isset( $settings['partnify_status'] ) ? $settings['partnify_status'] : '';
	 $partnify_email = isset( $settings['partnify_email'] ) ? $settings['partnify_email'] : '';
	 $partnify_api_key = isset( $settings['partnify_api_key'] ) ? $settings['partnify_api_key'] : '';
	 $status_message = 'Not Connected';
	 $status_class = 'not-connected';
	//  echo $partnify_status;
	 if ( $partnify_status == 'True' ){
		 $status_message = 'Connected';
		 $status_class = 'connected';
	 }  ?>
	 <style>
	 .notice-warning.mc4wp-is-dismissible{
		display:none;
	 }
	 </style>
     <div class="wrap partnify-settings">
        <h2>Partnify Settings</h2>
		<p>To get started you need a free account from <a href="https://www.partnify.com/invite/" target="_blank">here</a>.</p>
        <form method="post" action="" name="partnify_settings" id="partnify_settings" >
            <div class="form-field">
                <label class="form-field-label">Status</label>
                <p class="connection-status <?php echo esc_attr( $status_class ) ?>"><?php echo esc_html( $status_message ) ?></p>
            </div>
            <div class="form-field">
                <label class="form-field-label">Email Address</label>
                <input type="text" required name="partnify_email" class="partnify-email"  value="<?php echo esc_attr( $partnify_email ) ?>"  >
            </div>
            <div class="form-field">
                <label class="form-field-label">API Key</label>
                <input type="text" required data-parsley-pattern="^[A-Za-z0-9]{8}-[A-Za-z0-9]{4}-[A-Za-z0-9]{4}-[A-Za-z0-9]{4}-[A-Za-z0-9]{12}$" name="partnify_api_key" class="partnify-api-key" value="<?php echo esc_attr( $partnify_api_key ) ?>" >
            </div>
			<div class="form-field">
                <p>The API key for connecting with your Partnify account. <a href="https://www.partnify.com/wordpress-partner-plugin/" target="_blank" >Get your API key here</a></p>
            </div>

            <div class="form-field">
	            <div class="setting-changes">
	                <?php submit_button( __( 'Save Changes', 'partnify' ), 'primary', 'partnify_save_settings', false ); ?>
					<span class="spinner"></span>
				</div>
            </div>
        </form>
     </div>

     <?php
 }

 function partnify_save_settings_callback() {
    $url = 'https://app.partnify.com/wp/ValidatePartner';
    $settings = array();
    if ( isset( $_POST['partnify_email'] ) ) {
        $settings['partnify_email'] = $_POST['partnify_email'];
		$url = add_query_arg( 'email', $_POST['partnify_email'], $url );
    }
    if ( isset( $_POST['partnify_email'] ) ) {
		$settings['partnify_api_key'] = $_POST['partnify_api_key'];
		$url = add_query_arg( 'guid', $_POST['partnify_api_key'], $url );
    }
    $result =  wp_remote_get( $url );
	$settings['partnify_status'] = '';
	$message = 'Not Connected';
    if ( 200 === $result['response']['code'] ) {

		$settings['partnify_status'] = $result['body'];
		if ( 'True' == $result['body'] ) {
			$message = 'Connected';
		}
	}
	if( isset( $_POST['save'] ) && 'true' ===  $_POST['save'] ) {	

    	update_option( 'partnify_settings', $settings );
	}

	wp_send_json( array( 'status' =>  $settings['partnify_status'], 'message' => $message ) );
    
}

function partnify_get_campaign_callback() {
	$url = 'https://app.partnify.com/wp/GetVendorCampaigns';
	$settings = partnify_get_settings();
	$guid = $settings['partnify_api_key'];
	$url = add_query_arg( 'guid', $guid, $url );
	
	if ( isset( $_POST['vendorId'] ) ) {
		$url = add_query_arg( 'VendorId', $_POST['vendorId'], $url );
	}
	$inputCampaignID = '';
	if ( isset( $_POST['inputCampaignID'] ) ) {
		$inputCampaignID = $_POST['inputCampaignID'];
	}
	
	$result =  wp_remote_post( $url );
	
	$campaign_data = array();
	$status = '';
	if ( 200 === $result['response']['code'] ) {
		$campaign_data = json_decode( $result['body'] );
		$status= 'success';
	}
	wp_send_json( array( 'status' => $status, 'campaign_data' => $campaign_data, 'inputCampaignID' => $inputCampaignID ) );
}
function partnify_get_campaign_assets_callback() {
	$url = 'https://app.partnify.com/wp/GetCampaignAssets';
	$settings = partnify_get_settings();
	$guid = $settings['partnify_api_key'];
	$url = add_query_arg( 'guid', $guid, $url );
	
	if ( isset( $_POST['CampaignId'] ) ) {
		$url = add_query_arg( 'CampaignId', $_POST['CampaignId'], $url );
	}
	$inputRadioWrapper = '';
	if ( isset( $_POST['inputRadioWrapper'] ) ) {
		$inputRadioWrapper = $_POST['inputRadioWrapper'];
	}
	
	$result =  wp_remote_post( $url );
	
	$campaign_data = array();
	$status = '';
	if ( 200 === $result['response']['code'] ) {
		$campaign_data = json_decode( $result['body'] );
		$status= 'success';
	}
	wp_send_json( array( 'status' => $status, 'campaign_assets_data' => $campaign_data, 'inputRadioWrapper' => $inputRadioWrapper ) );
}

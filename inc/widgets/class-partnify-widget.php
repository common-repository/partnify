<?php
/**
 * Exit if accessed directly.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Aditem Search Widget.
 *
 * @category Widgets
 * @extends  WP_Widget
 */
class WP_Partnify_widget extends WP_Widget {
	/**
	 * Constructor.
	 */
	public $disconnected_message = 'Partnify ad Widget is disabled. Please connect Partnify first to use.';
	function __construct() {
		// Instantiate the parent object.
		parent::__construct( 'wp_partnify_widget', __( 'Partnify for WP', 'partnify' ), array( 'description' => __( 'Shows Partnify display ads.', 'partnify' ) ) );
	}


	/**
	 * Display widget.
	 *
	 * @param  Mixed $args     Arguments of widget.
	 * @param  Mixed $instance Instance value of widget.
	 */
	function widget( $args, $instance ) {

		extract( $args );
		// These are the widget options.
		$vendorId = isset( $instance['partnify_vendor_id'] ) ? $instance['partnify_vendor_id'] : '';
		$campaignId = isset( $instance['partnify_campaign_id'] ) ? $instance['partnify_campaign_id'] : '';
		$assetId = isset( $instance['partnify_campaign_asset_id'] ) ? $instance['partnify_campaign_asset_id'] : '';

		echo $before_widget;
		//echo ( $title ) ? $before_title . $title . $after_title : '';
		if ( pratnify_get_connection_status() ) :
			if ( $assetId ) :

				$partnify_add_id = 'partnify_vendor_banner_' . $vendorId . '_' . '_' . $campaignId . '_' . $assetId;
			?>

				<div id="<?php echo esc_attr( $partnify_add_id ) ?>"></div>
				
				
				<script>
				var $ = jQuery;
					jQuery(document).ready(function($){
						$('#<?php echo esc_attr( $partnify_add_id ) ?>')
						.load('https://app.partnify.com/Go/GetActiveVendorAsset?assetId=<?php echo $assetId ?>&partnerId=<?php echo $vendorId ?>');
					});
				</script>
			<?php
			endif;
		else : ?>
			<p><?php echo $this->disconnected_message ?></p>
			
		<?php endif;
		echo $after_widget;
	}
	/**
	 * Update widget.
	 *
	 * @param  Mixed $new_instance New instance of widget.
	 * @param  Mixed $old_instance Old instance of widget.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['partnify_vendor_id'] = sanitize_text_field( $new_instance['partnify_vendor_id'] );
		$instance['partnify_campaign_id'] = sanitize_text_field( $new_instance['partnify_campaign_id'] );
		$instance['partnify_campaign_asset_id'] = sanitize_text_field( $new_instance['partnify_campaign_asset_id'] );

		return $instance;
	}

	/**
	 * Search form of widget.
	 *
	 * @param  Mixed $instance Widget instance.
	 */
	function form( $instance ) {
		// Check values.
		$partnify_vendor_id = '';
		$partnify_campaign_id = '';
		if ( $instance ) {
			$partnify_vendor_id = isset( $instance['partnify_vendor_id'] ) ? $instance['partnify_vendor_id'] : '';
			$partnify_campaign_id = isset( $instance['partnify_campaign_id'] ) ? $instance['partnify_campaign_id'] : '';
			$partnify_campaign_asset_id = isset( $instance['partnify_campaign_asset_id'] ) ? $instance['partnify_campaign_asset_id'] : '';
			
		} ?>
		<div class="widget-partnify-wrapper">

		<?php 

		if ( pratnify_get_connection_status() ) :
			$settings = partnify_get_settings();
			$partnify_email = $settings['partnify_email'];
			$partnify_api_key = $settings['partnify_api_key'];

			$vendor_url = 'https://app.partnify.com/wp/GetPartnerVendors';			
			$vendor_url = add_query_arg( 'guid', $partnify_api_key, $vendor_url );

			$vendor_response = wp_remote_post( $vendor_url );
			$vendor_data = array();
			if ( isset( $vendor_response['response']['code'] ) && 200 === $vendor_response['response']['code'] ) {
				$vendor_data = json_decode( $vendor_response['body'] );
			} ?>
			<div class="input-wrapper vendor-wrapper">
				<label for="<?php echo esc_attr( $this->get_field_id( 'partnify_vendor_id' ) ); ?>"><?php esc_html_e( 'Vendor', 'partnify' ); ?>:</label>
				<select name="<?php echo esc_attr( $this->get_field_name( 'partnify_vendor_id' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'partnify_vendor_id' ) ); ?>" class="widefat input-partnify-vendor">
					<option value="">Select Vendor</option>
					<?php if ( is_array( $vendor_data ) && count( $vendor_data ) > 0 ) : ?>
						<?php foreach( $vendor_data as $vendor_detail ) : ?>
							<option value="<?php echo $vendor_detail->VendorId ?>" <?php selected( $vendor_detail->VendorId, $partnify_vendor_id ); ?>><?php echo $vendor_detail->CompanyName ?></option>
						<?php endforeach; ?>
					<?php endif; ?>				
				</select>
			</div>
			<?php
			$campaign_data = array();
			if ( $partnify_vendor_id ) {
				$campaign_url = 'https://app.partnify.com/wp/GetVendorCampaigns';
				$campaign_url = add_query_arg( 'VendorId', $partnify_vendor_id, $campaign_url );
				$campaign_url = add_query_arg( 'guid', $partnify_api_key, $campaign_url );

				$result =  wp_remote_post( $campaign_url );
				$status = '';
				if ( 200 === $result['response']['code'] ) {
					$campaign_data = json_decode( $result['body'] );
				}

			} ?>
			<div class="input-wrapper campaign-wrapper">
				<label for="<?php echo esc_attr( $this->get_field_id( 'partnify_campaign_id' ) ); ?>"><?php esc_html_e( 'Campaign', 'partnify' ); ?>:</label>
				<select name="<?php echo esc_attr( $this->get_field_name( 'partnify_campaign_id' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'partnify_campaign_id' ) ); ?>" class="widefat input-partnify-campaign">
					<option value="">Select Campaign</option>
					<?php if ( is_array( $campaign_data ) && count( $campaign_data ) > 0 ) : ?>
						<?php foreach( $campaign_data as $campaign_detail ) : ?>
							<option value="<?php echo $campaign_detail->CampaignId ?>" <?php selected( $campaign_detail->CampaignId, $partnify_campaign_id ); ?>><?php echo $campaign_detail->CampaignName ?></option>
						<?php endforeach; ?>
					<?php endif; ?>	
				</select>
			</div>

			<?php
			$campaign_assets_data = array();
			if ( $partnify_campaign_id ) {
				$campaign_assets_url = 'https://app.partnify.com/wp/GetCampaignAssets';
				$campaign_assets_url = add_query_arg( 'CampaignId', $partnify_campaign_id, $campaign_assets_url );
				$campaign_assets_url = add_query_arg( 'guid', $partnify_api_key, $campaign_assets_url );

				$result =  wp_remote_post( $campaign_assets_url );
				$status = '';
				if ( 200 === $result['response']['code'] ) {
					$campaign_assets_data = json_decode( $result['body'] );
				}

			} ?>
			<div class="input-wrapper campaign-assets-wrapper" data-name="<?php echo esc_attr( $this->get_field_name( 'partnify_campaign_asset_id' ) ); ?>">
				<label for="<?php echo esc_attr( $this->get_field_id( 'partnify_campaign_asset_id' ) ); ?>"><?php esc_html_e( 'Asset', 'partnify' ); ?>:</label>
				<div class="assets-radio" id="<?php echo esc_attr( $this->get_field_id( 'partnify_campaign_asset_id' ) ); ?>">
				<?php if ( is_array( $campaign_assets_data ) && count( $campaign_assets_data ) > 0 ) : ?>
					<?php foreach( $campaign_assets_data as $campaign_assets_detail ) : ?>
						<label> <input type="radio" name="<?php echo esc_attr( $this->get_field_name( 'partnify_campaign_asset_id' ) ); ?>" value="<?php echo $campaign_assets_detail->AssetId ?>"  class="widefat input-partnify-campaign-asset" <?php checked( $campaign_assets_detail->AssetId, $partnify_campaign_asset_id ); ?> /><img width="50" src="<?php echo $campaign_assets_detail->AssetUrl ?>" /></label>
					<?php endforeach; ?>
				<?php else : ?>
					<span class="assets-not-found">Assets not found.</span>
				<?php endif; ?>
				</div>
				
			</div>


		<?php else : ?>
			<p><?php echo $this->disconnected_message ?></p>
		<?php endif; ?>

		</div>
			
	<?php
	}
}

function partnify_register_widget() {
	register_widget( 'WP_Partnify_widget' );
}
add_action( 'widgets_init', 'partnify_register_widget' );

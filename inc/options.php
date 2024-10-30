<div class="wrap">
	<h2><?php _e('Settings: Google Tag Manager', 'lightag-lightweight-google-tag-manager'); ?></h2>
	
	<form method="post" action="options.php">
		<?php wp_nonce_field('update-options'); ?>
		<?php settings_fields(self::_NAMESPACE_PLUGIN); ?>
		
		<div id="poststuff">
			<div class="postbox">
				<div class="inside">
					<h3><?php _e('Settings', 'lightag-lightweight-google-tag-manager'); ?></h3>
					<table class="form-table">
						
						<tr valign="top">
							<th scope="row">
								<label for="lightag_gtm_container"><strong><?php _e('Google Tag Container', 'lightag-lightweight-google-tag-manager'); ?>:</strong></label><br/>
								<small><a href="https://tagmanager.google.com/" target="_blank"><?php _e('Visit Google Tag Manager', 'lightag-lightweight-google-tag-manager'); ?></a></small>
							</th>
							<td><input type="text" style="width:130px;" id="lightag_gtm_container" name="lightag_gtm_container" value="<?= esc_attr(get_option('lightag_gtm_container')); ?>" /> <small><?php _e('Example: GTM-AAAAAAA', 'lightag-lightweight-google-tag-manager'); ?></small></td>
						</tr>
										
					</table>
				
					<p>
						<?php submit_button(); ?>
					</p>
				</div>
			</div>
		</div>
	</form>
</div>
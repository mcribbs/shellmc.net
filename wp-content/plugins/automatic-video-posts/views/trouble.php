<div class="wrap ayvpp-wrap">
	
	<h2>Puglin Trouble Shooting</h2>

		<hr />
		<h3>Server Information</h3>
		
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><label>Your server's outbound IP address:</label></th>
				<td>
					<strong><?php if(isset($WP_ayvpp_ip) and $WP_ayvpp_ip) { echo $WP_ayvpp_ip; } else { ?>Your IP address could not be determined.<?php } ?></strong>
					<p class="description">For best pratcices, use this when setting up your Google API Key.</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label>PHP's "max_execution_time" setting:</label></th>
				<td>
					<strong><?php echo $WP_ayvpp_met; ?> seconds</strong>
					<p class="description">This setting is very important if you're importing many videos. A standard setting is 30 seconds. You may need much longer.</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label>Is plugin able change "max_execution_time" for longer imports itself?:</label></th>
				<td>
					<?php if($WP_ayvpp_met_hard) { ?>
						<strong>NO</strong>
						<p class="description">This is not ideal. You may want to change this setting in your server's php.ini file to something more like 300.</p>
					<?php } else { ?>
					<strong>YES</strong>
					<p class="description">This is great! The plugin should work for you for longer imports.</p>
					<?php } ?>
				</td>
			</tr>
		</table>
		
		<hr />
		
		<h3>Documentation</h3>
		<p>
			<a href="http://www.ternstyle.us/automatic-video-posts-plugin-for-wordpress/documentation" class="button-primary" target="_blank">Click here to read the documenation for this plugin</a>
			<a href="http://www.ternstyle.us/automatic-video-posts-plugin-for-wordpress/change-log" class="button-primary" target="_blank">Click here for the Change Log</a>
		</p>
		
		<hr />
		
		<h3>Keep up to date</h3>
		
		<a href="https://www.facebook.com/ternstyle" class="button-primary" target="_blank">Like us on Facebook</a>
		<a href="https://twitter.com/ternstyle" class="button-primary" target="_blank">Follow us on Twitter</a>
		<a href="https://confirmsubscription.com/h/t/EBF2E4E33FABB653" class="button-primary" target="_blank">Subscribe to our Newsletter</a>
</div>
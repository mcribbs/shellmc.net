<div class="wrap ayvpp-wrap">
	
	<h2>Automatic Video Posts Reset</h2>

	<form id="WP_ayvpp_reset_form" method="post">
		<hr />
		<h3>Refreshing your video lists</h3>
		<p>The following button will delete all videos imported and stored from the database and all WordPress posts associated with the videos.</p>
		<p><b>THIS MAY TAKE SOME TIME.</b></p>
		<input type="submit" value="Completely Refresh Videos" name="submit" class="button-primary action" />
		
		<hr />
		<h3>Plugin stopped importing?</h3>
		<p>When an import does not complete itself properly (usually by attempting to import too many videos) a value in the database needs to be reset.</p>
		<p><b>PLEASE NOTE: IF AN IMPORT IS ACTUALLY TAKING PLACE AND YOU CLICK THIS BUTTON THERE IS THE POSSIBILITY OF CREATING DUPLICATE POSTS.</b></p>
		<input type="submit" value="Reset Import Field in the Database" name="submit" class="button-primary action" />

		<hr />
		<h3>Completely reset this plugin</h3>
		<p>The following button will remove all the settings associated with this plugin as well as delete all videos imported and stored from the database and all WordPress posts associated with the videos.</p>
		<p><b>THIS MAY TAKE SOME TIME.</b></p>
		<input type="submit" value="Reset this Plugin" name="submit" class="button-primary action" />

		<hr />
		<h3>Keep video posts but refresh all plugin settings</h3>
		<p>The following button will remove all the settings associated with this plugin as well as delete all videos imported and stored from the database but will not delete all WordPress posts associated with the videos.</p>
		<input type="submit" value="Reset this Plugin but keep posts" name="submit" class="button-primary action" />
		
		<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo wp_create_nonce('WP_ayvpp_nonce');?>" />
		<input type="hidden" name="_wp_http_referer" value="<?php wp_get_referer(); ?>" />
	</form>
	
</div>
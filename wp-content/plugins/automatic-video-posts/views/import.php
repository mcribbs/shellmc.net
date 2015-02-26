<div class="wrap">
	
	<h2>Import Videos</h2>
	
	<h4>NOTES:</h4>
	<ol>
		<li>For channels: You can now import EVERY video available in a channel and playlist!</li>
	</ol>
	
	<h4>SERIOUS NOTES</h4>
	<ol>
		<li>If you're attempting to import from too many channels and/or playlists this import may require more memory than what your server allows.</li>
		<li>You  may need to increase the memory limits your server allocates to PHP.</li>
		<li>You can attempt to change the limit below but your server may not allow this change.</li>
		<li>Dependent on how much memory your server has in total, allocating too much to PHP could crash your server or cause it to function very slowly.</li>
	</ol>
	
	<h4>USE THIS AT YOUR OWN RISK. IT IS IMPORTANT TO KNOW THE LIMITATIONS OF YOUR SERVER.</h4>
	
	<table class="form-table">
		<tr valign="top">
			<th scope="row"><label for="memory">Maximum Memory Limit:</label></th>
			<td>
				<select id="memory" name="memory">
					<option value="">select</option>
					<option value="32">32M</option>
					<option value="37">37M</option>
					<option value="42">42M</option>
					<option value="47">47M</option>
					<option value="52">52M</option>
					<option value="57">57M</option>
					<option value="62">62M</option>
					<option value="67">67M</option>
				</select>
			</td>
		</tr>
	</table>
	
	<p class="submit">
		<input type="submit" id="ayvpp_import" name="submit" class="button-primary" <?php if(isset($_GET['channel']) and !empty($_GET['channel'])) { ?>value="Import Videos from Channel: <?php echo $ayvpp_options['channels'][$_GET['channel']]['name']; ?>" data-id="<?php echo $_GET['channel']; ?>" <?php } else { ?>value="Import All Videos"<?php } ?> />
		<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo wp_create_nonce('WP_ayvpp_nonce');?>" />
	</p>
	
	<div id="ayvpp_log"><div>
		<h1 id="ayvpp_total">Total Videos Imported: 0</h1>
		<div id="ayvpp_status"><div id="ayvpp_list"></div></div>
	</div></div>
	
</div>
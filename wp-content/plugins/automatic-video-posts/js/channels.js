/**************************************************************************************************/
/*
/*		File:
/*			channels.js
/*		Description:
/*			This file contains Javascript for ternstyle's Automatic Video Posts Plugin (Channels).
/*		Copyright:
/*			Copyright (c) 2014 Ternstyle LLC.
/*		License:
/*			This software is licensed under the terms of the End User License Agreement (EULA) 
/*			provided with this software. In the event the EULA is not present with this software
/*			or you have not read it, please visit:
/*			http://www.ternstyle.us/automatic-video-posts-plugin-for-wordpress/license.html
/*
/**************************************************************************************************/

/****************************************Commence Script*******************************************/

(function($) { $(document).ready(function () {
	
/*------------------------------------------------------------------------------------------------
	Validate Channel / URL
------------------------------------------------------------------------------------------------*/
	
	$('input[name=channel]').bind('keyup',function () {
		
		var v = $(this).val();
		
		//fix channels
		$(this).val(v.replace('/http(s)?://www.youtube.com\/user\//',''));
		
		//fix playlists
		if(/list=([a-zA-Z0-9_-]+)/.test(v)) {
			var m = /list=([a-zA-Z0-9_-]+)/.exec(v);
			v = m[1];
			$(this).val(v);
		}
		
		
	});

/*------------------------------------------------------------------------------------------------
	Edit Channel
------------------------------------------------------------------------------------------------*/
	
	$('.WP_ayvpp_edit').bind('click',function () {
		
		var p = $(this).parents('tr');
		
		//standard fields
		$('#WP_ayvpp_add_channel_form').find('[name=item]').val(p.find('input:first').val());
		$('#WP_ayvpp_add_channel_form').find('[name=name]').val(p.find('input[name=name]').val());
		$('#WP_ayvpp_add_channel_form').find('[name=channel]').val(p.find('input[name=channel]').val());
		$('#WP_ayvpp_add_channel_form').find('[name=type]').val(p.find('input[name=type]').val());
		$('#WP_ayvpp_add_channel_form').find('[name=author]').val(p.find('input[name=author]').val());

		//radio buttons
		setTimeout(function () {
			var a = ['publish','auto_play','related_show'];
			for(k in a) {
				var b = parseInt(p.find('input[name='+a[k]+']').val());
				$('#WP_ayvpp_add_channel_form').find('input[name='+a[k]+']').each(function () {
					if(b == $(this).val()) {
						$(this).prop('checked','checked');
						$(this).attr('checked',true);
					}
				});
			}
		},200);
		
		setTimeout(function () {
			//post type
			var c = p.find('input[name=publish_type]').val();
			$('#WP_ayvpp_add_channel_form .post_types input.chk').each(function() {
				console.log(c+":"+$(this).val()+":"+($(this).val() == c));
				if($(this).val() == c) {
					$(this).prop('checked','checked');
					$(this).attr('checked',true);
				}
				else {
					$(this).removeProp('checked');
					$(this).attr('checked',false);
				}
			});
		});
			
		//categories
		var c = p.find('input[name=cats]').val().split(',');
		$('#WP_ayvpp_add_channel_form input.chk').each(function() {
			if($.inArray($(this).val(),c) !== -1) {
				$(this).attr('checked',true);
			}
			else {
				$(this).removeProp('checked');
				$(this).attr('checked',false);
			}
		});
		
		
		
	});
	
/****************************************Terminate Script******************************************/
		
}); })(jQuery);
/**************************************************************************************************/
/*
/*		File:
/*			import.js
/*		Description:
/*			This file contains Javascript for ternstyle's Automatic Video Posts Plugin (Import).
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

(function($) {
		
/*------------------------------------------------------------------------------------------------
	Import
------------------------------------------------------------------------------------------------*/

	ayvpp_root = (location.href.substr(0,5) == 'https' && ayvpp_root.substr(0,5) !== 'https') ? ayvpp_root.replace('http','https') : ayvpp_root;
	
	var ayvpp = {
		
		timer : null,
		importing : false,
		status_text : '',
		nonce : '',
		memory : '',
		channel : false,
		page : 1,
		
		init : function (x) {
			
			this.nonce = $('#_wpnonce').val();
			this.memory = $('#memory').val() ? $('#memory').val() : '';
			this.channel = typeof(x) == 'undefined' ? '' : x;

			this.addToQueue(this.is_importing);
			this.addToQueue(this.import_start);
		},
		is_importing : function () {
			var self = this;
			$.ajax({
				async : true,
				type : 'GET',
				url : ayvpp_root+'/wp-admin/admin-ajax.php',
				dataType : 'json',
				data : 'page=ayvpp-import-videos&action=ayvpp_is_importing&_wpnonce='+$('#_wpnonce').val(),
				success : function (r) {
					if(r.code == 500) {
						self.importing = true;
						add_error('There is currently an import taking place. Please try again later or <a href="admin.php?page=ayvpp-reset">click here</a> to reset the import in the database.');
						return;
					}
					self.removeFromQueue();
				},
				error : function (m) {
					self.importing = true;
					add_error('There was an error while attemping to import. Please try again later.');
				}
			});
		},
		import_start : function () {
			
			var self = this;
			
			//console.log(ayvpp_root+'/wp-admin/admin-ajax.php?page=ayvpp-import-videos&action=ayvpp_import&memory='+$('#memory').val()+'&_wpnonce='+$('#_wpnonce').val()+'&channel='+this.channel);
			
			//this.timer = setInterval(function () {
			//	self.status();
			//},500);
			
			$.ajax({
				async : true,
				type : 'GET',
				url : ayvpp_root+'/wp-admin/admin-ajax.php',
				dataType : 'json',
				data : 'page=ayvpp-import-videos&action=ayvpp_import&memory='+$('#memory').val()+'&_wpnonce='+$('#_wpnonce').val()+'&channel='+this.channel+'&page='+self.page,
				success : function (r) {
					self.status_update(r.message);
					self.removeFromQueue();
					
					if(r.message.length < 1) {
						self.status_update('<h4 class="req">There seems to be an error. Aborting import.</h4>');
					}
					else if(r.channels_maxxed < r.channels_num/* && self.page < 3*/) {
						self.page++;
						self.addToQueue(self.import_start);
					}
					else {
						self.status_update('<h4 id="ayvpp_complete">Your import is complete!</h4>');
					}
					
				},
				error : function (m) {
					self.removeFromQueue();
				}
			});
		},
		status : function () {
			var self = this;
			$.ajax({
				async : false,
				type : 'GET',
				url : ayvpp_root+'/wp-admin/admin-ajax.php',
				dataType : 'text',
				data : 'page=ayvpp-import-videos&action=ayvpp_status&_wpnonce='+$('#_wpnonce').val(),
				success : function (m) {
					if(self.status_text != m) {
						$('#ayvpp_list').html(m);
						$('#ayvpp_total').html('Total Videos Imported: '+$('#ayvpp_list .imported').length);
						self.status_text = m;
						self.scroll();
					}
					if($('#ayvpp_complete').get(0)) {
						clearInterval(self.timer);
						add_alert('Your import is complete!');
					}
				},
				error : function () {
				}
			});
		},
		status_update : function (m) {
			$('#ayvpp_list').append(m);
			$('#ayvpp_total').html('Total Videos Imported: '+$('#ayvpp_list .imported').length);
			this.scroll();
		},
		scroll : function () {
			if($('#ayvpp_list').outerHeight() > $('#ayvpp_status').outerHeight() && $('#ayvpp_status').scrollTop()+$('#ayvpp_status').outerHeight()-20 < $('#ayvpp_list').outerHeight()) {
				
				$('#ayvpp_status').stop().animate({ scrollTop:$('#ayvpp_status').scrollTop()+$('#ayvpp_list').outerHeight() },{ duration : 2000,easing : 'easeInOutCirc' });
				
				//$('#ayvpp_status').scrollTop($('#ayvpp_status').scrollTop()+$('#ayvpp_list').outerHeight());
			}
			else if($('#ayvpp_complete').get(0)) {
				clearInterval(this.stimer);
			}
		},
		queue : [],
		addToQueue :
		function (f,a) {
			this.queue.push([f,a]);
			if(!this.iq) {
				this.startQueue();
			}
		},
		startQueue :
		function () {
			if(this.queue.length > 0) {
				this.iq = true;
				var a = this.queue[0][1] ? this.queue[0][1] : [];
				this.queue[0][0].apply(this,a);
				return;
			}
			this.iq = false;
		},
		removeFromQueue :
		function () {
			this.queue.splice(0,1);
			this.startQueue();
		}
	
	}

	$(document).ready(function() {
		$('#ayvpp_import').bind('click',function (e) {
			e.preventDefault();
			var self = this;
			$('#ayvpp_import').unbind('click').animate({ opacity:0 },{ duration:300 });

			$('#ayvpp_log').animate({ height:$('#ayvpp_log > div').outerHeight() },{ easing:'easeOutCirc',duration:1000,complete : function () {
				ayvpp.init($(self).attr('data-id'));
			}});
		});
		/*
		$('.import').bind('click',function (e) {
			e.preventDefault();
			var self = this;
			$('.import').animate({ opacity:0 },{ duration:300 });
			$('#ayvpp_log').animate({ height:$('#ayvpp_log > div').outerHeight() },{ easing:'easeOutCirc',duration:1000,complete : function () {
				ayvpp.init($(self).parents('tr').find('input:first').val());
			}});
		});
		*/
	});
	

/****************************************Terminate Script******************************************/

})(jQuery);
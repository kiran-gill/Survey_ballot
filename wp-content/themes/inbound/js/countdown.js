(function( $ ){
	var interval = 0;

	var methods = {
		init: function(options){
			var settings = {
				'times_array': Array(1, 60, 3600, 86400, 2629800, 31557600),
				'group_text': Array('seconds','minutes','hours','days','months','years'),
				'refresh_speed':	500,
				'animation_speed':	500
			};

			return this.each(function(){

				if(options)
					$.extend(settings, options);

				var group_text = settings['group_text'];

				var ar_date = [];
				var end_date = new Date(),
						number_of_groups = settings['times_array'].length,
						current_date = new Date();

				function dateDiff(date1, date2, groups_num){
					var sec = Math.floor((date2 - date1)/1000);
					var future = false;
					if(sec>0)
						future = true;
					else
						sec = Math.abs(sec);

					var groups_k = settings['times_array'],
							res = [],
							pre = 0;
					for(var i=groups_num-1; i>-1; i--){
						pre = 0;
						$.each(res, function(k){
							pre += this*groups_k[groups_num-1-k];
						});
						t_res = Math.floor((sec - pre)/groups_k[i]);
						res.push(t_res);
					}
					res.push(future);
					return res;
				}

				function SlideNumber($obj, new_text){
					$obj.parent().append('<span class="new_text">'+new_text+'</span>');
					var new_obj = $obj.parent().find('.new_text');
					new_obj.css('top','100%');
					$obj.animate({top:'-100%'},settings['animation_speed']);
					new_obj.animate({top:0},settings['animation_speed'], function(){
						$(this).removeClass('new_text');
						$obj.remove();
					});
				}

				function setCounter($obj){
					var obj_group = [];

					current_date = new Date();
					$obj.find('.counter__group').each(function(){
						obj_group.push($(this));
					});
					var res = dateDiff(current_date, end_date, number_of_groups),
							dom0 = 0,
							dom1 = 0;
					res.pop();
					$.each(res,function(i){
						var n = 0,
								str = String(this);
						if(str.length < 2)
							str = '0'+str;
						for(var x = 0; x < str.length; x++){
							var cur_div = obj_group[i].find('div:nth-child('+(x+1)+') span:last-child');
							if(!(str[x] === cur_div.text()))
								SlideNumber(cur_div, str[x]);
						}
					});
				}

				function initCounter($obj){
					$obj.html('');

					for(var i=number_of_groups-1; i > -1; i--){
						$obj.append('<div class="counter__group"><p>' + group_text[i] + '</p></div>');
					}
					ar_date = $obj.attr('data-end').split(',');
					while(ar_date.length < 6)
						ar_date.push('0');

					end_date = new Date(1*ar_date[0],1*ar_date[1]-1,1*ar_date[2],1*ar_date[3],1*ar_date[4],1*ar_date[5]);
					var dif = dateDiff(current_date, end_date, number_of_groups);
					dif.pop();

					// number of signs in each date group
					var si = [];
					$.each(dif, function(){
						if(String(this).length > 2)
							si.push(String(this).length);
						else
							si.push(2);
					});

					var i = 0;
					$obj.find('.counter__group').each(function(){
						var group = $(this);
						for(var k=0; k < si[i]; k++){
							group.prepend('<div><span></span></div>');
						}
						i++;
					});

					interval = setInterval(function(){
						if(!$obj.find('span:animated').length)
							setCounter($obj);
					},settings['refresh_speed']);

					setCounter($obj);
				}

				initCounter($(this));

			});
		},
		destroy: function(){
			return this.each(function(){
				if($(this).find('span').length)
					clearInterval(interval);
			});
		}

	};

	$.fn.oli_counter = function(method){
		if(methods[method]){
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		}
		else
		if(typeof method === 'object' || ! method){
			return methods.init.apply(this, arguments);
		}
		else{
			$.error('An error ocured in method ' +  method + '.');
		}
	};

})( jQuery );
jQuery(document).ready(function () {
    
    jQuery('.widget-images-list  .add-image-box').hover(function() {
        
		jQuery(this).find('.add-thumb-project').css('display','none');
		jQuery(this).find('.add-image-video').css('display','block');
	},function() {
		jQuery(this).find('.add-image-video').css('display','none');
		jQuery(this).find('.add-thumb-project').css('display','block');
	});
    
   
    popupsizes(jQuery('#jform_light_box_size_fix'));
	function popupsizes(checkbox){
			if(checkbox.is(':checked')){                           
				jQuery('.not-fixed-size').parents('.control-group').css({'display':'none'});
				jQuery('.fixed-size').parents('.control-group').css({'display':'block'});
			}else {
				jQuery('.fixed-size').parents('.control-group').css({'display':'none'});
				jQuery('.not-fixed-size').parents('.control-group').css({'display':'block'});
			}
		}
	jQuery('#jform_light_box_size_fix').change(function(){         
		popupsizes(jQuery(this));
	});  
        jQuery('input[data-slider="true"]').bind("slider:changed", function (event, data) {
		 jQuery(this).parent().find('span').html(parseInt(data.value)+"%");
		 jQuery(this).val(parseInt(data.value));
	});
});
	jQuery('#arrows-type input[name="params[slider_navigation_type]"]').change(function(){
		jQuery(this).parents('ul').find('li.active').removeClass('active');
		jQuery(this).parents('li').addClass('active');
	});
		
	jQuery('input[data-slider="true"]').bind("slider:changed", function (event, data) {
		 jQuery(this).parent().find('span').html(parseInt(data.value)+"%");
		 jQuery(this).val(parseInt(data.value));
	});	
	
	
	jQuery('#view-style-block ul li[data-id="'+jQuery('#light_box_style option[selected="selected"]').val()+'"]').addClass('active');
	
	jQuery('#light_box_style').change(function(){
		var strtr = jQuery(this).val();
		jQuery('#view-style-block ul li').removeClass('active');
		jQuery('#view-style-block ul li[data-id="'+strtr+'"]').addClass('active');
	});

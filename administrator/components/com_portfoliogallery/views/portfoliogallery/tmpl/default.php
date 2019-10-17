<?php
/**
 * @package Portfolio Gallery
 * @author Huge-IT
 * @copyright (C) 2014 Huge IT. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @website		http://www.huge-it.com/
 * */
defined('_JEXEC') or die;
JHtml::stylesheet(Juri::root() . 'media/com_portfoliogallery/style/admin.style.css');
JHtml::stylesheet(Juri::root() . 'media/com_portfoliogallery/style/style2-os.css');
$doc = JFactory::getDocument();
$editor = JFactory::getEditor('tinymce');
$doc->addScript(JURI::root(true) . "/media/com_portfoliogallery/js/admin/param_block.js");
JHTML::_('behavior.modal');
?>
<script src="<?php echo JURI::root(true) ?>/media/media/js/mediafield-mootools.min.js" type="text/javascript"></script>

<div class="">
    <?php $path_site2 = JUri::root()."media/com_portfoliogallery/images"; 
	?>
	<style>
        
		.free_version_banner {
			position:relative;
			display:block;
			background-image:url(<?php echo $path_site2; ?>/wp_banner_bg.jpg);
			background-position:top left;
			backround-repeat:repeat;
			overflow:hidden;
		}
		
		.free_version_banner .manual_icon {
			position:absolute;
			display:block;
			top:15px;
			left:15px;
		}
		
		.free_version_banner .usermanual_text {
                        font-weight: bold !important;
			display:block;
			float:left;
			width:270px;
			margin-left:75px;
			font-family:'Open Sans',sans-serif;
			font-size:12px;
			font-weight:300;
			font-style:italic;
			color:#ffffff;
			line-height:10px;
                        margin-top: 0;
                        padding-top: 15px;
		}
		
		.free_version_banner .usermanual_text a,
		.free_version_banner .usermanual_text a:link,
		.free_version_banner .usermanual_text a:visited {
			display:inline-block;
			font-family:'Open Sans',sans-serif;
			font-size:17px;
			font-weight:600;
			font-style:italic;
			color:#ffffff;
			line-height:30.5px;
			text-decoration:underline;
		}
		
		.free_version_banner .usermanual_text a:hover,
		.free_version_banner .usermanual_text a:focus,
		.free_version_banner .usermanual_text a:active {
			text-decoration:underline;
		}
		
		.free_version_banner .get_full_version,
		.free_version_banner .get_full_version:link,
		.free_version_banner .get_full_version:visited {
                        padding-left: 60px;
                        padding-right: 4px;
			display: inline-block;
                        position: absolute;
                        top: 15px;
                        right: calc(50% - 167px);
                        height: 38px;
                        width: 285px;
                        border: 1px solid rgba(255,255,255,.6);
                        font-family: 'Open Sans',sans-serif;
                        font-size: 23px;
                        color: #ffffff;
                        line-height: 43px;
                        text-decoration: none;
                        border-radius: 2px;
		}
		
		.free_version_banner .get_full_version:hover {
			background:#ffffff;
			color:#bf1e2e;
			text-decoration:none;
			outline:none;
		}
		
		.free_version_banner .get_full_version:focus,
		.free_version_banner .get_full_version:active {
			
		}
		
		.free_version_banner .get_full_version:before {
			content:'';
			display:block;
			position:absolute;
			width:33px;
			height:23px;
			left:25px;
			top:9px;
			background-image:url(<?php echo $path_site2; ?>/wp_shop.png);
			background-position:0px 0px;
			background-repeat:repeat;
		}
		
		.free_version_banner .get_full_version:hover:before {
			background-position:0px -27px;
		}
		
		.free_version_banner .huge_it_logo {
			float:right;
			margin:15px 15px;
		}
		
		.free_version_banner .description_text {
                        padding:0 0 13px 0;
			position:relative;
			display:block;
			width:100%;
			text-align:center;
			float:left;
			font-family:'Open Sans',sans-serif;
			color:#fffefe;
			line-height:inherit;
		}
                .free_version_banner .description_text p{
                        margin:0;
                        padding:0;
                        font-size: 14px;
                }
		</style>
	<div class="free_version_banner">
		<img class="manual_icon" src="<?php echo $path_site2; ?>/icon-user-manual.png" alt="user manual" />
		<p class="usermanual_text">If you have any difficulties in using the options, Follow the link to <a href="http://huge-it.com/joomla-portfolio-gallery-user-manual/" target="_blank">User Manual</a></p>
		<a class="get_full_version" href="http://huge-it.com/joomla-portfolio-gallery/" target="_blank">GET THE FULL VERSION</a>
                <a href="http://huge-it.com" target="_blank"><img class="huge_it_logo" src="<?php echo $path_site2; ?>/Huge-It-logo.png"/></a>
                <div style="clear: both;"></div>
		<div  class="description_text"><p>This is the free version of the plugin. Click "GET THE FULL VERSION" for more advanced options.   We appreciate every customer.</p></div>
	</div>
        <div style="clear:both;"></div>
	
		</div>


<script>
    window.onload = function() {
    setView();
  };
  
  function setView(){ 
    var portfolio_effects_list = document.getElementById('portfolio_effects_list');
    var postBox = document.getElementById('postbox');
    var selected = portfolio_effects_list.options[portfolio_effects_list.selectedIndex].value;
     if(selected == 5) {
       
            document.getElementById('postbox').style.display = 'none';
            
        } else 
           
            postBox.style.display ="block";       
      }
</script>
<script src="<?php echo JURI::root(true) ?>/media/com_portfoliogallery/js/admin/admin.js"></script>
<script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js" ></script>
<script type="text/javascript">
    var time = 0;
    par_images = [];
    Joomla.submitbutton = function(pressbutton)
    {
        if (document.adminForm.name.value == '' && pressbutton != 'cancel')
        {
            alert('Name is required.');
            document.adminForm.name.focus();
        }
        else
            submitform(pressbutton);
    }
                           //ok  
    jQuery(document).on('click', '#add_new_cat_buddon', function () {
       var newCatVal =  jQuery('.inside #add_cat_input input').val(); 
       if(newCatVal !== "") {
           var oldValue = jQuery('.inside input:hidden').val()
           var newValue = oldValue + newCatVal + ',';
           //console.log(newCatVal); console.log(newValue); console.log(oldValue);
           jQuery('.inside input:hidden').val(newValue.replace(/ /g,"_"));
           jQuery('.inside #add_cat_input input').val('');
           jQuery('.inside ul').find('#allCategories').before("\n\
                        <span style='display: block;'>\n\
                            <li class='hndle'>\n\
                                <input class='del_val' value='"+newCatVal+"' style=''>\n\
                                <span id='delete_cat' style='' value='a'>\n\
                                    <img src=<?php echo Juri::root()?>media/com_portfoliogallery/images/remove.jpg width='9' height='9' value='a'>\n\
                                </span>\n\
                                <span id='edit_cat' style=''>\n\
                                    <img src=<?php echo Juri::root()?>media/com_portfoliogallery/images/edit.png width='10' height='10'>\n\
                                </span>\n\
                            </li>\n\
                       </span>");
                                
          jQuery('.category-container #multipleSelect').each(function(){
              jQuery(this).append("<option attrForDelete='"+newCatVal+"'>"+newCatVal+"</option>");
          });
       }
       else { alert("Please fill the line"); }
});
        //  ok a 
        jQuery(document).on('click', '#delete_cat', function (){
            var del_val = jQuery(this).parent().find('.del_val').val().replace(/ /g, '_');
            del_val = del_val + ",";
            var old_val_for_delete = jQuery('.inside input:hidden').val();
            var newValue = old_val_for_delete.replace(del_val, "");
            jQuery('.inside input:hidden').val(newValue);
            jQuery(this).parent().parent().find('.hndle').remove();
            var valForDelete = del_val.replace(',', '').replace(/ /g, '_');
            jQuery('.category-container').each(function(){
                jQuery(this).find('option[value='+valForDelete+']').remove();
            });
             //console.log(del_val); console.log(old_val_for_delete); console.log(newValue); console.log(valForDelete);
        });
         //ok a
         
        jQuery(document).on('click', '#edit_cat', function (){
            jQuery(this).parent().find('.del_val').focus();
            var changing_val = jQuery(this).parent().find('.del_val').val().replace(/ /g, '_');
            jQuery('#changing_val').removeAttr('value').attr('value',changing_val);
            //console.log(changing_val);
        });
        //ok a
        
        jQuery(document).on('click', '#portfolios-list .active', function (){
            jQuery(this).find('input').focus();                                         
        });
                                        
        //getting category old name
        jQuery(document).on('focus', '.del_val', function (){ // Know which category we want to change 
                var changing_val = jQuery(this).val().replace(/ /g,"_");  //console.log(changing_val);
                jQuery('#changing_val').removeAttr('value').attr('value',changing_val);
        });
        
        
                                        
        jQuery(document).on('change', '.del_val', function (){
            //alert("ok")
                var no_edited_cats = jQuery("#allCategories").val().replace(/ /g,"_");
                var old_name = jQuery('#changing_val').val();
                var edited_cat = jQuery(this).val();
                edited_cat = edited_cat.replace(/ /g,"_");
                var new_cat = no_edited_cats.replace(old_name,edited_cat);
                jQuery('#allCategories').val(new_cat);  // console.log(no_edited_cats); console.log(old_name); console.log(edited_cat); console.log(new_cat);
        });


    jQuery(document).ready(function(){
        jQuery('.add-image-slide a.modal').on('click',function(){
            var id = jQuery(this).data('id');
            jQuery('.add-image-slide a input.add_new').on('change',function(){
                getImage('<?php echo $_SERVER['HTTP_HOST'] . JURI::root(true) ?>',id ,null,false);
            });

        })
    });


    jQuery(document).ready(function(){
        jQuery('ul.widget-images-list li a.modal').on('click',function(){
            var num = jQuery(this).data('number');
            var id = jQuery(this).data('id');
            jQuery('ul.widget-images-list li a.modal input.edit-image').on('change',function(){
                getImage('<?php echo $_SERVER['HTTP_HOST'] . JURI::root(true) ?>',id ,num ,false,true);
            });

        })
    });

</script>
<script type="text/javascript">
    var image_base_path = '<?php
		$params = JComponentHelper::getParams('com_media');
		echo $params->get('image_path', 'images');?>/';
    function submitbutton(pressbutton) {
        if (!document.getElementById('name').value) {
            alert("Name is required.");
            return;
        }

        document.getElementById("adminForm").action = document.getElementById("adminForm").action + "&task=" + pressbutton;
        document.getElementById("adminForm").submit();
    }
    function change_select() {
        submitbutton('apply');
    }
   jQuery(function() {
	jQuery( "#images-list" ).sortable({
          
	  stop: function() {
                        jQuery("#images-list > li").removeClass('has-background');
			count=jQuery("#images-list > li").length;
			for(var i=0;i<=count;i+=2){
                            jQuery("#images-list > li").eq(i).addClass("has-background");
			}
			jQuery("#images-list > li").each(function(){
				jQuery(this).find('.order_by').val(jQuery(this).index());
			});
	  },
	  revert: true
	});
   });
</script>

<?php  
function get_youtube_id_from_url($url){						
        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match)) {
                return $match[1];
        }
}
function get_image_from_video($image_url) {
    if(strpos($image_url,'youtube') !== false || strpos($image_url,'youtu') !== false) {
            $liclass="youtube";
            $video_thumb_url=get_youtube_id_from_url($image_url);
            $thumburl='http://img.youtube.com/vi/'.$video_thumb_url.'/mqdefault.jpg';
    } else 
    if (strpos($image_url,'vimeo') !== false) {	
            $liclass="vimeo";
            $vimeo = $image_url;
            $vimeo_explode = explode( "/", $vimeo );
            $imgid =  end($vimeo_explode);
            $hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/".$imgid.".php"));
            $imgsrc=$hash[0]['thumbnail_large'];
            $thumburl =$imgsrc;
    }
    return $thumburl;
}
function youtube_or_vimeo($url){
    if(strpos($url,'youtube') !== false || strpos($url,'youtu') !== false){	
            if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match)) {
                    return 'youtube';
            }
    }
    elseif(strpos($url,'vimeo') !== false) {
            $explode = explode("/",$url);
            $end = end($explode);
            if(strlen($end) == 8 || strlen($end) == 9 )
                    return 'vimeo';
    }
    return 'image';
}
$myrows = explode(",",$this->item->categories); ?>
<div class="wrap">
	 <form action="<?php echo JRoute::_('index.php?option=com_portfoliogallery&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm"  enctype="multipart/form-data">
    <div id="poststuff" >
	<div id="portfolio-header">
            <ul id="portfolios-list">
                <?php
                    foreach ($this->portfolioParams as $rowsldires) {
                        if ($rowsldires->id != $this->item->id) {
                ?>
                <li>
                    <a href="#" onclick="window.location.href = 'index.php?option=com_portfoliogallery&view=portfoliogallery&layout=edit&id=<?php echo $rowsldires->id; ?>'" ><?php echo $rowsldires->name; ?></a>
                </li>
                <?php
                } else {
                ?>
                <li class="active" style='background-image:url("<?= JURI::root().'media/com_portfoliogallery/images/edit.png' ?>")'>
                    <input class="text_area" onfocus="this.style.width = ((this.value.length + 1) * 8) + 'px'" type="text" name="name" id="name" maxlength="250" value="<?php echo stripslashes($this->item->name); ?>" />
                </li>
               <?php
                }
                }
                ?>
                <li class="add-new">
                    <a onclick="window.location.href = 'index.php?option=com_portfoliogallery&view=portfoliogallery&task=portfoliogalleries.add'">+</a>
                </li>
            </ul>
         </div>
	 <div id="post-body" class="metabox-holder columns-2">    
             <div id="post-body-content" class="btn-wrapper" style="width: 100%;float: left; margin: 0 0 10px 0;">

                 <a class="modal" title="Image" href="index.php?option=com_media&view=images&tmpl=component&e_name=tempimage&amp;fieldid=_unique_name_button"  rel="{handler: 'iframe', size: {x: 800, y: 500}}" style="float: right; ">
                     <div class="button2-left">
                         <div class="blank">
                             <input type="hidden"  onclick="jInsertFieldValue('', 'jform_params_image'); return false;" class= "btn btn-small btn-success" class="hugeitbutton" class="button wp-media-buttons-icon" name="_unique_name_button" id="_unique_name_button"  onchange="getImage('<?php echo $_SERVER['HTTP_HOST'] . JURI::root(true) ?>', <?php echo intval(JRequest::getVar('id')); ?>, null, true);" />
                             <input  type="button" class="btn btn-small btn-success"   value="Add Image" />
                         </div>
                     </div>
                 </a>

        
                <input type="hidden" name="imagess" id="_unique_name" />
                    <a class="modal" rel="{handler: 'iframe', size: {x: 800, y: 500}}" href="index.php?option=com_portfoliogallery&view=video&tmpl=component&pid=&projectId=<?php echo $this->item->id;?>" title="Image"   style="float: right;margin-right: 10px;">
                       <div class="button2-left">
                           <div class="blank">
                               <input type="button" class = "btn btn-small btn-success" class="hugeitbutton" class="button wp-media-buttons-icon" name="_unique_name_button" id="_unique_name_button" value="Add Video"  />
                           </div>
                       </div>
                   </a>
        
            </div>
            
            	 <div id="j-sidebar-container" class="j-sidebar-container j-sidebar-visible">
		<div id="j-toggle-sidebar-wrapper">
			<div id="sidebar" class="sidebar">
				<div class="sidebar-nav">
				<ul id="submenu" class="nav nav-list">
                        <li class="active">
                            <a href="index.php?option=com_portfoliogallery">Huge-IT Portfolio  Gallery</a>
                        </li>
                        <li>
                            <a href="index.php?option=com_portfoliogallery&amp;view=general">General Options</a>
                        </li>
                        <li>
                            <a href="index.php?option=com_portfoliogallery&amp;view=lightbox">Lightbox Options</a>
                    </li>
                        <li>
                            <a href="index.php?option=com_portfoliogallery&amp;view=featured">Featured Products</a>
                        </li>
                    </ul>
					<hr  class = "hr-sidebar">
				<div class="filter-select hidden-phone">
				
				 <div id="post-body-sidebar" class="meta-box-sortables ui-sortable">
                    <div id="portfolio-unique-options" class="postbox">
                        <h3 class="H3hndle"><span>Select The Portfolio/Gallery View</span></h3>
                        <ul id="portfolio-unique-options-list" style="margin: 0px">
                            <li style="display:none;">
                                <label for="sl_width">Width</label>
                                <input type="text" name="sl_width" id="sl_width" value="<?php echo $row->sl_width; ?>" class="text_area" />
                            </li>
                            <li style="display:none;">
                                <label for="sl_height">Height</label>
                                <input type="text" name="sl_height" id="sl_height" value="<?php echo $row->sl_height; ?>" class="text_area" />
                            </li>
                            <li style="display:none;">
                                <label for="pause_on_hover">Pause on hover</label>
                                <input type="hidden" value="off" name="pause_on_hover" />					
                                <input type="checkbox" name="pause_on_hover"  value="on" id="pause_on_hover"  <?php
                                if ($this->pause_on_hover == 'on') {
                                    echo 'checked="checked"';
                                }
                                ?> />
                            </li>
                            <li>
                                <select name="portfolio_effects_list" id="portfolio_effects_list" onchange="setView()">
                                    <option <?php
                                    if ($this->item->portfolio_list_effects_s == '0') {
                                        echo 'selected';
                                    }
                                    ?>  value="0">Blocks Toggle Up/Down</option>
                                    <option <?php
                                    if ($this->item->portfolio_list_effects_s == '1') {
                                        echo 'selected';
                                    }
                                    ?>  value="1">Full-Height Blocks</option>
                                    <option <?php
                                    if ($this->item->portfolio_list_effects_s == '2') {
                                        echo 'selected';
                                    }
                                    ?>  value="2">Gallery/Content-Popup</option>
                                    <option <?php
                                    if ($this->item->portfolio_list_effects_s == '3') {
                                        echo 'selected';
                                    }
                                    ?>  value="3">Full-Width Blocks</option>
                                    <option <?php
                                    if ($this->item->portfolio_list_effects_s == '4') {
                                        echo 'selected';
                                    }
                                    ?>  value="4">FAQ Toggle Up/Down</option>
                                    <option <?php
                                    if ($this->item->portfolio_list_effects_s == '5') {
                                        echo 'selected';
                                    }
                                    ?>  value="5">Content Slider</option>
                                    <option <?php
                                    if ($this->item->portfolio_list_effects_s == '6') {
                                        echo 'selected';
                                    }
                                    ?>  value="6">Lightbox-Gallery</option>
                                </select>
                            </li>

                            <li style="display:none;">
                                <label for="sl_pausetime">Pause time</label>
                                <input type="text" name="sl_pausetime" id="sl_pausetime" value="<?php echo $row->description; ?>" class="text_area" />
                            </li>
                            <li style="display:none;">
                                <label for="sl_changespeed">Change speed</label>
                                <input type="text" name="sl_changespeed" id="sl_changespeed" value="<?php echo $row->param; ?>" class="text_area" />
                            </li>
                            <li style="display:none;">
                                <label for="portfolio_position">portfolio Position</label>
                                <select name="sl_position" id="portfolio_position">
                                    <option <?php
                                    if ($this->item->sl_position == 'left') {
                                        echo 'selected';
                                    }
                                    ?>  value="left">Left</option>
                                    <option <?php
                                    if ($this->item->sl_position == 'right') {
                                        echo 'selected';
                                    }
                                    ?>   value="right">Right</option>
                                    <option <?php
                                    if ($this->item->sl_position == 'center') {
                                        echo 'selected';
                                    }
                                    ?>  value="center">Center</option>
                                </select>
                            </li>

                        </ul>
                        <script>
                            window.onload = galleryView; // 1,3
                            window.onload = setOptions;
                            function galleryView() {
                                var portfolio_effects_list = document.getElementById('portfolio_effects_list').value;
                                var view7 = document.getElementById('view7');
                                if(portfolio_effects_list == 'pagination' || portfolio_effects_list == 'load_more') {
                                    view7.style.display = "none";
                                } else {
                                    view7.style.display = "block";
                                }
                            }

                            function setOptions(){
                                var pagination_type = document.getElementById('pagination_type').value;
                                var content_per_page = document.getElementById('content_per_page');

                                if(pagination_type != 'show_all') {

                                    content_per_page.style.display = "block";
                                } else {
                                    content_per_page.style.display = "none";
                                }
                            }

                        </script>
                     <div class="">
                     <div id="view7">
						<ul style="margin: 0px;padding:0px">
							<li style="">
								<label for="pagination_type"><?php echo "Displaying Content";?></label>
								<select name="pagination_type" id="pagination_type" style="width: 50%;" onchange="setOptions()">
									<option <?php if($this->item->pagination_type == 'show_all'){ echo 'selected'; } ?>   value="show_all"><?php echo "Show All";?></option>
									<option <?php if($this->item->pagination_type == 'pagination'){ echo 'selected'; } ?> value="pagination"><?php echo "Pagination";?></option>
									<option <?php if($this->item->pagination_type == 'load_more'){ echo 'selected'; } ?>  value="load_more"><?php echo "Load More";?></option>
								</select>
							</li>
														
							<li style="">
                                <div id="content_per_page">
								    <label for="count_into_page"><?php echo "Content Per Page"?></label>
								    <input type="text" name="count_into_page" id="count_into_page" value="<?php echo $this->item->count_into_page; ?>" class="text_area" />
                                </div>
							</li>
						</ul>
                     </div>
                     </div>
                        <div id="postboxShowInCenter" > 
                            <h3 class="H3hndle" ><span>Show In Center</span></h3>
                                <select name="sl_position" id="slider_effect">
                                    <option <?php if($this->item->sl_position == 'off'){ echo 'selected'; }?> value="off">Off</option>
                                    <option <?php if($this->item->sl_position == 'on'){ echo 'selected'; }?> value="on">On</option>
                                </select>
                        </div>
                    </div>
                </div>
                <hr class="hr-sidebar">
                <div class="postbox" id="postbox">
                    <div class="inside2">
                        <ul>
                            <li class="allowIsotope">  Show Sorting Buttons :
                                <input type="hidden" value="off" name="ht_show_sorting" />
                                <input type="checkbox" id="ht_show_sorting"  <?php if($this->item->ht_show_sorting  == 'on'){ echo 'checked="checked"'; } ?>  name="ht_show_sorting" value="on" />
                            </li>
                            <li class="allowIsotope">
                                Show Category Buttons :
                                <input type="hidden" value="off" name="ht_show_filtering" />
                                <input type="checkbox" id="ht_show_filtering"  <?php if($this->item->ht_show_filtering  == 'on'){ echo 'checked="checked"'; } ?>  name="ht_show_filtering" value="on" />
                            </li>
                        </ul>
                    </div>
                      <hr class="hr-sidebar">
                </div>

               

                <div class="postbox">
                    <h3 class="H3hndle"><span>Categories</span></h3>
                        <div class="inside">
                            <ul>
                            <?php
                            $ifforempty= $this->item->categories;
                            $ifforempty= stripslashes($ifforempty);
                            $ifforempty= empty($ifforempty);
                            //echo   stripslashes($ifforempty);
                            // $ifforempty= esc_html($ifforempty);
                            if(!($ifforempty)) {
                                foreach ($myrows as $value) {
                                   if(!empty($value )) {?>
                                <span>
                                    <li class="hndle">
                                        <input class="del_val" value="<?php echo str_replace("_", " ", $value); ?>" <?php echo $value; ?>  style="padding:5px"> 
                                        <span id="edit_cat" style=""><img src="<?php echo Juri::root()?>media/com_portfoliogallery/images/edit.png" width="10" height="10"></span>
                                        <span id="delete_cat" style="" value="a"><img src="<?php echo Juri::root()?>media/com_portfoliogallery/images/remove.jpg" width="9" height="9" value="a"></span>
                                    </li>
                                </span>
                                    <?php
                                    }
                                }
                            }

                                ?>
                                <input type="hidden" value="<?php if (strpos($this->item->categories,',,') !== false)  { $this->item->categories = str_replace(",,",",",$this->item->categories); }echo $this->item->categories; ?>" id="allCategories" name="allCategories">
                                <li id="add_cat_input" style="">
                                    <input type="text" size="12" style="width: 90%">
                                    <a style="" id="add_new_cat_buddon">+ Add New Category  </a>
                                </li>
                            </ul>
                        <input type="hidden" value="" id="changing_val">
                        </div>
                </div>
					<hr class="hr-condensed">

					<div class="filter-select hidden-phone" style="padding: 0px">
                        <h4>Shortcodes:</h4>
                            <div class="inside" style="width: 100%;  margin: 0 52px 0 -22px;">
                                <ul>
                                    <li>
                                        <div class="shortcodeText"><p>Copy &amp; paste the shortcode directly into any Joomla article.</p></div>
                                        <textarea class="full" readonly="readonly">[huge_it_portfolio_id="<?php echo $this -> item ->id; ?>"]</textarea>
                                    </li>

                                </ul>
                            </div>
                    </div>
            	<hr class="hr-condensed">

					<div class="filter-select hidden-phone" style="padding: 0px">
                        <h4>Shortcodes:</h4>
                            <div class="inside" style="width: 100%;  margin: 0 52px 0 -22px;">
                                <ul>
                                    <li>
                                        <div class="shortcodeText"><p>Copy & paste this code into a template file to include the portfolio gallery within your theme.</p></div>
                                        <textarea class="full" readonly="readonly">&lt;?php echo huge_it_portfolio_id(<?php echo $this->item->id; ?>); ?&gt;</textarea>
                                    </li>

                                </ul>
                            </div>
                    </div>
				</div>
					</div>
	</div>
	<div id="j-toggle-sidebar"></div>
</div>
</div>
  


<?php 
    /*   Add  Video Inputs  */
?>
            <div id="j-main-container" class="span10 j-toggle-main">
    <div id = "post-body" class="metabox-holder columns-2">
			<ul id="images-list">
				<?php $j = 2; ?>
				<?php foreach ($this->prop as $key => $rowimages) : ?>
                                     <?php $imgurl = explode(";", $rowimages->image_url); unset($imgurl[count($imgurl) - 1]);  $i = 1; ?>
                                        <script type="text/javascript">
                                        url = '<?php echo $_SERVER['HTTP_HOST'] . JURI::root(true) ?>';
                                        par_images[<?php echo $rowimages->id; ?>] = new Array(<?php
                                    		for ($k = 0; $k < count($imgurl); $k++) {
		                                        if ($imgurl[$k] != "") {
		                                            echo "'" . addslashes(htmlspecialchars($imgurl[$k])) . ($k == count($imgurl) - 1 ? ("'") : "',");
		                                        }
                                   		 }
                                    ?>);
                                </script>
                                <?php 
                                           ?>
					<li <?php if ($j % 2 == 0) {   echo "class='has-background'"; }$j++; ?>>
                        <input class="order_by" type="hidden" name="order_by_<?php echo $rowimages->id; ?>" value="<?php echo $rowimages->ordering; ?>" />
                        <div class="image-container" deleteId = '1' id="sel_img<?php echo $rowimages->id; ?>">
                        	<ul  class="widget-images-list">
                                <?php $img = ""; ?>
                                <?php  $k = 0; foreach ($imgurl as $key1 => $img) {

                                    $cont = explode('/',$img);
                                    if($cont[0] == null  || $cont[0] == 'http:' || $cont[0] == 'https:'){
                                        $img = $img;
                                        
                                    }else {
                                        $img = JURI::root().$img;

                                    }

                                    ?>
                                    <?php if(youtube_or_vimeo($img) != 'image') {?>
                                            <li id = "editthisvideo_<?php echo $i; ?>_<?php echo $rowimages->id; ?>" class="editthisvideo editthisimage<?php echo $key; ?><?php if($i==0){echo 'first';} ?>" >
                                                <img class="editthisvideo" src="<?php echo get_image_from_video($img); ?>" data-video-src="<?php echo $img;?>"  alt = "<?php echo $img;?>" />
                                                <div class="play-icon <?php if (youtube_or_vimeo($img) == 'youtube') {?> youtube-icon<?php } else {?> vimeo-icon <?php }?>"></div>		
                                                <a onclick="IeCursorFix();return false;" rel="{handler: 'iframe', scope: this,size: {x: 800, y: 500}}" class="modal-button thickbox" href="index.php?option=com_portfoliogallery&view=video&tmpl=component&thumb_parent=&thumb=<?php echo $k;?>&edit=<?php echo $img;?>&pid=<?php echo $rowimages->id; ?>&projectId=<?php echo $this->item->id;?>" id="xxx">
                                                    <input type="button"   class="edit-video" id ="edit-video_<?php echo $rowimages->id; ?>_<?php echo $key; ?>" value="Edit" />
                                                </a>
                                                <a style="cursor: pointer;" onClick="RemoveVideo(<?php echo $i; ?>, <?php echo $rowimages->id; ?>);" title = "<?php echo $i;?>" class="remove-image">remove</a>	
                                            </li>
                                            <?php
                                    }
                                    else {?>

                                    <li id = "editthisimage_<?php echo $i; ?>_<?php echo $rowimages->id; ?>" class="editthisimage<?php echo $key; ?> <?php  if ($i == 1) {  echo 'first'; } ?>">
                                            <img id="sel_img_<?php echo $i; ?>"  value="<?php echo JURI::root() . $img; ?>" src="<?php echo $img; ?>" />
                                            <a class="modal" data-number="<?php echo $i;?>"  data-id="<?php echo $rowimages->id;?>" title="Image"  href="index.php?option=com_media&view=images&tmpl=component&e_name=tempimage&amp;fieldid=unique_name_button_<?php echo $i; ?>"  rel="{handler: 'iframe', size: {x: 800, y: 500}}" >
                                                <input type="button" class="edit-image" id="unique_name_button_<?php echo $i;?>" value="+" />
                                            </a>
                                            <a class ="remove-image" style="cursor: pointer;" onClick="Remove(<?php echo $i; ?>, <?php echo $rowimages->id; ?>);" />remove</a><br />
                                    </li>
                                <?php
                                    } $i++; 
                                    $k++;
                                    }  ?>
                                <li class="add-image-box">
                                    <?php $row_images_id = intval($rowimages->id); ?>
                                        <div class="add-thumb-project">
                                                <img src="<?php echo JUri::root()?>media/com_portfoliogallery/images/plus.png" class="plus" alt="" />
                                        </div>
                                     <div class="add-image-video">
                                        <input type="hidden" name="imagess<?php echo $rowimages->id; ?>" id="unique_name<?php echo $rowimages->id; ?>" class="all-urls" value="<?php echo $rowimages->image_url; ?>" />
                                        <a title="Add video" id="video<?php echo $rowimages->id;?>" onclick="IeCursorFix();return false;" rel="{handler: 'iframe', scope: this, size: {x: 800, y: 500}}"  class="modal  add-video-slide thickbox" href="index.php?option=com_portfoliogallery&view=video&thumb=&thumb_parent=<?php echo $rowimages->id;?>&tmpl=component&pid=<?php echo $rowimages->id; ?>&projectId=<?php echo $this->item->id;?>"><!--</add> thumb parent is project's image id-->
                                                <img src="<?php echo JUri::root()?>media/com_portfoliogallery/images/icon-video.png" title="Add video" alt="" class="plus" />
                                                <input type="button" class="button<?php echo $rowimages->id; ?> wp-media-buttons-icon add-video"  id="unique_name_button<?php echo $rowimages->id; ?>" value="+" />
                                        </a>
                                        <div class="add-image-slide" title="Add image">
                                            <a class="modal" data-number="<?php echo $i;?>" data-id="<?php echo $row_images_id; ?>"   title="Image" href="index.php?option=com_media&view=images&tmpl=component&e_name=tempimage&amp;fieldid=unique_name_button_<?php echo $row_images_id; ?>"  rel="{handler: 'iframe', size: {x: 800, y: 500}}">
                                                <img src="<?php echo JUri::root()?>media/com_portfoliogallery/images/icon-img.png" title="Add image" alt="" class="plus"/>
                                                <input type="button"  class="button<?php echo $row_images_id; ?> wp-media-buttons-icon add-image  add_new"  id="unique_name_button_<?php echo $row_images_id; ?>" value="+"  />
                                            </a>
                                        </div>
                                    </div>                             
                                </li>
                        	</ul>
                        </div>
                        
                         <div class="image-options">
                            <div class="options-container">
                            <div>
                                <label for="titleimage<?php echo $rowimages->id; ?>">Title:</label>
                                <input   style="width:81%" class="text_area" type="text" id="titleimage<?php echo $rowimages->id; ?>" name="titleimage<?php echo $rowimages->id; ?>" id="titleimage<?php echo $rowimages->id; ?>"  value="<?php echo $rowimages->name; ?>">
                            </div>
                            <div class="description-block">
                                <label for="im_description<?php echo $rowimages->id; ?>">Description:</label>
                                <textarea id="im_description<?php echo $rowimages->id; ?>" name="im_description<?php echo $rowimages->id; ?>" ><?php echo $rowimages->description; ?></textarea>
                            </div>
                            <div class="link-block">
                                <label for="sl_url<?php echo $rowimages->id; ?>">URL:</label>
                                <input class="text_area url-input" type="text" id="sl_url<?php echo $rowimages->id; ?>" name="sl_url<?php echo $rowimages->id; ?>"  value="<?php echo $rowimages->sl_url; ?>"  >
                                <label class="long" for="sl_link_target<?php echo $rowimages->id; ?>">
                                    <span>Open in new tab</span>
                                    <input type="hidden" name="sl_link_target<?php echo $rowimages->id; ?>" value="" />
                                    <input  <?php
                                    if ($rowimages->link_target == 'on') {
                                        echo 'checked="checked"';
                                    }
                                    ?>  class="link_target" type="checkbox" id="sl_link_target<?php echo $rowimages->id; ?>" name="sl_link_target<?php echo $rowimages->id; ?>" />
                                </label>
                            </div>
                        </div>
                               <?php /* Add  Category*/?>
  
                            <div class="category-container">
                                <strong>Select Categories</strong>
                                    <em>(Press Ctrl And Select multiply)</em>
                                        <?php 
                                    $huge_cat = explode(",",$rowimages->category);
                                                                        ?>
                                        <select id="multipleSelect" multiple="multiple">
                                            <?php           //    var_dump($huge_cat);
                                                $huge_cat = explode(",",$rowimages->category);
                                                    foreach ($myrows as $value) {
                                                    if(!empty($value))
                                                    { ?>
                                                        <option <?php if(in_array(str_replace(' ','_',$value),str_replace(' ','_',$huge_cat))) { echo "selected='selected' "; } ?> value="<?php echo str_replace(' ','_',$value); ?>" > <!-- attrForDelete="<?php// echo str_replace(" ","_",$value); ?>" -->
                                                            <?php echo str_replace('_',' ',$value); ?>
                                                        </option>
                                                    <?php
                                                    }
                                                }     ?>
                                        </select>
                                    <input type="hidden" id="category<?php echo $rowimages->id; ?>" name="category<?php echo $rowimages->id; ?>" value="<?php echo str_replace(' ','_',$rowimages->category); ?>"/>
<script type="text/javascript">
    jQuery('.category-container select').change(function(){
    var cat_new_val = jQuery(this).val();
    var new_cat_name = jQuery(this).parent().find('input').attr('name');
    jQuery('#'+new_cat_name).attr('value',cat_new_val+',');
    //console.log(cat_new_val);  console.log(new_cat_name);
    });
</script>
                                </div> 
                            <div class="remove-image-container">
                                 <a class="button remove-image" href="index.php?option=com_portfoliogallery&view=portfoliogallery&layout=edit&id=<?php echo $this->item->id ?>&task=portfoliogallery.deleteProject&removeslide=<?php echo $rowimages->id; ?>">Remove Project</a>
                            </div> 
                                <div class="clear"></div>                               
                        </div>
                        
                    </li>
                    
                    <?php 
 //   } 
    ?>
    
    <input hidden="" id= "image_url<?php echo $rowimages->id; ?>" name="image_url<?php echo $rowimages->id; ?>" value='<?php echo $rowimages->image_url; ?>'/>
        <?php	 ?>
        <?php endforeach; ?> 
    </ul>
	   
        <div style=" position:absolute; width:1px; height:1px; top:0px; overflow:hidden">
            <textarea id="tempimage" name="tempimage" class="mce_editable"></textarea><br />
    </div>
    <?php $editor->display('description', 'sss', '0', '0', '0', '0'); ?> </div>
            </div> </div>
        </div>
    </div>
       <div>
        <input type="hidden" name="task" value="" />
        <?php echo JHtml::_('form.token'); ?>
        </div>
        </form>
</div>
<?php 
?>
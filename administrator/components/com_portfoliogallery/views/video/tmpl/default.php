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
$doc = JFactory::getDocument();
$doc->addScript("http://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js");
$doc->addScript("http://code.jquery.com/ui/1.10.4/jquery-ui.js");
JHtml::script(Juri::root() . "/media/com_portfoliogallery/js/admin.js");
$doc->addScript(JURI::root(true) . "/media/com_portfoliogallery/js/admin/param_block.js");
JHtml::script(Juri::root() . "/media/com_portfoliogallery/js/simple-slider.js");

JHtml::stylesheet('media/com_portfoliogallery/style/simple-gallery.css');
$update = isset($_GET['update']) ? 1 : 0;
$thumb = isset($_GET['thumb']) ? $_GET['thumb'] : null;
?>

<style>
    
      #buttonPosition {
        float: right;
        display: inline;
        position: relative;
        top: -34px;
        right: 223px;
    }
    html.wp-toolbar {
        padding:0px !important;
    }
    #wpadminbar,#adminmenuback,#screen-meta, .update-nag,#dolly {
        display:none;
    }
    #wpbody-content {
        padding-bottom:30px;
    }
    #adminmenuwrap {display:none !important;}
    .auto-fold #wpcontent, .auto-fold #wpfooter {
        margin-left: 0px;
    }
    #wpfooter {display:none;}
    iframe {height:250px !important;}
    #TB_window {height:250px !important;}
</style>
<script type="text/javascript">
    jQuery(document).ready(function() {

        jQuery('.huge-it-insert-post-button').on('click', function() {
            var ID1 = jQuery('#huge_it_add_video_input').val();
            if (ID1 == "") {
                alert("Please copy and past url form Youtobe or Vimeo to insert into gallery.");
                return false;
            }

            window.parent.uploadID.val(ID1);

            tb_remove();
            $("#save-buttom").click();
        
        });

        jQuery('#huge_it_add_video_input').change(function() {

            if (jQuery(this).val().indexOf("youtube") >= 0) {
                jQuery('#add-video-popup-options > div').removeClass('active');
                jQuery('#add-video-popup-options  .youtube').addClass('active');
            } else if (jQuery(this).val().indexOf("vimeo") >= 0) {
                jQuery('#add-video-popup-options > div').removeClass('active');
                jQuery('#add-video-popup-options  .vimeo').addClass('active');
            } else {
                jQuery('#add-video-popup-options > div').removeClass('active');
                jQuery('#add-video-popup-options  .error-message').addClass('active');
            }
        })

        jQuery('.updated').css({"display": "none"});
<?php if (@$_GET["closepop"] == 1) { ?>
            $("#closepopup").click();
            self.parent.location.reload();
<?php } ?>

    });

</script>

<div class="slider-options-head">
<div style="float: left;">
    <div><a href="http://huge-it.com/joomla-portfolio-gallery-user-manual-2/" target="_blank">User Manual</a></div>
    <div>This feature is available in Pro version. To use it <a href="http://huge-it.com/joomla-portfolio-gallery" target="_blank">get Full version.</a></div>
</div>
</div>
<div style="clear: both;"></div>

<a id="closepopup"  onclick=" parent.eval('tb_remove()')" style="display:none;" > [X] </a>

<div id="huge_it_slider_add_videos">
    <div id="huge_it_slider_add_videos_wrap">
      <?php if (!isset($_GET['thumb_parent'])) {?>  <h2 style="color:#409740">Add Video URL From Youtobe or Vimeo</h2> <?php }?>
        <div class="control-panel">
             <?php 
             
                if (!isset($_GET['thumb_parent'])) {?>
                    <form action="<?php echo JRoute::_('index.php?option=com_portfoliogallery&view=video&task=video.save&thumb='.$thumb.'&id=' . $_GET['pid'].'&projectId='. $_GET['projectId'])?>" method="post" name="adminForm" id="adminForm"   enctype="multipart/form-data">
                        <input type="text" id="huge_it_add_video_input" name="huge_it_add_video_input" />
                        <div class="button2-left" style="margin: 0px 7px 0 0px; float: right;">
                            <div class="blank">
                              <button class='btn btn-large btn-success' id='huge-it-insert-video-button' onClick="alert('Sorry, Add Video feature is disabled in free version, please purchase the commercial version to use it.');self.parent.location.reload();">Insert Video </button>
                            </div>
                        </div>               
                            <div id="add-video-popup-options">
                                <div>
                                    <div>
                                        <label for="show_title">Title:</label>	
                                        <div>
                                            <input name="show_title" value="" type="text" />
                                        </div>
                                    </div>
                                    <div>
                                        <label for="show_description">Description:</label>
                                        <textarea id="show_description" name="show_description"></textarea>
                                    </div>
                                    <div>
                                        <label for="show_url">Url:</label>
                                        <input type="text" name="show_url" value="" />	
                                    </div>
                                </div>
                            </div>              
                    </form>
              <?php } else if (!isset($_GET['edit'])) {  $thumb_parent = $_GET["thumb_parent"];?>           
                    <form action="<?php echo JRoute::_('index.php?option=com_portfoliogallery&view=video&task=video.save&thumb='.$thumb.'&id=' . $_GET['pid'].'&projectId='. $_GET['projectId'] .'&thumb_parent='.$thumb_parent)?>" method="post" name="adminForm" id="adminForm"   enctype="multipart/form-data">
                        <h2 style="color:#409740">Add Video URL From Youtobe or Vimeo</h2>
                        <input type="text" id="huge_it_add_video_input" name="huge_it_add_video_input" value="" placeholder = "New video url"  style=""/><br />
                        <button class='btn btn-large btn-success' onClick="alert('Sorry, Add Video feature is disabled in free version, please purchase the commercial version to use it.');self.parent.location.reload();" id='huge-it-insert-video-button' style=" width:18%; position: relative; float: right; top: -51px;">Insert Video</button>
                    </form>
            <?php 
             } else {
                 
             ?>
            <h1 style="color: #378137">Update video</h1>
            <form method="post" action="<?php echo JRoute::_('index.php?option=com_portfoliogallery&view=video&task=video.save&thumb='.$thumb.'&id=' . $_GET['pid'].'&projectId='. $_GET['projectId'] .'&thumb_parent=')?>" >
                <div class="iframe-text-area">
                    <?php                     
                    $video = youtube_or_vimeo(get_image_from_video($_GET['edit']));                  
                    ?>
                    <?php $video_id = get_video_id_from_url_portfolio($_GET['edit']);          ?>
                        <iframe class="iframe-area" src="<?php if($video == 'youtube') { ?>//www.youtube.com/embed/<?php echo $video_id[0]; ?>?modestbranding=1&showinfo=0&controls=0
                    <?php }
                    else { ?>//player.vimeo.com/video/<?php echo $video_id[0]; ?>?title=0&amp;byline=0&amp;portrait=0 <?php } ?>" frameborder="0" allowfullscreen></iframe>
                  

                        <textarea rows="3" cols="50" class="text-area" disabled >
                    <?php echo $_GET['edit'];?>
                        </textarea>
                        <input type="text"  name="huge_it_add_video_input" value="" placeholder = "New video url" style="width: 50%;margin-left:8px;margin-top:10px"/><br />
                        <input type="hidden" id="huge_it_edit_video_input" name="huge_it_edit_video_input" value="" placeholder = "New video url" /><br />
                </div>
                <div id="buttonPosition">
                <a class='button-primary set-new-video btn btn-small btn-success'>See New Video</a>
                <button class='btn btn-small btn-success' onClick="alert('Sorry, Add Video feature is disabled in free version, please purchase the commercial version to use it.');self.parent.location.reload();" id='huge-it-insert-video-button'>Insert Video Slide</button>
                </div>
            </form>
<?php }?>
        </div>
    </div>	
</div>

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
function get_video_id_from_url_portfolio($url){
    if(strpos($url,'youtube') !== false || strpos($url,'youtu') !== false){	
        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match)) {
                return array ($match[1],'youtube');
        }
    }else {
        $vimeoid =  explode( "/", $url );
        $vimeoid =  end($vimeoid);
        return array($vimeoid,'vimeo');
    }
}
?>

<style>
html.wp-toolbar {
        padding:0px !important;
}
#wpadminbar,#adminmenuback,#screen-meta, .update-nag,#dolly {
        display:none;
}
#wpbody-content {
        padding-bottom:30px;
}
#adminmenuwrap {display:none !important;}
.auto-fold #wpcontent, .auto-fold #wpfooter {
        margin-left: 0px;
}
#wpfooter {display:none;}

#TB_window {height:250px !important;}
.html5-video-player:not(.ad-interrupting):not(.hide-info-bar) .html5-info-bar {
    display: none !important;
}
.iframe-text-area {
        float: left;
}
.iframe-area {
        float: left;
        height: 100%;
        width: 40%;
        margin: 5px;
}
iframe {height:150px !important;}
.text-area {
        float: left;
        width: 50%;
        margin: 5px;
}
textarea:disabled {
    background: rgba(255,255,255,.5);
    border-color: rgba(222,222,222,.75);
    -webkit-box-shadow: inset 0 1px 2px rgba(0,0,0,.04);
    box-shadow: inset 0 1px 2px rgba(0,0,0,.04);
    color: rgba(51,51,51,.5);
    cursor: default;
}
</style>

<script>
function youtube_parser(url){
    var regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?]*).*/;
    var match = url.match(regExp); 
    var match_vimeo = /vimeo.*\/(\d+)/i.exec( url );
    if (match&&match[7].length==11){
            return match[7];
    }else if(match_vimeo) {
                return match_vimeo[1];
    }
    else {
            return false;
    }
}
jQuery(function($) {		
        jQuery(".set-new-video").on('click',function() {
            var showcontrols,prefix;
            var new_video = jQuery("#huge_it_add_video_input").val();
          
            //alert(new_video);return;
            var new_video_id= youtube_parser(new_video);
            if(!new_video_id) 
                    return;
            if(new_video_id.length == 11) {
                     showcontrols = "?modestbranding=1&showinfo=0&controls=0";
                     prefix = "//www.youtube.com/embed/";
            }
            else {
             showcontrols = "?title=0&amp;byline=0&amp;portrait=0";
             prefix = "//player.vimeo.com/video/";

            }
            var old_url =jQuery(".text-area");



            jQuery(".iframe-area").fadeOut(300);
            old_url.html("");
            jQuery(".text-area").html(new_video);
            jQuery(".iframe-area").attr("src",prefix+new_video_id+showcontrols);
            jQuery("#huge_it_edit_video_input").val(prefix+new_video_id+showcontrols);
            jQuery(".iframe-area").fadeIn(1000);
        })
});
</script>
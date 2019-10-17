<?php
/**
 * @package Huge-IT Portfolio Gallery
 * @author Huge-IT
 * @copyright (C) 2014 Huge IT. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @website		http://www.huge-it.com/
 * */
defined('_JEXEC') or die('Restircted access');

class PortfoliogallerysHelper {

    private function add_scripts() {
        JHtml::stylesheet(Juri::root() .'media/com_portfoliogallery/style/css/font-awesome.min.css');
        JHtml::stylesheet(Juri::root() . 'media/com_portfoliogallery/style/gallery-all.css');
        JHtml::stylesheet(Juri::root() . 'media/com_portfoliogallery/style/style2-os.css');
        JHtml::stylesheet(Juri::root() . 'media/com_portfoliogallery/style/lightbox.css');
        JHtml::stylesheet(Juri::root() . 'media/com_portfoliogallery/style/css/font-awesome.css');
        $doc = JFactory::getDocument();

     
}

    private function get_data() {
        $db = JFactory::getDBO();
        $id = $this->portfolio_id;
        $query = $db->getQuery(true);
        $query->select('*,#__huge_itportfolio_images.name as imgname');
        $query->from('#__huge_itportfolio_portfolios,#__huge_itportfolio_images');
        $query->where('#__huge_itportfolio_portfolios.id =' . $id)->where('#__huge_itportfolio_portfolios.id = #__huge_itportfolio_images.portfolio_id');
		$query->order('#__huge_itportfolio_images.ordering asc ');
        $db->setQuery($query);
        $this->_data = $db->loadObjectList();
    }

    
    function huge_it_title_img_display($image_name,$title) {
        for($i = 0;$i < count($title);$i++) {
                $title_explode = explode("_-_-_",$title[$i]);
                if($title_explode[1] == $image_name) {
                        echo $title_explode[0];  
                }
                else { 
                        echo "" ;
                }
        }
}
    private function get_dataParams() {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__huge_itportfolio_params');
        $db->setQuery($query);
        $this->options_params = $db->loadObjectList();
        }

            function get_video_id_from_url_portfolio($url) {
                if (strpos($url, 'youtube') !== false || strpos($url, 'youtu') !== false) {
                    if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match)) {
                        return array($match[1], 'youtube');
                    }
                } else {
                    $vimeoid = explode("/", $url);
                    $vimeoid = end($vimeoid);
                    return array($vimeoid, 'vimeo');
                }
            }

            function youtube_or_vimeo_portfolio($url) {
                if (strpos($url, 'youtube') !== false || strpos($url, 'youtu') !== false) {
                    if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match)) {
                        return 'youtube';
                    }
                } elseif(strpos($url, 'vimeo') !== false) {
                $explode = explode("/", $url);
                $end = end($explode);
                if (strlen($end) == 8 || strlen($end) == 9)
                    return 'vimeo';
            }

            return 'image';
        }

    public function render_html() {
        ob_start();


        if ($this->type != 'plugin')
        $this->add_scripts();
        $this->get_data();
        $this->get_dataParams();
        $cis_options = array();
        $paramssld = array();
        foreach ($this->options_params as $rowpar) {
            $key = $rowpar->name;
            $value = $rowpar->value;
            $paramssld[$key] = $value;
        }

        for ($i = 0, $n = count($this->_data); $i < $n; $i++) {
            $cis_options[$this->_data[$i]->id][] = $this->_data[$i];
        }
        if (sizeof($cis_options) > 0) {

            reset($cis_options);
            $first_key = key($cis_options);
            $cis_options_value = $cis_options[$first_key][0];
            $images = $this->_data;
            $portfolioID = $cis_options_value->portfolio_id;
            $portfoliotitle = $cis_options_value->name;
            $portfolioheight = $cis_options_value->sl_height;
            $portfoliowidth = $cis_options_value->sl_width;
            $slidepausetime = ($cis_options_value->description + $cis_options_value->param);
            $portfolioeffect = $cis_options_value->portfolio_list_effects_s;
            $portfoliopauseonhover = $cis_options_value->pause_on_hover;
            $portfolioposition = $cis_options_value->sl_position;
            $slidechangespeed = $cis_options_value->param;
            $portfolioposition = $cis_options_value->sl_position;
            $portfolioCats = $cis_options_value->categories;
            $portfolioShowSorting = $cis_options_value->ht_show_sorting;
            $portfolioShowFiltering = $cis_options_value->ht_show_filtering;
            $menu = JFactory::getApplication()->getMenu();
            $level = $menu->getActive()->home;
            if($level == '1'){
                $path_menu = '';
            }
            else{
                $path_menu = '../';
            }

        }
        if ($paramssld['light_box_href'] == 'on')
            $paramssld['light_box_href'] = "true";
        else
            $paramssld['light_box_href'] = "false";
        if ($paramssld['light_box_title'] == 'on')
            $paramssld['light_box_title'] = "true";
        else
            $paramssld['light_box_title'] = "false";
        if ($paramssld['light_box_rel'] == 'on')
            $paramssld['light_box_rel'] = "true";
        else
            $paramssld['light_box_rel'] = "false";
        if ($paramssld['light_box_scrolling'] == 'on')
            $paramssld['light_box_scrolling'] = "true";
        else
            $paramssld['light_box_scrolling'] = "false";
        if ($paramssld['light_box_open'] == 'on')
            $paramssld['light_box_open'] = "true";
        else
            $paramssld['light_box_open'] = "false";
        if ($paramssld['light_box_esckey'] == 'on')
            $paramssld['light_box_esckey'] = "true";
        else
            $paramssld['light_box_esckey'] = "false";
        if ($paramssld['light_box_arrowkey'] == 'on')
            $paramssld['light_box_arrowkey'] = "true";
        else
            $paramssld['light_box_arrowkey'] = "false";
        if ($paramssld['light_box_data'] == 'on')
            $paramssld['light_box_data'] = "true";
        else
            $paramssld['light_box_data'] = "false";
        if ($paramssld['light_box_classname'] == 'on')
            $paramssld['light_box_classname'] = "true";
        else
            $paramssld['light_box_classname'] = "false";
        if ($paramssld['light_box_closebutton'] == 'on')
            $paramssld['light_box_closebutton'] = "true";
        else
            $paramssld['light_box_closebutton'] = "false";
        if ($paramssld['light_box_iframe'] == 'on')
            $paramssld['light_box_iframe'] = "true";
        else
            $paramssld['light_box_iframe'] = "false";
        if ($paramssld['light_box_inline'] == 'on')
            $paramssld['light_box_inline'] = "true";
        else
            $paramssld['light_box_inline'] = "false";
        if ($paramssld['light_box_html'] == 'on')
            $paramssld['light_box_html'] = "true";
        else
            $paramssld['light_box_html'] = "false";
        if ($paramssld['light_box_photo'] == 'on')
            $paramssld['light_box_photo'] = "true";
        else
            $paramssld['light_box_photo'] = "false";
        if ($paramssld['light_box_innerwidth'] == 'on')
            $paramssld['light_box_innerwidth'] = "true";
        else
            $paramssld['light_box_innerwidth'] = "false";
        if ($paramssld['light_box_innerheight'] == 'on')
            $paramssld['light_box_innerheight'] = "true";
        else
            $paramssld['light_box_innerheight'] = "false";
        if ($paramssld['light_box_slideshow'] == 'on')
            $paramssld['light_box_slideshow'] = "true";
        else
            $paramssld['light_box_slideshow'] = "false";
        if ($paramssld['light_box_top'] == 'on')
            $paramssld['light_box_top'] = "true";
        else
            $paramssld['light_box_top'] = "false";
        if ($paramssld['light_box_bottom'] == 'on')
            $paramssld['light_box_bottom'] = "true";
        else
            $paramssld['light_box_bottom'] = "false";
        if ($paramssld['light_box_left'] == 'on')
            $paramssld['light_box_left'] = "true";
        else
            $paramssld['light_box_left'] = "false";
        if ($paramssld['light_box_right'] == 'on')
            $paramssld['light_box_right'] = "true";
        else
            $paramssld['light_box_right'] = "false";
        if ($paramssld['light_box_reposition'] == 'on')
            $paramssld['light_box_reposition'] = "true";
        else
            $paramssld['light_box_reposition'] = "false";
        if ($paramssld['light_box_retinaurl'] == 'on')
            $paramssld['light_box_retinaurl'] = "true";
        else
            $paramssld['light_box_retinaurl'] = "false";
        if ($paramssld['light_box_size_fix'] == 'on')
            $paramssld['light_box_size_fix'] = "true";
        else
            $paramssld['light_box_size_fix'] = "false";
        if ($paramssld['light_box_scalephotos'] == 'on')
            $paramssld['light_box_scalephotos'] = "true";
        else
            $paramssld['light_box_scalephotos'] = "false";
        if ($paramssld['light_box_overlayclose'] == 'on')
            $paramssld['light_box_overlayclose'] = "true";
        else
            $paramssld['light_box_overlayclose'] = "true";
        if ($paramssld['light_box_loop'] == 'on')
            $paramssld['light_box_loop'] = "true";
        else
            $paramssld['light_box_loop'] = "false";
        if ($paramssld['light_box_slideshowauto'] == 'on')
            $paramssld['light_box_slideshowauto'] = "true";
        else
            $paramssld['light_box_slideshowauto'] = "false";
        if ($paramssld['light_box_fixed'] == 'on')
            $paramssld['light_box_fixed'] = "true";
        else
            $paramssld['light_box_fixed'] = "false";
        if ($paramssld['light_box_trapfocus'] == 'on')
            $paramssld['light_box_trapfocus'] = "true";
        else
            $paramssld['light_box_trapfocus'] = "false";
        if ($paramssld['light_box_fastiframe'] == 'on')
            $paramssld['light_box_fastiframe'] = "true";
        else
            $paramssld['light_box_fastiframe'] = "false";
        if ($paramssld['light_box_returnfocus'] == 'on')
            $paramssld['light_box_returnfocus'] = "true";
        else
            $paramssld['light_box_returnfocus'] = "false";
        if ($paramssld['light_box_preloading'] == 'on')
            $paramssld['light_box_preloading'] = "true";
        else
            $paramssld['light_box_preloading'] = "false";
        if ($paramssld['light_box_retinaimage'] == 'on')
            $paramssld['light_box_retinaimage'] = "true";
        else
            $paramssld['light_box_retinaimage'] = "false";
        ?>

<?php 

?>
        <script>
            var lightbox_transition = '<?php echo $paramssld['light_box_transition']; ?>';
            var lightbox_speed = <?php echo $paramssld['light_box_speed']; ?>;
            var lightbox_fadeOut = <?php echo $paramssld['light_box_fadeout']; ?>;
            var lightbox_title = <?php echo $paramssld['light_box_title']; ?>;
            var lightbox_scalePhotos = <?php echo $paramssld['light_box_scalephotos']; ?>;
            var lightbox_scrolling = <?php echo $paramssld['light_box_scrolling']; ?>;
            var lightbox_opacity = <?php echo ($paramssld['light_box_opacity'] / 100) + 0.001; ?>;
            var lightbox_open = <?php echo $paramssld['light_box_open']; ?>;
            var lightbox_returnFocus = <?php echo $paramssld['light_box_returnfocus']; ?>;
            var lightbox_trapFocus = <?php echo $paramssld['light_box_trapfocus']; ?>;
            var lightbox_fastIframe = <?php echo $paramssld['light_box_fastiframe']; ?>;
            var lightbox_preloading = <?php echo $paramssld['light_box_preloading']; ?>;
            var lightbox_overlayClose = <?php echo $paramssld['light_box_overlayclose']; ?>;
            var lightbox_escKey = <?php echo $paramssld['light_box_esckey']; ?>;
            var lightbox_arrowKey = <?php echo $paramssld['light_box_arrowkey']; ?>;
            var lightbox_loop = <?php echo $paramssld['light_box_loop']; ?>;
            var lightbox_closeButton = <?php echo $paramssld['light_box_closebutton']; ?>;
            var lightbox_previous = "<?php echo $paramssld['light_box_previous']; ?>";
            var lightbox_next = "<?php echo $paramssld['light_box_next']; ?>";
            var lightbox_close = "<?php echo $paramssld['light_box_close']; ?>";
            var lightbox_html = <?php echo $paramssld['light_box_html']; ?>;
            var lightbox_photo = <?php echo $paramssld['light_box_photo']; ?>;
            var lightbox_width = '<?php if ($paramssld['light_box_size_fix'] == 'false') {
            echo '';
        } else {
            echo $paramssld['light_box_width'];
        } ?>';
            var lightbox_height = '<?php if ($paramssld['light_box_size_fix'] == 'false') {
            echo '';
        } else {
            echo $paramssld['light_box_height'];
        } ?>';
            var lightbox_innerWidth = '<?php echo $paramssld['light_box_innerwidth']; ?>';
            var lightbox_innerHeight = '<?php echo $paramssld['light_box_innerheight']; ?>';
            var lightbox_initialWidth = '<?php echo $paramssld['light_box_initialwidth']; ?>';
            var lightbox_initialHeight = '<?php echo $paramssld['light_box_initialheight']; ?>';

            var maxwidth = jQuery(window).width();
            if (maxwidth ><?php echo $paramssld['light_box_maxwidth']; ?>) {
                maxwidth =<?php echo $paramssld['light_box_maxwidth']; ?>;
            }
            var lightbox_maxWidth = <?php if ($paramssld['light_box_size_fix'] == 'true') {
            echo '"100%"';
        } else {
            echo 'maxwidth';
        } ?>;
            var lightbox_maxHeight = <?php if ($paramssld['light_box_size_fix'] == 'true') {
            echo '"100%"';
        } else {
            echo $paramssld['light_box_maxheight'];
        } ?>;

            var lightbox_slideshow = <?php echo $paramssld['light_box_slideshow']; ?>;
            var lightbox_slideshowSpeed = <?php echo $paramssld['light_box_slideshowspeed']; ?>;
            var lightbox_slideshowAuto = <?php echo $paramssld['light_box_slideshowauto']; ?>;
            var lightbox_slideshowStart = "<?php echo $paramssld['light_box_slideshowstart']; ?>";
            var lightbox_slideshowStop = "<?php echo $paramssld['light_box_slideshowstop']; ?>";
            var lightbox_fixed = <?php echo $paramssld['light_box_fixed']; ?>;

        <?php
        $pos = $paramssld['slider_title_position'];
        switch ($pos) {
            case 1:
                ?>
                    var lightbox_top = '10%';
                    var lightbox_bottom = false;
                    var lightbox_left = '10%';
                    var lightbox_right = false;
                <?php
                break;
            case 1:
                ?>
                    var lightbox_top = '10%';
                    var lightbox_bottom = false;
                    var lightbox_left = '10%';
                    var lightbox_right = false;
                <?php
                break;
            case 2:
                ?>
                    var lightbox_top = '10%';
                    var lightbox_bottom = false;
                    var lightbox_left = false;
                    var lightbox_right = false;
                <?php
                break;
            case 3:
                ?>
                    var lightbox_top = '10%';
                    var lightbox_bottom = false;
                    var lightbox_left = false;
                    var lightbox_right = '10%';
                <?php
                break;
            case 4:
                ?>
                    var lightbox_top = false;
                    var lightbox_bottom = false;
                    var lightbox_left = '10%';
                    var lightbox_right = false;
                <?php
                break;
            case 5:
                ?>
                    var lightbox_top = false;
                    var lightbox_bottom = false;
                    var lightbox_left = false;
                    var lightbox_right = false;
                <?php
                break;
            case 6:
                ?>
                    var lightbox_top = false;
                    var lightbox_bottom = false;
                    var lightbox_left = false;
                    var lightbox_right = '10%';
                <?php
                break;
            case 7:
                ?>
                    var lightbox_top = false;
                    var lightbox_bottom = '10%';
                    var lightbox_left = '10%';
                    var lightbox_right = false;
                <?php
                break;
            case 8:
                ?>
                    var lightbox_top = false;
                    var lightbox_bottom = '10%';
                    var lightbox_left = false;
                    var lightbox_right = false;
                <?php
                break;
            case 9:
                ?>
                    var lightbox_top = false;
                    var lightbox_bottom = '10%';
                    var lightbox_left = false;
                    var lightbox_right = '10%';
                <?php
                break;
        }
        ?>

            var lightbox_reposition = <?php echo $paramssld['light_box_reposition']; ?>;
            var lightbox_retinaImage = <?php echo $paramssld['light_box_retinaimage']; ?>;
            var lightbox_retinaUrl = <?php echo $paramssld['light_box_retinaurl']; ?>;
            var lightbox_retinaSuffix = "<?php echo $paramssld['light_box_retinasuffix']; ?>";
            jQuery(document).ready(function () {
                jQuery("#huge_it_portfolio_content_<?php echo $portfolioID; ?> a[href$='.jpg'], #huge_it_portfolio_content_<?php echo $portfolioID; ?> a[href$='.png'], #huge_it_portfolio_content_<?php echo $portfolioID; ?> a[href$='.gif']").addClass('group1');
               
                var group_count = 0;
                var groups = <?php echo $portfolioID; ?>;
                jQuery(".element_<?php echo $portfolioID; ?>").each(function () {
                    group_count++;
                });
                for (var i = 1; i <= group_count; i++) {
                    jQuery(".portfolio-group" + i + "-" + groups).colorbox({rel: 'portfolio-group' + i + "-" + groups});
                }
                jQuery(".portfolio-lightbox-group").colorbox({rel: "portfolio-lightbox-group"});
                jQuery(".portfolio-lightbox a[href$='.png'],.portfolio-lightbox a[href$='.jpg'],.portfolio-lightbox a[href$='.gif'],.portfolio-lightbox a[href$='.jpeg']").addClass("portfolio-lightbox-group");
                var groups = <?php echo $portfolioID; ?>;
                var group_count_slider = 0;
                jQuery(".slider-content").each(function () {
                    group_count_slider++;
                });
                var group_count_slider_clone = 0;
                jQuery(".portfolio-group-slider" + i).colorbox({rel: 'portfolio-group-slider' + i});
                for (var i = 1; i <= group_count_slider; i++) {
                    jQuery(".portfolio-group-slider" + i).colorbox({rel: 'portfolio-group-slider' + i});
                    jQuery(".clone .slide_number" + i + " a").removeClass("portfolio-group-slider" + i + " cboxElement");

                }
                jQuery(".youtube").colorbox({iframe: true, innerWidth: 640, innerHeight: 390});
                jQuery(".vimeo").colorbox({iframe: true, innerWidth: 640, innerHeight: 390});
                jQuery(".callbacks").colorbox({
                    onOpen: function () {
                        alert('onOpen: colorbox is about to open');
                    },
                    onLoad: function () {
                        alert('onLoad: colorbox has started to load the targeted content');
                    },
                    onComplete: function () {
                        alert('onComplete: colorbox has displayed the loaded content');
                    },
                    onCleanup: function () {
                        alert('onCleanup: colorbox has begun the close process');
                    },
                    onClosed: function () {
                        alert('onClosed: colorbox has completely closed');
                    }
                });

                jQuery('.non-retina').colorbox({rel: 'group5', transition: 'none'})
                jQuery('.retina').colorbox({rel: 'group5', transition: 'none', retinaImage: true, retinaUrl: true});


                jQuery("#click").click(function () {
                    jQuery('#click').css({"background-color": "#f00", "color": "#fff", "cursor": "inherit"}).text("Open this window again and this message will still be here.");
                    return false;
                });
                jQuery("huge_it_portfolio_filters_<?php $portfolioID; ?>")
                jQuery("#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?> ul li a").click(function () {
                    jQuery("#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?> ul li").removeClass("active");
                    jQuery(this).parent().addClass("active");

                });
            });

        </script>
        <link href="<?php echo JURI::root() . 'media/com_portfoliogallery/style/colorbox-' . $paramssld['light_box_style'] . '.css' ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo JURI::root() . 'media/com_portfoliogallery/style/portfolio-all.css' ?>" rel="stylesheet" type="text/css" />
        <script src="<?php echo JURI::root() . 'media/com_portfoliogallery/js/jquery.colorbox.js' ?>"></script>
        <script src="<?php echo JURI::root() . 'media/com_portfoliogallery/js/portfolio-all.js' ?>"></script>
        <link  rel="stylesheet" href="<?php echo JURI::root() . 'media/com_portfoliogallery/style/style2-os.css' ?>" />
        <script src="<?php echo JURI::root() . 'media/com_portfoliogallery/js/jquery.hugeitmicro.min.js' ?>"></script>
        <link href="<?php echo JURI::root() . 'media/com_portfoliogallery/style/lightbox.css' ?>" rel="stylesheet" type="text/css" />



        <?php 
        $i = $portfolioeffect;
        $left_to_top = "";
        switch ($i) {
            case 0:  
                ?> 
                <?php
                if ($paramssld["ht_view0_sorting_float"] == "left" && $paramssld["ht_view0_filtering_float"] == "right" ||
                    $paramssld["ht_view0_sorting_float"] == "right" && $paramssld["ht_view0_filtering_float"] == "left" ||
                    $paramssld["ht_view0_sorting_float"] == $paramssld["ht_view0_filtering_float"]) {
                    $sorting_block_width = "20%";
                    $filtering_block_width = "20%";
                    $middle_with = "56%";
                } else if ($paramssld["ht_view0_sorting_float"] == "left" || $paramssld["ht_view0_sorting_float"] == "right" && $paramssld["ht_view0_filtering_float"] == "top") {
                    $sorting_block_width = "30%";
                    $filtering_block_width = "100%";
                    $paramssld["ht_view0_filtering_float"] = "none";
                    $width_middle = "65%";
                } else if ($paramssld["ht_view0_filtering_float"] == "left" || $paramssld["ht_view0_filtering_float"] == "right" && $paramssld["ht_view0_sorting_float"] == "top") {
                    $sorting_block_width = "100%";
                    $filtering_block_width = "30%";
                    $paramssld["ht_view0_sorting_float"] = "none";
                    $width_middle = "65%";
                }
                if ($paramssld["ht_view0_sorting_float"] == "top" && $paramssld["ht_view0_filtering_float"] == "top") {
                    $sorting_block_width = "100%";
                    $filtering_block_width = "100%";
                    $left_to_top = "ok";
                }
                ?>
               
         
        
        <style type="text/css">
/***<add>***/
.element_<?php echo $portfolioID; ?> .play-icon.youtube-icon  {
    background: url(<?php echo JUri::root().'media/com_portfoliogallery/images/play.youtube.png';?>) center center no-repeat;
	background-size: 30% 30%;
}
.element_<?php echo $portfolioID; ?> .play-icon.vimeo-icon  {
	background: url(<?php echo  JUri::root().'media/com_portfoliogallery/images/play.vimeo.png';?>) center center no-repeat;
	background-size: 30% 30%;
}
.element_<?php echo $portfolioID; ?> .play-icon {
    position: absolute;
    top: 0px;
    left: 0px;
    width: 100%;
    height: 100%;
}
.element_<?php echo $portfolioID; ?> .dropdownable .play-icon {
	display: none;
}
.element_<?php echo $portfolioID; ?>  .add-H-relative {
	position: relative;
}		
/***</add>***/
.element_<?php echo $portfolioID; ?> {
	background:#<?php echo $paramssld['ht_view0_element_background_color']?>;
	width:<?php echo $paramssld['ht_view0_block_width']; ?>px !important;
	margin: 5px;
	float: left;
	overflow: hidden;
	outline:none;
	border:<?php echo $paramssld['ht_view0_element_border_width']; ?>px solid #<?php echo $paramssld['ht_view0_element_border_color']; ?>;
}

.element_<?php echo $portfolioID; ?>.large,
.variable-sizes .element_<?php echo $portfolioID; ?>.large,
.variable-sizes .element_<?php echo $portfolioID; ?>.large.width2.height2 {
	width: <?php echo $paramssld['ht_view0_block_width']; ?>px;
	z-index: 10;
}

.default-block_<?php echo $portfolioID; ?> {
	position:relative;
	width:<?php echo $paramssld['ht_view0_block_width']; ?>px !important;
	height:<?php echo $paramssld['ht_view0_block_height']+45;?>px !important;
} 

.default-block_<?php echo $portfolioID; ?> .image-block_<?php echo $portfolioID; ?> {
	margin:0px;
	padding:0px;
	line-height:0px;
	border-bottom:1px solid #<?php echo $paramssld['ht_view0_element_border_color']; ?>;
}

.default-block_<?php echo $portfolioID; ?> img {
	margin:0px !important;
	padding:0px !important;
	max-width:none !important;
	width:<?php echo $paramssld['ht_view0_block_width']; ?>px !important;
	height:<?php echo $paramssld['ht_view0_block_height']; ?>px !important;
	border-radius:0px;
}

.default-block_<?php echo $portfolioID; ?> .title-block_<?php echo $portfolioID; ?> {
	position:relative;
	display:block;
	height:35px;
	padding:10px 0px 0px 0px;
	width:<?php echo $paramssld['ht_view0_block_width']; ?>px !important;
}

.default-block_<?php echo $portfolioID; ?> .title-block_<?php echo $portfolioID; ?> h3 {
	position:relative;
	margin:0px !important;
	padding:0px 0px 0px 5px !important;
	width:<?php echo $paramssld['ht_view0_block_width']-30; ?>px !important;
	text-overflow: ellipsis;
	overflow: hidden; 
	white-space:nowrap;
	font-weight:normal;
	color:#<?php echo $paramssld['ht_view0_title_font_color']; ?>;
	font-size:<?php echo $paramssld['ht_view0_title_font_size']; ?>px !important;
	line-height:<?php echo $paramssld['ht_view0_title_font_size']+4; ?>px !important;
}

.element_<?php echo $portfolioID; ?> .title-block_<?php echo $portfolioID; ?> .open-close-button {
	width:20px;
	height:20px;
	display:block;
	position:absolute;
	top:13px;
	right:2%;
        background:url('<?php echo JUri::root().'media/com_portfoliogallery/images/open-close.'.$paramssld['ht_view0_togglebutton_style'].'.png'; ?>') left top no-repeat;
	z-index:5;
	cursor:pointer;
	opacity:0.33;
}

 .element_<?php echo $portfolioID; ?>:hover .title-block_<?php echo $portfolioID; ?> .open-close-button {opacity:1;}

.element_<?php echo $portfolioID; ?>.large .open-close-button {
    background:url('<?php echo JUri::root().'media/com_portfoliogallery/images/open-close.'.$paramssld['ht_view0_togglebutton_style'].'.png'; ?>') left bottom no-repeat;
}

.wd-portfolio-panel_<?php echo $portfolioID; ?> {
	position: absolute;
	display:block;
	width:<?php echo $paramssld['ht_view0_block_width']-10; ?>px !important;
	margin:0px 5px 0px 5px;
	padding:0px;
	text-align:left;
	top:<?php echo $paramssld['ht_view0_block_height']+45; ?>px;  
	z-index:6; 
	height:200px;
}


.wd-portfolio-panel_<?php echo $portfolioID; ?> .description-block_<?php echo $portfolioID; ?>, .element_<?php echo $portfolioID; ?> div.right-block .description-block_<?php echo $portfolioID; ?> * {
	position:relative;
	clear:both;
}

.wd-portfolio-panel_<?php echo $portfolioID; ?> .description-block_<?php echo $portfolioID; ?> p,.wd-portfolio-panel_<?php echo $portfolioID; ?> .description-block_<?php echo $portfolioID; ?> * {	
	text-align:justify;
	font-weight:normal;
	font-size:<?php echo $paramssld['ht_view0_description_font_size']; ?>px;
	color:#<?php echo $paramssld['ht_view0_description_color']; ?>;
	margin:0px;
	padding:0px;
}



.wd-portfolio-panel_<?php echo $portfolioID; ?> .description-block_<?php echo $portfolioID; ?> h1,
.wd-portfolio-panel_<?php echo $portfolioID; ?> .description-block_<?php echo $portfolioID; ?> h2,
.wd-portfolio-panel_<?php echo $portfolioID; ?> .description-block_<?php echo $portfolioID; ?> h3,
.wd-portfolio-panel_<?php echo $portfolioID; ?> .description-block_<?php echo $portfolioID; ?> h4,
.wd-portfolio-panel_<?php echo $portfolioID; ?> .description-block_<?php echo $portfolioID; ?> h5,
.wd-portfolio-panel_<?php echo $portfolioID; ?> .description-block_<?php echo $portfolioID; ?> h6,
.wd-portfolio-panel_<?php echo $portfolioID; ?> .description-block_<?php echo $portfolioID; ?> p, 
.wd-portfolio-panel_<?php echo $portfolioID; ?> .description-block_<?php echo $portfolioID; ?> strong,
.wd-portfolio-panel_<?php echo $portfolioID; ?> .description-block_<?php echo $portfolioID; ?> span {
	padding:2px !important;
	margin:0px !important;
}

.wd-portfolio-panel_<?php echo $portfolioID; ?> .description-block_<?php echo $portfolioID; ?> ul,
.wd-portfolio-panel_<?php echo $portfolioID; ?> .description-block_<?php echo $portfolioID; ?> li {
	padding:2px 0px 2px 5px;
	margin:0px 0px 0px 8px;
}


.wd-portfolio-panel_<?php echo $portfolioID; ?> .thumbs-list_<?php echo $portfolioID; ?> {
	position:relative;
	clear:both;
	list-style:none;
	display:table;
	width:100%;
	padding:0px;
	margin:3px 0px 0px 0px;
	text-align:center;
}

.wd-portfolio-panel_<?php echo $portfolioID; ?> .thumbs-list_<?php echo $portfolioID; ?> li {
	display:inline-block;
	margin:0px 3px 0px 2px;
}

.wd-portfolio-panel_<?php echo $portfolioID; ?> .thumbs-list_<?php echo $portfolioID; ?> li a {
	display:block;
	width:<?php echo $paramssld['ht_view0_thumbs_width']; ?>px;
	height:<?php echo $paramssld['ht_view0_thumbs_width']; ?>px;
	opacity:0.7;
	display:table;
}

.wd-portfolio-panel_<?php echo $portfolioID; ?> .thumbs-list_<?php echo $portfolioID; ?> li a:hover {
	opacity:1;
}

.wd-portfolio-panel_<?php echo $portfolioID; ?> img {
	margin:0px !important;
	padding:0px !important;
	display:table-cell;
	vertical-align:middle;
	width:<?php echo $paramssld['ht_view0_thumbs_width']; ?>px !important;
	max-height:<?php echo $paramssld['ht_view0_thumbs_width']; ?>px !important;
	width:100%;
	height:100%;
}

.wd-portfolio-panel_<?php echo $portfolioID; ?> > div {
	position:relative;
	clear:both;
	padding-top:10px;
	margin-bottom:10px;
	<?php if($paramssld['ht_view0_show_separator_lines']=="on") {?>
        background:url('<?php echo JUri::root(). 'media/com_portfoliogallery/images/divider.line.png'; ?>') center top repeat-x;
	<?php } ?>
}

.wd-portfolio-panel_<?php echo $portfolioID; ?> .button-block {
	padding-top:10px;
	margin-bottom:10px;
	
}

.wd-portfolio-panel_<?php echo $portfolioID; ?> .button-block a, .wd-portfolio-panel_<?php echo $portfolioID; ?> .button-block a:link, .wd-portfolio-panel_<?php echo $portfolioID; ?> .button-block a:visited {
	padding:6px 12px;
	text-decoration:none;
	display:inline-block;
	font-size:<?php echo $paramssld['ht_view0_linkbutton_font_size']; ?>px;
	background:#<?php echo $paramssld['ht_view0_linkbutton_background_color']; ?>;
	color:#<?php echo $paramssld['ht_view0_linkbutton_color']; ?>;
}

.wd-portfolio-panel_<?php echo $portfolioID; ?> .button-block a:hover, .wd-portfolio-panel_<?php echo $portfolioID; ?> .button-block a:focus, .wd-portfolio-panel_<?php echo $portfolioID; ?> .button-block a:active {
	background:#<?php echo $paramssld['ht_view0_linkbutton_background_hover_color']; ?>;
	color:#<?php echo $paramssld['ht_view0_linkbutton_font_hover_color']; ?>;
	text-decoration:none;
}

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_options_<?php echo $portfolioID; ?> {
    display:block;
    <?php
    if($paramssld["ht_view0_filtering_float"] == 'left' && $paramssld["ht_view0_sorting_float"] == 'none') {  if($portfolioShowFiltering == "on") { echo "margin-left: 31%;"; } else { echo "margin-left: 1%;"; }   }
    else if($paramssld["ht_view0_filtering_float"] == 'right' && $paramssld["ht_view0_sorting_float"] == 'none' || ($sorting_block_width == '100%' && $filtering_block_width == "100%")) { echo "margin-left: 1%;"; } ?>
    overflow: hidden;
    margin-top: 5px;
    float: <?php echo $paramssld["ht_view0_sorting_float"]; ?>;
    width: <?php echo $sorting_block_width; ?>;
}

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_options_<?php echo $portfolioID; ?> ul {
  margin: 0px !important;
  padding: 0px !important;
  list-style: none;
<?php if($paramssld["ht_view0_sorting_float"] == 'top') {
      echo "float:left;margin-left:1%;";
      } ?>
}

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?> ul {
  margin: 0px !important;
  padding: 0px !important;
  overflow: hidden;
  <?php if($paramssld["ht_view0_filtering_float"] == 'top') {
      echo "float:left;margin-left:1%;";
      } ?>
}

<?php if($paramssld["ht_view0_sorting_float"] == 'none') { ?>
            #huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_options_<?php echo $portfolioID; ?> ul {
                float: left;
            }
    <?php } ?>
            
#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_options_<?php echo $portfolioID; ?> ul li {
    border-radius: <?php echo $paramssld["ht_view0_sortbutton_border_radius"];?>px;
    list-style-type: none;
    margin: 0px !important;
	padding: 0;
    <?php
        if($sorting_block_width == "100%" ) {
            echo "float:left !important;margin: 4px 8px 4px 0px !important;";
        }
        if($left_to_top == "ok")
        { echo "float:left !important;"; }
        if($paramssld["ht_view0_sorting_float"] == "left" || $paramssld["ht_view0_sorting_float"] == "right")
        { echo 'border-bottom: 1px solid #ccc;'; }
        else
        { echo 'border: 1px solid #ccc;'; }
    ?>
}

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_options_<?php echo $portfolioID; ?> ul li a {
    background-color: #<?php echo $paramssld["ht_view0_sortbutton_background_color"];?> !important;
    font-size:<?php echo $paramssld["ht_view0_sortbutton_font_size"];?>px !important;
    color:#<?php echo $paramssld["ht_view0_sortbutton_font_color"];?> !important;
    text-decoration: none;
    cursor: pointer;
    margin: 0px !important;
    display: block;
    padding:3px;
}

/*#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_options_<?php echo $portfolioID; ?> ul li:hover {
    
}*/

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_options_<?php echo $portfolioID; ?> ul li a:hover {
    background-color: #<?php echo $paramssld["ht_view0_sortbutton_hover_background_color"];?> !important;
    color:#<?php echo $paramssld["ht_view0_sortbutton_hover_font_color"];?> !important;
    cursor: pointer;
}

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?> {
    margin-top: 5px;
    float: <?php echo $paramssld["ht_view0_filtering_float"]; ?>;
    width: <?php echo $filtering_block_width; ?>;
    display:block;
    <?php
        if($paramssld["ht_view0_filtering_float"] == 'none' && ($paramssld["ht_view0_sorting_float"] == 'left') ) {  if($portfolioShowSorting == 'on') { echo "margin-left: 31%;"; } else echo "margin-left: 1%"; } 
        if(($paramssld["ht_view0_filtering_float"] == 'none' && ($paramssld["ht_view0_sorting_float"] == 'right')) || ($sorting_block_width == '100%' && $filtering_block_width == "100%")) { echo "margin-left: 1%";}
    ?>
}

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?> ul li {
    list-style-type: none;
    <?php
        if($filtering_block_width == "100%") { echo "float:left !important;margin: 4px 8px 4px 0px !important;"; }
        if($left_to_top == "ok") { echo "float:left !important;"; }
        if($paramssld["ht_view0_filtering_float"] == "left" || $paramssld["ht_view0_filtering_float"] == "right")
        { echo 'border-bottom: 1px solid #ccc;'; }
        else echo "border: 1px solid #ccc;";
    ?>
}

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?> ul li a,
#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?> ul li a:link,
#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?> ul li a:visited {
    font-size:<?php echo $paramssld["ht_view0_filterbutton_font_size"];?>px !important;
    color:#<?php echo $paramssld["ht_view0_filterbutton_font_color"];?> !important;
    background-color: #<?php echo $paramssld["ht_view0_filterbutton_background_color"];?> !important;
    border-radius: <?php echo $paramssld["ht_view0_filterbutton_border_radius"];?>px;
    padding: 3px;
    display: block;
    text-decoration: none;
}

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?>  ul li a:hover,
#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?>  ul li a:focus,
#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?>  ul li a:active {
    color:#<?php echo $paramssld["ht_view0_filterbutton_hover_font_color"];?> !important;
    background-color: #<?php echo $paramssld["ht_view0_filterbutton_hover_background_color"];?> !important;
    cursor: pointer;
}

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?> ul li.active a,
#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?> ul li.active a:link,
#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?> ul li.active a:visited,
#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?>  ul li.active a:hover,
#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?>  ul li.active a:focus,
#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?>  ul li.active a:active {
    color:#<?php echo $paramssld["ht_view0_filterbutton_hover_font_color"];?> !important;
    background-color: #<?php echo $paramssld["ht_view0_filterbutton_hover_background_color"];?> !important;
    cursor: pointer;
}

#huge_it_portfolio_content_<?php echo $portfolioID; ?> section {
    position:relative;
    display:block;
}

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_container_<?php echo $portfolioID; ?> {
<?php if($paramssld["ht_view0_sorting_float"] == "left" && $paramssld["ht_view0_filtering_float"] == "right" ||
         $paramssld["ht_view0_sorting_float"] == "right" && $paramssld["ht_view0_filtering_float"] == "left")
       { echo "margin: 0px auto;"; }
       if((($paramssld["ht_view0_filtering_float"] == "left" || $paramssld["ht_view0_filtering_float"] == "right" && $paramssld["ht_view0_sorting_float"] == "top") || ($paramssld["ht_view0_sorting_float"] == "left" || $paramssld["ht_view0_sorting_float"] == "right" && $paramssld["ht_view0_filting_float"] == "top")) && $portfolioShowFiltering == "on" && $portfolioShowSorting == "on")
       {
?>
        width: <?php echo $width_middle; ?> !important;
 <?php } ?>
}

.load_more4 {
    margin: 10px 0;
    position:relative;
    text-align:<?php if($paramssld['ht_view0_loadmore_position'] == 'left') {echo 'left';}
			elseif ($paramssld['ht_view0_loadmore_position'] == 'center') { echo 'center'; }
			elseif($paramssld['ht_view0_loadmore_position'] == 'right') { echo 'right'; }?>;

    width:100%;


}

.load_more_button4 {
    border-radius: 10px;
    display:inline-block;
    padding:5px 15px;
    font-size:<?php echo $paramssld['ht_view0_loadmore_fontsize']; ?>px !important;;
    color:<?php echo '#'.$paramssld['ht_view0_loadmore_font_color']; ?> !important;;
    background:<?php echo '#'.$paramssld['ht_view0_button_color']; ?> !important;
    cursor:pointer;

}
.load_more_button4:hover{
    color:<?php echo '#'.$paramssld['ht_view0_loadmore_font_color_hover']; ?> !important;
    background:<?php echo '#'.$paramssld['ht_view0_button_color_hover']; ?> !important;
}

.loading4 {
    display:none;
}
.paginate4{
    font-size:<?php echo $paramssld['ht_view0_paginator_fontsize']; ?>px !important;
    color:<?php echo '#'.$paramssld['ht_view0_paginator_color']; ?> !important;
    text-align: <?php echo $paramssld['ht_view0_paginator_position']; ?>;
    margin-top: 25px;
}
.paginate4 a{
    border-bottom: none !important;
    color: #<?php echo $paramssld['ht_view0_paginator_icon_color']; ?> !important;
    font-size:<?php echo $paramssld['ht_view0_paginator_icon_size']; ?>px !important;
}
.icon-style4{
    font-size: <?php echo $paramssld['ht_view0_paginator_icon_size']; ?>px !important;
    color:<?php echo '#'.$paramssld['ht_view0_paginator_icon_color']; ?> !important;
}
.clear{
    clear:both;
}



</style>

<section id="huge_it_portfolio_content_<?php echo $portfolioID; ?>">
    <?php if($portfolioShowSorting == "on"){ ?>
          <div id="huge_it_portfolio_options_<?php echo $portfolioID; ?>" class="" >
            <ul id="sort-by" class="option-set clearfix" data-option-key="sortBy">
                <li><a href="#sortBy=original-order" data-option-value="original-order" class="selected" data><?php echo $paramssld["ht_view0_sorting_name_by_default"]; ?></a></li>
                <li><a href="#sortBy=id" data-option-value="id"><?php echo $paramssld["ht_view0_sorting_name_by_id"]; ?></a></li>
                <li><a href="#sortBy=symbol" data-option-value="symbol"><?php echo $paramssld["ht_view0_sorting_name_by_name"]; ?></a></li>
                <li id="shuffle"><a href='#shuffle'><?php echo $paramssld["ht_view0_sorting_name_by_random"]; ?></a></li>
            </ul>
              
            <ul id="sort-direction" class="option-set clearfix" data-option-key="sortAscending">
                <li><a href="#sortAscending=true" data-option-value="true" class="selected"><?php echo $paramssld["ht_view0_sorting_name_by_asc"]; ?></a></li>
                <li><a href="#sortAscending=false" data-option-value="false"><?php echo $paramssld["ht_view0_sorting_name_by_desc"]; ?></a></li>
            </ul>
          </div>
  <?php }
     if($portfolioShowFiltering == "on"){ ?>
         <div id="huge_it_portfolio_filters_<?php echo $portfolioID; ?>" style>
            <ul>
                <li rel="*"><a><?php echo $paramssld["ht_view0_cat_all"];?></a></li>
                <?php
                $portfolioCats = explode(",", $portfolioCats);
                foreach ($portfolioCats as $portfolioCatsValue) {
                    if(!empty($portfolioCatsValue))
                    {
                ?>
                <li rel=".<?php echo str_replace(" ","_",$portfolioCatsValue); ?>"><a><?php echo str_replace("_"," ",$portfolioCatsValue); ?></a></li>
                    <?php
                    }
                }
                ?>
            </ul>
        </div>
<?php } ?>
<div id="huge_it_portfolio_container_<?php echo $portfolioID; ?>" class="super-list variable-sizes clearfix" <?php if($paramssld["ht_view0_sorting_float"] == "top" && $paramssld["ht_view0_filtering_float"] == "top") echo "style='clear: both;'";?>>
    <?php
    $gallery = '';
    foreach($images as $image){
        $idofgallery=$image->portfolio_id ;
    }

    $db = JFactory::getDBO();
    $query2 = $db->getQuery(true);
    $query2->select('*');
    $query2->from('#__huge_itportfolio_portfolios');
    $query2 -> where('id ='.$idofgallery);
    $query2 ->order('#__huge_itportfolio_portfolios.ordering asc');
    $db->setQuery($query2);
    $gallery = $db->loadObjectList();
    $pattern='/-/';
    $pID = '';
    $post = 0;
    foreach ($gallery as $gall) {
        global $post;
        $pID=$post;
        $disp_type=$gall->pagination_type;
        $count_page=$gall->count_into_page;
        if($count_page==0){
            $count_page=999;
        }elseif(preg_match($pattern, $count_page)){
            $count_page=preg_replace($pattern, '', $count_page);
        }
    }
    $num=$count_page;
    $total = intval(((count($images) - 1) / $num) + 1);
    if(isset($_GET['page-img'.$portfolioID.$pID])){
        $page = $_GET['page-img'.$portfolioID.$pID];
    }else{
        $page = '';

    }
    $page = intval($page);
    if(empty($page) or $page < 0) $page = 1;
    if($page > $total) $page = $total;
    $start = $page * $num - $num;
    $query = $db->getQuery(true);
    $query->select('*');
    $query->from('#__huge_itportfolio_images');
    $query -> where('portfolio_id ='.$idofgallery);
    $query ->order('#__huge_itportfolio_images.ordering asc');
    $db->setQuery($query,$start,$num);
    $page_images = $db->loadObjectList();
    if($disp_type=='show_all'){

        $page_images=$images;
        $count_page=9999;
    }

    $group_key1= 0;
    foreach($page_images as $key=>$row) {
        $group_key1++;
        $group_key = (string)$group_key1;
        $portfolioID1 = (string)$portfolioID;
        $group_key =$group_key."-".$portfolioID;
        $link = $row->sl_url;
        $descnohtml=strip_tags($row->description);
        $result = substr($descnohtml, 0, 50);
        $catForFilter = explode(",", $row->category);
        $imgurl=explode(";",$row->image_url);
        $lighboxable = (count($imgurl) == 2)?"lighboxable":"dropdownable";
        ?>
        <input type="hidden" class="pagenum" value="1" />
        <input type="hidden" id="total" value="<?=$total; ?>" />
        <div class="element_<?php echo $portfolioID; ?> colorbox_grouping <?php foreach ($catForFilter as $catForFilterValue) { echo str_replace(" ","-",$catForFilterValue)." ";} ?>" data-symbol="<?php echo $row->name; ?>" data-category="alkaline-earth">
                        <div class="default-block_<?php echo $portfolioID; ?> <?php echo $lighboxable; ?>">
                            <div class="image-block_<?php echo $portfolioID; ?>  add-H-relative">
                                <?php $imgurl=explode(";",$row->image_url);?>
                                <?php
                                $imgurl_exp = explode('/',$imgurl[0]);
                                if($imgurl_exp[0] == 'http:' || $imgurl_exp[0] == 'https:'  ){
                                    $path_juri = '';
                                }
                                else{
                                    $path_juri = JUri::root();
                                }
                                    if($row->image_url != ';'){
                                        switch($this -> youtube_or_vimeo_portfolio($imgurl[0])) { 
                                            case 'image':?>	
                                                <a href="<?php echo $path_juri.$imgurl[0];; ?>" class="portfolio-group<?php if( $lighboxable == "lighboxable")  echo $group_key;?>"  title = "<?php echo $row->name;?>">
                                                    <img alt="<?php echo $row->name; ?>" id="wd-cl-img<?php echo $key; ?>" src="<?php echo $imgurl[0]; ?>" />
                                                </a>
                                                <?php 
                                                break;
                                                case 'youtube':
                                                $videourl=$this->get_video_id_from_url_portfolio($imgurl[0]);?>
                                                <a href="https://www.youtube.com/embed/<?php echo $videourl[0];?>" class="huge_it_portfolio_item youtube portfolio-group<?php if( $lighboxable == "lighboxable")  echo $group_key;?> "  title = "<?php echo $row->name;?>" >
                                                <img alt="<?php echo $row->name; ?>" id="wd-cl-img<?php echo $key; ?>"  src="http://img.youtube.com/vi/<?php echo $videourl[0]; ?>/mqdefault.jpg"  />
                                                <div class="play-icon <?php echo $videourl[1];?>-icon"></div>
                                                </a>
                                                <?php
                                                break;
                                                case 'vimeo':
                                                $videourl=$this -> get_video_id_from_url_portfolio($imgurl[0]);
                                                $hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/".$videourl[0].".php"));
                                                $imgsrc=$hash[0]['thumbnail_large'];
                                        ?>
                                                <a href="http://player.vimeo.com/video/<?php echo $videourl[0]; ?>" class="huge_it_portfolio_item vimeo portfolio-group<?php if( $lighboxable == "lighboxable")  echo $group_key;?> "  title = "<?php echo $row->name;?>">
                                                    <img alt="<?php echo $row->name; ?>" src="<?php echo $imgsrc; ?>"  />
                                                <div class="play-icon <?php echo $videourl[1];?>-icon"></div>
                                                </a>
                                                <?php break;
                                        }
                                    } else { ?>
                                    <img alt="<?php echo $row->name; ?>" id="wd-cl-img<?php echo $key; ?>" src="images/noimage.jpg" />
                                    <?php
                                    } ?>	
                                    </div>
                                      <div class="title-block_<?php echo $portfolioID; ?>">
                                        <h3 class="title"><?php echo $row->name; ?></h3>
                                        <div class="open-close-button"></div>
                                      </div>
                              </div>

                              <div class="wd-portfolio-panel_<?php echo $portfolioID; ?>" id="panel<?php echo $key; ?>" >
                              <?php  $imgurl=explode(";",$row->image_url);
                                if($paramssld['ht_view0_show_thumbs']=='on' and $paramssld['ht_view0_thumbs_position']=="before" && count($imgurl) != 2){?>
                                    <div>
                                        <ul class="thumbs-list_<?php echo $portfolioID; ?>">  <?php
                                            array_pop($imgurl);
                                                foreach($imgurl as $key1=>$img) { ?>
                                                    <li> <?php 
                                                        switch($this->youtube_or_vimeo_portfolio($img)) { 
                                                            case 'image':?>
                                                        <a href="<?php echo $img; ?>" class=" portfolio-group<?php echo $group_key;?> "  title = ""> <img src="<?php echo $img; ?>"></a>
                                                            <?php 
                                                                break;
                                                                    case 'youtube':
                                                                    $videourl=$this->get_video_id_from_url_portfolio($img);?>
                                                                    <a href="https://www.youtube.com/embed/<?php echo $videourl[0];?>" class="huge_it_portfolio_item youtube portfolio-group<?php echo $group_key;?> "  title = "" style="position:relative">
                                                                    <img src="http://img.youtube.com/vi/<?php echo $videourl[0]; ?>/mqdefault.jpg"><div class="play-icon youtube-icon"></div></a>
                                                            <?php 
                                                                break;
                                                                case 'vimeo':
                                                                        $videourl=$this->get_video_id_from_url_portfolio($img);
                                                                        $hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/".$videourl[0].".php"));
                                                                        $imgsrc=$hash[0]['thumbnail_large'];?>
                                                                        <a class="huge_it_portfolio_item vimeo portfolio-group<?php echo $group_key;?> " href="http://player.vimeo.com/video/<?php echo $videourl[0]; ?>" title="<?php echo $row->name; ?>"  style="position:relative">
                                                                            <img src="<?php echo JUri::root().$imgsrc; ?>" alt="<?php echo $row->name; ?>" /><div class="play-icon vimeo-icon"></div>
                                                                        </a>
                                                                <?php	
                                                                break;
                                                                }?>
                                                    </li>
                                                              <?php
                                                              }
                                                              ?>
                                                      </ul>
                                              </div>
                                      <?php } 
                                      if($paramssld['ht_view0_show_description']=='on'){?>
                                              <div class="description-block_<?php echo $portfolioID; ?>">
                                                      <p><?php echo $row->description; ?></p>
                                              </div>
                                      <?php }
									   $imgurl=explode(";",$row->image_url);
                                      if($paramssld['ht_view0_show_thumbs']=='on' and $paramssld['ht_view0_thumbs_position']=="after" && count($imgurl) != 2){?>
                                              <div>
                                                      <ul class="thumbs-list_<?php echo $portfolioID; ?>">
                                                              <?php
                                                             
                                                              array_pop($imgurl);
                                                              foreach($imgurl as $key1=>$img)
                                                              {
                                                              ?>
                                                              <li>
															  <?php 
															  switch($this ->youtube_or_vimeo_portfolio($img)) { 
																case 'image':?>
                                                                 
                                                                      <a href="<?php echo $img; ?>" class=" portfolio-group<?php echo $group_key;?> "  title = "<?php huge_it_title_img_display($img,$title);?>"><img src="<?php echo get_huge_image($img,$image_prefix); ?>"></a>
															<?php 
																break;
																case 'youtube':
																	$videourl=get_video_id_from_url_portfolio($img);?>
                                                                    <a href="https://www.youtube.com/embed/<?php echo $videourl[0];?>" class="huge_it_portfolio_item youtube portfolio-group<?php echo $group_key;?> "  title = "<?php huge_it_title_img_display($img,$title);?>" style="position:relative">
																	<img src="http://img.youtube.com/vi/<?php echo $videourl[0]; ?>/mqdefault.jpg"><div class="play-icon youtube-icon"></div></a>
																	
															  <?php 
															    break;
																case 'vimeo':
																	$videourl=get_video_id_from_url_portfolio($img);
																	$hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/".$videourl[0].".php"));
																	$imgsrc=$hash[0]['thumbnail_large'];?>
																	<a class="huge_it_portfolio_item vimeo portfolio-group<?php echo $group_key;?> " href="http://player.vimeo.com/video/<?php echo $videourl[0]; ?>" title="<?php echo $row->name; ?>"  style="position:relative">
																	<img src="<?php echo $imgsrc; ?>" alt="<?php echo $row->name; ?>" /><div class="play-icon vimeo-icon"></div>
																	</a>
																<?php	
																break;
																}?>
															  </li>
                                                              <?php
                                                              }
                                                              ?>
                                                      </ul>
                                              </div>
                                      <?php } 
                                      if($paramssld['ht_view0_show_linkbutton']=='on' && $link != ''){?>
                                              <div class="button-block">
                                                      <a href="<?php echo $link; ?>" <?php if ($row->link_target=="on"){echo 'target="_blank"';}?>><?php echo $paramssld['ht_view0_linkbutton_text']; ?></a>
                                              </div>
                                      <?php } ?>
                              </div>
                      </div>

                      <?php
              }
              ?>
        </div>
    <?php


    $a=$disp_type;
    if($a=='load_more'){
        $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
        $actual_link = $protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."";
        $pattern="/\?p=/";
        $pattern2="/&page-img[0-9]+=[0-9]+/";
        $pattern3="/\?page-img[0-9]+=[0-9]+/";
        if(preg_match($pattern, $actual_link)){

            if(preg_match($pattern2, $actual_link)){
                $actual_link=preg_replace($pattern2, '', $actual_link);
                header("Location:".$actual_link."");
                exit;

            }
        }elseif(preg_match($pattern3, $actual_link)){
            $actual_link=preg_replace($pattern3, '', $actual_link);
            header("Location:".$actual_link."");
            exit;

        }
        ?>
        <?php $paramssld['ht_view0_loading_type'] = $paramssld['slider_navigation_type']; ?>
        <div class="load_more4">
            <div class="load_more_button4"><?=$paramssld['ht_view0_loadmore_text']; ?></div>
            <div class="loading4"><img src="<?php echo JUri::root()."media/com_portfoliogallery/images/loading.white.gif";?>"></div>
            <input type="hidden" class="load-more-elements-count" value="<?php echo $count_page; ?>"/>


            <script>
                jQuery(document).ready(function(){
                    if(jQuery("#huge_it_portfolio_content_<?php echo $portfolioID; ?> .load_more_button4").length){
                        jQuery("#huge_it_portfolio_content_<?php echo $portfolioID; ?> .load_more_button4").on("click tap",function(){
                            if(parseInt(jQuery("#huge_it_portfolio_content_<?php echo $portfolioID; ?> .pagenum:last").val())<parseInt(jQuery("#huge_it_portfolio_container_<?php echo $portfolioID; ?> #total").val())) {
                                var pagenum = parseInt(jQuery("#huge_it_portfolio_content_<?php echo $portfolioID; ?> .pagenum:last").val()) + 1;
                                var perpage =<?=$count_page; ?>;
                                var galleryid="<?=$image->portfolio_id; ?>";
                                getresult(pagenum,perpage,galleryid);
                            }else{

                                jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?> .load_more_button4').hide();
                            }
                            return false;
                        });
                    }
                    if(parseInt(jQuery("#huge_it_portfolio_content_<?php echo $portfolioID; ?> .pagenum:last").val())==parseInt(jQuery("#huge_it_portfolio_container_<?php echo $portfolioID; ?> #total").val())) {
                        jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?> .load_more_button4').hide();
                    }
                    function getresult(pagenum,perpage,galleryid){
                        var data = {
                            action:'my_action',
                            post:"huge_it_portfolio_gallery_ajax",
                            task:'load_portfolio_videos_lightbox',
                            page:pagenum,
                            perpage:perpage,
                            galleryid:galleryid,
                            level:<?php echo $level?>,
                        }
                        jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?> .loading4').show();

                        jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?> .load_more_button4').hide();

                        jQuery.post("<?php echo $path_menu?>components/com_portfoliogallery/ajax_url.php",data,function(response){
                            if(response.success){
                                //alert(response.success); //hugeitmicro
                                var $objnewitems= jQuery(response.success);
                                jQuery('#huge_it_portfolio_container_<?php echo $portfolioID; ?>').append( $objnewitems ).hugeitmicro('reloadItems' ).hugeitmicro({ sortBy: 'original-order' }).hugeitmicro( 'reLayout' );
                                jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?> img').on('load',function(){

                                    //############# End


                                    jQuery(".group1").colorbox({rel:'group1'});
                                    jQuery(".youtube").colorbox({iframe:true, innerWidth:640, innerHeight:390});
                                    jQuery(".vimeo").colorbox({iframe:true, innerWidth:640, innerHeight:390});
                                    jQuery(".iframe").colorbox({iframe:true, width:"80%", height:"80%"});
                                    jQuery(".inline").colorbox({inline:true, width:"50%"});
                                    jQuery(".callbacks").colorbox({
                                        onOpen:function(){ alert('onOpen: colorbox is about to open'); },
                                        onLoad:function(){ alert('onLoad: colorbox has started to load the targeted content'); },
                                        onComplete:function(){ alert('onComplete: colorbox has displayed the loaded content'); },
                                        onCleanup:function(){ alert('onCleanup: colorbox has begun the close process'); },
                                        onClosed:function(){ alert('onClosed: colorbox has completely closed'); }
                                    });

                                    jQuery('.non-retina').colorbox({rel:'group5', transition:'none'})
                                    jQuery('.retina').colorbox({rel:'group5', transition:'none', retinaImage:true, retinaUrl:true});
                                    var defaultBlockWidth=<?php echo $paramssld['ht_view6_width']; ?>+20+<?php echo $paramssld['ht_view6_width']*2; ?>;
                                    jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?> .load_more_button4').show();
                                    jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?> .loading4').hide();
                                    if(parseInt(jQuery("#huge_it_portfolio_content_<?php echo $portfolioID; ?> .pagenum:last").val())==parseInt(jQuery("#huge_it_portfolio_container_<?php echo $portfolioID; ?> #total").val())) {
                                        jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?> .load_more_button4').hide();
                                    }
                                });


                            }else{
                                alert("no");
                            }
                        },"json");
                    }

                });
            </script>
        </div>
        <?php
    }elseif($a=='pagination'){
        ?>
        <div class="paginate4">
            <?php
            $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
            $actual_link = $protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."";


            $checkREQ='';
            $pattern="/\?p=/";
            $pattern2="/&page-img[0-9]+=[0-9]+/";
            if(preg_match($pattern, $actual_link)){

                if(preg_match($pattern2, $actual_link)){
                    $actual_link=preg_replace($pattern2, '', $actual_link);
                }

                $checkREQ=$actual_link.'&page-img'.$portfolioID.$pID;

            }else{
                $checkREQ='?page-img'.$portfolioID.$pID;

            }
            $pervpage='';
            if ($page != 1) $pervpage = '<a href= '.$checkREQ.'=1><i class="hugeiticons hugeiticons-fast-backward" ></i></a>  
                             <a href= '.$checkREQ.'='. ($page - 1) .'><i class="hugeiticons hugeiticons-chevron-left"></i></a> ';
            $nextpage='';
            if ($page != $total) $nextpage = '<a href= '.$checkREQ.'='. ($page + 1) .'><i class="hugeiticons hugeiticons-chevron-right"></i></a>  
                                  <a href= '.$checkREQ.'=' .$total. '><i class="hugeiticons hugeiticons-fast-forward" ></i></a>';
            echo $pervpage.$page.'/'.$total.$nextpage;

            ?>
        </div>
        <?php
    }
    ?>

</section>
<script>
jQuery(function(){
var defaultBlockHeight=<?php echo $paramssld['ht_view0_block_height']; ?>;
var defaultBlockWidth=<?php echo $paramssld['ht_view0_block_width']; ?>;
var $container = jQuery('#huge_it_portfolio_container_<?php echo $portfolioID; ?>');
    
    
// add randomish size classes
$container.find('.element_<?php echo $portfolioID; ?>').each(function(){
  var $this = jQuery(this),
      number = parseInt( $this.find('.number').text(), 10 );
                  //alert(number);
  if ( number % 7 % 2 === 1 ) {
    $this.addClass('width2');
  }
  if ( number % 3 === 0 ) {
    $this.addClass('height2');
  }
});
    
    $container.hugeitmicro({
      itemSelector : '.element_<?php echo $portfolioID; ?>',
      masonry : {
        columnWidth :  <?php echo $paramssld['ht_view0_block_width']; ?>+20+<?php echo $paramssld['ht_view0_element_border_width']*2; ?>
      },
      masonryHorizontal : {
        rowHeight: 300+20
      },
      cellsByRow : {
        columnWidth : 300+20,
        rowHeight : 240
      },
      cellsByColumn : {
        columnWidth : 300+20,
        rowHeight : 240
      },
      getSortData : {
        symbol : function( $elem ) {
          return $elem.attr('data-symbol');
        },
        category : function( $elem ) {
          return $elem.attr('data-category');
        },
        number : function( $elem ) {
          return parseInt( $elem.find('.number').text(), 10 );
        },
        weight : function( $elem ) {
          return parseFloat( $elem.find('.weight').text().replace( /[\(\)]/g, '') );
        },
        id : function ( $elem ) {
          return $elem.find('.id').text();
        }
      }
    });
        

    var $optionSets = jQuery('#huge_it_portfolio_options_<?php echo $portfolioID; ?> .option-set'),
          $optionLinks = $optionSets.find('a');

      $optionLinks.click(function(){
        var $this = jQuery(this);

        if ( $this.hasClass('selected') ) {
          return false;
        }
        var $optionSet = $this.parents('.option-set');
        $optionSet.find('.selected').removeClass('selected');
        $this.addClass('selected');
  

        var options = {},
            key = $optionSet.attr('data-option-key'),
            value = $this.attr('data-option-value');

        value = value === 'false' ? false : value;
        options[ key ] = value;
        if ( key === 'layoutMode' && typeof changeLayoutMode === 'function' ) {

          changeLayoutMode( $this, options )
        } else {

          $container.hugeitmicro( options );
        }
        
        return false;
      });

      var isHorizontal = false;
      function changeLayoutMode( $link, options ) {
        var wasHorizontal = isHorizontal;
        isHorizontal = $link.hasClass('horizontal');

        if ( wasHorizontal !== isHorizontal ) {

          var style = isHorizontal ? 
            { height: '75%', width: $container.width() } : 
            { width: 'auto' };

          $container.filter(':animated').stop();

          $container.addClass('no-transition').css( style );
          setTimeout(function(){
            $container.removeClass('no-transition').hugeitmicro( options );
          }, 100 )
        } else {
          $container.hugeitmicro( options );
        }
      }
     $container.delegate( '.default-block_<?php echo $portfolioID; ?>.dropdownable', 'click', function(){
          var strheight=0;
          jQuery(this).parents('.element_<?php echo $portfolioID; ?>').find('.wd-portfolio-panel_<?php echo $portfolioID; ?> > div').each(function(){
                strheight+=jQuery(this).outerHeight()+10;
          })
          strheight+=<?php echo $paramssld['ht_view0_block_height']+45; ?>;
	  			if(jQuery(this).parents('.element_<?php echo $portfolioID; ?>').hasClass("large")){
			jQuery(this).parents('.element_<?php echo $portfolioID; ?>').animate({
				height: "<?php echo $paramssld['ht_view0_block_height']+45; ?>px"
			}, 300, function() {
				jQuery(this).removeClass('large');
				$container.hugeitmicro('reLayout');
			});
			
			jQuery(this).parents('.element_<?php echo $portfolioID; ?>').removeClass("active");
			return false;
		}
		jQuery(this).parents('.element_<?php echo $portfolioID; ?>').css({height:strheight});
		jQuery(this).parents('.element_<?php echo $portfolioID; ?>').addClass('large');

		$container.hugeitmicro('reLayout');
		jQuery(this).parents('.element_<?php echo $portfolioID; ?>').css({height:"<?php echo $paramssld['ht_view0_block_height']+45; ?>px"});		 
	
		 
		 jQuery(this).parents('.element_<?php echo $portfolioID; ?>').animate({
			height:strheight+"px",
		  }, 300,function(){	$container.hugeitmicro('reLayout');});
		  return false;
	});
     $container.delegate( '.title-block_<?php echo $portfolioID; ?>', 'click', function(){
          var strheight=0;
          jQuery(this).parents('.element_<?php echo $portfolioID; ?>').find('.wd-portfolio-panel_<?php echo $portfolioID; ?> > div').each(function(){
                strheight+=jQuery(this).outerHeight()+10;
          })
          strheight+=<?php echo $paramssld['ht_view0_block_height']+45; ?>;
	  			if(jQuery(this).parents('.element_<?php echo $portfolioID; ?>').hasClass("large")){
			jQuery(this).parents('.element_<?php echo $portfolioID; ?>').animate({
				height: "<?php echo $paramssld['ht_view0_block_height']+45; ?>px"
			}, 300, function() {
				jQuery(this).removeClass('large');
				$container.hugeitmicro('reLayout');
			});
			
			jQuery(this).parents('.element_<?php echo $portfolioID; ?>').removeClass("active");
			return false;
		}
		jQuery(this).parents('.element_<?php echo $portfolioID; ?>').css({height:strheight});
		jQuery(this).parents('.element_<?php echo $portfolioID; ?>').addClass('large');

		$container.hugeitmicro('reLayout');
		jQuery(this).parents('.element_<?php echo $portfolioID; ?>').css({height:"<?php echo $paramssld['ht_view0_block_height']+45; ?>px"});
		 jQuery(this).parents('.element_<?php echo $portfolioID; ?>').animate({
			height:strheight+"px",
		  }, 300,function(){	$container.hugeitmicro('reLayout');});
		  		  return false;

	});

    var $sortBy =  jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?> #sort-by');
    jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?> #shuffle a').click(function(){
      $container.hugeitmicro('shuffle');
      $sortBy.find('.selected').removeClass('selected');
      $sortBy.find('[data-option-value="random"]').addClass('selected');
      return false;
    });
    
        // bind filter on select change
        jQuery(document).ready(function(){
            jQuery('#huge_it_portfolio_filters_<?php echo $portfolioID; ?> ul li').click(function() {
              // get filter value from option value
              var filterValue = jQuery(this).attr('rel');
              // use filterFn if matches value
              filterValue = filterValue;
              $container.hugeitmicro({ filter: filterValue });
            });
            <?php if(($paramssld["ht_view0_sorting_float"] == "left" || $paramssld["ht_view0_sorting_float"] == "right") && $paramssld["ht_view0_filtering_float"] == "none")
                  { ?>
                        var topmargin = jQuery("#huge_it_portfolio_filters_<?php echo $portfolioID; ?> ul").height();
                        jQuery("#huge_it_portfolio_options_<?php echo $portfolioID; ?>").css({'margin-top':parseInt(topmargin) + 5});
            <?php }
            else  {
                    if(($paramssld["ht_view0_filtering_float"] == "left" || $paramssld["ht_view0_filtering_float"] == "right") && $paramssld["ht_view0_sorting_float"] == "none")
                      { ?>
                         var topmargin = jQuery("#huge_it_portfolio_options_<?php echo $portfolioID; ?>").height();
                         jQuery("#huge_it_portfolio_filters_<?php echo $portfolioID; ?>").css({'margin-top':'5px'});
                <?php }
                  } ?>
        });
        
        jQuery(window).load(function(){

            $container.hugeitmicro({ filter: '*' });
        });
  });
</script>

<?php        
        break;
        ///////////////////////////////// VIEW 1 FullHeight Blocks //////////////////////////////////////////////
	case 1;
       if($paramssld["ht_view1_sorting_float"] == "left" && $paramssld["ht_view1_filtering_float"] == "right" ||
       $paramssld["ht_view1_sorting_float"] == "right" && $paramssld["ht_view1_filtering_float"] == "left" ||
       $paramssld["ht_view1_sorting_float"] == $paramssld["ht_view1_filtering_float"])
       { $sorting_block_width ="20%"; $filtering_block_width ="20%"; $middle_with = "56%"; }
    else if($paramssld["ht_view1_sorting_float"] == "left" || $paramssld["ht_view1_sorting_float"] == "right" && $paramssld["ht_view1_filtering_float"] == "top")
       { $sorting_block_width ="30%"; $filtering_block_width ="100%"; $paramssld["ht_view1_filtering_float"] = "none"; $width_middle = "65%"; }
    else if($paramssld["ht_view1_filtering_float"] == "left" || $paramssld["ht_view1_filtering_float"] == "right" && $paramssld["ht_view1_sorting_float"] == "top")
       { $sorting_block_width ="100%"; $filtering_block_width ="30%"; $paramssld["ht_view1_sorting_float"] = "none"; $width_middle = "65%"; }
    if($paramssld["ht_view1_sorting_float"] == "top" && $paramssld["ht_view1_filtering_float"] == "top")
       { $sorting_block_width ="100%"; $filtering_block_width ="100%"; $left_to_top = "ok"; }
?>
<style type="text/css"> 
/***<add>***/
.element_<?php echo $portfolioID; ?> .play-icon.youtube-icon  {
    background: url(<?php echo JUri::root().'media/com_portfoliogallery/images/play.youtube.png';?>) center center no-repeat;
	background-size: 30% 30%;
}
.element_<?php echo $portfolioID; ?> .play-icon.vimeo-icon  {
	background: url(<?php echo JUri::root().'media/com_portfoliogallery/images/play.vimeo.png';?>) center center no-repeat;
	background-size: 30% 30%;
}
.element_<?php echo $portfolioID; ?> .play-icon {
    position: absolute;
    top: 0px;
    left: 0px;
    width: 100%;
    height: 100%;
}	
.element_<?php echo $portfolioID; ?> .add-H-relative {
    position: relative;
}	
/***</add>***/
.element_<?php echo $portfolioID; ?> {
  width:<?php echo $paramssld['ht_view1_block_width']; ?>px;
  height:auto;
  margin: 5px;
  float: left;
  overflow: hidden;
  position: relative;
  outline:none; 
  background:#<?php echo $paramssld['ht_view1_element_background_color']?>;
  border:<?php echo $paramssld['ht_view1_element_border_width']; ?>px solid #<?php echo $paramssld['ht_view1_element_border_color']; ?>;
}

.default-block_<?php echo $portfolioID; ?> {
    position:relative;;
    width:<?php echo $paramssld['ht_view1_block_width']; ?>px;
} 

.default-block_<?php echo $portfolioID; ?> .image-block_<?php echo $portfolioID; ?> {
    margin:0px;
    padding:0px;
    line-height:0px;
    border:1px solid #<?php echo $paramssld['ht_view1_element_border_color']; ?>;
}

.default-block_<?php echo $portfolioID; ?> img {
    margin:0px !important;
    padding:0px !important;
    max-width:none !important;
    width:<?php echo $paramssld['ht_view1_block_width']; ?>px !important;
    border-radius:0px;
}

.default-block_<?php echo $portfolioID; ?> .title-block_<?php echo $portfolioID; ?> {
    display:block;
    height:35px;
    padding:10px 0px 0px 0px;
    width:100%;
}

.default-block_<?php echo $portfolioID; ?> .title-block_<?php echo $portfolioID; ?> h3 {
    position:relative;
    margin:0px !important;
    padding:0px 5px 0px 5px !important;
    width:<?php echo $paramssld['ht_view1_block_width']; ?>px !important;
    text-overflow: ellipsis;
    overflow: hidden; 
    white-space:nowrap;
    font-weight:normal;
    color:#<?php echo $paramssld['ht_view1_title_font_color']; ?>;
    font-size:<?php echo $paramssld['ht_view1_title_font_size']; ?>px !important;
    line-height:<?php echo $paramssld['ht_view1_title_font_size']+4; ?>px !important;
}


.wd-portfolio-panel_<?php echo $portfolioID; ?> {
    position: relative;
    display:block;
    width:<?php echo $paramssld['ht_view1_block_width']-10; ?>px !important;
    margin:10px 5px 0px 5px;
    padding:0px;
    text-align:left;
}

.wd-portfolio-panel_<?php echo $portfolioID; ?> .description-block_<?php echo $portfolioID; ?> p,.wd-portfolio-panel_<?php echo $portfolioID; ?> .description-block_<?php echo $portfolioID; ?> * {	
    text-align:justify;
    font-weight:normal;
    font-size:<?php echo $paramssld['ht_view1_description_font_size']; ?>px !important;
    color:#<?php echo $paramssld['ht_view1_description_color']; ?>;
    margin:0px !important;
    padding:0px !important;
}


.wd-portfolio-panel_<?php echo $portfolioID; ?> .description-block_<?php echo $portfolioID; ?> h1,
.wd-portfolio-panel_<?php echo $portfolioID; ?> .description-block_<?php echo $portfolioID; ?> h2,
.wd-portfolio-panel_<?php echo $portfolioID; ?> .description-block_<?php echo $portfolioID; ?> h3,
.wd-portfolio-panel_<?php echo $portfolioID; ?> .description-block_<?php echo $portfolioID; ?> h4,
.wd-portfolio-panel_<?php echo $portfolioID; ?> .description-block_<?php echo $portfolioID; ?> h5,
.wd-portfolio-panel_<?php echo $portfolioID; ?> .description-block_<?php echo $portfolioID; ?> h6,
.wd-portfolio-panel_<?php echo $portfolioID; ?> .description-block_<?php echo $portfolioID; ?> p, 
.wd-portfolio-panel_<?php echo $portfolioID; ?> .description-block_<?php echo $portfolioID; ?> strong,
.wd-portfolio-panel_<?php echo $portfolioID; ?> .description-block_<?php echo $portfolioID; ?> span {
	padding:2px !important;
	margin:0px !important;
}

.wd-portfolio-panel_<?php echo $portfolioID; ?> .description-block_<?php echo $portfolioID; ?> ul,
.wd-portfolio-panel_<?php echo $portfolioID; ?> .description-block_<?php echo $portfolioID; ?> li {
	padding:2px 0px 2px 5px;
	margin:0px 0px 0px 8px;
}


.wd-portfolio-panel_<?php echo $portfolioID; ?> .thumbs-list_<?php echo $portfolioID; ?> {
	list-style:none;
	clear:both;
	display:table;
	width:100%;
	padding:0px;
	margin:3px 0px 0px 0px;
	text-align:center;
}

.wd-portfolio-panel_<?php echo $portfolioID; ?> .thumbs-list_<?php echo $portfolioID; ?> li {
	display:inline-block;
	margin:0px 3px 0px 2px;
}

.wd-portfolio-panel_<?php echo $portfolioID; ?> .thumbs-list_<?php echo $portfolioID; ?> li a {
	display:block;
	width:<?php echo $paramssld['ht_view1_thumbs_width']; ?>px;
	height:<?php echo $paramssld['ht_view1_thumbs_width']; ?>px;
	opacity:0.7;
	display:table;
}

.wd-portfolio-panel_<?php echo $portfolioID; ?> .thumbs-list_<?php echo $portfolioID; ?> li a:hover {
	opacity:1;
}

.wd-portfolio-panel_<?php echo $portfolioID; ?> img {
	margin:0px !important;
	padding:0px !important;
	display:table-cell;
	vertical-align:middle;
	width:<?php echo $paramssld['ht_view1_thumbs_width']; ?>px !important;
	max-height:<?php echo $paramssld['ht_view1_thumbs_width']; ?>px !important;
	width:100%;
	height:100%;
}

.wd-portfolio-panel_<?php echo $portfolioID; ?> > div {
	padding-top:10px;
	margin-bottom:10px;
	<?php if($paramssld['ht_view1_show_separator_lines']=="on") {?>
		background:url('<?php  echo JUri::root().'media/com_portfoliogallery/images/divider.line.png'; ?>') center top repeat-x;
	<?php } ?>
}

.wd-portfolio-panel_<?php echo $portfolioID; ?> .button-block {
    padding-top:10px;
    margin-bottom:10px;
	
}

.wd-portfolio-panel_<?php echo $portfolioID; ?> .button-block a, .wd-portfolio-panel_<?php echo $portfolioID; ?> .button-block a:link, .wd-portfolio-panel_<?php echo $portfolioID; ?> .button-block a:visited {
    padding:10px;
    display:inline-block;
    font-size:<?php echo $paramssld['ht_view1_linkbutton_font_size']; ?>px;
    background:#<?php echo $paramssld['ht_view1_linkbutton_background_color']; ?>;
    color:#<?php echo $paramssld['ht_view1_linkbutton_color']; ?>;
    padding:6px 12px;
    text-decoration:none;
}

.wd-portfolio-panel_<?php echo $portfolioID; ?> .button-block a:hover, .wd-portfolio-panel_<?php echo $portfolioID; ?> .button-block a:focus, .wd-portfolio-panel_<?php echo $portfolioID; ?> .button-block a:active {
    background:#<?php echo $paramssld['ht_view1_linkbutton_background_hover_color']; ?>;
    color:#<?php echo $paramssld['ht_view1_linkbutton_font_hover_color']; ?>;
    text-decoration:none;
}

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_options_<?php echo $portfolioID; ?> {
    <?php 
    display:block;
    if($paramssld["ht_view1_filtering_float"] == 'left' && $paramssld["ht_view1_sorting_float"] == 'none') { if($portfolioShowFiltering == "on") { echo "margin-left: 31%;"; } else echo "margin-left: 1%;"; }
    else if($paramssld["ht_view1_filtering_float"] == 'right' && $paramssld["ht_view1_sorting_float"] == 'none' || ($sorting_block_width == '100%' && $filtering_block_width == "100%")) { echo "margin-left: 1%;"; } ?>
    overflow: hidden;
    margin-top: 5px;
    float: <?php echo $paramssld["ht_view1_sorting_float"]; ?>;
    width:<?php echo $sorting_block_width; ?>;
}

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_options_<?php echo $portfolioID; ?> ul {
    margin: 0px !important;
    padding: 0px !important;
    list-style: none;
<?php if($paramssld["ht_view1_sorting_float"] == 'top') {
      echo "float:left;margin-left:1%;";
      } ?>
}

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?> ul {
    margin: 0px !important;
    padding: 0px !important;
    overflow: hidden;
  <?php if($paramssld["ht_view1_filtering_float"] == 'top') {
      echo "float:left;margin-left:1%;";
      } ?>
}

<?php if($paramssld["ht_view1_sorting_float"] == 'none') { ?>
            #huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_options_<?php echo $portfolioID; ?> ul {
                float: left;
            }
    <?php } ?>


#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_options_<?php echo $portfolioID; ?> ul li {
    border-radius: <?php echo $paramssld["ht_view1_sortbutton_border_radius"];?>px;
    list-style-type: none;
    margin: 0px !important;
	padding: 0;
    <?php
        if($sorting_block_width == "100%" ) {
            echo "float:left !important;margin: 4px 8px 4px 0px !important;";
        }
        if($left_to_top == "ok")
        { echo "float:left !important;"; }
        if($paramssld["ht_view1_sorting_float"] == "left" || $paramssld["ht_view1_sorting_float"] == "right")
        { echo 'border-bottom: 1px solid #ccc;'; }
        else
        { echo 'border: 1px solid #ccc;'; }
    ?>
}

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_options_<?php echo $portfolioID; ?> ul li a {
    background-color: #<?php echo $paramssld["ht_view1_sortbutton_background_color"];?> !important;
    font-size:<?php echo $paramssld["ht_view1_sortbutton_font_size"];?>px !important;
    color:#<?php echo $paramssld["ht_view1_sortbutton_font_color"];?> !important;
    text-decoration: none;
    cursor: pointer;
    margin: 0px !important;
    display: block;
    padding:3px;
}

/*#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_options_<?php echo $portfolioID; ?> ul li:hover {
    
}*/

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_options_<?php echo $portfolioID; ?> ul li a:hover {
    background-color: #<?php echo $paramssld["ht_view1_sortbutton_hover_background_color"];?> !important;
    color:#<?php echo $paramssld["ht_view1_sortbutton_hover_font_color"];?> !important;
    cursor: pointer;
}

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?> {
    margin-top: 5px;
    display:block;
    float: <?php echo $paramssld["ht_view1_filtering_float"]; ?>;
    width: <?php echo $filtering_block_width; ?>;
    <?php
        if($paramssld["ht_view1_filtering_float"] == 'none' && $paramssld["ht_view1_sorting_float"] == 'left' ) { if($portfolioShowSorting == 'on') { echo "margin-left: 31%;"; } else echo "margin-left: 1%";} 
        if(($paramssld["ht_view1_filtering_float"] == 'none' && ($paramssld["ht_view1_sorting_float"] == 'right')) || ($sorting_block_width == '100%' && $filtering_block_width == "100%")) { echo "margin-left: 1%";}
    ?>
}

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?> ul li {
    list-style-type: none;
    <?php
        if($filtering_block_width == "100%") { echo "float:left !important;margin: 4px 8px 4px 0px !important;"; }
        if($left_to_top == "ok") { echo "float:left !important;"; }
        if($paramssld["ht_view1_filtering_float"] == "left" || $paramssld["ht_view1_filtering_float"] == "right")
        { echo 'border-bottom: 1px solid #ccc;'; }
        else echo "border: 1px solid #ccc;";
    ?>
}

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?> ul li a {
    font-size:<?php echo $paramssld["ht_view1_filterbutton_font_size"];?>px !important;
    color:#<?php echo $paramssld["ht_view1_filterbutton_font_color"];?> !important;
    background-color: #<?php echo $paramssld["ht_view1_filterbutton_background_color"];?> !important;
    border-radius: <?php echo $paramssld["ht_view1_filterbutton_border_radius"];?>px;
    padding: 3px;
    display: block;
    text-decoration: none;
}

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?>  ul li a:hover {
    color:#<?php echo $paramssld["ht_view1_filterbutton_hover_font_color"];?> !important;
    background-color: #<?php echo $paramssld["ht_view1_filterbutton_hover_background_color"];?> !important;
    cursor: pointer
}
#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?> ul li.active a,
#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?> ul li.active a:link,
#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?> ul li.active a:visited,
#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?>  ul li.active a:hover,
#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?>  ul li.active a:focus,
#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?>  ul li.active a:active {
    color:#<?php echo $paramssld["ht_view1_filterbutton_hover_font_color"];?> !important;
    background-color: #<?php echo $paramssld["ht_view1_filterbutton_hover_background_color"];?> !important;
    cursor: pointer;
}
#huge_it_portfolio_content_<?php echo $portfolioID; ?> section {
    position:relative;
    display:block;
}

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_container_<?php echo $portfolioID; ?> {
<?php if($paramssld["ht_view1_sorting_float"] == "left" && $paramssld["ht_view1_filtering_float"] == "right" ||
         $paramssld["ht_view1_sorting_float"] == "right" && $paramssld["ht_view1_filtering_float"] == "left")
       { echo "margin: 0px auto;"; }
       if((($paramssld["ht_view1_filtering_float"] == "left" || $paramssld["ht_view1_filtering_float"] == "right" && $paramssld["ht_view1_sorting_float"] == "top") || ($paramssld["ht_view1_sorting_float"] == "left" || $paramssld["ht_view1_sorting_float"] == "right" && $paramssld["ht_view1_filting_float"] == "top")) && $portfolioShowFiltering == "on" && $portfolioShowSorting == "on")
       {
?>
    width: <?php echo $width_middle; ?> !important;
 <?php } ?>
}


.load_more4 {
    margin: 10px 0;
    position:relative;
    text-align:<?php if($paramssld['ht_view1_loadmore_position'] == 'left') {echo 'left';}
			elseif ($paramssld['ht_view1_loadmore_position'] == 'center') { echo 'center'; }
			elseif($paramssld['ht_view1_loadmore_position'] == 'right') { echo 'right'; }?>;

    width:100%;


}

.load_more_button4 {
    border-radius: 10px;
    display:inline-block;
    padding:5px 15px;
    font-size:<?php echo $paramssld['ht_view1_loadmore_fontsize']; ?>px !important;;
    color:<?php echo '#'.$paramssld['ht_view1_loadmore_font_color']; ?> !important;;
    background:<?php echo '#'.$paramssld['ht_view1_button_color']; ?> !important;
    cursor:pointer;

}
.load_more_button4:hover{
    color:<?php echo '#'.$paramssld['ht_view1_loadmore_font_color_hover']; ?> !important;
    background:<?php echo '#'.$paramssld['ht_view1_button_color_hover']; ?> !important;
}

.loading4 {
    display:none;
}
.paginate4{
    font-size:<?php echo $paramssld['ht_view1_paginator_fontsize']; ?>px !important;
    color:<?php echo '#'.$paramssld['ht_view1_paginator_color']; ?> !important;
    text-align: <?php echo $paramssld['ht_view1_paginator_position']; ?>;
    margin-top: 25px;
}
.paginate4 a{
    border-bottom: none !important;
    color: #<?php echo $paramssld['ht_view1_paginator_icon_color']; ?> !important;
    font-size:<?php echo $paramssld['ht_view1_paginator_icon_size']; ?>px !important;
}
</style>
<section id="huge_it_portfolio_content_<?php echo $portfolioID; ?>">
 <?php if($portfolioShowSorting == "on"){ ?>
        <div id="huge_it_portfolio_options_<?php echo $portfolioID; ?>" class="">
          <ul id="sort-by" class="option-set clearfix" data-option-key="sortBy">
              <li><a href="#sortBy=original-order" data-option-value="original-order" class="selected" data><?php echo $paramssld["ht_view1_sorting_name_by_default"]; ?></a></li>
              <li><a href="#sortBy=id" data-option-value="id"><?php echo $paramssld["ht_view1_sorting_name_by_id"]; ?></a></li>
              <li><a href="#sortBy=symbol" data-option-value="symbol"><?php echo $paramssld["ht_view1_sorting_name_by_name"]; ?></a></li>
              <li id="shuffle"><a href='#shuffle'><?php echo $paramssld["ht_view1_sorting_name_by_random"]; ?></a></li>
          </ul>

          <ul id="sort-direction" class="option-set clearfix" data-option-key="sortAscending">
              <li><a href="#sortAscending=true" data-option-value="true" class="selected"><?php echo $paramssld["ht_view1_sorting_name_by_asc"]; ?></a></li>
              <li><a href="#sortAscending=false" data-option-value="false"><?php echo $paramssld["ht_view1_sorting_name_by_desc"]; ?></a></li>
          </ul>
        </div>
  <?php }
   if($portfolioShowFiltering == "on"){ ?>
        <div id="huge_it_portfolio_filters_<?php echo $portfolioID; ?>" >
           <ul>
               <li rel="*"><a><?php echo $paramssld["ht_view1_cat_all"];?></a></li>
               <?php
               $portfolioCats = explode(",", $portfolioCats);
               foreach ($portfolioCats as $portfolioCatsValue) {
                   if(!empty($portfolioCatsValue))
                   {
               ?>
               <li rel=".<?php echo str_replace(" ","_",$portfolioCatsValue); ?>"><a><?php echo str_replace("_"," ",$portfolioCatsValue); ?></a></li>
                   <?php
                   }
               }
               ?>
           </ul>
       </div>
<?php } ?>

<div id="huge_it_portfolio_container_<?php echo $portfolioID; ?>" class="super-list variable-sizes clearfix" <?php if($paramssld["ht_view1_sorting_float"] == "top" && $paramssld["ht_view1_filtering_float"] == "top") echo "style='clear: both;'";?>>
    <?php
    foreach($images as $image){
        $idofgallery=$image->portfolio_id ;
    }


    $db = JFactory::getDBO();
    $query2 = $db->getQuery(true);
    $query2->select('*');
    $query2->from('#__huge_itportfolio_portfolios');
    $query2 -> where('id ='.$idofgallery);
    $query2 ->order('#__huge_itportfolio_portfolios.ordering asc');
    $db->setQuery($query2);
    $gallery = $db->loadObjectList();
    $pattern='/-/';
    $pID = '';
    $post = 0;
    foreach ($gallery as $gall) {
        global $post;
        $pID=$post;
        $disp_type=$gall->pagination_type;
        $count_page=$gall->count_into_page;
        if($count_page==0){
            $count_page=999;
        }elseif(preg_match($pattern, $count_page)){
            $count_page=preg_replace($pattern, '', $count_page);
        }
    }
    $num=$count_page;
    $total = intval(((count($images) - 1) / $num) + 1);
    if(isset($_GET['page-img'.$portfolioID.$pID])){
        $page = $_GET['page-img'.$portfolioID.$pID];
    }else{
        $page = '';

    }
    $page = intval($page);
    if(empty($page) or $page < 0) $page = 1;
    if($page > $total) $page = $total;
    $start = $page * $num - $num;
    $query = $db->getQuery(true);
    $query->select('*');
    $query->from('#__huge_itportfolio_images');
    $query -> where('portfolio_id ='.$idofgallery);
    $query ->order('#__huge_itportfolio_images.ordering asc');
    $db->setQuery($query,$start,$num);
    $page_images = $db->loadObjectList();
    if($disp_type=='show_all'){

        $page_images=$images;
        $count_page=9999;
    }

    $group_key1= 0;
    foreach($page_images as $key=>$row) {
        $group_key1++;
        $group_key = (string)$group_key1;
        $portfolioID1 = (string)$portfolioID;
        $group_key =$group_key."-".$portfolioID;
        $link = $row->sl_url;
        $descnohtml=strip_tags($row->description);
        $result = substr($descnohtml, 0, 50);
        $catForFilter = explode(",", $row->category);
        $imgurl=explode(";",$row->image_url);
        $lighboxable = (count($imgurl) == 2)?"lighboxable":"dropdownable";
        ?>
        <input type="hidden" class="pagenum" value="1" />
        <input type="hidden" id="total" value="<?=$total; ?>" />
                      <div class="element_<?php echo $portfolioID; ?> colorbox_grouping <?php foreach ($catForFilter as $catForFilterValue) { echo str_replace(" ","-",$catForFilterValue)." ";} ?>" data-symbol="<?php echo $row->name; ?>" data-category="alkaline-earth">
                            <div class="default-block_<?php echo $portfolioID; ?>">
                                <div class="image-block_<?php echo $portfolioID; ?> add-H-relative" >
                                    <?php $imgurl=explode(";",$row->image_url); ?>
                                    <?php
                                    if($row->image_url != ';'){
                                        switch($this->youtube_or_vimeo_portfolio($imgurl[0])) { 
                                            case 'image':		?>	
                                                <a href="<?php echo$imgurl[0]; ?>" class=" portfolio-group<?php echo $group_key;?> " title="<?php echo $row->name;?>">														
                                                    <img alt="<?php echo $row->name; ?>" id="wd-cl-img<?php echo $key; ?>"  src="<?php echo $imgurl[0]; ?>" />
                                                </a>	
                                    <?php 
                                            break;
                                            case 'youtube':
                                            $videourl=$this -> get_video_id_from_url_portfolio($imgurl[0]);?>
                                            <a href="https://www.youtube.com/embed/<?php echo $videourl[0];?>" class="huge_it_portfolio_item youtube portfolio-group<?php echo $group_key;?>" title="<?php echo $row->name;?>">
                                                <img alt="<?php echo $row->name; ?>" id="wd-cl-img<?php echo $key; ?>"  src="http://img.youtube.com/vi/<?php echo $videourl[0]; ?>/mqdefault.jpg"  />
                                                    <div class="play-icon <?php echo $videourl[1];?>-icon"></div>
					    </a>	
                                    <?php
                                            break;
                                            case 'vimeo':
                                            $videourl=$this->get_video_id_from_url_portfolio($imgurl[0]);
                                            $hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/".$videourl[0].".php"));
                                            $imgsrc=$hash[0]['thumbnail_large'];
                                    ?>
                                            <a class="huge_it_portfolio_item vimeo portfolio-group<?php echo $group_key;?> " href="http://player.vimeo.com/video/<?php echo $videourl[0]; ?>" title="<?php echo $row->name; ?>">
                                                <img alt="<?php echo $row->name; ?>" src="<?php echo $imgsrc; ?>"  />
                                                <div class="play-icon <?php echo $videourl[1];?>-icon"></div>
                                            </a>	
                                    <?php   break;
					}
				    }else { ?>
                                              <img alt="<?php echo $row->name; ?>" id="wd-cl-img<?php echo $key; ?>" src="images/noimage.jpg" />
                                              <?php
                                              } ?>	
                                      </div>
                                      <div class="title-block_<?php echo $portfolioID; ?>">
                                              <h3 class="title"><?php echo $row->name; ?></h3>
                                      </div>
                              </div>

                              <div class="wd-portfolio-panel_<?php echo $portfolioID; ?>" id="panel<?php echo $key; ?>">
                              <?php	$imgurl=explode(";",$row->image_url);
                                        array_shift($imgurl);
                                    if($paramssld['ht_view1_show_thumbs']=='on' and $paramssld['ht_view1_thumbs_position']=="before" && count($imgurl) !=1 ){?>
                                        <div>
                                            <ul class="thumbs-list_<?php echo $portfolioID; ?>">
                                            <?php
                                                array_pop($imgurl);
                                                foreach($imgurl as $key1=>$img) {  ?>
                                                    <li>  <?php 
                                                        switch($this->youtube_or_vimeo_portfolio($img)) { 
                                                            case 'image':?>
                                                                <a href="<?php echo $img; ?>" class=" portfolio-group<?php echo $group_key;?> "  title = ""><img src="<?php echo $img; ?>"></a>
                                                                    <?php 
                                                                            break;
                                                                            case 'youtube':
                                                                                $videourl=$this->get_video_id_from_url_portfolio($img);?>
                                                                                    <a href="https://www.youtube.com/embed/<?php echo $videourl[0];?>" class="huge_it_portfolio_item youtube portfolio-group<?php echo $group_key;?> "  title="<?php echo $row->name; ?>" style="position:relative">
                                                                                        <img src="http://img.youtube.com/vi/<?php echo $videourl[0]; ?>/mqdefault.jpg"><div class="play-icon youtube-icon"></div></a>
                                                                                            <?php 
                                                                                                break;
                                                                                                    case 'vimeo':
                                                                                                        $videourl= $this->get_video_id_from_url_portfolio($img);
                                                                                                        $hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/".$videourl[0].".php"));
                                                                                                        $imgsrc=$hash[0]['thumbnail_large'];?>
                                                                                                        <a class="huge_it_portfolio_item vimeo portfolio-group<?php echo $group_key;?> " href="http://player.vimeo.com/video/<?php echo $videourl[0]; ?>" title="<?php echo $row->name; ?>"  style="position:relative">
                                                                                                            <img src="<?php echo JUri::root().$imgsrc; ?>" alt="<?php echo $row->name; ?>" /><div class="play-icon vimeo-icon"></div>
                                                                                                        </a>
                                                                                            <?php	
                                                                                break;
                                                        }?>
                                                    </li>
                                                              <?php
                                                }
                                                              ?>
                                                      </ul>
                                              </div>
                                      <?php } 
                                      if($paramssld['ht_view1_show_description']=='on'){?>
                                              <div class="description-block_<?php echo $portfolioID; ?>">
                                                      <p><?php echo $row->description; ?></p>
                                              </div>
                                      <?php }
                                      $imgurl=explode(";",$row->image_url);
									  array_shift($imgurl);
                                      if($paramssld['ht_view1_show_thumbs']=='on' and $paramssld['ht_view1_thumbs_position']=="after" && count($imgurl) != 1){?>
                                              <div>
                                                      <ul class="thumbs-list_<?php echo $portfolioID; ?>">
                                                              <?php
                                                              array_pop($imgurl);
                                                              foreach($imgurl as $key1=>$img)
                                                              {
                                                              ?>
                                                              <li>
															  <?php 
															  switch(youtube_or_vimeo_portfolio($img)) { 
																case 'image':?>
                                                                      <a href="<?php echo $img; ?>" class=" portfolio-group<?php echo $group_key;?> "  title = "<?php huge_it_title_img_display($img,$title);?>"><img src="<?php echo get_huge_image($img,$image_prefix); ?>"></a>
															<?php 
																break;
																case 'youtube':
																	$videourl=get_video_id_from_url_portfolio($img);?>
                                                                    <a href="https://www.youtube.com/embed/<?php echo $videourl[0];?>" class="huge_it_portfolio_item youtube portfolio-group<?php echo $group_key;?> "  title="<?php echo $row->name; ?>" style="position:relative">
																	<img src="http://img.youtube.com/vi/<?php echo $videourl[0]; ?>/mqdefault.jpg"><div class="play-icon youtube-icon"></div></a>
																	
															  <?php 
															    break;
																case 'vimeo':
																	$videourl=get_video_id_from_url_portfolio($img);
																	$hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/".$videourl[0].".php"));
																	$imgsrc=$hash[0]['thumbnail_large'];?>
																	<a class="huge_it_portfolio_item vimeo portfolio-group<?php echo $group_key;?> " href="http://player.vimeo.com/video/<?php echo $videourl[0]; ?>" title="<?php echo $row->name; ?>"  style="position:relative">
																	<img src="<?php echo $imgsrc; ?>" alt="<?php echo $row->name; ?>" /><div class="play-icon vimeo-icon"></div>
																	</a>
																<?php	
																break;
																}?>
															  </li>
                                                              <?php
                                                              }
                                                              ?>
                                                      </ul>
                                              </div>
                                      <?php } 
                                      if($paramssld['ht_view1_show_linkbutton']=='on' && $link != ''){?>
                                              <div class="button-block">
                                                      <a href="<?php echo $link; ?>" <?php if ($row->link_target=="on"){echo 'target="_blank"';}?>><?php echo $paramssld['ht_view1_linkbutton_text']; ?></a>
                                              </div>
                                      <?php } ?>
                              </div>
                      </div>

                      <?php
              }
              ?>
        </div>
    <?php

    $a=$disp_type;
    if($a=='load_more'){
        $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
        $actual_link = $protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."";
        $pattern="/\?p=/";
        $pattern2="/&page-img[0-9]+=[0-9]+/";
        $pattern3="/\?page-img[0-9]+=[0-9]+/";
        if(preg_match($pattern, $actual_link)){

            if(preg_match($pattern2, $actual_link)){
                $actual_link=preg_replace($pattern2, '', $actual_link);
                header("Location:".$actual_link."");
                exit;

            }
        }elseif(preg_match($pattern3, $actual_link)){
            $actual_link=preg_replace($pattern3, '', $actual_link);
            header("Location:".$actual_link."");
            exit;

        }
        ?>
        <?php $paramssld['ht_view1_loading_type'] = $paramssld['slider_navigation_type']; ?>
        <div class="load_more4">
            <div class="load_more_button4"><?=$paramssld['ht_view1_loadmore_text']; ?></div>
            <div class="loading4"><img src="<?php echo JUri::root()."media/com_portfoliogallery/images/loading.white.gif";?>"></div>
            <input type="hidden" class="load-more-elements-count" value="<?php echo $count_page; ?>"/>


            <script>
                jQuery(document).ready(function(){
                    if(jQuery("#huge_it_portfolio_content_<?php echo $portfolioID; ?> .load_more_button4").length){
                        jQuery("#huge_it_portfolio_content_<?php echo $portfolioID; ?> .load_more_button4").on("click tap",function(){
                            //alert(0) xndir
                            if(parseInt(jQuery("#huge_it_portfolio_content_<?php echo $portfolioID; ?> .pagenum:last").val())<parseInt(jQuery("#huge_it_portfolio_container_<?php echo $portfolioID; ?> #total").val())) {
                                var pagenum = parseInt(jQuery("#huge_it_portfolio_content_<?php echo $portfolioID; ?> .pagenum:last").val()) + 1;
                                var perpage =<?=$count_page; ?>;
                                var galleryid="<?=$image->portfolio_id; ?>";
                                getresult(pagenum,perpage,galleryid);
                            }else{

                                jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?> .load_more_button4').hide();
                            }
                            return false;
                        });
                    }
                    if(parseInt(jQuery("#huge_it_portfolio_content_<?php echo $portfolioID; ?> .pagenum:last").val())==parseInt(jQuery("#huge_it_portfolio_container_<?php echo $portfolioID; ?> #total").val())) {
                        jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?> .load_more_button4').hide();
                    }
                    function getresult(pagenum,perpage,galleryid){
                        var data = {
                            action:'my_action',
                            post:"huge_it_portfolio_gallery_ajax1",
                            task:'load_portfolio_videos_lightbox',
                            page:pagenum,
                            perpage:perpage,
                            galleryid:galleryid,
                            level:<?php echo $level?>,
                        }
                        jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?> .loading4').show();

                        jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?> .load_more_button4').hide();

                        jQuery.post("<?php echo $path_menu?>components/com_portfoliogallery/ajax_url.php",data,function(response){
                            if(response.success){
                                var $objnewitems= jQuery(response.success);
                                jQuery('#huge_it_portfolio_container_<?php echo $portfolioID; ?>').append( $objnewitems ).hugeitmicro('reloadItems' ).hugeitmicro({ sortBy: 'original-order' }).hugeitmicro( 'reLayout' );
                                jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?> img').on('load',function(){
                                    //############# End


                                    jQuery(".group1").colorbox({rel:'group1'});
                                    jQuery(".youtube").colorbox({iframe:true, innerWidth:640, innerHeight:390});
                                    jQuery(".vimeo").colorbox({iframe:true, innerWidth:640, innerHeight:390});
                                    jQuery(".iframe").colorbox({iframe:true, width:"80%", height:"80%"});
                                    jQuery(".inline").colorbox({inline:true, width:"50%"});
                                    jQuery(".callbacks").colorbox({
                                        onOpen:function(){ alert('onOpen: colorbox is about to open'); },
                                        onLoad:function(){ alert('onLoad: colorbox has started to load the targeted content'); },
                                        onComplete:function(){ alert('onComplete: colorbox has displayed the loaded content'); },
                                        onCleanup:function(){ alert('onCleanup: colorbox has begun the close process'); },
                                        onClosed:function(){ alert('onClosed: colorbox has completely closed'); }
                                    });

                                    jQuery('.non-retina').colorbox({rel:'group5', transition:'none'})
                                    jQuery('.retina').colorbox({rel:'group5', transition:'none', retinaImage:true, retinaUrl:true});
                                    var defaultBlockWidth=<?php echo $paramssld['ht_view6_width']; ?>+20+<?php echo $paramssld['ht_view6_width']*2; ?>;
                                    jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?> .load_more_button4').show();
                                    jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?> .loading4').hide();
                                    if(parseInt(jQuery("#huge_it_portfolio_content_<?php echo $portfolioID; ?> .pagenum:last").val())==parseInt(jQuery("#huge_it_portfolio_container_<?php echo $portfolioID; ?> #total").val())) {
                                        jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?> .load_more_button4').hide();
                                    }
                                });


                            }else{
                                alert("no");
                            }
                        },"json");
                    }

                });
            </script>
        </div>
        <?php
    }elseif($a=='pagination'){
        ?>
        <div class="paginate4">
            <?php
            $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
            $actual_link = $protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."";
            $checkREQ='';
            $pattern="/\?p=/";
            $pattern2="/&page-img[0-9]+=[0-9]+/";
            if(preg_match($pattern, $actual_link)){

                if(preg_match($pattern2, $actual_link)){
                    $actual_link=preg_replace($pattern2, '', $actual_link);
                }

                $checkREQ=$actual_link.'&page-img'.$portfolioID.$pID;

            }else{
                $checkREQ='?page-img'.$portfolioID.$pID;

            }
            $pervpage='';
            if ($page != 1) $pervpage = '<a href= '.$checkREQ.'=1><i class="hugeiticons hugeiticons-fast-backward" ></i></a>  
                             <a href= '.$checkREQ.'='. ($page - 1) .'><i class="hugeiticons hugeiticons-chevron-left"></i></a> ';
            $nextpage='';
            if ($page != $total) $nextpage = '<a href= '.$checkREQ.'='. ($page + 1) .'><i class="hugeiticons hugeiticons-chevron-right"></i></a>  
                                  <a href= '.$checkREQ.'=' .$total. '><i class="hugeiticons hugeiticons-fast-forward" ></i></a>';
            echo $pervpage.$page.'/'.$total.$nextpage;

            ?>
        </div>
        <?php
    }
    ?>


</section>

<script>
jQuery(function(){
var defaultBlockWidth=<?php echo $paramssld['ht_view1_block_width']; ?>;
    
    var $container = jQuery('#huge_it_portfolio_container_<?php echo $portfolioID; ?>');
    
    
      // add randomish size classes
      $container.find('.element_<?php echo $portfolioID; ?>').each(function(){//console.log("rand");
        var $this = jQuery(this),
            number = parseInt( $this.find('.number').text(), 10 );
			//alert(number);
        if ( number % 7 % 2 === 1 ) {
          $this.addClass('width2');
        }
        if ( number % 3 === 0 ) {
          $this.addClass('height2');
        }
      });
    
    $container.hugeitmicro({
      itemSelector : '.element_<?php echo $portfolioID; ?>',
      masonry : {
        columnWidth : <?php echo $paramssld['ht_view1_block_width']; ?>+20+<?php echo $paramssld['ht_view1_element_border_width']*2; ?>
      },
      masonryHorizontal : {
        rowHeight: 300+20
      },
      cellsByRow : {
        columnWidth : 300+20,
        rowHeight : 240
      },
      cellsByColumn : {
        columnWidth : 300+20,
        rowHeight : 240
      },
      getSortData : {
        symbol : function( $elem ) {
          return $elem.attr('data-symbol');
        },
        category : function( $elem ) {
          return $elem.attr('data-category');
        },
        number : function( $elem ) {
          return parseInt( $elem.find('.number').text(), 10 );
        },
        weight : function( $elem ) {
          return parseFloat( $elem.find('.weight').text().replace( /[\(\)]/g, '') );
        },
        id : function ( $elem ) {
          return $elem.find('.id').text();
        }
      }
    });
    
    
      var $optionSets = jQuery('#huge_it_portfolio_options_<?php echo $portfolioID; ?> .option-set'),
          $optionLinks = $optionSets.find('a');

      $optionLinks.click(function(){
        var $this = jQuery(this);

        if ( $this.hasClass('selected') ) {
          return false;
        }
        var $optionSet = $this.parents('.option-set');
        $optionSet.find('.selected').removeClass('selected');
        $this.addClass('selected');
  

        var options = {},
            key = $optionSet.attr('data-option-key'),
            value = $this.attr('data-option-value');

        value = value === 'false' ? false : value;
        options[ key ] = value;
        if ( key === 'layoutMode' && typeof changeLayoutMode === 'function' ) {

          changeLayoutMode( $this, options )
        } else {

          $container.hugeitmicro( options );
        }
        
        return false;
      });


    

      var isHorizontal = false;
      function changeLayoutMode( $link, options ) {
        var wasHorizontal = isHorizontal;
        isHorizontal = $link.hasClass('horizontal');

        if ( wasHorizontal !== isHorizontal ) {

          var style = isHorizontal ? 
            { height: '75%', width: $container.width() } : 
            { width: 'auto' };

          $container.filter(':animated').stop();

          $container.addClass('no-transition').css( style );
          setTimeout(function(){
            $container.removeClass('no-transition').hugeitmicro( options );
          }, 100 )
        } else {
          $container.hugeitmicro( options );
        }
      }

    var $sortBy =  jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?> #sort-by');
    jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?> #shuffle a').click(function(){//console.log("changeLayoutMode");//
      $container.hugeitmicro('shuffle');
      $sortBy.find('.selected').removeClass('selected');
      $sortBy.find('[data-option-value="random"]').addClass('selected');
      return false;
    });
    
    ////filteringgggggg

        // bind filter on select change
        jQuery(document).ready(function(){
            jQuery('#huge_it_portfolio_filters_<?php echo $portfolioID; ?> ul li').click(function() {//console.log("changeLayoutMode")//
              // get filter value from option value
              var filterValue = jQuery(this).attr('rel');
              // use filterFn if matches value
              filterValue = filterValue;//filterFns[ filterValue ] || 
              $container.hugeitmicro({ filter: filterValue });
            });
            <?php if(($paramssld["ht_view1_sorting_float"] == "left" || $paramssld["ht_view1_sorting_float"] == "right") && $paramssld["ht_view1_filtering_float"] == "none")
                  { ?>
                        var topmargin = jQuery("#huge_it_portfolio_filters_<?php echo $portfolioID; ?> ul").height();
                        jQuery("#huge_it_portfolio_options_<?php echo $portfolioID; ?>").css({'margin-top':parseInt(topmargin) + 5});
            <?php }
            else  {
                    if(($paramssld["ht_view1_filtering_float"] == "left" || $paramssld["ht_view1_filtering_float"] == "right") && $paramssld["ht_view1_sorting_float"] == "none")
                      { ?>
                         var topmargin = jQuery("#huge_it_portfolio_options_<?php echo $portfolioID; ?>").height();
                         jQuery("#huge_it_portfolio_filters_<?php echo $portfolioID; ?>").css({'margin-top':'5px'});
                <?php }
                  } ?>
        });
        
        jQuery(window).load(function(){
            $container.hugeitmicro({ filter: '*' });
        });
  });
</script>

  <?php
    break;
/////////////////////////////// VIEW 2 Popup /////////////////////////////
    case 2:
    ?>
      <?php
    if($paramssld["ht_view2_sorting_float"] == "left" && $paramssld["ht_view2_filtering_float"] == "right" ||
       $paramssld["ht_view2_sorting_float"] == "right" && $paramssld["ht_view2_filtering_float"] == "left" ||
       $paramssld["ht_view2_sorting_float"] == $paramssld["ht_view2_filtering_float"])
       { $sorting_block_width ="20%"; $filtering_block_width ="20%"; $middle_with = "56%"; }
    else if($paramssld["ht_view2_sorting_float"] == "left" || $paramssld["ht_view2_sorting_float"] == "right" && $paramssld["ht_view2_filtering_float"] == "top")
       { $sorting_block_width ="30%"; $filtering_block_width ="100%"; $paramssld["ht_view2_filtering_float"] = "none"; $width_middle = "65%"; }
    else if($paramssld["ht_view2_filtering_float"] == "left" || $paramssld["ht_view2_filtering_float"] == "right" && $paramssld["ht_view2_sorting_float"] == "top")
       { $sorting_block_width ="100%"; $filtering_block_width ="30%"; $paramssld["ht_view2_sorting_float"] = "none"; $width_middle = "65%"; }
    if($paramssld["ht_view2_sorting_float"] == "top" && $paramssld["ht_view2_filtering_float"] == "top")
       { $sorting_block_width ="100%"; $filtering_block_width ="100%"; $left_to_top = "ok"; }
?>
<script>
jQuery(function(){
    var defaultBlockWidth=<?php echo $paramssld['ht_view2_element_width']; ?>;
    var defaultBlockHeight=<?php echo $paramssld['ht_view2_element_height']; ?>;
    var $container = jQuery('#huge_it_portfolio_container_<?php echo $portfolioID; ?>');
    
      // add randomish size classes
    $container.find('.element_<?php echo $portfolioID; ?>').each(function(){ //hech
        var $this = jQuery(this),
            number = parseInt( $this.find('.number').text(), 10 );
        if ( number % 7 % 2 === 1 ) {
          $this.addClass('width2');
        }
        if ( number % 3 === 0 ) {
          $this.addClass('height2');
        }
      });
    
    $container.hugeitmicro({//
      itemSelector : '.element_<?php echo $portfolioID; ?>',
      masonry : {
        columnWidth : <?php echo $paramssld['ht_view2_element_width']; ?>+20+<?php echo $paramssld['ht_view2_element_border_width']*2; ?>
      },
      masonryHorizontal : {
        rowHeight: 300+20
      },
      cellsByRow : {
        columnWidth : 300+20,
        rowHeight : 240
      },
      cellsByColumn : {
        columnWidth : 300+20,
        rowHeight : 240
      },
      getSortData : {
        symbol : function( $elem ) {
          return $elem.attr('data-symbol');
        },
        category : function( $elem ) {
          return $elem.attr('data-category');
        },
        number : function( $elem ) {
          return parseInt( $elem.find('.number').text(), 10 );
        },
        weight : function( $elem ) {
          return parseFloat( $elem.find('.weight').text().replace( /[\(\)]/g, '') );
        },
        id : function ( $elem ) {
          return $elem.find('.id').text();
        }
      }
    });
    
    
      var $optionSets = jQuery('#huge_it_portfolio_options_<?php echo $portfolioID; ?> .option-set'),
          $optionLinks = $optionSets.find('a');

      $optionLinks.click(function(){//
        var $this = jQuery(this);

        if ( $this.hasClass('selected') ) {
          return false;
        }
        var $optionSet = $this.parents('.option-set');
        $optionSet.find('.selected').removeClass('selected');
        $this.addClass('selected');
  

        var options = {},
            key = $optionSet.attr('data-option-key'),
            value = $this.attr('data-option-value');

        value = value === 'false' ? false : value;
        options[ key ] = value;
        if ( key === 'layoutMode' && typeof changeLayoutMode === 'function' ) {

          changeLayoutMode( $this, options )
        } else {

          $container.hugeitmicro( options );
        }
        
        return false;
      });

      var isHorizontal = false;
      function changeLayoutMode( $link, options ) {//console.log("changeLayoutMode")
        var wasHorizontal = isHorizontal;
        isHorizontal = $link.hasClass('horizontal');

        if ( wasHorizontal !== isHorizontal ) {

          var style = isHorizontal ? 
            { height: '75%', width: $container.width() } : 
            { width: 'auto' };

          $container.filter(':animated').stop();

          $container.addClass('no-transition').css( style );
          setTimeout(function(){
            $container.removeClass('no-transition').hugeitmicro( options );
          }, 100 )
        } else {
          $container.hugeitmicro( options );
        }
      }


    

      $container.delegate( '.default-block_<?php echo $portfolioID; ?>', 'click', function(){//console.log("changeLayoutMode")
          var strheight=0;
          jQuery(this).parents('.element_<?php echo $portfolioID; ?>').find('.wd-portfolio-panel_<?php echo $portfolioID; ?> > div').each(function(){
                strheight+=jQuery(this).outerHeight()+10;
          })
          strheight+=<?php echo $paramssld['ht_view0_block_height']+45; ?>;
	  			if(jQuery(this).parents('.element_<?php echo $portfolioID; ?>').hasClass("large")){
			jQuery(this).parents('.element_<?php echo $portfolioID; ?>').animate({
				height: "<?php echo $paramssld['ht_view0_block_height']+45; ?>px"
			}, 300, function() {
				jQuery(this).removeClass('large');
				$container.hugeitmicro('reLayout');
			});
			
			jQuery(this).parents('.element_<?php echo $portfolioID; ?>').removeClass("active");
			return false;
		}
		
	
		jQuery(this).parents('.element_<?php echo $portfolioID; ?>').css({height:strheight});
		jQuery(this).parents('.element_<?php echo $portfolioID; ?>').addClass('large');

		$container.hugeitmicro('reLayout');
		jQuery(this).parents('.element_<?php echo $portfolioID; ?>').css({height:"<?php echo $paramssld['ht_view0_block_height']+45; ?>px"});
		 
		 jQuery(this).parents('.element_<?php echo $portfolioID; ?>').animate({
			height:strheight+"px",
		  }, 300,function(){	$container.hugeitmicro('reLayout');});
	});

    var $sortBy =  jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?> #sort-by');
    jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?> #shuffle a').click(function(){//random dasavorum
      $container.hugeitmicro('shuffle');
      $sortBy.find('.selected').removeClass('selected');
      $sortBy.find('[data-option-value="random"]').addClass('selected');
      return false;
    });
    
    ////filteringgggggg

        // bind filter on select change
        jQuery(document).ready(function(){
            jQuery('#huge_it_portfolio_filters_<?php echo $portfolioID; ?> ul li').click(function() {//console.log("filter");
              // get filter value from option value
              var filterValue = jQuery(this).attr('rel');
              // use filterFn if matches value
              filterValue = filterValue;
              $container.hugeitmicro({ filter: filterValue });
            });
            <?php if(($paramssld["ht_view2_sorting_float"] == "left" || $paramssld["ht_view2_sorting_float"] == "right") && $paramssld["ht_view2_filtering_float"] == "none")
                  { ?>
                        var topmargin = jQuery("#huge_it_portfolio_filters_<?php echo $portfolioID; ?> ul").height();
                        jQuery("#huge_it_portfolio_options_<?php echo $portfolioID; ?>").css({'margin-top':parseInt(topmargin) + 5});
            <?php }
            else  {
                    if(($paramssld["ht_view2_filtering_float"] == "left" || $paramssld["ht_view2_filtering_float"] == "right") && $paramssld["ht_view2_sorting_float"] == "none")
                      { ?>
                         var topmargin = jQuery("#huge_it_portfolio_options_<?php echo $portfolioID; ?>").height();
                         jQuery("#huge_it_portfolio_filters_<?php echo $portfolioID; ?>").css({'margin-top':'5px'});
                <?php }
                  } ?>
        });

  });
  jQuery(document).ready(function(){

      document.onkeydown = function(e){
          (e.keyCode == 39)&&(jQuery('.pupup-element.active .right-change a').click());
          (e.keyCode == 37)&&(jQuery('.pupup-element.active .left-change a').click());
          (e.keyCode == 27)&&(closePopup());

      };
      jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?>').on('click','.element_<?php echo $portfolioID; ?> .image-block_<?php echo $portfolioID; ?> .image-overlay a',function(){//console.log("filter");//
		var strid = jQuery(this).attr('href').replace('#','');
		jQuery('body').append('<div id="huge-popup-overlay_<?php echo $portfolioID; ?>"></div>');
		jQuery('#huge_it_portfolio_popup_list_<?php echo $portfolioID; ?>').insertBefore('#huge-popup-overlay_<?php echo $portfolioID; ?>');
		var height = jQuery(window).height();
		var width=jQuery(window).width();
		if(width<=767){
			jQuery('body').scrollTop(0);
		}
		jQuery('#huge_it_portfolio_pupup_element_'+strid).addClass('active').css({height:height*0.7});
		jQuery('#huge_it_portfolio_popup_list_<?php echo $portfolioID; ?>').addClass('active');
		
		jQuery('#huge_it_portfolio_pupup_element_'+strid+' ul.thumbs-list_<?php echo $portfolioID; ?> li:first-child').addClass('active');
		var strsrc=jQuery('#huge_it_portfolio_pupup_element_'+strid+' ul.thumbs-list_<?php echo $portfolioID; ?> li:first-child a img').attr('src');
		jQuery('#huge_it_portfolio_pupup_element_'+strid+' .image-block_<?php echo $portfolioID; ?> img').attr('src',strsrc);
          if(1.1*parseInt(jQuery('.pupup-element.active .description').height()) > parseInt(jQuery('.pupup-element.active .right-block').height())){
			if(jQuery('.pupup-element.active .image-block_<?php echo $portfolioID; ?> img').height() > jQuery('.pupup-element.active .image-block_<?php echo $portfolioID; ?>').height()){
				jQuery('.pupup-element.active .popup-wrapper_<?php echo $portfolioID; ?>').css({
					'overflow-y':'auto',
				});

			}
			else{
                jQuery('.pupup-element.active .popup-wrapper_<?php echo $portfolioID; ?>').css({
                    'overflow-y':'auto',
                    'width':'98.8%',
                });
				jQuery('.pupup-element.active .right-block').css({
                    'overflow-y':'auto',
                    'width':'37.5%',
                    'padding': '0px 62px 0px 0px',
                });
			}

        }
		else if(jQuery('.pupup-element.active .description').height() < jQuery('.pupup-element.active .right-block').height() && jQuery('.pupup-element.active img').height() > jQuery('.pupup-element.active .image-block_<?php echo $portfolioID; ?>').height() ){
			jQuery('.pupup-element.active .popup-wrapper_<?php echo $portfolioID; ?>').css({
				'overflow-y':'auto',
				'width':'96%',
			});
		}
          return false;

	});
	
	jQuery('#huge_it_portfolio_popup_list_<?php echo $portfolioID; ?> .popup-wrapper_<?php echo $portfolioID; ?> .right-block ul.thumbs-list_<?php echo $portfolioID; ?> li ').on('click','a.img-thumb',function(){//thubnail neri meccnelna
		var width=jQuery(window).width();
		var strsrc = jQuery(this).find('img').attr('src')
		if(width<=767){
			jQuery('body').scrollTop(0);
		}
		jQuery(this).parent().parent().find('li.active').removeClass('active');
		jQuery(this).parent().addClass('active');
		var left_block = jQuery(this).parents('.right-block').prev();
		if(left_block.find('img').length !=0) 
			left_block.find('img').attr('src',strsrc);
		else 
		{	
			left_block.html('<img src="'+strsrc+'" />');
		}
		if(jQuery('.pupup-element.active .image-block_<?php echo $portfolioID; ?> img').height() > jQuery('.pupup-element.active .image-block_<?php echo $portfolioID; ?>').height()){
				jQuery('.pupup-element.active .popup-wrapper_<?php echo $portfolioID; ?>').css({
					'overflow-y':'auto',
				});

			}
		
				return false;

	});
	
	jQuery('#huge_it_portfolio_popup_list_<?php echo $portfolioID; ?> .heading-navigation_<?php echo $portfolioID; ?> .close').on('click',function(){//console.log("filter");
		closePopup();
		return false;
	});
      /*      <-- POPUP LEFT CLICK -->        */
      jQuery("#huge_it_portfolio_popup_list_<?php echo $portfolioID; ?> .heading-navigation_<?php echo $portfolioID; ?> .left-change").click(function(){
          var height = jQuery(window).height();
          var num = jQuery(this).find("a").attr("href").replace('#', '');
          if(num >= 1){
              var strid = jQuery(this).closest(".pupup-element").prev(".pupup-element").find('a').data('popupid').replace('#','');
              jQuery('#huge_it_portfolio_pupup_element_'+strid).css({height:height*0.7});
              jQuery(this).closest(".pupup-element").removeClass("active");
              jQuery(this).closest(".pupup-element").prev(".pupup-element").addClass("active");
          }else{
              var strid = jQuery("#huge_it_portfolio_popup_list_<?php echo $portfolioID; ?>").find(".pupup-element").last().find('a').data('popupid').replace('#','');
              jQuery('#huge_it_portfolio_pupup_element_'+strid).css({height:height*0.7});
              jQuery(this).closest(".pupup-element").removeClass("active");
              jQuery("#huge_it_portfolio_popup_list_<?php echo $portfolioID; ?>").find(".pupup-element").last().addClass("active");
          }


          if(1.1*parseInt(jQuery('.pupup-element.active .description').height()) > parseInt(jQuery('.pupup-element.active .right-block').height())){
			if(jQuery('.pupup-element.active .image-block_<?php echo $portfolioID; ?> img').height() > jQuery('.pupup-element.active .image-block_<?php echo $portfolioID; ?>').height()){
				jQuery('.pupup-element.active .popup-wrapper_<?php echo $portfolioID; ?>').css({
					'overflow-y':'auto',
				});

			}
			else{
                jQuery('.pupup-element.active .popup-wrapper_<?php echo $portfolioID; ?>').css({
                    'overflow-y':'auto',
                    'width':'98.8%',
                });
				jQuery('.pupup-element.active .right-block').css({
                    'overflow-y':'auto',
                    'width':'37.5%',
                    'padding': '0px 62px 0px 0px',
                });
			}

        }
		else if(jQuery('.pupup-element.active .description').height() < jQuery('.pupup-element.active .right-block').height() && jQuery('.pupup-element.active img').height() > jQuery('.pupup-element.active .image-block_<?php echo $portfolioID; ?>').height() ){
			jQuery('.pupup-element.active .popup-wrapper_<?php echo $portfolioID; ?>').css({
				'overflow-y':'auto',
				'width':'96%',
			});
		}
		  return false

      });

      /*      <-- POPUP RIGHT CLICK -->        */
      jQuery("#huge_it_portfolio_popup_list_<?php echo $portfolioID; ?> .heading-navigation_<?php echo $portfolioID; ?> .right-change").click(function(){
          var height = jQuery(window).height();
          var num = jQuery(this).find("a").attr("href").replace('#', '');
          var cnt = 0;
          jQuery("#huge_it_portfolio_popup_list_<?php echo $portfolioID; ?>").find(".pupup-element").each(function(){
              cnt++;
          });
          if(num <= cnt){
              var strid = jQuery(this).closest(".pupup-element").next(".pupup-element").find('a').data('popupid').replace('#','');
              jQuery('#huge_it_portfolio_pupup_element_'+strid).css({height:height*0.7});
              jQuery(this).closest(".pupup-element").removeClass("active");
              jQuery(this).closest(".pupup-element").next(".pupup-element").addClass("active");
          }else{
              var strid = jQuery("#huge_it_portfolio_popup_list_<?php echo $portfolioID; ?>").find(".pupup-element:first-child a").data('popupid').replace('#','');
              jQuery('#huge_it_portfolio_pupup_element_'+strid).css({height:height*0.7});
              jQuery(this).closest(".pupup-element").removeClass("active");
              jQuery("#huge_it_portfolio_popup_list_<?php echo $portfolioID; ?>").find(".pupup-element:first-child").addClass("active");
          }


          if(1.1*parseInt(jQuery('.pupup-element.active .description').height()) > parseInt(jQuery('.pupup-element.active .right-block').height())){
			if(jQuery('.pupup-element.active .image-block_<?php echo $portfolioID; ?> img').height() > jQuery('.pupup-element.active .image-block_<?php echo $portfolioID; ?>').height()){
				jQuery('.pupup-element.active .popup-wrapper_<?php echo $portfolioID; ?>').css({
					'overflow-y':'auto',
				});

			}
			else{
                jQuery('.pupup-element.active .popup-wrapper_<?php echo $portfolioID; ?>').css({
                    'overflow-y':'auto',
                    'width':'98.8%',
                });
				jQuery('.pupup-element.active .right-block').css({
                    'overflow-y':'auto',
                    'width':'37.5%',
                    'padding': '0px 62px 0px 0px',
                });
			}

        }
		else if(jQuery('.pupup-element.active .description').height() < jQuery('.pupup-element.active .right-block').height() && jQuery('.pupup-element.active img').height() > jQuery('.pupup-element.active .image-block_<?php echo $portfolioID; ?>').height() ){
			jQuery('.pupup-element.active .popup-wrapper_<?php echo $portfolioID; ?>').css({
				'overflow-y':'auto',
				'width':'96%',
			});
		}
		  return false
      });
	
	jQuery('body').on('click','#huge-popup-overlay_<?php echo $portfolioID; ?>',function(){
		closePopup();
		return false;
	});
	
	function closePopup() {
		var end_video_src = jQuery('#huge_it_portfolio_popup_list_<?php echo $portfolioID; ?> li.active iframe').attr('src');
		var end_video = '&enablejsapi=1';
		jQuery('#huge_it_portfolio_popup_list_<?php echo $portfolioID; ?> li.active iframe').attr('src',end_video_src+end_video);
		jQuery('#huge-popup-overlay_<?php echo $portfolioID; ?>').remove();
		jQuery('#huge_it_portfolio_popup_list_<?php echo $portfolioID; ?> li').removeClass('active');
		jQuery('#huge_it_portfolio_popup_list_<?php echo $portfolioID; ?>').removeClass('active');
	}
        jQuery(window).load(function(){
            $container.hugeitmicro({ filter: '*' });
        });
});
/***<add>***/
jQuery(function(){
	jQuery("#huge_it_portfolio_popup_list_<?php echo $portfolioID; ?> .video-thumb .play-icon").on('click',function() {
		new_video_id = jQuery(this).attr("title");
		var showcontrols,prefix,add_src;
			var showcontrols,new_video_id,prefix;
			if(!new_video_id) 
				return;
			if(new_video_id.length == 11) {
				 showcontrols = "?modestbranding=1&showinfo=0&controls=1";
				 prefix = "//www.youtube.com/embed/";
			}
			else {
			 showcontrols = "?title=0&amp;byline=0&amp;portrait=0";
			 prefix = "//player.vimeo.com/video/";

			}
			add_src = prefix+new_video_id+showcontrols;
			var left_block = jQuery(this).parents('.right-block').prev();
			if(left_block.find('iframe').length !=0) 
				left_block.find('iframe').attr('src',add_src);
			else 
				left_block.html('<iframe src="'+add_src+'" frameborder allowfullscreen></iframe> ');
			
			return false;
	});
}); 
/***</add>***/ 
</script>
<style type="text/css"> 
/***<add>***/
.element_<?php echo $portfolioID; ?> .play-icon.youtube-icon, 
 .play-icon.youtube-icon {
     background: url(<?php echo JUri::root().'media/com_portfoliogallery/images/play.youtube.png';?>) center center no-repeat;
	background-size: 30% 30%;
}
.element_<?php echo $portfolioID; ?> .play-icon.vimeo-icon,
 .play-icon.vimeo-icon {
	background: url(<?php echo JUri::root().'media/com_portfoliogallery/images/play.vimeo.png';?>) center center no-repeat;
	background-size: 30% 30%;
}

.element_<?php echo $portfolioID; ?> .play-icon,.play-icon {
    position: absolute;
    top: 0px;
    left: 0px;
    width: 100%;
    height: 100%;
}	
/***</add>***/
.element_<?php echo $portfolioID; ?> {
	width:<?php echo $paramssld['ht_view2_element_width']; ?>px;
	height:<?php echo $paramssld['ht_view2_element_height']+45; ?>px;
	margin:0px 0px 10px 0px;
	background:#<?php echo $paramssld['ht_view2_element_background_color']; ?>;
	border:<?php echo $paramssld['ht_view2_element_border_width']; ?>px solid #<?php echo $paramssld['ht_view2_element_border_color']; ?>;
	outline:none;
}

.element_<?php echo $portfolioID; ?> .image-block_<?php echo $portfolioID; ?> {
	position:relative;
	width:100%;
}

.element_<?php echo $portfolioID; ?> .image-block_<?php echo $portfolioID; ?> img {
	margin:0px !important;
	padding:0px !important;
	width:<?php echo $paramssld['ht_view2_element_width']; ?>px !important;
	height:<?php echo $paramssld['ht_view2_element_height']; ?>px !important;
	display:block;
	border-radius: 0px !important;
	box-shadow: 0 0px 0px rgba(0, 0, 0, 0) !important; 
}

.element_<?php echo $portfolioID; ?> .image-block_<?php echo $portfolioID; ?> .image-overlay {
	position:absolute;
	top:0px;
	left:0px;
	width:100%;
	height:100%;
	background: <?php
			list($r,$g,$b) = array_map('hexdec',str_split($paramssld['ht_view2_element_overlay_color'],2));
				$titleopacity=$paramssld["ht_view2_element_overlay_transparency"]/100;						
				echo 'rgba('.$r.','.$g.','.$b.','.$titleopacity.')  !important'; 		
	?>;
	display:none;
}

.element_<?php echo $portfolioID; ?>:hover .image-block_<?php echo $portfolioID; ?>  .image-overlay {
	display:block;
}

.element_<?php echo $portfolioID; ?> .image-block_<?php echo $portfolioID; ?> .image-overlay a {
	position:absolute;
	top:0px;
	left:0px;
	display:block;
	width:100%;
	height:100%;
	background:url('<?php echo JUri::root().'media/com_portfoliogallery/images/zoom.'.$paramssld["ht_view2_zoombutton_style"].'.png'; ?>') center center no-repeat;
}

.element_<?php echo $portfolioID; ?> .title-block_<?php echo $portfolioID; ?> {
	position:relative;
	height: 30px;
	margin: 0;
	padding: 15px 0px 15px 0px;
	-webkit-box-shadow: inset 0 1px 0 rgba(0,0,0,.1);
	box-shadow: inset 0 1px 0 rgba(0,0,0,.1);
}

.element_<?php echo $portfolioID; ?> .title-block_<?php echo $portfolioID; ?> h3 {
	position:relative;
	margin:0px !important;
	padding:0px 1% 0px 1% !important;
	width:98%;
	text-overflow: ellipsis;
	overflow: hidden; 
	white-space:nowrap;
	font-weight:normal;
	font-size: <?php echo $paramssld["ht_view2_popup_title_font_size"];?>px !important;
	line-height: <?php echo $paramssld["ht_view2_popup_title_font_size"]+4;?>px !important;
	color:#<?php echo $paramssld["ht_view2_element_title_font_color"];?>;
}

.element_<?php echo $portfolioID; ?> .title-block_<?php echo $portfolioID; ?> .button-block {
	position:absolute;
	right:0px;
	top:0px;
	display:none;
	vertical-align:middle;
	height:30px;
	padding:10px 10px 4px 10px;
	background: <?php
			list($r,$g,$b) = array_map('hexdec',str_split($paramssld['ht_view2_element_overlay_color'],2));
				$titleopacity=$paramssld["ht_view2_element_overlay_transparency"]/100;						
				echo 'rgba('.$r.','.$g.','.$b.','.$titleopacity.')  !important'; 		
	?>;
	border-left: 1px solid rgba(0,0,0,.05);
}
.element_<?php echo $portfolioID; ?>:hover .title-block_<?php echo $portfolioID; ?> .button-block {display:block;}

.element_<?php echo $portfolioID; ?> .title-block_<?php echo $portfolioID; ?> a,.element_<?php echo $portfolioID; ?> .title-block_<?php echo $portfolioID; ?> a:link,.element_<?php echo $portfolioID; ?> .title-block_<?php echo $portfolioID; ?> a:visited,
.element_<?php echo $portfolioID; ?> .title-block_<?php echo $portfolioID; ?> a:hover,.element_<?php echo $portfolioID; ?> .title-block_<?php echo $portfolioID; ?> a:focus,.element_<?php echo $portfolioID; ?> .title-block_<?php echo $portfolioID; ?> a:active {
	position:relative;
	display:block;
	vertical-align:middle;
	padding: 3px 10px 3px 10px; 
	border-radius:3px;
	font-size:<?php echo $paramssld["ht_view2_element_linkbutton_font_size"];?>px;
	background:#<?php echo $paramssld["ht_view2_element_linkbutton_background_color"];?>;
	color:#<?php echo $paramssld["ht_view2_element_linkbutton_color"];?>;
	text-decoration:none;
}

/*#####POPUP#####*/

#huge_it_portfolio_popup_list_<?php echo $portfolioID; ?> {
	position:fixed;
	display:table;
	width:80%;
	top:7%;
	left:7%;
	margin:0px !important;
	list-style:none;
	z-index:2000;
	display:none;
	height:90%;
}

#huge_it_portfolio_popup_list_<?php echo $portfolioID; ?>.active {display:table;}

#huge_it_portfolio_popup_list_<?php echo $portfolioID; ?> li.pupup-element {
	position:relative;
	display:none;
	width:100%;
	padding:40px 0px 20px 0px;
	min-height:100%;
	position:relative;
	background:#<?php echo $paramssld["ht_view2_popup_background_color"];?>;
}

#huge_it_portfolio_popup_list_<?php echo $portfolioID; ?> li.pupup-element.active {
	display:block;
}

#huge_it_portfolio_popup_list_<?php echo $portfolioID; ?> .heading-navigation_<?php echo $portfolioID; ?> {
	position:absolute;
	width:100%;
	height:40px;
	top:0px;
	left:0px;
	z-index:2001;
	background:url('<?php echo JUri::root().'media/com_portfoliogallery/images/divider.line.png'; ?>') center bottom repeat-x;
}

#huge_it_portfolio_popup_list_<?php echo $portfolioID; ?> .heading-navigation_<?php echo $portfolioID; ?> .close,#huge_it_portfolio_popup_list_<?php echo $portfolioID; ?> .heading-navigation_<?php echo $portfolioID; ?> .close:link, #huge_it_portfolio_popup_list_<?php echo $portfolioID; ?> .heading-navigation_<?php echo $portfolioID; ?> .close:visited {
	position:relative;
	float:right;
	width:40px;
	height:40px;
	display:block;
	background:url('<?php echo JUri::root().'media/com_portfoliogallery/images/close.popup.'.$paramssld["ht_view2_popup_closebutton_style"].'.png' ; ?>') center center no-repeat;
	border-left:1px solid #ccc;
	opacity:.65;
}

#huge_it_portfolio_popup_list_<?php echo $portfolioID; ?> .heading-navigation_<?php echo $portfolioID; ?> .close:hover, #huge_it_portfolio_popup_list_<?php echo $portfolioID; ?> .heading-navigation_<?php echo $portfolioID; ?> .close:focus, #huge_it_portfolio_popup_list_<?php echo $portfolioID; ?> .heading-navigation_<?php echo $portfolioID; ?> .close:active {opacity:1;}


#huge_it_portfolio_popup_list_<?php echo $portfolioID; ?> li.pupup-element .popup-wrapper_<?php echo $portfolioID; ?> {
	overflow-y:scroll;
	position:relative;
	width:96%;
	height:98%;
	padding:2% 2% 0% 2%;
}

#huge_it_portfolio_popup_list_<?php echo $portfolioID; ?> .popup-wrapper_<?php echo $portfolioID; ?> .image-block_<?php echo $portfolioID; ?> {
	width:60%;
        <?php if($paramssld['ht_view2_popup_full_width'] == 'off') { echo "height:100%;"; } ?>
	position:relative;
	height: 60%;
	float:left;
	margin-right:2%;
	border-right:1px solid #ccc;
	min-width:200px;
}

#huge_it_portfolio_popup_list_<?php echo $portfolioID; ?> .popup-wrapper_<?php echo $portfolioID; ?> .image-block_<?php echo $portfolioID; ?> img {
        <?php
            if($paramssld['ht_view2_popup_full_width'] == 'off') { echo "max-width:100% !important; max-height:100% !important; margin: 0px auto !important; position:relative;"; }
            else { echo "width:100% !important;"; }
        ?>
	display:block;
	padding:0px !important;
}

#huge_it_portfolio_popup_list_<?php echo $portfolioID; ?> .popup-wrapper_<?php echo $portfolioID; ?> .right-block {
	width:37%;
	position:relative;
	float:left;
}

#huge_it_portfolio_popup_list_<?php echo $portfolioID; ?> li.pupup-element .popup-wrapper_<?php echo $portfolioID; ?> .right-block > div {
	padding-top:10px;
	margin-bottom:10px;
	<?php if($paramssld['ht_view2_show_separator_lines']=="on") {?>
		background:url('<?php echo JUri::root().'media/com_portfoliogallery/images/divider.line.png'; ?>') center top repeat-x;
	<?php } ?>
}
#huge_it_portfolio_popup_list_<?php echo $portfolioID; ?> li.pupup-element .popup-wrapper_<?php echo $portfolioID; ?> .right-block > div:last-child {background:none;}


#huge_it_portfolio_popup_list_<?php echo $portfolioID; ?> .popup-wrapper_<?php echo $portfolioID; ?> .right-block .title {
	position:relative;
	display:block;
	margin:0px 0px 10px 0px !important;
	font-size:<?php echo $paramssld["ht_view2_element_title_font_size"];?>px !important;
	line-height:<?php echo $paramssld["ht_view2_element_title_font_size"]+4;?>px !important;
	color:#<?php echo $paramssld["ht_view2_popup_title_font_color"];?>;
}

#huge_it_portfolio_popup_list_<?php echo $portfolioID; ?> .popup-wrapper_<?php echo $portfolioID; ?> .right-block .description {
	clear:both;
	position:relative;
	font-weight:normal;
	text-align:justify;
	font-size:<?php echo $paramssld["ht_view2_description_font_size"];?>px !important;
	color:#<?php echo $paramssld["ht_view2_description_color"];?>;
}

#huge_it_portfolio_popup_list_<?php echo $portfolioID; ?> .popup-wrapper_<?php echo $portfolioID; ?> .right-block .description h1,
#huge_it_portfolio_popup_list_<?php echo $portfolioID; ?> .popup-wrapper_<?php echo $portfolioID; ?> .right-block .description h2,
#huge_it_portfolio_popup_list_<?php echo $portfolioID; ?> .popup-wrapper_<?php echo $portfolioID; ?> .right-block .description h3,
#huge_it_portfolio_popup_list_<?php echo $portfolioID; ?> .popup-wrapper_<?php echo $portfolioID; ?> .right-block .description h4,
#huge_it_portfolio_popup_list_<?php echo $portfolioID; ?> .popup-wrapper_<?php echo $portfolioID; ?> .right-block .description h5,
#huge_it_portfolio_popup_list_<?php echo $portfolioID; ?> .popup-wrapper_<?php echo $portfolioID; ?> .right-block .description h6,
#huge_it_portfolio_popup_list_<?php echo $portfolioID; ?> .popup-wrapper_<?php echo $portfolioID; ?> .right-block .description p, 
#huge_it_portfolio_popup_list_<?php echo $portfolioID; ?> .popup-wrapper_<?php echo $portfolioID; ?> .right-block .description strong,
#huge_it_portfolio_popup_list_<?php echo $portfolioID; ?> .popup-wrapper_<?php echo $portfolioID; ?> .right-block .description span {
	padding:2px !important;
	margin:0px !important;
}

#huge_it_portfolio_popup_list_<?php echo $portfolioID; ?> .popup-wrapper_<?php echo $portfolioID; ?> .right-block .description ul,
#huge_it_portfolio_popup_list_<?php echo $portfolioID; ?> .popup-wrapper_<?php echo $portfolioID; ?> .right-block .description li {
	padding:2px 0px 2px 5px;
	margin:0px 0px 0px 8px;
}

#huge_it_portfolio_popup_list_<?php echo $portfolioID; ?> .popup-wrapper_<?php echo $portfolioID; ?> .right-block ul.thumbs-list_<?php echo $portfolioID; ?> {
	list-style:none;
	display:table;
	position:relative;
	clear:both;
	width:100%;
	margin:0px auto;
	padding:0px;
}

#huge_it_portfolio_popup_list_<?php echo $portfolioID; ?> .popup-wrapper_<?php echo $portfolioID; ?> .right-block ul.thumbs-list_<?php echo $portfolioID; ?> li {
	display:block;
	float:left;
	width:<?php echo $paramssld["ht_view2_thumbs_width"];?>px;
	height:<?php echo $paramssld["ht_view2_thumbs_height"];?>px;
	margin:0px 2% 5px 1% !important;
	opacity:0.45;
}

#huge_it_portfolio_popup_list_<?php echo $portfolioID; ?> .popup-wrapper_<?php echo $portfolioID; ?> .right-block ul.thumbs-list_<?php echo $portfolioID; ?> li.active,#huge_it_portfolio_popup_list_<?php echo $portfolioID; ?> .popup-wrapper_<?php echo $portfolioID; ?> .right-block ul.thumbs-list_<?php echo $portfolioID; ?> li:hover {
	opacity:1;
}

#huge_it_portfolio_popup_list_<?php echo $portfolioID; ?> .popup-wrapper_<?php echo $portfolioID; ?> .right-block ul.thumbs-list_<?php echo $portfolioID; ?> li a {
	display:block;
}

#huge_it_portfolio_popup_list_<?php echo $portfolioID; ?> .popup-wrapper_<?php echo $portfolioID; ?> .right-block ul.thumbs-list_<?php echo $portfolioID; ?> li img {
	margin:0px !important;
	padding:0px !important;
	width:<?php echo $paramssld["ht_view2_thumbs_width"];?>px !important;
	height:<?php echo $paramssld["ht_view2_thumbs_height"];?>px !important;
}


#huge_it_portfolio_popup_list_<?php echo $portfolioID; ?> .popup-wrapper_<?php echo $portfolioID; ?> .image-block_<?php echo $portfolioID; ?> iframe  {
	width:100% !important;
	height:100%;
	display:block;

}

.pupup-element .button-block {
	position:relative;
}

.pupup-element .button-block a,.pupup-element .button-block a:link,.pupup-element .button-block a:visited{
	position:relative;
	display:inline-block;
	padding:6px 12px;
	background:#<?php echo $paramssld["ht_view2_popup_linkbutton_background_color"];?>;
	color:#<?php echo $paramssld["ht_view2_popup_linkbutton_color"];?>;
	font-size:<?php echo $paramssld["ht_view2_popup_linkbutton_font_size"];?>px;
	text-decoration:none;
}

.pupup-element .button-block a:hover,.pupup-element .button-block a:focus,.pupup-element .button-block a:active {
	background:#<?php echo $paramssld["ht_view2_popup_linkbutton_background_hover_color"];?>;
	color:#<?php echo $paramssld["ht_view2_popup_linkbutton_font_hover_color"];?>;
}


#huge-popup-overlay_<?php echo $portfolioID; ?> {
	position:fixed;
	top:0px;
	left:0px;
	width:100%;
	height:100%;
	z-index:199;
	background: <?php
			list($r,$g,$b) = array_map('hexdec',str_split($paramssld['ht_view2_popup_overlay_color'],2));
				$titleopacity=$paramssld["ht_view2_popup_overlay_transparency_color"]/100;						
				echo 'rgba('.$r.','.$g.','.$b.','.$titleopacity.')  !important'; 		
	?>
}


@media only screen and (max-width: 767px) {
	
	#huge_it_portfolio_popup_list_<?php echo $portfolioID; ?> {
		position:absolute;
		left:0px;
		top:0px;
		width:100%;
		height:auto !important;
		left:0px;
	}
	
	#huge_it_portfolio_popup_list_<?php echo $portfolioID; ?> li.pupup-element {
		margin:0px;
		height:auto !important;
		position:absolute;
		left:0px;
		top:0px;
	}

	#huge_it_portfolio_popup_list_<?php echo $portfolioID; ?> li.pupup-element .popup-wrapper_<?php echo $portfolioID; ?> {
		height:auto !important;
		overflow-y:auto;
	}


	#huge_it_portfolio_popup_list_<?php echo $portfolioID; ?> .popup-wrapper_<?php echo $portfolioID; ?> .image-block_<?php echo $portfolioID; ?> {
		width:100%;
		float:none;
		clear:both;
		margin-right:0px;
		border-right:0px;
	}

	#huge_it_portfolio_popup_list_<?php echo $portfolioID; ?> .popup-wrapper_<?php echo $portfolioID; ?> .right-block {
		width:100%;
		float:none;
		clear:both;
		margin-right:0px;
		border-right:0px;
	}

	#huge-popup-overlay_<?php echo $portfolioID; ?> {
		position:fixed;
		top:0px;
		left:0px;
		width:100%;
		height:100%;
		z-index:199;
	}

}

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_options_<?php echo $portfolioID; ?> {
    <?php 
    if($paramssld["ht_view2_filtering_float"] == 'left' && $paramssld["ht_view2_sorting_float"] == 'none') { if($portfolioShowFiltering == "on") { echo "margin-left: 30%;"; } else { echo "margin-left: 0%;"; } }
    //else if($paramssld["ht_view2_filtering_float"] == 'right' && $paramssld["ht_view2_sorting_float"] == 'none' || ($sorting_block_width == '100%' && $filtering_block_width == "100%")) { echo "margin-left: 1%;"; } ?>
    overflow: hidden;
    display:block;
    /*margin-top: 5px;*/
    float: <?php echo $paramssld["ht_view2_sorting_float"]; ?>;
    width: <?php echo $sorting_block_width; ?>;
}

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_options_<?php echo $portfolioID; ?> ul {
  margin: 0px !important;
  padding: 0px !important;
  list-style: none;
<?php if($paramssld["ht_view2_sorting_float"] == 'top') {
      echo "float:left;margin-left:1%;";
      } ?>
}

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?> ul {
  margin: 0px !important;
  padding: 0px !important;
  overflow: hidden;
  <?php if($paramssld["ht_view2_filtering_float"] == 'top') {
      echo "float:left;margin-left:1%;";
      } ?>
}

<?php if($paramssld["ht_view2_sorting_float"] == 'none') { ?>
            #huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_options_<?php echo $portfolioID; ?> ul {
                float: left;
            }
    <?php } ?>
            
            
#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_options_<?php echo $portfolioID; ?> ul li {
    border-radius: <?php echo $paramssld["ht_view2_sortbutton_border_radius"];?>px;
    list-style-type: none;
    margin: 0px !important;
	padding: 0;
    <?php
        if($sorting_block_width == "100%" ) {
            echo "float:left !important;margin: 4px 8px 4px 0px !important;";
        }
        if($left_to_top == "ok")
        { echo "float:left !important;"; }
        if($paramssld["ht_view2_sorting_float"] == "left" || $paramssld["ht_view2_sorting_float"] == "right")
        { echo 'border-bottom: 1px solid #ccc;'; }
        else
        { echo 'border: 1px solid #ccc;'; }
    ?>
}

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_options_<?php echo $portfolioID; ?> ul li a {
    background-color: #<?php echo $paramssld["ht_view2_sortbutton_background_color"];?> !important;
    font-size:<?php echo $paramssld["ht_view2_sortbutton_font_size"];?>px !important;
    color:#<?php echo $paramssld["ht_view2_sortbutton_font_color"];?> !important;
    text-decoration: none;
    cursor: pointer;
    margin: 0px !important;
    display: block;
    padding:3px;
}

/*#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_options_<?php echo $portfolioID; ?> ul li:hover {
    
}*/

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_options_<?php echo $portfolioID; ?> ul li a:hover {
    background-color: #<?php echo $paramssld["ht_view2_sortbutton_hover_background_color"];?> !important;
    color:#<?php echo $paramssld["ht_view2_sortbutton_hover_font_color"];?> !important;
    cursor: pointer;
}

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?> {
    /*margin-top: 5px;*/
    display:block;
    float: <?php echo $paramssld["ht_view2_filtering_float"]; ?>;
    width: <?php echo $filtering_block_width; ?>;
    <?php
        if($paramssld["ht_view2_filtering_float"] == 'top' && ($paramssld["ht_view2_sorting_float"] == 'left') ) {  if($portfolioShowSorting == 'on') { echo "margin-left: 31%;"; } else echo "margin-left: 1%"; } 
    ?>
}

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?> ul li {
    list-style-type: none;
    <?php
        if($filtering_block_width == "100%") { echo "float:left !important;margin: 4px 8px 4px 0px !important;"; }
        if($left_to_top == "ok") { echo "float:left !important;"; }
        if($paramssld["ht_view2_filtering_float"] == "left" || $paramssld["ht_view2_filtering_float"] == "right")
        { echo 'border-bottom: 1px solid #ccc;'; }
        else echo "border: 1px solid #ccc;";
    ?>
}

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?> ul li a {
    font-size:<?php echo $paramssld["ht_view2_filterbutton_font_size"];?>px !important;
    color:#<?php echo $paramssld["ht_view2_filterbutton_font_color"];?> !important;
    background-color: #<?php echo $paramssld["ht_view2_filterbutton_background_color"];?> !important;
    border-radius: <?php echo $paramssld["ht_view2_filterbutton_border_radius"];?>px;
    padding: 3px;
    display: block;
    text-decoration: none;
}

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?>  ul li a:hover {
    color:#<?php echo $paramssld["ht_view2_filterbutton_hover_font_color"];?> !important;
    background-color: #<?php echo $paramssld["ht_view2_filterbutton_hover_background_color"];?> !important;
    cursor: pointer
}
#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?> ul li.active a,
#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?> ul li.active a:link,
#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?> ul li.active a:visited,
#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?>  ul li.active a:hover,
#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?>  ul li.active a:focus,
#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?>  ul li.active a:active {
    color:#<?php echo $paramssld["ht_view2_filterbutton_hover_font_color"];?> !important;
    background-color: #<?php echo $paramssld["ht_view2_filterbutton_hover_background_color"];?> !important;
    cursor: pointer;
}
#huge_it_portfolio_content_<?php echo $portfolioID; ?> section {
    position:relative;
    display:block;
}

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_container_<?php echo $portfolioID; ?> {
<?php if($paramssld["ht_view2_sorting_float"] == "left" && $paramssld["ht_view2_filtering_float"] == "right" ||
         $paramssld["ht_view2_sorting_float"] == "right" && $paramssld["ht_view2_filtering_float"] == "left")
       { echo "margin: 0px auto;"; }
       if((($paramssld["ht_view2_filtering_float"] == "left" || $paramssld["ht_view2_filtering_float"] == "right" && $paramssld["ht_view2_sorting_float"] == "top") || ($paramssld["ht_view2_sorting_float"] == "left" || $paramssld["ht_view2_sorting_float"] == "right" && $paramssld["ht_view2_filting_float"] == "top")) && $portfolioShowFiltering == "on" && $portfolioShowSorting == "on")
       {
?>
        width: <?php echo $width_middle; ?> !important;
 <?php } ?>
}

.load_more4 {
    margin: 10px 0;
    position:relative;
    text-align:<?php if($paramssld['ht_view2_loadmore_position'] == 'left') {echo 'left';}
			elseif ($paramssld['ht_view2_loadmore_position'] == 'center') { echo 'center'; }
			elseif($paramssld['ht_view2_loadmore_position'] == 'right') { echo 'right'; }?>;

    width:100%;


}

.load_more_button4 {
    border-radius: 10px;
    display:inline-block;
    padding:5px 15px;
    font-size:<?php echo $paramssld['ht_view2_loadmore_fontsize']; ?>px !important;;
    color:<?php echo '#'.$paramssld['ht_view2_loadmore_font_color']; ?> !important;;
    background:<?php echo '#'.$paramssld['ht_view2_button_color']; ?> !important;
    cursor:pointer;

}
.load_more_button4:hover{
    color:<?php echo '#'.$paramssld['ht_view2_loadmore_font_color_hover']; ?> !important;
    background:<?php echo '#'.$paramssld['ht_view2_button_color_hover']; ?> !important;
}

.loading4 {
    display:none;
}
.paginate4{
    font-size:<?php echo $paramssld['ht_view2_paginator_fontsize']; ?>px !important;
    color:<?php echo '#'.$paramssld['ht_view2_paginator_color']; ?> !important;
    text-align: <?php echo $paramssld['ht_view2_paginator_position']; ?>;
    margin-top: 25px;
}
.paginate4 a{
    border-bottom: none !important;
    color: #<?php echo $paramssld['ht_view2_paginator_icon_color']; ?> !important;
    font-size:<?php echo $paramssld['ht_view2_paginator_icon_size']; ?>px !important;
}
#huge_it_portfolio_popup_list_<?php echo $portfolioID; ?> .heading-navigation_<?php echo $portfolioID; ?> .left-change, #huge_it_portfolio_popup_list_<?php echo $portfolioID; ?> .heading-navigation_<?php echo $portfolioID; ?> .right-change{
    width: 40px;
    height: 39px;
    font-size: 25px;
    display: inline-block;
    text-align: center;
    border: 1px solid #eee;
    border-bottom: none;
    border-top: none;
}
#huge_it_portfolio_popup_list_<?php echo $portfolioID; ?> .heading-navigation_<?php echo $portfolioID; ?> .right-change{
    position: relative;
    margin-left: -6px;
}
#huge_it_portfolio_popup_list_<?php echo $portfolioID; ?> .heading-navigation_<?php echo $portfolioID; ?> .right-change:hover, #huge_it_portfolio_popup_list_<?php echo $portfolioID; ?> .heading-navigation_<?php echo $portfolioID; ?> .left-change:hover{
    background: #ddd;
    border-color: #ccc;
    color: #000 !important;
    cursor: pointer;
}

#huge_it_portfolio_popup_list_<?php echo $portfolioID; ?> .heading-navigation_<?php echo $portfolioID; ?> .right-change a, #huge_it_portfolio_popup_list_<?php echo $portfolioID; ?> .heading-navigation_<?php echo $portfolioID; ?> .left-change a{
    position: absolute;
    transform: translate(-50%, 50%) !important;
    color: #777;
    text-decoration: none;
    width: 12px;
    height: 17px;
    display: inline-block;
}
</style>

<section id="huge_it_portfolio_content_<?php echo $portfolioID; ?>">
    <?php if($portfolioShowSorting == "on"){ ?>
          <div id="huge_it_portfolio_options_<?php echo $portfolioID; ?>" class="">
            <ul id="sort-by" class="option-set clearfix" data-option-key="sortBy">
                <li><a href="#sortBy=original-order" data-option-value="original-order" class="selected" data><?php echo $paramssld["ht_view2_sorting_name_by_default"]; ?></a></li>
                <li><a href="#sortBy=id" data-option-value="id"><?php echo $paramssld["ht_view2_sorting_name_by_id"]; ?></a></li>
                <li><a href="#sortBy=symbol" data-option-value="symbol"><?php echo $paramssld["ht_view2_sorting_name_by_name"]; ?></a></li>
                <li id="shuffle"><a href='#shuffle'><?php echo $paramssld["ht_view2_sorting_name_by_random"]; ?></a></li>
            </ul>
              
            <ul id="sort-direction" class="option-set clearfix" data-option-key="sortAscending">
                <li><a href="#sortAscending=true" data-option-value="true" class="selected"><?php echo $paramssld["ht_view2_sorting_name_by_asc"]; ?></a></li>
                <li><a href="#sortAscending=false" data-option-value="false"><?php echo $paramssld["ht_view2_sorting_name_by_desc"]; ?></a></li>
            </ul>
          </div>
  <?php }
   if($portfolioShowFiltering == "on")
      { ?>
         <div id="huge_it_portfolio_filters_<?php echo $portfolioID; ?>">
            <ul>
                <li rel="*"><a><?php echo $paramssld["ht_view2_cat_all"];?></a></li>
                <?php
                $portfolioCats = explode(",", $portfolioCats);
                foreach ($portfolioCats as $portfolioCatsValue) {
                    if(!empty($portfolioCatsValue))
                    {
                ?>
                <li rel=".<?php echo str_replace(" ","_",$portfolioCatsValue); ?>"><a><?php echo str_replace("_"," ",$portfolioCatsValue); ?></a></li>
                    <?php
                    }
                }
                ?>
            </ul>
        </div>
<?php } ?>
    <div id="huge_it_portfolio_container_<?php echo $portfolioID; ?>" class="super-list variable-sizes clearfix" <?php if($paramssld["ht_view2_sorting_float"] == "top" && $paramssld["ht_view2_filtering_float"] == "top") echo "style='clear: both;'";?> style="margin-top: 5px;">
        <?php
        $gallery = '';
        foreach($images as $image){
            $idofgallery=intval($image->portfolio_id);
        }

        $db = JFactory::getDBO();
        $query2 = $db->getQuery(true);
        $query2->select('*');
        $query2->from('#__huge_itportfolio_portfolios');
        $query2 -> where('id ='.$idofgallery);
        $query2 ->order('#__huge_itportfolio_portfolios.ordering asc');
        $db->setQuery($query2);
        $gallery = $db->loadObjectList();
        $pattern='/-/';
        $pID = '';
        $post = 0;
        foreach ($gallery as $gall) {
            global $post;
            $pID=$post;
            $disp_type=$gall->pagination_type;
            $count_page=$gall->count_into_page;
            if($count_page==0){
                $count_page=999;
            }elseif(preg_match($pattern, $count_page)){
                $count_page=preg_replace($pattern, '', $count_page);
            }
        }
        $num=$count_page;
        $total = intval(((count($images) - 1) / $num) + 1);
        if(isset($_GET['page-img'.$portfolioID.$pID])){
            $page = $_GET['page-img'.$portfolioID.$pID];
        }else{
            $page = '';

        }
        $page = intval($page);
        if(empty($page) or $page < 0) $page = 1;
        if($page > $total) $page = $total;
        $start = $page * $num - $num;
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__huge_itportfolio_images');
        $query -> where('portfolio_id ='.$idofgallery);
        $query ->order('#__huge_itportfolio_images.ordering asc');
        $db->setQuery($query,$start,$num);
        $page_images = $db->loadObjectList();
        if($disp_type=='show_all'){
            $count_page=9999;
        }

        $group_key1= 0;
        foreach($page_images as $key=>$row) {
            $group_key1++;
            $group_key = (string)$group_key1;
            $portfolioID1 = (string)$portfolioID;
            $group_key =$group_key."-".$portfolioID;
            $link = $row->sl_url;
            $descnohtml=strip_tags($row->description);
            $descnohtml = html_entity_decode($descnohtml);
            $result = substr($descnohtml, 0, 50);
            $catForFilter = explode(",", $row->category);
            $imgurl=explode(";",$row->image_url);
            $lighboxable = (count($imgurl) == 2)?"lighboxable":"dropdownable";
            ?>
            <input type="hidden" class="pagenum" value="1" />
            <input type="hidden" id="total" value="<?=$total; ?>" />
                      <div class="element_<?php echo $portfolioID; ?>  <?php foreach ($catForFilter as $catForFilterValue) { echo str_replace(" ","-",$catForFilterValue)." ";} ?>" tabindex="0" data-symbol="<?php echo $row->name; ?>" data-category="alkaline-earth">
                            <div class="image-block_<?php echo $portfolioID; ?>">
                                <?php $imgurl=explode(";",$row->image_url); ?>
                                    <?php 	
                                        if($row->image_url != ';'){
                                            switch($this->youtube_or_vimeo_portfolio($imgurl[0])) { 
                                                case 'image':		?>										  
                                <img alt="<?php echo $row->name; ?>" id="wd-cl-img<?php echo $key; ?>" src="<?php echo $imgurl[0]; ?>" />
                                                        <?php 
                                                                break;
                                                                case 'youtube':
                                                                $videourl=$this->get_video_id_from_url_portfolio($imgurl[0]);?>
                                                                <img alt="<?php echo $row->name; ?>" id="wd-cl-img<?php echo $key; ?>"  src="http://img.youtube.com/vi/<?php echo $videourl[0]; ?>/mqdefault.jpg"  />
                                                                <?php
                                                                break;
                                                                case 'vimeo':
                                                                $videourl=$this->get_video_id_from_url_portfolio($imgurl[0]);
                                                                $hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/".$videourl[0].".php"));
                                                                $imgsrc=$hash[0]['thumbnail_large'];
                                                        ?>
                                                                <img alt="<?php echo $row->name; ?>" src="<?php echo $imgsrc; ?>"/>
                                                                <?php break;

                                            }
                                        }else { ?>
                                            <img alt="<?php echo $row->name; ?>" id="wd-cl-img<?php echo $key; ?>" src="images/noimage.jpg" />
                                            <?php
                                        } ?>	
                                      <div class="image-overlay"><a href="#<?php echo $row->id; ?>"></a></div>
                            </div>
                            <div class="title-block_<?php echo $portfolioID; ?>">
                                <h3><?php echo $row->name; ?></h3>
                                    <?php if($paramssld["ht_view2_element_show_linkbutton"]=='on'  && $link != '' ){?>
                                            <div class="button-block"><a href="<?php echo $row->sl_url; ?>" <?php if ($row->link_target=="on"){echo 'target="_blank"';}?> ><?php echo $paramssld["ht_view2_element_linkbutton_text"]; ?></a></div>
                                    <?php } ?>
                            </div>
                      </div>	
                      <?php
              }?>
              <div style="clear:both;"></div>
        </div>

    <?php
    $a=$disp_type;
    if($a=='load_more'){
        $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
        $actual_link = $protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."";
        $pattern="/\?p=/";
        $pattern2="/&page-img[0-9]+=[0-9]+/";
        $pattern3="/\?page-img[0-9]+=[0-9]+/";
        if(preg_match($pattern, $actual_link)){

            if(preg_match($pattern2, $actual_link)){
                $actual_link=preg_replace($pattern2, '', $actual_link);
                header("Location:".$actual_link."");
                exit;

            }
        }elseif(preg_match($pattern3, $actual_link)){
            $actual_link=preg_replace($pattern3, '', $actual_link);
            header("Location:".$actual_link."");
            exit;

        }
        ?>
        <?php $paramssld['ht_view2_loading_type'] = $paramssld['slider_navigation_type']; ?>
        <div class="load_more4">
            <div class="load_more_button4"><?=$paramssld['ht_view2_loadmore_text']; ?></div>
            <div class="loading4"><img src="<?php echo JUri::root()."media/com_portfoliogallery/images/loading.white.gif";?>"></div>
            <input type="hidden" class="load-more-elements-count" value="<?php echo $count_page; ?>"/>


            <script>
                jQuery(document).ready(function(){
                    if(jQuery("#huge_it_portfolio_content_<?php echo $portfolioID; ?> .load_more_button4").length){
                        jQuery("#huge_it_portfolio_content_<?php echo $portfolioID; ?> .load_more_button4").on("click tap",function(){
                            if(parseInt(jQuery("#huge_it_portfolio_content_<?php echo $portfolioID; ?> .pagenum:last").val())<parseInt(jQuery("#huge_it_portfolio_container_<?php echo $portfolioID; ?> #total").val())) {
                                var pagenum = parseInt(jQuery("#huge_it_portfolio_content_<?php echo $portfolioID; ?> .pagenum:last").val()) + 1;
                                var perpage =<?=$count_page; ?>;
                                var galleryid="<?=$image->portfolio_id; ?>";
                                getresult(pagenum,perpage,galleryid);
                            }else{

                                jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?> .load_more_button4').hide();
                            }
                            return false;
                        });
                    }
                    if(parseInt(jQuery("#huge_it_portfolio_content_<?php echo $portfolioID; ?> .pagenum:last").val())==parseInt(jQuery("#huge_it_portfolio_container_<?php echo $portfolioID; ?> #total").val())) {
                        jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?> .load_more_button4').hide();
                    }
                    function getresult(pagenum,perpage,galleryid){
                        var data = {
                            action:'my_action',
                            post:"huge_it_portfolio_gallery_ajax2",
                            task:'load_portfolio_videos_lightbox',
                            page:pagenum,
                            perpage:perpage,
                            galleryid:galleryid,
                            level:<?php echo $level?>,
                        }
                        jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?> .loading4').show();

                        jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?> .load_more_button4').hide();

                        jQuery.post("<?php echo $path_menu?>components/com_portfoliogallery/ajax_url.php",data,function(response){
                            if(response.success){
                                var $objnewitems= jQuery(response.success);
                                jQuery('#huge_it_portfolio_container_<?php echo $portfolioID; ?>').append( $objnewitems ).hugeitmicro('reloadItems' ).hugeitmicro({ sortBy: 'original-order' }).hugeitmicro( 'reLayout' );
                                jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?> img').on('load',function(){

                                    //############# End


                                    jQuery(".group1").colorbox({rel:'group1'});
                                    jQuery(".youtube").colorbox({iframe:true, innerWidth:640, innerHeight:390});
                                    jQuery(".vimeo").colorbox({iframe:true, innerWidth:640, innerHeight:390});
                                    jQuery(".iframe").colorbox({iframe:true, width:"80%", height:"80%"});
                                    jQuery(".inline").colorbox({inline:true, width:"50%"});
                                    jQuery(".callbacks").colorbox({
                                        onOpen:function(){ alert('onOpen: colorbox is about to open'); },
                                        onLoad:function(){ alert('onLoad: colorbox has started to load the targeted content'); },
                                        onComplete:function(){ alert('onComplete: colorbox has displayed the loaded content'); },
                                        onCleanup:function(){ alert('onCleanup: colorbox has begun the close process'); },
                                        onClosed:function(){ alert('onClosed: colorbox has completely closed'); }
                                    });

                                    jQuery('.non-retina').colorbox({rel:'group5', transition:'none'})
                                    jQuery('.retina').colorbox({rel:'group5', transition:'none', retinaImage:true, retinaUrl:true});
                                    var defaultBlockWidth=<?php echo $paramssld['ht_view6_width']; ?>+20+<?php echo $paramssld['ht_view6_width']*2; ?>;
                                    jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?> .load_more_button4').show();
                                    jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?> .loading4').hide();
                                    if(parseInt(jQuery("#huge_it_portfolio_content_<?php echo $portfolioID; ?> .pagenum:last").val())==parseInt(jQuery("#huge_it_portfolio_container_<?php echo $portfolioID; ?> #total").val())) {
                                        jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?> .load_more_button4').hide();
                                    }
                                });


                            }else{
                                alert("no");
                            }
                        },"json");
                    }

                });
            </script>
        </div>
        <?php
    }elseif($a=='pagination'){
        ?>
        <div class="paginate4">
            <?php
            $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
            $actual_link = $protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."";


            $checkREQ='';
            $pattern="/\?p=/";
            $pattern2="/&page-img[0-9]+=[0-9]+/";
            if(preg_match($pattern, $actual_link)){

                if(preg_match($pattern2, $actual_link)){
                    $actual_link=preg_replace($pattern2, '', $actual_link);
                }
                $checkREQ=$actual_link.'&page-img'.$portfolioID.$pID;

            }else{
                $checkREQ='?page-img'.$portfolioID.$pID;

            }
            $pervpage='';
            if ($page != 1) $pervpage = '<a href= '.$checkREQ.'=1><i class="hugeiticons hugeiticons-fast-backward" ></i></a>  
                             <a href= '.$checkREQ.'='. ($page - 1) .'><i class="hugeiticons hugeiticons-chevron-left"></i></a> ';
            $nextpage='';
            if ($page != $total) $nextpage = '<a href= '.$checkREQ.'='. ($page + 1) .'><i class="hugeiticons hugeiticons-chevron-right"></i></a>  
                                  <a href= '.$checkREQ.'=' .$total. '><i class="hugeiticons hugeiticons-fast-forward" ></i></a>';
            echo $pervpage.$page.'/'.$total.$nextpage;

            ?>
        </div>
        <?php
    }
    ?>



</section>
<ul id="huge_it_portfolio_popup_list_<?php echo $portfolioID; ?>">
    <?php
    $changePopup=1;
    foreach($images as $key=>$row) {
        $imgurl=explode(";",$row->image_url);
        array_pop($imgurl);
        $link = $row->sl_url;
        $descnohtml=strip_tags($row->description);
        $descnohtml = html_entity_decode($descnohtml);
        $result = substr($descnohtml, 0, 50);
        ?>
            <li class="pupup-element" id="huge_it_portfolio_pupup_element_<?php echo $row->id; ?>">
                <div class="heading-navigation_<?php echo $portfolioID; ?>">
                    <div style="display: inline-block; float: left;">
                        <div class="left-change" ><a href="#<?php echo $changePopup - 1; ?>" data-popupid="#<?php echo $row->id; ?>"><</a></div>
                        <div class="right-change" ><a href="#<?php echo $changePopup + 1; ?>" data-popupid="#<?php echo $row->id; ?>">></a></div>
                    </div>
                    <?php $changePopup=$changePopup+1; ?>
                    <a href="#close" class="close"></a>
                    <div style="clear:both;"></div>
                </div>
                <div class="popup-wrapper_<?php echo $portfolioID; ?>">
                    <div class="image-block_<?php echo $portfolioID; ?>">
                        <?php 	
                            if($row->image_url != ';'){ 
                                switch($this->youtube_or_vimeo_portfolio($imgurl[0])) {
                                    case 'image':
                                    ?>
                        <img alt="<?php echo $row->name; ?>" id="wd-cl-img<?php echo $key; ?>" src="<?php echo $imgurl[0]; ?>" />
                                    <?php 
                                        break;
                                        case 'youtube':
                                        $videourl=$this->get_video_id_from_url_portfolio($imgurl[0]);//var_dump($videourl[0]);?>
                                        <iframe src="//www.youtube.com/embed/<?php echo $videourl[0]; ?>?modestbranding=1&showinfo=0" frameborder="0" allowfullscreen></iframe>
                                    <?php 
                                            break;
                                            case 'vimeo':
                                            $videourl=$this->get_video_id_from_url_portfolio($imgurl[0]);//var_dump($videourl[0]);?>
                                            <iframe src="//player.vimeo.com/video/<?php echo $videourl[0]; ?>?title=0&amp;byline=0&amp;portrait=0" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                                    <?php break;
                                    }
                                    }
                                    else { ?>
                                    <img alt="<?php echo $row->name; ?>" id="wd-cl-img<?php echo $key; ?>" src="images/noimage.jpg" />
                                    <?php
                                    } ?>					
				</div>
				<div class="right-block">
                                    <?php if($paramssld["ht_view2_show_popup_title"]=='on'){?><h3 class="title"><?php echo $row->name; ?></h3><?php } ?>
					<?php if($paramssld["ht_view2_thumbs_position"]=='before' and $paramssld["ht_view2_show_thumbs"] == 'on' && count($imgurl)!=1){?>
					<div><ul class="thumbs-list_<?php echo $portfolioID; ?>">
                                            <?php   
                                                foreach($imgurl as $key=>$img){?>
                                                    <li>
                                                        <?php
                                                            switch($this->youtube_or_vimeo_portfolio($img)) {
                                                                case 'image':?>
                                                                    <a href="<?php echo $row->sl_url; ?>" class="img-thumb" title="<?php echo $row->name; ?>"><img src="<?php echo $img; ?>"></a>
									<?php
                                                                            break;
                                                                            case 'youtube':
                                                                            $videourl=$this->get_video_id_from_url_portfolio($img);?>
                                                                            <a href="https://www.youtube.com/embed/<?php echo $videourl[0];?>" class="video-thumb"  title="<?php echo $row->name; ?>" style="position:relative">
                                                                            <img src="http://img.youtube.com/vi/<?php echo $videourl[0]; ?>/mqdefault.jpg"><div class="play-icon youtube-icon" title="<?php echo $videourl[0]; ?>"></div>
                                                                            </a>
								<?php	break;
                                                                            case 'vimeo':
                                                                            $videourl=$this->get_video_id_from_url_portfolio($img);	
                                                                            $hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/".$videourl[0].".php"));
                                                                            $imgsrc=$hash[0]['thumbnail_large'];
                                                                            ?>
                                                                            <a class=" video-thumb" href="http://player.vimeo.com/video/<?php echo $videourl[0]; ?>" title="<?php echo $row->name; ?>"  style="position:relative">
                                                                                    <img src="<?php echo $imgsrc; ?>" alt="<?php echo $row->name; ?>" /><div class="play-icon vimeo-icon"  title="<?php echo $videourl[0]; ?>"></div>
                                                                            </a>
									<?php
                                                                            break;
									}
									?>
									</li>
								<?php 
								} ?>
					</ul></div>
					<?php } ?>
					
					<?php if($paramssld["ht_view2_show_description"]=='on'){?><div class="description"><?php echo $row->description; ?></div><?php } ?>
					
					<?php if($paramssld["ht_view2_thumbs_position"]=='after' and $paramssld["ht_view2_show_thumbs"] == 'on'  && count($imgurl)!=1){?>
					<div><ul class="thumbs-list_<?php echo $portfolioID; ?>">
                                            <?php   
                                                foreach($imgurl as $key=>$img){?>
                                                    <li>
                                                        <?php
                                                            switch($this->youtube_or_vimeo_portfolio($img)) {
                                                                case 'image':?>
                                                                    <a href="<?php echo html_entity_decode($row->sl_url); ?>" class="img-thumb" title="<?php echo html_entity_decode($row->name); ?>"><img src="<?php echo $img; ?>"></a>
									<?php
                                                                            break;
                                                                            case 'youtube':
                                                                            $videourl=$this->get_video_id_from_url_portfolio($img);?>
                                                                            <a href="https://www.youtube.com/embed/<?php echo $videourl[0];?>" class=" video-thumb"  title="<?php echo $row->name; ?>" style="position:relative">
                                                                            <img src="http://img.youtube.com/vi/<?php echo $videourl[0]; ?>/mqdefault.jpg"><div class="play-icon youtube-icon" title="<?php echo $videourl[0]; ?>"></div>
                                                                            </a>
								<?php	break;
                                                                            case 'vimeo':
                                                                            $videourl=$this->get_video_id_from_url_portfolio($img);	
                                                                            $hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/".$videourl[0].".php"));
                                                                            $imgsrc=$hash[0]['thumbnail_large'];
                                                                            ?>
                                                                            <a class="video-thumb" href="http://player.vimeo.com/video/<?php echo $videourl[0]; ?>" title="<?php echo $row->name; ?>"  style="position:relative">
                                                                                <img src="<?php echo $imgsrc; ?>" alt="<?php echo $row->name; ?>" /><div class="play-icon vimeo-icon"  title="<?php echo $videourl[0]; ?>"></div>
                                                                            </a>
									<?php
                                                                            break;
										
									}
									?>
									</li>
								<?php 
								} ?>
					</ul></div>
					<?php } ?>
					
					<?php if($paramssld["ht_view2_show_popup_linkbutton"]=='on' && $link != ''){?>
						<div class="button-block">
						<a href="<?php echo $link; ?>"  <?php if ($row->link_target=="on"){echo 'target="_blank"';}?>><?php echo $paramssld["ht_view2_popup_linkbutton_text"]; ?></a>
						</div>
					<?php } ?>
					<div style="clear:both;"></div>
				</div>
				<div style="clear:both;"></div>
			</div>
		</li>
		<?php
	}?>
</ul>

<script>

</script>

<?php	  
    break;
    ////////////////////////////// VIEW 3 FullWidth ////////////////////////////////////////////// 
    case 3:
?>
<?php
    if($paramssld["ht_view3_sorting_float"] == "left" && $paramssld["ht_view3_filtering_float"] == "right" ||
       $paramssld["ht_view3_sorting_float"] == "right" && $paramssld["ht_view3_filtering_float"] == "left" ||
       $paramssld["ht_view3_sorting_float"] == $paramssld["ht_view3_filtering_float"])
       { $sorting_block_width ="20%"; $filtering_block_width ="20%"; $middle_with = "56%"; }
    else if($paramssld["ht_view3_sorting_float"] == "left" || $paramssld["ht_view3_sorting_float"] == "right" && $paramssld["ht_view3_filtering_float"] == "top")
       { $sorting_block_width ="30%"; $filtering_block_width ="100%"; $paramssld["ht_view3_filtering_float"] = "none"; $width_middle = "65%"; }
    else if($paramssld["ht_view3_filtering_float"] == "left" || $paramssld["ht_view3_filtering_float"] == "right" && $paramssld["ht_view3_sorting_float"] == "top")
       { $sorting_block_width ="100%"; $filtering_block_width ="30%"; $paramssld["ht_view3_sorting_float"] = "none"; $width_middle = "65%"; }
    if($paramssld["ht_view3_sorting_float"] == "top" && $paramssld["ht_view3_filtering_float"] == "top")
       { $sorting_block_width ="100%"; $filtering_block_width ="100%"; $left_to_top = "ok"; }
?>
<style type="text/css"> 
/***<add>***/
.element_<?php echo $portfolioID; ?> .play-icon.youtube-icon  {
    background: url(<?php echo JUri::root().'media/com_portfoliogallery/images/play.youtube.png';?>) center center no-repeat;
	background-size: 30% 30%;
}
.element_<?php echo $portfolioID; ?> .play-icon.vimeo-icon  {
	background: url(<?php echo JUri::root().'media/com_portfoliogallery/play.vimeo.png';?>) center center no-repeat;
	background-size: 30% 30%;
}
.element_<?php echo $portfolioID; ?> .play-icon {
    position: absolute;
    top: 0px;
    left: 0px;
    width: 100%;
    height: 100%;
}
.add-H-relative {
	position: relative;
}	
.add-H-block {
	display: block;
}		
/***</add>***/
.element_<?php echo $portfolioID; ?> {
	position: relative;
	width:93%; 
	margin:5px 0px 5px 0px;
	padding:2%;
	clear:both;
	overflow: hidden;
	border:<?php echo $paramssld['ht_view3_element_border_width']; ?>px solid #<?php echo $paramssld['ht_view3_element_border_color']; ?>;
	background:#<?php echo $paramssld['ht_view3_element_background_color']; ?>;
}

.element_<?php echo $portfolioID; ?> > div {
	display:table-cell;
}

.element_<?php echo $portfolioID; ?> div.left-block_<?php echo $portfolioID; ?> {
	padding-right:10px;
}

.element_<?php echo $portfolioID; ?> div.left-block_<?php echo $portfolioID; ?> .main-image-block_<?php echo $portfolioID; ?> {
	clear:both;
	width:<?php echo $paramssld['ht_view3_mainimage_width']; ?>px; 
}

.element_<?php echo $portfolioID; ?> div.left-block_<?php echo $portfolioID; ?> .main-image-block_<?php echo $portfolioID; ?> img {
	margin:0px !important;
	padding:0px !important;
	width:<?php echo $paramssld['ht_view3_mainimage_width']; ?>px !important; 
	height:auto;
}

.element_<?php echo $portfolioID; ?> div.left-block_<?php echo $portfolioID; ?> .thumbs-block {
	position:relative;
	margin-top:10px;
}

.element_<?php echo $portfolioID; ?> div.left-block_<?php echo $portfolioID; ?> .thumbs-block ul {
	width:<?php echo $paramssld['ht_view3_mainimage_width']; ?>px; 
	height:auto;
	display:table;
	margin:0px;
	padding:0px;
	list-style:none;
}

.element_<?php echo $portfolioID; ?> div.left-block_<?php echo $portfolioID; ?> .thumbs-block ul li {
	margin:2px 3px 0px 2px;
	padding:0px;
	width:<?php echo $paramssld['ht_view3_thumbs_width']; ?>px; 
	height:<?php echo $paramssld['ht_view3_thumbs_height']; ?>px; 
	float:left;
}

.element_<?php echo $portfolioID; ?> div.left-block_<?php echo $portfolioID; ?> .thumbs-block ul li a {
	display:block;
	width:<?php echo $paramssld['ht_view3_thumbs_width']; ?>px; 
	height:<?php echo $paramssld['ht_view3_thumbs_height']; ?>px; 
}

.element_<?php echo $portfolioID; ?> div.left-block_<?php echo $portfolioID; ?> .thumbs-block ul li a img {
	margin:0px !important;
	padding:0px !important;
	width:<?php echo $paramssld['ht_view3_thumbs_width']; ?>px; 
	height:<?php echo $paramssld['ht_view3_thumbs_height']; ?>px; 
}

.element_<?php echo $portfolioID; ?> div.right-block {
	vertical-align:top;
}

.element_<?php echo $portfolioID; ?> div.right-block > div {
	width:100%;
	padding-bottom:10px;
	margin-top:10px;
	<?php if($paramssld['ht_view3_show_separator_lines']=="on") {?>
		background:url('<?php echo JUri::root().'media/com_portfoliogallery/images/divider.line.png'; ?>') center bottom repeat-x;
	<?php } ?>	
}

.element_<?php echo $portfolioID; ?> div.right-block > div:last-child {
	background:none;
}

.element_<?php echo $portfolioID; ?> div.right-block .title-block_<?php echo $portfolioID; ?>  {
	margin-top:3px;
}

.element_<?php echo $portfolioID; ?> div.right-block .title-block_<?php echo $portfolioID; ?> h3 {
	margin:0px;
	padding:0px;
	font-weight:normal;
	font-size:<?php echo $paramssld['ht_view3_title_font_size']; ?>px !important;
	line-height:<?php echo $paramssld['ht_view3_title_font_size']+4; ?>px !important;
	color:#<?php echo $paramssld['ht_view3_title_font_color']; ?>;
}

.element_<?php echo $portfolioID; ?> div.right-block .description-block_<?php echo $portfolioID; ?> p,.element_<?php echo $portfolioID; ?> div.right-block .description-block_<?php echo $portfolioID; ?> * {
	margin:0px;
	padding:0px;
	font-weight:normal;
	font-size:<?php echo $paramssld['ht_view3_description_font_size']; ?>px;
	color:#<?php echo $paramssld['ht_view3_description_color']; ?>;
}


.element_<?php echo $portfolioID; ?> div.right-block .description-block_<?php echo $portfolioID; ?> h1,
.element_<?php echo $portfolioID; ?> div.right-block .description-block_<?php echo $portfolioID; ?> h2,
.element_<?php echo $portfolioID; ?> div.right-block .description-block_<?php echo $portfolioID; ?> h3,
.element_<?php echo $portfolioID; ?> div.right-block .description-block_<?php echo $portfolioID; ?> h4,
.element_<?php echo $portfolioID; ?> div.right-block .description-block_<?php echo $portfolioID; ?> h5,
.element_<?php echo $portfolioID; ?> div.right-block .description-block_<?php echo $portfolioID; ?> h6,
.element_<?php echo $portfolioID; ?> div.right-block .description-block_<?php echo $portfolioID; ?> p, 
.element_<?php echo $portfolioID; ?> div.right-block .description-block_<?php echo $portfolioID; ?> strong,
.element_<?php echo $portfolioID; ?> div.right-block .description-block_<?php echo $portfolioID; ?> span {
	padding:2px !important;
	margin:0px !important;
}

.element_<?php echo $portfolioID; ?> div.right-block .description-block_<?php echo $portfolioID; ?> ul,
.element_<?php echo $portfolioID; ?> div.right-block .description-block_<?php echo $portfolioID; ?> li {
	padding:2px 0px 2px 5px;
	margin:0px 0px 0px 8px;
}

.element_<?php echo $portfolioID; ?> .button-block {
	position:relative;
}

.element_<?php echo $portfolioID; ?> div.right-block .button-block a,.element_<?php echo $portfolioID; ?> div.right-block .button-block a:link,.element_<?php echo $portfolioID; ?> div.right-block .button-block a:visited {
	position:relative;
	display:inline-block;
	padding:6px 12px;
	background:#<?php echo $paramssld["ht_view3_linkbutton_background_color"];?>;
	color:#<?php echo $paramssld["ht_view3_linkbutton_color"];?>;
	font-size:<?php echo $paramssld["ht_view3_linkbutton_font_size"];?>;
	text-decoration:none;
}

.element_<?php echo $portfolioID; ?> div.right-block .button-block a:hover,.pupup-elemen.element div.right-block .button-block a:focus,.element_<?php echo $portfolioID; ?> div.right-block .button-block a:active {
	background:#<?php echo $paramssld["ht_view3_linkbutton_background_hover_color"];?>;
	color:#<?php echo $paramssld["ht_view3_linkbutton_font_hover_color"];?>;
}



@media only screen and (max-width: 767px) {
	
	.element_<?php echo $portfolioID; ?> > div {
		display:block;
		width:100%;
		clear:both;
	}

	.element_<?php echo $portfolioID; ?> div.left-block_<?php echo $portfolioID; ?> {
		padding-right:0px;
	}

	.element_<?php echo $portfolioID; ?> div.left-block_<?php echo $portfolioID; ?> .main-image-block_<?php echo $portfolioID; ?> {
		clear:both;
		width:100%; 
	}

	.element_<?php echo $portfolioID; ?> div.left-block_<?php echo $portfolioID; ?> .main-image-block_<?php echo $portfolioID; ?> img {
		margin:0px !important;
		padding:0px !important;
		width:100% !important;  
		height:auto;
	}

	.element_<?php echo $portfolioID; ?> div.left-block_<?php echo $portfolioID; ?> .thumbs-block ul {
		width:100%; 
	}
}

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_options_<?php echo $portfolioID; ?> {
    <?php 
    if($paramssld["ht_view3_filtering_float"] == 'left' && $paramssld["ht_view3_sorting_float"] == 'none') {  if($portfolioShowFiltering == "on") { echo "margin-left: 30%;"; } else { echo "margin-left: 0%;"; }   } ?>
    overflow: hidden;
    display:block;
    margin-top: 5px;
    float: <?php echo $paramssld["ht_view3_sorting_float"]; ?>;
    width: <?php echo $sorting_block_width; ?>;
}

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_options_<?php echo $portfolioID; ?> ul {
  margin: 0px !important;
  padding: 0px !important;
  list-style: none;
<?php if($paramssld["ht_view3_sorting_float"] == 'top') {
      echo "float:left;margin-left:1%;";
      } ?>
}

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?> ul {
  margin: 0px !important;
  padding: 0px !important;
  overflow: hidden;
  <?php if($paramssld["ht_view3_filtering_float"] == 'top') {
      echo "float:left;margin-left:1%;";
      } ?>
}

<?php if($paramssld["ht_view3_sorting_float"] == 'none') { ?>
            #huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_options_<?php echo $portfolioID; ?> ul {
                float: left;
            }
    <?php } ?>

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_options_<?php echo $portfolioID; ?> ul li {
    border-radius: <?php echo $paramssld["ht_view3_sortbutton_border_radius"];?>px;
    list-style-type: none;
    margin: 0px !important;
	padding: 0;
    <?php
        if($sorting_block_width == "100%" ) {
            echo "float:left !important;margin: 4px 8px 4px 0px !important;";
        }
        if($left_to_top == "ok")
        { echo "float:left !important;"; }
        if($paramssld["ht_view3_sorting_float"] == "left" || $paramssld["ht_view3_sorting_float"] == "right")
        { echo 'border-bottom: 1px solid #ccc;'; }
        else
        { echo 'border: 1px solid #ccc;'; }
    ?>
}

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_options_<?php echo $portfolioID; ?> ul li a {
    background-color: #<?php echo $paramssld["ht_view3_sortbutton_background_color"];?> !important;
    font-size:<?php echo $paramssld["ht_view3_sortbutton_font_size"];?>px !important;
    color:#<?php echo $paramssld["ht_view3_sortbutton_font_color"];?> !important;
    text-decoration: none;
    cursor: pointer;
    margin: 0px !important;
    display: block;
    padding:3px;
}

/*#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_options_<?php echo $portfolioID; ?> ul li:hover {
    
}*/

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_options_<?php echo $portfolioID; ?> ul li a:hover {
    background-color: #<?php echo $paramssld["ht_view3_sortbutton_hover_background_color"];?> !important;
    color:#<?php echo $paramssld["ht_view3_sortbutton_hover_font_color"];?> !important;
    cursor: pointer;
}

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?> {
    margin-top: 5px;
    float: <?php echo $paramssld["ht_view3_filtering_float"]; ?>;
   width: <?php echo $filtering_block_width; ?>;
    <?php
        if ($paramssld["ht_view3_show_filtering"] == 'off') echo "display:none;";
        if($paramssld["ht_view3_filtering_float"] == 'none' && ($paramssld["ht_view3_sorting_float"] == 'left') ) {  if($portfolioShowSorting == 'on') { echo "margin-left: 30%;"; } else echo "margin-left: 0%"; } 
//        if(($paramssld["ht_view3_filtering_float"] == 'none' && ($paramssld["ht_view3_sorting_float"] == 'right')) || ($sorting_block_width == '100%' && $filtering_block_width == "100%")) { echo "margin-left: 1%";}
    ?>
}

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?> ul li {
    list-style-type: none;
    <?php
        if($filtering_block_width == "100%") { echo "float:left !important;margin: 4px 8px 4px 0px !important;"; }
        if($left_to_top == "ok") { echo "float:left !important;"; }
        if($paramssld["ht_view3_filtering_float"] == "left" || $paramssld["ht_view3_filtering_float"] == "right")
        { echo 'border-bottom: 1px solid #ccc;'; }
        else echo "border: 1px solid #ccc;";
    ?>
}

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?> ul li a {
    font-size:<?php echo $paramssld["ht_view3_filterbutton_font_size"];?>px !important;
    color:#<?php echo $paramssld["ht_view3_filterbutton_font_color"];?> !important;
    background-color: #<?php echo $paramssld["ht_view3_filterbutton_background_color"];?> !important;
    border-radius: <?php echo $paramssld["ht_view3_filterbutton_border_radius"];?>px;
    padding: 3px;
    display: block;
    text-decoration: none;
}

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?>  ul li a:hover {
    color:#<?php echo $paramssld["ht_view3_filterbutton_hover_font_color"];?> !important;
    background-color: #<?php echo $paramssld["ht_view3_filterbutton_hover_background_color"];?> !important;
    cursor: pointer;
}
#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?> ul li.active a,
#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?> ul li.active a:link,
#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?> ul li.active a:visited,
#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?>  ul li.active a:hover,
#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?>  ul li.active a:focus,
#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?>  ul li.active a:active {
    color:#<?php echo $paramssld["ht_view3_filterbutton_hover_font_color"];?> !important;
    background-color: #<?php echo $paramssld["ht_view3_filterbutton_hover_background_color"];?> !important;
    cursor: pointer;
}
#huge_it_portfolio_content_<?php echo $portfolioID; ?> section {
    position:relative;
    display:block;
}

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_container_<?php echo $portfolioID; ?> {

    <?php
        if($paramssld["ht_view3_sorting_float"] == "left" && $paramssld["ht_view3_filtering_float"] == "right" ||
           $paramssld["ht_view3_sorting_float"] == "right" && $paramssld["ht_view3_filtering_float"] == "left")
        { ?>
            margin-left: 21%;
        <?php }
  if(((($paramssld["ht_view3_filtering_float"] == "left" || $paramssld["ht_view3_filtering_float"] == "right") && $paramssld["ht_view3_sorting_float"] == "top") || (($paramssld["ht_view3_sorting_float"] == "left" || $paramssld["ht_view3_sorting_float"] == "right") && $paramssld["ht_view3_filtering_float"] == "top")) && $portfolioShowFiltering == "on" && $portfolioShowSorting == "on")
       { ?>
        width: <?php echo $width_middle; ?> !important;
 <?php } ?>
}
.load_more4 {
    margin: 10px 0;
    position:relative;
    text-align:<?php if($paramssld['ht_view3_loadmore_position'] == 'left') {echo 'left';}
			elseif ($paramssld['ht_view3_loadmore_position'] == 'center') { echo 'center'; }
			elseif($paramssld['ht_view3_loadmore_position'] == 'right') { echo 'right'; }?>;

    width:100%;


}

.load_more_button4 {
    border-radius: 10px;
    display:inline-block;
    padding:5px 15px;
    font-size:<?php echo $paramssld['ht_view3_loadmore_fontsize']; ?>px !important;;
    color:<?php echo '#'.$paramssld['ht_view3_loadmore_font_color']; ?> !important;;
    background:<?php echo '#'.$paramssld['ht_view3_button_color']; ?> !important;
    cursor:pointer;

}
.load_more_button4:hover{
    color:<?php echo '#'.$paramssld['ht_view3_loadmore_font_color_hover']; ?> !important;
    background:<?php echo '#'.$paramssld['ht_view3_button_color_hover']; ?> !important;
}

.loading4 {
    display:none;
}
.paginate4{
    font-size:<?php echo $paramssld['ht_view3_paginator_fontsize']; ?>px !important;
    color:<?php echo '#'.$paramssld['ht_view3_paginator_color']; ?> !important;
    text-align: <?php echo $paramssld['ht_view3_paginator_position']; ?>;
    margin-top: 25px;
}
.paginate4 a{
    border-bottom: none !important;
    color: #<?php echo $paramssld['ht_view3_paginator_icon_color']; ?> !important;
    font-size:<?php echo $paramssld['ht_view3_paginator_icon_size']; ?>px !important;
}
.icon-style4{
    font-size: <?php echo $paramssld['ht_view3_paginator_icon_size']; ?>px !important;
    color:<?php echo '#'.$paramssld['ht_view3_paginator_icon_color']; ?> !important;
}
.clear{
    clear:both;
}



</style>

<section id="huge_it_portfolio_content_<?php echo $portfolioID; ?>"> 
    
     <?php if($portfolioShowSorting == "on") { ?>
        <div id="huge_it_portfolio_options_<?php echo $portfolioID; ?>" class="">
            <ul id="sort-by" class="option-set clearfix" data-option-key="sortBy">
                <li><a href="#sortBy=original-order" data-option-value="original-order" class="selected" data><?php echo $paramssld["ht_view3_sorting_name_by_default"]; ?></a></li>
                <li><a href="#sortBy=id" data-option-value="id"><?php echo $paramssld["ht_view3_sorting_name_by_id"]; ?></a></li>
                <li><a href="#sortBy=symbol" data-option-value="symbol"><?php echo $paramssld["ht_view3_sorting_name_by_name"]; ?></a></li>
                <li id="shuffle"><a href='#shuffle'><?php echo $paramssld["ht_view3_sorting_name_by_random"]; ?></a></li>
            </ul>
              
            <ul id="sort-direction" class="option-set clearfix" data-option-key="sortAscending">
                <li><a href="#sortAscending=true" data-option-value="true" class="selected"><?php echo $paramssld["ht_view3_sorting_name_by_asc"]; ?></a></li>
                <li><a href="#sortAscending=false" data-option-value="false"><?php echo $paramssld["ht_view3_sorting_name_by_desc"]; ?></a></li>
            </ul>
          </div>
  <?php }
    if($portfolioShowFiltering == "on") { ?>
        <div id="huge_it_portfolio_filters_<?php echo $portfolioID; ?>">
            <ul>
                <li rel="*"><a><?php echo $paramssld["ht_view3_cat_all"];?></a></li>
                <?php
                $portfolioCats = explode(",", $portfolioCats);
                foreach ($portfolioCats as $portfolioCatsValue) {
                    if(!empty($portfolioCatsValue))
                    {
                ?>
                <li rel=".<?php echo str_replace(" ","_",$portfolioCatsValue); ?>"><a><?php echo str_replace("_"," ",$portfolioCatsValue); ?></a></li>
                    <?php
                    }
                }
                ?>
            </ul>
        </div>
<?php } ?> 
<div id="huge_it_portfolio_container_<?php echo $portfolioID; ?>" class="super-list variable-sizes clearfix" <?php if($paramssld["ht_view3_sorting_float"] == "top" && $paramssld["ht_view3_filtering_float"] == "top") echo "style='clear: both;'";?>>
    <?php
    $gallery = '';
    foreach($images as $image){
        $idofgallery=intval($image->portfolio_id);
    }

    $db = JFactory::getDBO();
    $query2 = $db->getQuery(true);
    $query2->select('*');
    $query2->from('#__huge_itportfolio_portfolios');
    $query2 -> where('id ='.$idofgallery);
    $query2 ->order('#__huge_itportfolio_portfolios.ordering asc');
    $db->setQuery($query2);
    $gallery = $db->loadObjectList();
    $pattern='/-/';
    $pID = '';
    $post = 0;
    foreach ($gallery as $gall) {
        global $post;
        $pID=$post;
        $disp_type=$gall->pagination_type;
        $count_page=$gall->count_into_page;
        if($count_page==0){
            $count_page=999;
        }elseif(preg_match($pattern, $count_page)){
            $count_page=preg_replace($pattern, '', $count_page);
        }
    }
    $num=$count_page;
    $total = intval(((count($images) - 1) / $num) + 1);
    if(isset($_GET['page-img'.$portfolioID.$pID])){
        $page = $_GET['page-img'.$portfolioID.$pID];
    }else{
        $page = '';

    }
    $page = intval($page);
    if(empty($page) or $page < 0) $page = 1;
    if($page > $total) $page = $total;
    $start = $page * $num - $num;
    $query = $db->getQuery(true);
    $query->select('*');
    $query->from('#__huge_itportfolio_images');
    $query -> where('portfolio_id ='.$idofgallery);
    $query ->order('#__huge_itportfolio_images.ordering asc');
    $db->setQuery($query,$start,$num);
    $page_images = $db->loadObjectList();
    if($disp_type=='show_all'){

        $page_images=$images;
        $count_page=9999;
    }

    $group_key1= 0;
    foreach($page_images as $key=>$row) {
        $group_key1++;
        $group_key = (string)$group_key1;
        $portfolioID1 = (string)$portfolioID;
        $group_key =$group_key."-".$portfolioID;
        $link = $row->sl_url;
        $descnohtml=strip_tags($row->description);
        $descnohtml = html_entity_decode($descnohtml);
        $result = substr($descnohtml, 0, 50);
        $catForFilter = explode(",", $row->category);
        $imgurl=explode(";",$row->image_url);
        $lighboxable = (count($imgurl) == 2)?"lighboxable":"dropdownable";
        ?>
        <input type="hidden" class="pagenum" value="1" />
        <input type="hidden" id="total" value="<?=$total; ?>" />
            <div class="element_<?php echo $portfolioID; ?> colorbox_grouping  <?php foreach ($catForFilter as $catForFilterValue) { echo str_replace(" ","-",$catForFilterValue)." ";} ?>" data-symbol="<?php echo $row->name; ?>" data-category="alkaline-earth">
                <div class="left-block_<?php echo $portfolioID; ?>">
                    <div class="main-image-block_<?php echo $portfolioID; ?> add-H-relative" >
                        <?php $imgurl=explode(";",$row->image_url); ?>
                        <?php
                        $imgurl_exp = explode('/',$imgurl[0]);
                        if($imgurl_exp[0] == 'http:' || $imgurl_exp[0] == 'https:'  ){
                            $path_juri = '';
                        }
                        else{
                            $path_juri = JUri::root();
                        }
                            if($row->image_url != ';') {
                                switch($this->youtube_or_vimeo_portfolio($imgurl[0])) {
                                    case 'image':
				?>
                        <a href="<?php echo $path_juri.$imgurl[0]; ?>" class=" portfolio-group<?php echo $group_key; ?>" title="<?php echo $row->name; ?>" ><img alt="<?php echo $row->name; ?>" id="wd-cl-img<?php echo $key; ?>" src="<?php echo $imgurl[0]; ?>"></a>
                        <?php 
                                    break;
                                    case 'youtube':
                                    $videourl=$this->get_video_id_from_url_portfolio($imgurl[0]);?>
                                        <a href="https://www.youtube.com/embed/<?php echo $videourl[0];?>" class="huge_it_portfolio_item youtube portfolio-group<?php echo $group_key;?> add-H-block"  title="<?php echo $row->name; ?>"  >
                                            <img src="http://img.youtube.com/vi/<?php echo $videourl[0]; ?>/mqdefault.jpg"><div class="play-icon <?php echo $videourl[1];?>-icon"></div>
                                        </a>
                            <?php   break;
                                    case 'vimeo':
                                    $videourl=$this->get_video_id_from_url_portfolio($imgurl[0]);
                                    $hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/".$videourl[0].".php"));
                                    $imgsrc=$hash[0]['thumbnail_large'];?>
                                    <a class="huge_it_portfolio_item vimeo portfolio-group<?php echo $group_key;?> add-H-block" href="http://player.vimeo.com/video/<?php echo $videourl[0]; ?> " title="<?php echo $row->name; ?>" >
                                        <img src="<?php echo $imgsrc; ?>" alt="<?php echo $row->name; ?>" /><div class="play-icon <?php echo $videourl[1];?>-icon"></div>
                                    </a>
                        <?php  }
                           } else { ?>
                        <a href="<?php echo JUri::root().$imgurl[0]; ?>" class=" portfolio-group<?php echo $group_key; ?>"><img alt="<?php echo $row->name; ?>" id="wd-cl-img<?php echo $key; ?>" src="images/noimage.jpg"></a>
                            <?php
                                }
                            ?>
                    </div>
                    <div class="thumbs-block">
                    <?php
                        if($paramssld["ht_view3_show_thumbs"] == 'on') {
                    ?>
                        <ul class="thumbs-list_<?php echo $portfolioID; ?>">					
                        <?php
                            $imgurl=explode(";",$row->image_url);
                            array_pop($imgurl);
                            array_shift($imgurl);

                            foreach($imgurl as $key=>$img) {
                                switch($this->youtube_or_vimeo_portfolio($img)) {
                                    case 'image':  ?>
                            <li><a href="<?php echo $img;?>" class=" portfolio-group<?php echo $group_key; ?> "  title = "<?php ?>"><img src="<?php echo $img; ?>"></a></li>
                                            <?php   break;
                                                    case 'youtube':
                                                    $videourl=$this -> get_video_id_from_url_portfolio($img);?>
                                                     <li>
                                                         <a href="https://www.youtube.com/embed/<?php echo $videourl[0];?>" class="huge_it_portfolio_item youtube portfolio-group<?php echo $group_key;?>  add-H-relative"  title="<?php echo $row->name; ?>" >
                                                            <img src="http://img.youtube.com/vi/<?php echo $videourl[0]; ?>/mqdefault.jpg"><div class="play-icon <?php echo $videourl[1];?>-icon"></div>
                                                         </a>
                                                     </li>
                                            <?php
                                                    break;
                                                    case 'vimeo':
                                                    $videourl=$this->get_video_id_from_url_portfolio($img);
                                                    $hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/".$videourl[0].".php"));
                                                    $imgsrc=$hash[0]['thumbnail_large'];?>
                                                    <li>
                                                        <a class="huge_it_portfolio_item vimeo portfolio-group<?php echo $group_key;?>  add-H-relative" href="http://player.vimeo.com/video/<?php echo $videourl[0]; ?>" title="<?php echo $row->name; ?>">
                                                            <img src="<?php echo $imgsrc; ?>" alt="<?php echo $row->name; ?>" /><div class="play-icon <?php echo $videourl[1];?>-icon"></div>
                                                        </a>
                                                    </li>																	
                                                    <?php
                                                          break;	
                                }
															  
                            }
                            ?>
                        </ul>
                            <?php
                            }
                            ?>
                    </div>
                </div>
                <div class="right-block">
                    <?php if($row->name!=''){?><div class="title-block_<?php echo $portfolioID; ?>"><h3><?php echo $row->name; ?></h3></div><?php } ?>
                        <?php
                            if($paramssld["ht_view3_show_description"] == 'on') {
                                if($row->description!='') { ?>
                                    <div class="description-block_<?php echo $portfolioID; ?>"><p><?php echo $row->description; ?></p></div>
                                    <?php } ?>
                              <?php }

                                if($link!='') { 
                                    if($paramssld["ht_view3_show_linkbutton"] == 'on' && $paramssld["ht_view3_linkbutton_text"] != '' && $link != '') {
                                ?>
                                    <div class="button-block">
                                            <a href="<?php echo $link; ?>" <?php if ($row->link_target=="on"){echo 'target="_blank"';}?>><?php echo $paramssld["ht_view3_linkbutton_text"]; ?></a>
                                    </div>
                                <?php }
                                } ?>
                </div>
            </div>

          <?php
              }
              ?>

        </div>
    <?php
    $a=$disp_type;
    if($a=='load_more'){
        $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
        $actual_link = $protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."";
        $pattern="/\?p=/";
        $pattern2="/&page-img[0-9]+=[0-9]+/";
        $pattern3="/\?page-img[0-9]+=[0-9]+/";
        if(preg_match($pattern, $actual_link)){

            if(preg_match($pattern2, $actual_link)){
                $actual_link=preg_replace($pattern2, '', $actual_link);
                header("Location:".$actual_link."");
                exit;

            }
        }elseif(preg_match($pattern3, $actual_link)){
            $actual_link=preg_replace($pattern3, '', $actual_link);
            header("Location:".$actual_link."");
            exit;

        }
        ?>
        <?php $paramssld['ht_view3_loading_type'] = $paramssld['slider_navigation_type']; ?>
        <div class="load_more4">
            <div class="load_more_button4"><?=$paramssld['ht_view3_loadmore_text']; ?></div>
            <div class="loading4"><img src="<?php echo JUri::root()."media/com_portfoliogallery/images/loading.white.gif";?>"></div>
            <input type="hidden" class="load-more-elements-count" value="<?php echo $count_page; ?>"/>


            <script>
                jQuery(document).ready(function(){
                    if(jQuery("#huge_it_portfolio_content_<?php echo $portfolioID; ?> .load_more_button4").length){
                        jQuery("#huge_it_portfolio_content_<?php echo $portfolioID; ?> .load_more_button4").on("click tap",function(){
                            if(parseInt(jQuery("#huge_it_portfolio_content_<?php echo $portfolioID; ?> .pagenum:last").val())<parseInt(jQuery("#huge_it_portfolio_container_<?php echo $portfolioID; ?> #total").val())) {
                                var pagenum = parseInt(jQuery("#huge_it_portfolio_content_<?php echo $portfolioID; ?> .pagenum:last").val()) + 1;
                                var perpage =<?=$count_page; ?>;
                                var galleryid="<?=$image->portfolio_id; ?>";
                                getresult(pagenum,perpage,galleryid);
                            }else{

                                jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?> .load_more_button4').hide();
                            }
                            return false;
                        });
                    }
                    if(parseInt(jQuery("#huge_it_portfolio_content_<?php echo $portfolioID; ?> .pagenum:last").val())==parseInt(jQuery("#huge_it_portfolio_container_<?php echo $portfolioID; ?> #total").val())) {
                        jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?> .load_more_button4').hide();
                    }
                    function getresult(pagenum,perpage,galleryid){
                        var data = {
                            action:'my_action',
                            post:"huge_it_portfolio_gallery_ajax3",
                            task:'load_portfolio_videos_lightbox',
                            page:pagenum,
                            perpage:perpage,
                            galleryid:galleryid,
                            level:<?php echo $level?>,
                        }
                        jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?> .loading4').show();

                        jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?> .load_more_button4').hide();

                        jQuery.post("<?php echo $path_menu?>components/com_portfoliogallery/ajax_url.php",data,function(response){
                            if(response.success){
                                var $objnewitems= jQuery(response.success);
                                jQuery('#huge_it_portfolio_container_<?php echo $portfolioID; ?>').append( $objnewitems ).hugeitmicro('reloadItems' ).hugeitmicro({ sortBy: 'original-order' }).hugeitmicro( 'reLayout' );
                                jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?> img').on('load',function(){

                                    //############# End


                                    jQuery(".group1").colorbox({rel:'group1'});
                                    jQuery(".youtube").colorbox({iframe:true, innerWidth:640, innerHeight:390});
                                    jQuery(".vimeo").colorbox({iframe:true, innerWidth:640, innerHeight:390});
                                    jQuery(".iframe").colorbox({iframe:true, width:"80%", height:"80%"});
                                    jQuery(".inline").colorbox({inline:true, width:"50%"});
                                    jQuery(".callbacks").colorbox({
                                        onOpen:function(){ alert('onOpen: colorbox is about to open'); },
                                        onLoad:function(){ alert('onLoad: colorbox has started to load the targeted content'); },
                                        onComplete:function(){ alert('onComplete: colorbox has displayed the loaded content'); },
                                        onCleanup:function(){ alert('onCleanup: colorbox has begun the close process'); },
                                        onClosed:function(){ alert('onClosed: colorbox has completely closed'); }
                                    });

                                    jQuery('.non-retina').colorbox({rel:'group5', transition:'none'})
                                    jQuery('.retina').colorbox({rel:'group5', transition:'none', retinaImage:true, retinaUrl:true});
                                    var defaultBlockWidth=<?php echo $paramssld['ht_view6_width']; ?>+20+<?php echo $paramssld['ht_view6_width']*2; ?>;
                                    jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?> .load_more_button4').show();
                                    jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?> .loading4').hide();
                                    if(parseInt(jQuery("#huge_it_portfolio_content_<?php echo $portfolioID; ?> .pagenum:last").val())==parseInt(jQuery("#huge_it_portfolio_container_<?php echo $portfolioID; ?> #total").val())) {
                                        jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?> .load_more_button4').hide();
                                    }




                                });


                            }else{
                                alert("no");
                            }
                        },"json");
                    }

                });
            </script>
        </div>
        <?php
    }elseif($a=='pagination'){
        ?>
        <div class="paginate4">
            <?php
            $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
            $actual_link = $protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."";


            $checkREQ='';
            $pattern="/\?p=/";
            $pattern2="/&page-img[0-9]+=[0-9]+/";
            if(preg_match($pattern, $actual_link)){
                if(preg_match($pattern2, $actual_link)){
                    $actual_link=preg_replace($pattern2, '', $actual_link);
                }
                $checkREQ=$actual_link.'&page-img'.$portfolioID.$pID;

            }else{
                $checkREQ='?page-img'.$portfolioID.$pID;

            }
            $pervpage='';
            if ($page != 1) $pervpage = '<a href= '.$checkREQ.'=1><i class="hugeiticons hugeiticons-fast-backward" ></i></a>  
                             <a href= '.$checkREQ.'='. ($page - 1) .'><i class="hugeiticons hugeiticons-chevron-left"></i></a> ';
            $nextpage='';
            if ($page != $total) $nextpage = '<a href= '.$checkREQ.'='. ($page + 1) .'><i class="hugeiticons hugeiticons-chevron-right"></i></a>  
                                  <a href= '.$checkREQ.'=' .$total. '><i class="hugeiticons hugeiticons-fast-forward" ></i></a>';
            echo $pervpage.$page.'/'.$total.$nextpage;

            ?>
        </div>
        <?php
    }
    ?>
 </section>
<script>
jQuery(function(){
var defaultBlockWidth=<?php echo $paramssld['ht_view3_mainimage_width']; ?>;
    
    var $container = jQuery('#huge_it_portfolio_container_<?php echo $portfolioID; ?>');
      // add randomish size classes
      $container.find('.element_<?php echo $portfolioID; ?>').each(function(){
        var $this = jQuery(this),
            number = parseInt( $this.find('.number').text(), 10 );
        if ( number % 7 % 2 === 1 ) {
          $this.addClass('width2');
        }
        if ( number % 3 === 0 ) {
          $this.addClass('height2');
        }
      });
    
    $container.hugeitmicro({
      itemSelector : '.element_<?php echo $portfolioID; ?>',
      masonry : {
        columnWidth : <?php echo $paramssld['ht_view3_mainimage_width']; ?>+20+<?php echo $paramssld['ht_view3_element_border_width']*2; ?>
      },
      masonryHorizontal : {
        rowHeight: 300+20
      },
      cellsByRow : {
        columnWidth : 300+20,
        rowHeight : 240
      },
      cellsByColumn : {
        columnWidth : 300+20,
        rowHeight : 240
      },
      getSortData : {
        symbol : function( $elem ) {
          return $elem.attr('data-symbol');
        },
        category : function( $elem ) {
          return $elem.attr('data-category');
        },
        number : function( $elem ) {
          return parseInt( $elem.find('.number').text(), 10 );
        },
        weight : function( $elem ) {
          return parseFloat( $elem.find('.weight').text().replace( /[\(\)]/g, '') );
        },
        id : function ( $elem ) {
          return $elem.find('.id').text();
        }
      }
    });
      var $optionSets = jQuery('#huge_it_portfolio_options_<?php echo $portfolioID; ?> .option-set'),
          $optionLinks = $optionSets.find('a');

      $optionLinks.click(function(){
        var $this = jQuery(this);

        if ( $this.hasClass('selected') ) {
          return false;
        }
        var $optionSet = $this.parents('.option-set');
        $optionSet.find('.selected').removeClass('selected');
        $this.addClass('selected');
  

        var options = {},
            key = $optionSet.attr('data-option-key'),
            value = $this.attr('data-option-value');

        value = value === 'false' ? false : value;
        options[ key ] = value;
        if ( key === 'layoutMode' && typeof changeLayoutMode === 'function' ) {

          changeLayoutMode( $this, options )
        } else {

          $container.hugeitmicro( options );
        }
        
        return false;
      });
      var isHorizontal = false;
      function changeLayoutMode( $link, options ) {
        var wasHorizontal = isHorizontal;
        isHorizontal = $link.hasClass('horizontal');

        if ( wasHorizontal !== isHorizontal ) {

          var style = isHorizontal ? 
            { height: '75%', width: $container.width() } : 
            { width: 'auto' };

          $container.filter(':animated').stop();

          $container.addClass('no-transition').css( style );
          setTimeout(function(){
            $container.removeClass('no-transition').hugeitmicro( options );
          }, 100 )
        } else {
          $container.hugeitmicro( options );
        }
      }


    

      $container.delegate( '.default-block_<?php echo $portfolioID; ?>', 'click', function(){
          var strheight=0;
          jQuery(this).parents('.element_<?php echo $portfolioID; ?>').find('.wd-portfolio-panel_<?php echo $portfolioID; ?> > div').each(function(){
                strheight+=jQuery(this).outerHeight()+10;
          })
          strheight+=<?php echo $paramssld['ht_view0_block_height']+45; ?>;
	  			if(jQuery(this).parents('.element_<?php echo $portfolioID; ?>').hasClass("large")){
			jQuery(this).parents('.element_<?php echo $portfolioID; ?>').animate({
				height: "<?php echo $paramssld['ht_view0_block_height']+45; ?>px"
			}, 300, function() {
				jQuery(this).removeClass('large');
				$container.hugeitmicro('reLayout');
			});
			
			jQuery(this).parents('.element_<?php echo $portfolioID; ?>').removeClass("active");
			return false;
		}
		jQuery(this).parents('.element_<?php echo $portfolioID; ?>').css({height:strheight});
		jQuery(this).parents('.element_<?php echo $portfolioID; ?>').addClass('large');

		$container.hugeitmicro('reLayout');
		jQuery(this).parents('.element_<?php echo $portfolioID; ?>').css({height:"<?php echo $paramssld['ht_view0_block_height']+45; ?>px"});
		 jQuery(this).parents('.element_<?php echo $portfolioID; ?>').animate({
			height:strheight+"px",
		  }, 300,function(){	$container.hugeitmicro('reLayout');});
	});

    var $sortBy =  jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?> #sort-by');
    jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?> #shuffle a').click(function(){
      $container.hugeitmicro('shuffle');
      $sortBy.find('.selected').removeClass('selected');
      $sortBy.find('[data-option-value="random"]').addClass('selected');
      return false;
    });
    
    ////filteringgggggg

        // bind filter on select change
        jQuery(document).ready(function(){
            jQuery('#huge_it_portfolio_filters_<?php echo $portfolioID; ?> ul li').click(function() {
              // get filter value from option value
              var filterValue = jQuery(this).attr('rel');
              // use filterFn if matches value
              filterValue = filterValue;
              $container.hugeitmicro({ filter: filterValue });
            });
            <?php if(($paramssld["ht_view3_sorting_float"] == "left" || $paramssld["ht_view3_sorting_float"] == "right") && $paramssld["ht_view3_filtering_float"] == "none")
                  { ?>
                        var topmargin = jQuery("#huge_it_portfolio_filters_<?php echo $portfolioID; ?> ul").height();
                        jQuery("#huge_it_portfolio_options_<?php echo $portfolioID; ?>").css({'margin-top':parseInt(topmargin) + 5});
            <?php }
            else  {
                    if(($paramssld["ht_view3_filtering_float"] == "left" || $paramssld["ht_view3_filtering_float"] == "right") && $paramssld["ht_view3_sorting_float"] == "none")
                      { ?>
                         var topmargin = jQuery("#huge_it_portfolio_options_<?php echo $portfolioID; ?>").height();
                         jQuery("#huge_it_portfolio_filters_<?php echo $portfolioID; ?>").css({'margin-top':'5px'});
                <?php }
                  } ?>
        });
        
        //end of filtering
        
        jQuery(window).load(function(){
		$container.hugeitmicro('reLayout');
		jQuery(window).resize(function(){$container.hugeitmicro('reLayout');});
	});

  });
</script>

<?php
    break;
/////////////////////////////////// VIEW 4 FAQ ////////////////////////////////////
    case 4;

?>
<?php
    if($paramssld["ht_view4_sorting_float"] == "left" && $paramssld["ht_view4_filtering_float"] == "right" ||
       $paramssld["ht_view4_sorting_float"] == "right" && $paramssld["ht_view4_filtering_float"] == "left" ||
       $paramssld["ht_view4_sorting_float"] == $paramssld["ht_view4_filtering_float"])
       { $sorting_block_width ="20%"; $filtering_block_width ="20%"; $middle_with = "56%"; }
    else if($paramssld["ht_view4_sorting_float"] == "left" || $paramssld["ht_view4_sorting_float"] == "right" && $paramssld["ht_view4_filtering_float"] == "top")
       { $sorting_block_width ="30%"; $filtering_block_width ="100%"; $paramssld["ht_view4_filtering_float"] = "none"; $width_middle = "65%"; }
    else if($paramssld["ht_view4_filtering_float"] == "left" || $paramssld["ht_view4_filtering_float"] == "right" && $paramssld["ht_view4_sorting_float"] == "top")
       { $sorting_block_width ="100%"; $filtering_block_width ="30%"; $paramssld["ht_view4_sorting_float"] = "none"; $width_middle = "65%"; }
    if($paramssld["ht_view4_sorting_float"] == "top" && $paramssld["ht_view4_filtering_float"] == "top")
       { $sorting_block_width ="100%"; $filtering_block_width ="100%"; $left_to_top = "ok"; }
?>
<style type="text/css"> 
.element_<?php echo $portfolioID; ?> {
	background:#<?php echo $paramssld['ht_view4_element_background_color']?>;
	width:<?php echo $paramssld['ht_view4_block_width']; ?>px;
	height:45px;
	margin: 5px;
	float: left;
	overflow: hidden;
	position: relative;
	outline:none;
	border:<?php echo $paramssld['ht_view4_element_border_width']; ?>px solid #<?php echo $paramssld['ht_view4_element_border_color']; ?>;
}

.element_<?php echo $portfolioID; ?>.large,
.variable-sizes .element_<?php echo $portfolioID; ?>.large,
.variable-sizes .element_<?php echo $portfolioID; ?>.large.width2.height2 {
	width: <?php echo $paramssld['ht_view4_block_width']; ?>px;
	z-index: 10;
}


.title-block_<?php echo $portfolioID; ?> {
	position:relative;
	display:block;
	height:45px;
	padding:10px 0px 0px 0px;
	width:<?php echo $paramssld['ht_view4_block_width']; ?>px;
        max-width: 467px;
}

.title-block_<?php echo $portfolioID; ?> h3 {
	position:relative;
	margin:0px;
	padding:0px 5px 0px 5px;
	width:<?php echo $paramssld['ht_view4_block_width']-40; ?>px;
	text-overflow: ellipsis;
	overflow: hidden; 
	white-space:nowrap;
	font-weight:normal;
	color:#<?php echo $paramssld['ht_view4_title_font_color']; ?>;
	font-size:<?php echo $paramssld['ht_view4_title_font_size']; ?>;
}

.element_<?php echo $portfolioID; ?> .title-block_<?php echo $portfolioID; ?> .open-close-button {
	width:20px;
	height:20px;
	position:absolute;
	top:13px;
	right:5px;
	background:url('<?php  echo JUri::root().'media/com_portfoliogallery/images/open-close.'.$paramssld['ht_view4_togglebutton_style'].'.png' ; ?>') left top no-repeat;
	z-index:5;
	cursor:pointer;
	opacity:0.33;
}

.element_<?php echo $portfolioID; ?>:hover .title-block_<?php echo $portfolioID; ?> .open-close-button {opacity:1;}

.element_<?php echo $portfolioID; ?>.large .open-close-button {
	background:url('<?php echo JUri::root().'media/com_portfoliogallery/images/open-close.'.$paramssld['ht_view4_togglebutton_style'].'.png'; ?>') left bottom no-repeat;
}

.wd-portfolio-panel_<?php echo $portfolioID; ?> {
	position:relative;
	clear:both;
	display:block;
	width:<?php echo $paramssld['ht_view4_block_width']-10; ?>px;
	margin:0px 5px 0px 5px !important;
	padding:0px;
	text-align:left;
	z-index:6; 
}

.wd-portfolio-panel_<?php echo $portfolioID; ?> .description-block_<?php echo $portfolioID; ?> p,.element_<?php echo $portfolioID; ?> div.right-block .description-block_<?php echo $portfolioID; ?> * {	
	text-align:justify;
	font-weight:normal;
	font-size:<?php echo $paramssld['ht_view4_description_font_size']; ?>px;
	color:#<?php echo $paramssld['ht_view4_description_color']; ?>;
	margin:0px;
	padding:0px;
}

.wd-portfolio-panel_<?php echo $portfolioID; ?> .description-block_<?php echo $portfolioID; ?> h1,
.wd-portfolio-panel_<?php echo $portfolioID; ?> .description-block_<?php echo $portfolioID; ?> h2,
.wd-portfolio-panel_<?php echo $portfolioID; ?> .description-block_<?php echo $portfolioID; ?> h3,
.wd-portfolio-panel_<?php echo $portfolioID; ?> .description-block_<?php echo $portfolioID; ?> h4,
.wd-portfolio-panel_<?php echo $portfolioID; ?> .description-block_<?php echo $portfolioID; ?> h5,
.wd-portfolio-panel_<?php echo $portfolioID; ?> .description-block_<?php echo $portfolioID; ?> h6,
.wd-portfolio-panel_<?php echo $portfolioID; ?> .description-block_<?php echo $portfolioID; ?> p, 
.wd-portfolio-panel_<?php echo $portfolioID; ?> .description-block_<?php echo $portfolioID; ?> strong,
.wd-portfolio-panel_<?php echo $portfolioID; ?> .description-block_<?php echo $portfolioID; ?> span {
	line-height:auto !important;
	padding:2px !important;
	margin:0px !important;
}

.wd-portfolio-panel_<?php echo $portfolioID; ?> .description-block_<?php echo $portfolioID; ?> ul,
.wd-portfolio-panel_<?php echo $portfolioID; ?> .description-block_<?php echo $portfolioID; ?> li {
	padding:2px 0px 2px 5px;
	margin:0px 0px 0px 8px;
}

.wd-portfolio-panel_<?php echo $portfolioID; ?> > div {
	padding-top:10px;
	margin-bottom:10px;
	<?php if($paramssld['ht_view4_show_separator_lines']=="on") {?>
        background:url('<?php echo JUri::root().'media/com_portfoliogallery/images/divider.line.png'; ?>') center top repeat-x;
	<?php } ?>
}

.wd-portfolio-panel_<?php echo $portfolioID; ?> .button-block {
	padding:10px 0px 10px 0px;
}

.wd-portfolio-panel_<?php echo $portfolioID; ?> .button-block a, .wd-portfolio-panel_<?php echo $portfolioID; ?> .button-block a:link, .wd-portfolio-panel_<?php echo $portfolioID; ?> .button-block a:visited {
	padding:6px 12px;
	display:inline-block;
	font-size:<?php echo $paramssld['ht_view4_linkbutton_font_size']; ?>px;
	background:#<?php echo $paramssld['ht_view4_linkbutton_background_color']; ?>;
	color:#<?php echo $paramssld['ht_view4_linkbutton_color']; ?>;
	text-decoration:none;
}

.wd-portfolio-panel_<?php echo $portfolioID; ?> .button-block a:hover, .wd-portfolio-panel_<?php echo $portfolioID; ?> .button-block a:focus, .wd-portfolio-panel_<?php echo $portfolioID; ?> .button-block a:active {
	background:#<?php echo $paramssld['ht_view4_linkbutton_background_hover_color']; ?>;
	color:#<?php echo $paramssld['ht_view4_linkbutton_font_hover_color']; ?>;
	text-decoration:none;
}


@media only screen and (max-width: <?php echo $paramssld['ht_view4_block_width']; ?>px) {
	.element_<?php echo $portfolioID; ?> {
	  width:95%;
	}

	.element_<?php echo $portfolioID; ?>.large,
	.variable-sizes .element_<?php echo $portfolioID; ?>.large,
	.variable-sizes .element_<?php echo $portfolioID; ?>.large.width2.height2 {
	  width: 95%;
	}


	.title-block_<?php echo $portfolioID; ?> {
		width:88%;
	}
}

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_options_<?php echo $portfolioID; ?> {
    display:block;
    <?php
    if($paramssld["ht_view4_filtering_float"] == 'left' && $paramssld["ht_view4_sorting_float"] == 'none') { if($portfolioShowFiltering == "on") { echo "margin-left: 31%;"; } else { echo "margin-left: 1%;"; }  }
    else if($paramssld["ht_view4_filtering_float"] == 'right' && $paramssld["ht_view4_sorting_float"] == 'none' || ($sorting_block_width == '100%' && $filtering_block_width == "100%")) { echo "margin-left: 1%;"; } ?>
    overflow: hidden;
    margin-top: 5px;
    float: <?php echo $paramssld["ht_view4_sorting_float"]; ?>;
    width: <?php echo $sorting_block_width; ?>;
}

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_options_<?php echo $portfolioID; ?> ul {
  margin: 0px !important;
  padding: 0px !important;
  list-style: none;
<?php if($paramssld["ht_view4_sorting_float"] == 'top') {
      echo "float:left;margin-left:1%;";
      } ?>
}

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?> ul {
  margin: 0px !important;
  padding: 0px !important;
  overflow: hidden;
  <?php if($paramssld["ht_view4_filtering_float"] == 'top') {
      echo "float:left;margin-left:1%;";
      } ?>
}

<?php if($paramssld["ht_view4_sorting_float"] == 'none') { ?>
            #huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_options_<?php echo $portfolioID; ?> ul {
                float: left;
            }
    <?php } ?>
            
#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_options_<?php echo $portfolioID; ?> ul li {
    border-radius: <?php echo $paramssld["ht_view4_sortbutton_border_radius"];?>px;
    list-style-type: none;
    margin: 0px !important;
	padding: 0;
    <?php
        if($sorting_block_width == "100%" ) {
            echo "float:left !important;margin: 4px 8px 4px 0px !important;";
        }
        if($left_to_top == "ok")
        { echo "float:left !important;"; }
        if($paramssld["ht_view4_sorting_float"] == "left" || $paramssld["ht_view4_sorting_float"] == "right")
        { echo 'border-bottom: 1px solid #ccc;'; }
        else
        { echo 'border: 1px solid #ccc;'; }
    ?>
}

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_options_<?php echo $portfolioID; ?> ul li a {
    background-color: #<?php echo $paramssld["ht_view4_sortbutton_background_color"];?> !important;
    font-size:<?php echo $paramssld["ht_view4_sortbutton_font_size"];?>px !important;
    color:#<?php echo $paramssld["ht_view4_sortbutton_font_color"];?> !important;
    text-decoration: none;
    cursor: pointer;
    margin: 0px !important;
    display: block;
    padding:3px;
}

/*#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_options_<?php echo $portfolioID; ?> ul li:hover {
    
}*/

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_options_<?php echo $portfolioID; ?> ul li a:hover {
    background-color: #<?php echo $paramssld["ht_view4_sortbutton_hover_background_color"];?> !important;
    color:#<?php echo $paramssld["ht_view4_sortbutton_hover_font_color"];?> !important;
    cursor: pointer;
}

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?> {
    margin-top: 5px;
    display:block;
    float: <?php echo $paramssld["ht_view4_filtering_float"]; ?>;
    width: <?php echo $filtering_block_width; ?>;
    <?php
        if($paramssld["ht_view4_filtering_float"] == 'none' && ($paramssld["ht_view4_sorting_float"] == 'left') ) { if($portfolioShowSorting == "on") { echo "margin-left: 31%;"; } else { echo "margin-left: 1%;"; } } 
        if(($paramssld["ht_view4_filtering_float"] == 'none' && ($paramssld["ht_view4_sorting_float"] == 'right')) || ($sorting_block_width == '100%' && $filtering_block_width == "100%")) { echo "margin-left: 1%";}
    ?>
}

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?> ul li {
    list-style-type: none;
    <?php
        if($filtering_block_width == "100%") { echo "float:left !important;margin: 4px 8px 4px 0px !important;"; }
        if($left_to_top == "ok") { echo "float:left !important;"; }
        if($paramssld["ht_view4_filtering_float"] == "left" || $paramssld["ht_view4_filtering_float"] == "right")
        { echo 'border-bottom: 1px solid #ccc;'; }
        else echo "border: 1px solid #ccc;";
    ?>
}

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?> ul li a {
    font-size:<?php echo $paramssld["ht_view4_filterbutton_font_size"];?>px !important;
    color:#<?php echo $paramssld["ht_view4_filterbutton_font_color"];?> !important;
    background-color: #<?php echo $paramssld["ht_view4_filterbutton_background_color"];?> !important;
    border-radius: <?php echo $paramssld["ht_view4_filterbutton_border_radius"];?>px;
    padding: 3px;
    display: block;
    text-decoration: none;
}

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?>  ul li a:hover {
    color:#<?php echo $paramssld["ht_view4_filterbutton_hover_font_color"];?> !important;
    background-color: #<?php echo $paramssld["ht_view4_filterbutton_hover_background_color"];?> !important;
    cursor: pointer
}
#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?> ul li.active a,
#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?> ul li.active a:link,
#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?> ul li.active a:visited,
#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?>  ul li.active a:hover,
#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?>  ul li.active a:focus,
#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?>  ul li.active a:active {
    color:#<?php echo $paramssld["ht_view4_filterbutton_hover_font_color"];?> !important;
    background-color: #<?php echo $paramssld["ht_view4_filterbutton_hover_background_color"];?> !important;
    cursor: pointer;
}
#huge_it_portfolio_content_<?php echo $portfolioID; ?> section {
    position:relative;
    display:block;
}

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_container_<?php echo $portfolioID; ?> {
<?php if($paramssld["ht_view4_sorting_float"] == "left" && $paramssld["ht_view4_filtering_float"] == "right" ||
         $paramssld["ht_view4_sorting_float"] == "right" && $paramssld["ht_view4_filtering_float"] == "left")
       { echo "margin: 0px auto;"; }
       if((($paramssld["ht_view4_filtering_float"] == "left" || $paramssld["ht_view4_filtering_float"] == "right" && $paramssld["ht_view4_sorting_float"] == "top") || ($paramssld["ht_view4_sorting_float"] == "left" || $paramssld["ht_view4_sorting_float"] == "right" && $paramssld["ht_view4_filting_float"] == "top")) && $portfolioShowFiltering == "on" && $portfolioShowSorting == "on")
       {
?>
        width: <?php echo $width_middle; ?> !important;
 <?php } ?>
}
.load_more4 {
    margin: 10px 0;
    position:relative;
    text-align:<?php if($paramssld['ht_view4_loadmore_position'] == 'left') {echo 'left';}
			elseif ($paramssld['ht_view4_loadmore_position'] == 'center') { echo 'center'; }
			elseif($paramssld['ht_view4_loadmore_position'] == 'right') { echo 'right'; }?>;

    width:100%;


}

.load_more_button4 {
    border-radius: 10px;
    display:inline-block;
    padding:5px 15px;
    font-size:<?php echo $paramssld['ht_view4_loadmore_fontsize']; ?>px !important;;
    color:<?php echo '#'.$paramssld['ht_view4_loadmore_font_color']; ?> !important;;
    background:<?php echo '#'.$paramssld['ht_view4_button_color']; ?> !important;
    cursor:pointer;

}
.load_more_button4:hover{
    color:<?php echo '#'.$paramssld['ht_view4_loadmore_font_color_hover']; ?> !important;
    background:<?php echo '#'.$paramssld['ht_view4_button_color_hover']; ?> !important;
}

.loading4 {
    display:none;
}
.paginate4{
    font-size:<?php echo $paramssld['ht_view4_paginator_fontsize']; ?>px !important;
    color:<?php echo '#'.$paramssld['ht_view4_paginator_color']; ?> !important;
    text-align: <?php echo $paramssld['ht_view4_paginator_position']; ?>;
    margin-top: 25px;
}
.paginate4 a{
    border-bottom: none !important;
    color: #<?php echo $paramssld['ht_view4_paginator_icon_color']; ?> !important;
    font-size:<?php echo $paramssld['ht_view4_paginator_icon_size']; ?>px !important;
}
.icon-style4{
    font-size: <?php echo $paramssld['ht_view4_paginator_icon_size']; ?>px !important;
    color:<?php echo '#'.$paramssld['ht_view4_paginator_icon_color']; ?> !important;
}
.clear{
    clear:both;
}


</style>

<section id="huge_it_portfolio_content_<?php echo $portfolioID; ?>">
    <?php if($portfolioShowSorting == "on") {?>
        <div id="huge_it_portfolio_options_<?php echo $portfolioID; ?>" class="">
            <ul id="sort-by" class="option-set clearfix" data-option-key="sortBy">
                <li><a href="#sortBy=original-order" data-option-value="original-order" class="selected" data><?php echo $paramssld["ht_view4_sorting_name_by_default"]; ?></a></li>
                <li><a href="#sortBy=id" data-option-value="id"><?php echo $paramssld["ht_view4_sorting_name_by_id"]; ?></a></li>
                <li><a href="#sortBy=symbol" data-option-value="symbol"><?php echo $paramssld["ht_view4_sorting_name_by_name"]; ?></a></li>
                <li id="shuffle"><a href='#shuffle'><?php echo $paramssld["ht_view4_sorting_name_by_random"]; ?></a></li>
            </ul>
              
            <ul id="sort-direction" class="option-set clearfix" data-option-key="sortAscending">
                <li><a href="#sortAscending=true" data-option-value="true" class="selected"><?php echo $paramssld["ht_view4_sorting_name_by_asc"]; ?></a></li>
                <li><a href="#sortAscending=false" data-option-value="false"><?php echo $paramssld["ht_view4_sorting_name_by_desc"]; ?></a></li>
            </ul>
        </div>
  <?php }
   if($portfolioShowFiltering == "on"){ ?>
        <div id="huge_it_portfolio_filters_<?php echo $portfolioID; ?>">
            <ul>
                <li rel="*"><a><?php echo $paramssld["ht_view4_cat_all"];?></a></li>
                <?php
                $portfolioCats = explode(",", $portfolioCats);
                foreach ($portfolioCats as $portfolioCatsValue) {
                    if(!empty($portfolioCatsValue))
                    {
                ?>
                <li rel=".<?php echo str_replace(" ","_",$portfolioCatsValue); ?>"><a><?php echo str_replace("_"," ",$portfolioCatsValue); ?></a></li>
                    <?php
                    }
                }
                ?>
            </ul>
        </div>
<?php } ?>
        <div id="huge_it_portfolio_container_<?php echo $portfolioID; ?>" class="super-list variable-sizes clearfix" <?php if($paramssld["ht_view4_sorting_float"] == "top" && $paramssld["ht_view4_filtering_float"] == "top") echo "style='clear: both;'";?>>
            <?php
            $gallery = '';
            foreach($images as $image){
                $idofgallery=intval($image->portfolio_id) ;
            }

            $db = JFactory::getDBO();
            $query2 = $db->getQuery(true);
            $query2->select('*');
            $query2->from('#__huge_itportfolio_portfolios');
            $query2 -> where('id ='.$idofgallery);
            $query2 ->order('#__huge_itportfolio_portfolios.ordering asc');
            $db->setQuery($query2);
            $gallery = $db->loadObjectList();
            $pattern='/-/';
            $pID = '';
            $post = 0;
            foreach ($gallery as $gall) {
                global $post;
                $pID=$post;
                $disp_type=$gall->pagination_type;
                $count_page=$gall->count_into_page;
                if($count_page==0){
                    $count_page=999;
                }elseif(preg_match($pattern, $count_page)){
                    $count_page=preg_replace($pattern, '', $count_page);
                }
            }
            $num=$count_page;
            $total = intval(((count($images) - 1) / $num) + 1);
            if(isset($_GET['page-img'.$portfolioID.$pID])){
                $page = $_GET['page-img'.$portfolioID.$pID];
            }else{
                $page = '';

            }
            $page = intval($page);
            if(empty($page) or $page < 0) $page = 1;
            if($page > $total) $page = $total;
            $start = $page * $num - $num;
            $query = $db->getQuery(true);
            $query->select('*');
            $query->from('#__huge_itportfolio_images');
            $query -> where('portfolio_id ='.$idofgallery);
            $query ->order('#__huge_itportfolio_images.ordering asc');
            $db->setQuery($query,$start,$num);
            $page_images = $db->loadObjectList();
            if($disp_type=='show_all'){

                $page_images=$images;
                $count_page=9999;
            }
            $group_key1= 0;
            foreach($page_images as $key=>$row) {
                $group_key1++;
                $group_key = (string)$group_key1;
                $portfolioID1 = (string)$portfolioID;
                $group_key =$group_key."-".$portfolioID;
                $link = $row->sl_url;
                $descnohtml=strip_tags($row->description);
                $descnohtml = html_entity_decode($descnohtml);
                $result = substr($descnohtml, 0, 50);
                $catForFilter = explode(",", $row->category);
                $imgurl=explode(";",$row->image_url);
                $lighboxable = (count($imgurl) == 2)?"lighboxable":"dropdownable";
                ?>
                <input type="hidden" class="pagenum" value="1" />
                <input type="hidden" id="total" value="<?=$total; ?>" />
                <div class="element_<?php echo $portfolioID; ?>  <?php foreach ($catForFilter as $catForFilterValue) { echo str_replace(" ","-",$catForFilterValue)." ";} ?> " data-symbol="<?php echo $row->name; ?>" data-category="alkaline-earth">
                    <div class="title-block_<?php echo $portfolioID; ?>">
                        <h3 class="title"><?php echo $row->name; ?></h3>
                        <div class="open-close-button"></div>
                    </div>

                <div class="wd-portfolio-panel_<?php echo $portfolioID; ?>" id="panel<?php echo $key; ?>">
                    <?php
                        if($paramssld['ht_view4_show_description']=='on'){?>
                            <div class="description-block_<?php echo $portfolioID; ?>">
                                    <p><?php echo $row->description; ?></p>
                            </div>
                      <?php }
                        if(isset($paramssld['ht_view4_show_thumbs']) && $paramssld['ht_view4_show_thumbs']=='on' and $paramssld['ht_view4_thumbs_position']=="after"){?>
                            <div>
                                <ul class="thumbs-list_<?php echo $portfolioID; ?>">
                                <?php
                                    $imgurl=explode(";",$row->image_url);
                                    array_pop($imgurl);
                                    foreach($imgurl as $key=>$img) {  ?>
                                        <li>
                                            <a href="<?php echo $img; ?>" class="group1"  title = "<?php huge_it_title_img_display($img,$title);?>"><img src="<?php echo $img; ?>"></a>
                                        </li>
                                    <?php  }  ?>
                                </ul>
                            </div>
                 <?php  } 
                        if($paramssld['ht_view4_show_linkbutton']=='on'){

                            if($link != "")
                            {
                                ?>

                                <div class="button-block">
                                        <a href="<?php  echo $link; ?>" <?php if ($row->link_target=="on"){echo 'target="_blank"';}?>><?php echo $paramssld['ht_view4_linkbutton_text']; ?></a>
                                </div>
                        <?php }
                        } ?>
                        <div style="clear:both"></div>
                </div>
                </div>
                      <?php
            }?>

        </div>
    <?php
    $a=$disp_type;
    if($a=='load_more'){
        $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
        $actual_link = $protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."";
        $pattern="/\?p=/";
        $pattern2="/&page-img[0-9]+=[0-9]+/";
        $pattern3="/\?page-img[0-9]+=[0-9]+/";
        if(preg_match($pattern, $actual_link)){
            if(preg_match($pattern2, $actual_link)){
                $actual_link=preg_replace($pattern2, '', $actual_link);
                header("Location:".$actual_link."");
                exit;

            }
        }elseif(preg_match($pattern3, $actual_link)){
            $actual_link=preg_replace($pattern3, '', $actual_link);
            header("Location:".$actual_link."");
            exit;

        }
        ?>
        <?php $paramssld['ht_view4_loading_type'] = $paramssld['slider_navigation_type']; ?>
        <div class="load_more4">
            <div class="load_more_button4"><?=$paramssld['ht_view4_loadmore_text']; ?></div>
            <div class="loading4"><img src="<?php echo JUri::root()."media/com_portfoliogallery/images/loading.white.gif";?>"></div>
            <input type="hidden" class="load-more-elements-count" value="<?php echo $count_page; ?>"/>


            <script>
                jQuery(document).ready(function(){
                    if(jQuery("#huge_it_portfolio_content_<?php echo $portfolioID; ?> .load_more_button4").length){
                        jQuery("#huge_it_portfolio_content_<?php echo $portfolioID; ?> .load_more_button4").on("click tap",function(){
                            if(parseInt(jQuery("#huge_it_portfolio_content_<?php echo $portfolioID; ?> .pagenum:last").val())<parseInt(jQuery("#huge_it_portfolio_container_<?php echo $portfolioID; ?> #total").val())) {
                                var pagenum = parseInt(jQuery("#huge_it_portfolio_content_<?php echo $portfolioID; ?> .pagenum:last").val()) + 1;
                                var perpage =<?=$count_page; ?>;
                                var galleryid="<?=$image->portfolio_id; ?>";
                                getresult(pagenum,perpage,galleryid);
                            }else{
                                jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?> .load_more_button4').hide();
                            }
                            return false;
                        });
                    }
                    if(parseInt(jQuery("#huge_it_portfolio_content_<?php echo $portfolioID; ?> .pagenum:last").val())==parseInt(jQuery("#huge_it_portfolio_container_<?php echo $portfolioID; ?> #total").val())) {
                        jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?> .load_more_button4').hide();
                    }
                    function getresult(pagenum,perpage,galleryid){
                        var data = {
                            action:'my_action',
                            post:"huge_it_portfolio_gallery_ajax4",
                            task:'load_portfolio_videos_lightbox',
                            page:pagenum,
                            perpage:perpage,
                            galleryid:galleryid,
                            level:<?php echo $level?>,
                        }
                        jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?> .loading4').show();

                        jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?> .load_more_button4').hide();

                        jQuery.post("<?php echo $path_menu?>components/com_portfoliogallery/ajax_url.php",data,function(response){
                            if(response.success){
                                var $objnewitems= jQuery(response.success);
                                jQuery('#huge_it_portfolio_container_<?php echo $portfolioID; ?>').append( $objnewitems ).hugeitmicro('reloadItems' ).hugeitmicro({ sortBy: 'original-order' }).hugeitmicro( 'reLayout' );

                                jQuery(".group1").colorbox({rel:'group1'});
                                jQuery(".youtube").colorbox({iframe:true, innerWidth:640, innerHeight:390});
                                jQuery(".vimeo").colorbox({iframe:true, innerWidth:640, innerHeight:390});
                                jQuery(".iframe").colorbox({iframe:true, width:"80%", height:"80%"});
                                jQuery(".inline").colorbox({inline:true, width:"50%"});
                                jQuery(".callbacks").colorbox({
                                    onOpen:function(){ alert('onOpen: colorbox is about to open'); },
                                    onLoad:function(){ alert('onLoad: colorbox has started to load the targeted content'); },
                                    onComplete:function(){ alert('onComplete: colorbox has displayed the loaded content'); },
                                    onCleanup:function(){ alert('onCleanup: colorbox has begun the close process'); },
                                    onClosed:function(){ alert('onClosed: colorbox has completely closed'); }
                                });

                                jQuery('.non-retina').colorbox({rel:'group5', transition:'none'})
                                jQuery('.retina').colorbox({rel:'group5', transition:'none', retinaImage:true, retinaUrl:true});
                                var defaultBlockWidth=<?php echo $paramssld['ht_view6_width']; ?>+20+<?php echo $paramssld['ht_view6_width']*2; ?>;

                                jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?> .load_more_button4').show();
                                jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?> .loading4').hide();
                                if(parseInt(jQuery("#huge_it_portfolio_content_<?php echo $portfolioID; ?> .pagenum:last").val())==parseInt(jQuery("#huge_it_portfolio_container_<?php echo $portfolioID; ?> #total").val())) {

                                    jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?> .load_more_button4').hide();
                                }else{

                                }


                            }else{
                                alert("no");
                            }
                        },"json");
                    }

                });
            </script>
        </div>
        <?php
    }elseif($a=='pagination'){
        ?>
        <div class="paginate4">
            <?php
            $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
            $actual_link = $protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."";
            $checkREQ='';
            $pattern="/\?p=/";
            $pattern2="/&page-img[0-9]+=[0-9]+/";
            if(preg_match($pattern, $actual_link)){
                if(preg_match($pattern2, $actual_link)){
                    $actual_link=preg_replace($pattern2, '', $actual_link);
                }
                $checkREQ=$actual_link.'&page-img'.$portfolioID.$pID;

            }else{
                $checkREQ='?page-img'.$portfolioID.$pID;
            }
            $pervpage='';
            if ($page != 1) $pervpage = '<a href= '.$checkREQ.'=1><i class="hugeiticons hugeiticons-fast-backward" ></i></a>  
                             <a href= '.$checkREQ.'='. ($page - 1) .'><i class="hugeiticons hugeiticons-chevron-left"></i></a> ';
            $nextpage='';
            if ($page != $total) $nextpage = '<a href= '.$checkREQ.'='. ($page + 1) .'><i class="hugeiticons hugeiticons-chevron-right"></i></a>  
                                  <a href= '.$checkREQ.'=' .$total. '><i class="hugeiticons hugeiticons-fast-forward" ></i></a>';
            echo $pervpage.$page.'/'.$total.$nextpage;

            ?>
        </div>
        <?php
    }
    ?>
 </section>

<script>
jQuery(function(){

var defaultBlockWidth=<?php echo $paramssld['ht_view4_block_width']; ?>;
    var $container = jQuery('#huge_it_portfolio_container_<?php echo $portfolioID; ?>');
    $container.find('.element_<?php echo $portfolioID; ?>').each(function(){
	var $this = jQuery(this),
		number = parseInt( $this.find('.number').text(), 10 );
	if ( number % 7 % 2 === 1 ) {
	  $this.addClass('width2');
	}
	if ( number % 3 === 0 ) {
	  $this.addClass('height2');
	}
    });
    
    $container.hugeitmicro({
      itemSelector : '.element_<?php echo $portfolioID; ?>',
      masonry : {
        columnWidth : <?php echo $paramssld['ht_view4_block_width']; ?>+20+<?php echo $paramssld['ht_view4_element_border_width']*2; ?>
      },
      masonryHorizontal : {
        rowHeight: 300+20
      },
      cellsByRow : {
        columnWidth : 300+20,
        rowHeight : 240
      },
      cellsByColumn : {
        columnWidth : 300+20,
        rowHeight : 240
      },
      getSortData : {
        symbol : function( $elem ) {
	  return $elem.attr('data-symbol');
	},
	category : function( $elem ) {
	  return $elem.attr('data-category');
	},
	number : function( $elem ) {
	  return parseInt( $elem.find('.number').text(), 10 );
	},
	weight : function( $elem ) {
	  return parseFloat( $elem.find('.weight').text().replace( /[\(\)]/g, '') );
	},
        id : function ( $elem ) {
          return $elem.find('.id').text();
        }
      }
    });
    
    
      var $optionSets = jQuery('#huge_it_portfolio_options_<?php echo $portfolioID; ?> .option-set'),
	  $optionLinks = $optionSets.find('a');

  $optionLinks.click(function(){
	var $this = jQuery(this);

	if ( $this.hasClass('selected') ) {
	  return false;
	}
	var $optionSet = $this.parents('.option-set');
	$optionSet.find('.selected').removeClass('selected');
	$this.addClass('selected');


	var options = {},
		key = $optionSet.attr('data-option-key'),
		value = $this.attr('data-option-value');

	value = value === 'false' ? false : value;
	options[ key ] = value;
	if ( key === 'layoutMode' && typeof changeLayoutMode === 'function' ) {

	  changeLayoutMode( $this, options )
	} else {

	  $container.hugeitmicro( options );
	}
	
	return false;
  });
      var isHorizontal = false;
  function changeLayoutMode( $link, options ) {
	var wasHorizontal = isHorizontal;
	isHorizontal = $link.hasClass('horizontal');

	if ( wasHorizontal !== isHorizontal ) {

	  var style = isHorizontal ? 
		{ height: '100%', width: $container.width() } : 
		{ width: 'auto' };

	  $container.filter(':animated').stop();

	  $container.addClass('no-transition').css( style );
	  setTimeout(function(){
		$container.removeClass('no-transition').hugeitmicro( options );
	  }, 700)
	} else {
	  $container.hugeitmicro( options );
	}
  }
      $container.delegate( '.open-close-button', 'click', function(){//	console.log("filter");	//bacel pakel@
		if(jQuery(this).parents('.element_<?php echo $portfolioID; ?>').hasClass("large")){
			jQuery(this).parents('.element_<?php echo $portfolioID; ?>').animate({//
				height: "45px"//pakeluc heto erevacox masi chap@
			}, 700, function() {
				jQuery(this).removeClass('large');
				$container.hugeitmicro('reLayout');
			});
			
			jQuery(this).parents('.element_<?php echo $portfolioID; ?>').removeClass("active");
			return false;
		}
		jQuery(this).parents('.element_<?php echo $portfolioID; ?>').css({height:45+jQuery(this).parents('.element_<?php echo $portfolioID; ?>').find('.wd-portfolio-panel_<?php echo $portfolioID; ?>').height()});
		jQuery(this).parents('.element_<?php echo $portfolioID; ?>').addClass('large');

		$container.hugeitmicro('reLayout');
		jQuery(this).parents('.element_<?php echo $portfolioID; ?>').css({height:"45px"});
		  
		 var strheight=(jQuery(this).parents('.element_<?php echo $portfolioID; ?>').find('.wd-portfolio-panel_<?php echo $portfolioID; ?>').height()+35);
		 
		 
		 jQuery(this).parents('.element_<?php echo $portfolioID; ?>').animate({
			height:strheight+"px",
		  }, 700,function(){	$container.hugeitmicro('reLayout');});
	});

    var $sortBy =  jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?> #sort-by');
    jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?> #shuffle a').click(function(){
      $container.hugeitmicro('shuffle');
      $sortBy.find('.selected').removeClass('selected');
      $sortBy.find('[data-option-value="random"]').addClass('selected');
      return false;
    });
    
    ////filteringgggggg

        // bind filter on select change
        jQuery(document).ready(function(){
            jQuery('#huge_it_portfolio_filters_<?php echo $portfolioID; ?> ul li').click(function() {
              var filterValue = jQuery(this).attr('rel');
              filterValue = filterValue;
              $container.hugeitmicro({ filter: filterValue });
            });
            <?php if(($paramssld["ht_view4_sorting_float"] == "left" || $paramssld["ht_view4_sorting_float"] == "right") && $paramssld["ht_view4_filtering_float"] == "none")
                  { ?>
                        var topmargin = jQuery("#huge_it_portfolio_filters_<?php echo $portfolioID; ?> ul").height();
                        jQuery("#huge_it_portfolio_options_<?php echo $portfolioID; ?>").css({'margin-top':parseInt(topmargin) + 5});
            <?php }
            else  {
                    if(($paramssld["ht_view4_filtering_float"] == "left" || $paramssld["ht_view4_filtering_float"] == "right") && $paramssld["ht_view4_sorting_float"] == "none")
                      { ?>
                         var topmargin = jQuery("#huge_it_portfolio_options_<?php echo $portfolioID; ?>").height();
                         jQuery("#huge_it_portfolio_filters_<?php echo $portfolioID; ?>").css({'margin-top':'5px'});
                <?php }
                  } ?>
        });

  });
  jQuery(window).load(function(){
		jQuery(window).resize(function(){$container.hugeitmicro('reLayout');});
	});
</script>
 <?php
    break;
/////////////////////////////////// VIEW 5 Slider ////////////////////////////////////
    case 5;
?>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.touchswipe/1.6.4/jquery.touchSwipe.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.0.0/animate.min.css">
  <link href="<?php echo JUri::root().'media/com_portfoliogallery/style/liquid-slider.css';?>" rel="stylesheet" type="text/css" />
  
  <style>
/***<add>***/
#main-slider_<?php echo $portfolioID; ?> .play-icon.youtube-icon  {
    background: url(<?php echo JUri::root().'media/com_portfoliogallery/images/play.youtube.png';?>) center center no-repeat;
	background-size: 30% 30%;
}
#main-slider_<?php echo $portfolioID; ?> .play-icon.vimeo-icon  {
	background: url(<?php echo JUri::root().'media/com_portfoliogallery/images/play.vimeo.png' ;?>) center center no-repeat;
	background-size: 30% 30%;
}
#main-slider_<?php echo $portfolioID; ?> .play-icon {
    position: absolute;
    top: 0px;
    left: 0px;
    width: 100%;
    height: 100%;
}
#main-slider_<?php echo $portfolioID; ?>  .add-H-relative {
	position: relative;
}	
#main-slider_<?php echo $portfolioID; ?>  .add-H-block {
	display: block;
}	
/***</add>***/
#main-slider_<?php echo $portfolioID; ?>-wrapper .ls-nav { display: none; }
#main-slider_<?php echo $portfolioID; ?> {background:#<?php echo $paramssld["ht_view5_slider_background_color"];?>;}

#main-slider_<?php echo $portfolioID; ?> div.slider-content {
	position:relative;
	width:100%;
	padding:0px 0px 0px 0px;
	position:relative;
	background:#<?php echo $paramssld["ht_view5_slider_background_color"];?>;
}



[class$="-arrow"] {
	background-image:url(<?php echo JUri::root().'media/com_portfoliogallery/images/arrow.'.$paramssld["ht_view5_icons_style"].'.png';?>);
}

.ls-select-box {
	background:url(<?php echo JUri::root().'media/com_portfoliogallery/images/menu.'.$paramssld["ht_view5_icons_style"].'.png';?>) right center no-repeat #<?php echo $paramssld["ht_view5_slider_background_color"];?>;
}

#main-slider_<?php echo $portfolioID; ?>-nav-select {
	color:#<?php echo $paramssld["ht_view5_title_font_color"];?>;
}

#main-slider_<?php echo $portfolioID; ?> div.slider-content .slider-content-wrapper {
	position:relative;
	width:100%;
	padding:0px;
	display:block;
}

#main-slider_<?php echo $portfolioID; ?> .slider-content-wrapper .image-block_<?php echo $portfolioID; ?> {
	width:<?php echo $paramssld["ht_view5_main_image_width"];?>px;
	display:table-cell;
	padding:0px 10px 0px 0px;
	float:left;
}

#main-slider_<?php echo $portfolioID; ?> .slider-content-wrapper .image-block_<?php echo $portfolioID; ?> img.main-image {
	position:relative;
	width:100%;
	height:auto;
	display:block;
}

#main-slider_<?php echo $portfolioID; ?> .slider-content-wrapper .image-block_<?php echo $portfolioID; ?> ul.thumbs-list_<?php echo $portfolioID; ?> {
	list-style:none;
	display:table;
	position:relative;
	clear:both;
	width:100%;
	margin:10px 0px 0px 0px;
	padding:0px;
	clear:both;
}

#main-slider_<?php echo $portfolioID; ?> .slider-content-wrapper .image-block_<?php echo $portfolioID; ?> ul.thumbs-list_<?php echo $portfolioID; ?> li {
	display:block;
	float:left;
	width:<?php echo $paramssld["ht_view5_thumbs_width"];?>px;
	height:<?php echo $paramssld["ht_view5_thumbs_height"];?>px;
	margin:0px 2% 5px 1%;
	opacity:0.45;
}

#main-slider_<?php echo $portfolioID; ?> .slider-content-wrapper .image-block_<?php echo $portfolioID; ?> ul.thumbs-list_<?php echo $portfolioID; ?> li.active,#main-slider_<?php echo $portfolioID; ?> .slider-content-wrapper .image-block_<?php echo $portfolioID; ?> ul.thumbs-list_<?php echo $portfolioID; ?> li:hover {
	opacity:1;
}

#main-slider_<?php echo $portfolioID; ?> .slider-content-wrapper .image-block_<?php echo $portfolioID; ?> ul.thumbs-list_<?php echo $portfolioID; ?> li a {
	display:block;
}

#main-slider_<?php echo $portfolioID; ?> .slider-content-wrapper .image-block_<?php echo $portfolioID; ?> ul.thumbs-list_<?php echo $portfolioID; ?> li img {
	margin:0px !important;
	padding:0px !important;
	width:<?php echo $paramssld["ht_view5_thumbs_width"];?>px !important;
	height:<?php echo $paramssld["ht_view5_thumbs_height"];?>px !important;
}

#main-slider_<?php echo $portfolioID; ?> .slider-content-wrapper .right-block {
	display:table-cell;
}

#main-slider_<?php echo $portfolioID; ?> .slider-content-wrapper .right-block > div {
	padding-bottom:10px;
	margin-top:10px;
	<?php if($paramssld['ht_view5_show_separator_lines']=="on") {?>
		background:url('<?php echo JUri::root().'media/com_portfoliogallery/images/divider.line.png'; ?>') center bottom repeat-x;
	<?php } ?>
}
#main-slider_<?php echo $portfolioID; ?> .slider-content-wrapper .right-block > div:last-child {background:none;}


#main-slider_<?php echo $portfolioID; ?> .slider-content-wrapper .right-block .title {
	position:relative;
	display:block;
	margin:-10px 0px 0px 0px;
	font-size:<?php echo $paramssld["ht_view5_title_font_size"];?>px !important;
	line-height:<?php echo $paramssld["ht_view5_title_font_size"]+4;?>px !important;
	color:#<?php echo $paramssld["ht_view5_title_font_color"];?>;
}

#main-slider_<?php echo $portfolioID; ?> .slider-content-wrapper .right-block .description {
	clear:both;
	position:relative;
	font-weight:normal;
	text-align:justify;
	font-size:<?php echo $paramssld["ht_view5_description_font_size"];?>px !important;
	line-height:<?php echo $paramssld["ht_view5_description_font_size"]+4;?>px !important;
	color:#<?php echo $paramssld["ht_view5_description_color"];?>;
}

#main-slider_<?php echo $portfolioID; ?> .slider-content-wrapper .right-block .description h1,
#main-slider_<?php echo $portfolioID; ?> .slider-content-wrapper .right-block .description h2,
#main-slider_<?php echo $portfolioID; ?> .slider-content-wrapper .right-block .description h3,
#main-slider_<?php echo $portfolioID; ?> .slider-content-wrapper .right-block .description h4,
#main-slider_<?php echo $portfolioID; ?> .slider-content-wrapper .right-block .description h5,
#main-slider_<?php echo $portfolioID; ?> .slider-content-wrapper .right-block .description h6,
#main-slider_<?php echo $portfolioID; ?> .slider-content-wrapper .right-block .description p, 
#main-slider_<?php echo $portfolioID; ?> .slider-content-wrapper .right-block .description strong,
#main-slider_<?php echo $portfolioID; ?> .slider-content-wrapper .right-block .description span {
	padding:2px !important;
	margin:0px !important;
}

#main-slider_<?php echo $portfolioID; ?> .slider-content-wrapper .right-block .description ul,
#main-slider_<?php echo $portfolioID; ?> .slider-content-wrapper .right-block .description li {
	padding:2px 0px 2px 5px;
	margin:0px 0px 0px 8px;
}



#main-slider_<?php echo $portfolioID; ?> .slider-content-wrapper .button-block {
	position:relative;
}

#main-slider_<?php echo $portfolioID; ?> .slider-content-wrapper .button-block a,#main-slider_<?php echo $portfolioID; ?> .slider-content-wrapper .button-block a:link,#main-slider_<?php echo $portfolioID; ?> .slider-content-wrapper .button-block a:visited{
	position:relative;
	display:inline-block;
	padding:6px 12px;
	background:#<?php echo $paramssld["ht_view5_linkbutton_background_color"];?>;
	color:#<?php echo $paramssld["ht_view5_linkbutton_color"];?>;
	font-size:<?php echo $paramssld["ht_view5_linkbutton_font_size"];?>;
	text-decoration:none;
}

#main-slider_<?php echo $portfolioID; ?> .slider-content-wrapper .button-block a:hover,#main-slider_<?php echo $portfolioID; ?> .slider-content-wrapper .button-block a:focus,#main-slider_<?php echo $portfolioID; ?> .slider-content-wrapper .button-block a:active {
	background:#<?php echo $paramssld["ht_view5_linkbutton_background_hover_color"];?>;
	color:#<?php echo $paramssld["ht_view5_linkbutton_font_hover_color"];?>;
}

@media only screen and (min-width:500px) {
	#main-slider-nav-ul {
		visibility:hidden !important;
		height:1px;
	}
}

@media only screen and (max-width:500px) {
	#main-slider_<?php echo $portfolioID; ?> .slider-content-wrapper .image-block_<?php echo $portfolioID; ?>,#main-slider_<?php echo $portfolioID; ?> .slider-content-wrapper .right-block {
		width:100%;
		display:block;
		float:none;
		clear:both;
	}
}
</style>
<div id="main-slider_<?php echo $portfolioID; ?>" class="liquid-slider">
    <?php
        $group_key = 0;
	foreach($images as $key=>$row){	
	    $group_key++;
        $group_key1 = (string)$group_key;
		$imgurl=explode(";",$row->image_url);array_pop($imgurl);
		$link = $row->sl_url;
		$descnohtml=strip_tags($row->description);
        $descnohtml = html_entity_decode($descnohtml);

		$result = substr($descnohtml, 0, 50);

        $imgurl_exp = explode('/',$imgurl[0]);
        if($imgurl_exp[0] == 'http:' || $imgurl_exp[0] == 'https:'  ){
            $path_juri = '';
        }
        else{
            $path_juri = JUri::root();
        }
		?>
		<div class="slider-content">
                    <div class="slider-content-wrapper slide_number<?php echo $group_key1;?>" >
                        <div class="image-block_<?php echo $portfolioID; ?>">
                            <?php 	
                                    if($row->image_url != ';'){
                                        switch($this->youtube_or_vimeo_portfolio($imgurl[0])) {
                                            case 'image':?>
                            <a class="portfolio-group-slider<?php  echo $group_key1; ?>" href="<?php echo $path_juri.$imgurl[0]; ?>" title="<?php echo $row->name; ?>"><img alt="<?php echo $row->name; ?>" class="main-image" src="<?php echo $imgurl[0]; ?>" /></a>
						<?php 
                                                    break;
                                                    case 'youtube':
                                                    $videourl=$this->get_video_id_from_url_portfolio($imgurl[0]);?>
                                                    <a href="https://www.youtube.com/embed/<?php echo $videourl[0];?>" class="huge_it_portfolio_item youtube portfolio-group-slider<?php  echo $group_key1; ?> add-H-relative add-H-block"  title="<?php echo $row->name; ?>">
                                                        <img src="http://img.youtube.com/vi/<?php echo $videourl[0]; ?>/mqdefault.jpg"><div class="play-icon <?php echo $videourl[1];?>-icon"></div>
                                                    </a> 	
						<?php
                                                    break;
                                                    case 'vimeo':
                                                    $videourl=$this->get_video_id_from_url_portfolio($imgurl[0]);
                                                    $hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/".$videourl[0].".php"));
                                                    $imgsrc=$hash[0]['thumbnail_large'];
                                                    ?>
                                                    <a class="huge_it_portfolio_item vimeo portfolio-group-slider<?php  echo $group_key1; ?>   add-H-relative add-H-block" href="http://player.vimeo.com/video/<?php echo $videourl[0]; ?>" title="<?php echo $row->name; ?>">
                                                        <img src="<?php echo $imgsrc; ?>" alt="<?php echo $row->name; ?>" /><div class="play-icon <?php echo $videourl[1];?>-icon"></div>
                                                    </a>
					<?php	break;	
                                        }
                                    } else { ?>
                                    <img alt="<?php echo $row->name; ?>" class="main-image" src="images/noimage.jpg" />
                                    <?php } ?>
					
                                    <?php if($paramssld["ht_view5_show_thumbs"]){?>
					<div><ul class="thumbs-list_<?php echo $portfolioID; ?>">
                                            <?php  
						array_shift($imgurl);
                                                    foreach($imgurl as $key=>$img){
                                                        switch($this->youtube_or_vimeo_portfolio($img)) {
                                                            case 'image':
                                                        ?>
                                                <li><a class="portfolio-group-slider<?php echo $group_key1; ?>" href="<?php echo $img; ?>" title = "<?php ?>"><img src="<?php echo $img; ?>"></a></li>
                                                        <?php 
                                                        break;
                                                        case 'youtube':
                                                        $videourl=$this->get_video_id_from_url_portfolio($img);?>
                                                            <li>
                                                                <a href="https://www.youtube.com/embed/<?php echo $videourl[0];?>" class="huge_it_portfolio_item youtube portfolio-group-slider<?php  echo $group_key1; ?>  add-H-relative"  title = "<?php  ?>">
                                                                    <img src="http://img.youtube.com/vi/<?php echo $videourl[0]; ?>/mqdefault.jpg"><div class="play-icon <?php echo $videourl[1];?>-icon"></div>
                                                                </a>
                                                            </li>	
                                                        <?php
                                                        break;
                                                        case 'vimeo':
                                                        $videourl = $this->get_video_id_from_url_portfolio($img);
                                                        $hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/".$videourl[0].".php"));
                                                        $imgsrc=$hash[0]['thumbnail_large'];?>
                                                        <li>
                                                            <a class="huge_it_portfolio_item vimeo portfolio-group-slider<?php  echo $group_key1; ?>  add-H-relative" href="http://player.vimeo.com/video/<?php echo $videourl[0]; ?> " title="<?php echo $row->name; ?>"  style="position:relative">
                                                                <img src="<?php echo $imgsrc; ?>" alt="<?php echo $row->name; ?>" /><div class="play-icon <?php echo $videourl[1];?>-icon"></div>
                                                            </a>
                                                        </li>
                                                <?php 
                                                        break;	
                                                        }
                                                    } ?>
					</ul></div>
					<?php } ?>					
				</div>
				<div class="right-block">
                                    <div><h2 class="title"><?php echo $row->name; ?></h2></div>
                                    <?php if($paramssld["ht_view5_show_description"]=='on'){?><div class="description"><?php echo $row->description; ?></div><?php } ?>
                                    <?php if($paramssld["ht_view5_show_linkbutton"]=='on' && $paramssld["ht_view5_linkbutton_text"] != '' && $link != ''){?>
                                        <div class="button-block">
                                            <a class="" href="<?php echo $link; ?>"  <?php if ($row->link_target=="on"){echo 'target="_blank"';}?>><?php echo $paramssld["ht_view5_linkbutton_text"]; ?></a>
                                        </div>
                                    <?php } ?>
				</div>
			</div>
		</div>
		<?php
	} ?>
</div>

<script src="<?php echo JUri::root().'media/com_portfoliogallery/js/jquery.liquid-slider.min.js';?>"></script>  
   <script>
    /**
     * If you need to access the internal property or methods, use this:
     * var api = $.data( jQuery('#main-slider_<?php echo $portfolioID; ?>')[0], 'liquidSlider');
     * console.log(api);
     */
	 jQuery('#main-slider_<?php echo $portfolioID; ?>').liquidSlider();
  </script>
  <?php  
    break;
/////////////////////////////// VIEW 6 Gallery /////////////////////////////
    case 6:
?>
  <style type="text/css">

<?php
    if($paramssld["ht_view6_sorting_float"] == "left" && $paramssld["ht_view6_filtering_float"] == "right" ||
       $paramssld["ht_view6_sorting_float"] == "right" && $paramssld["ht_view6_filtering_float"] == "left" ||
       $paramssld["ht_view6_sorting_float"] == $paramssld["ht_view6_filtering_float"])
       { $sorting_block_width ="20%"; $filtering_block_width ="20%"; $width_middle = "56%"; }
    else if($paramssld["ht_view6_sorting_float"] == "left" || $paramssld["ht_view6_sorting_float"] == "right" && $paramssld["ht_view6_filtering_float"] == "top")
       { $sorting_block_width ="30%"; $filtering_block_width ="100%"; $paramssld["ht_view6_filtering_float"] = "none"; $width_middle = "65%"; }
    else if($paramssld["ht_view6_filtering_float"] == "left" || $paramssld["ht_view6_filtering_float"] == "right" && $paramssld["ht_view6_sorting_float"] == "top")
       { $sorting_block_width ="100%"; $filtering_block_width ="30%"; $paramssld["ht_view6_sorting_float"] = "none"; $width_middle = "65%"; }
    if($paramssld["ht_view6_sorting_float"] == "top" && $paramssld["ht_view6_filtering_float"] == "top")
       { $sorting_block_width ="100%"; $filtering_block_width ="100%"; $left_to_top = "ok"; }
?>
/***<add>***/
 .play-icon.youtube-icon  {
     background: url(<?php echo JUri::root().'media/com_portfoliogallery/images/play.youtube.png';?>) center center no-repeat;
	background-size: 30% 30%;
}
 .play-icon.vimeo-icon  {
	background: url(<?php echo JUri::root().'media/com_portfoliogallery/images/play.vimeo.png' ;?>) center center no-repeat;
	background-size: 30% 30%;
}
.play-icon {
    position: absolute;
    top: 0px;
    left: 0px;
    width: 100%;
    height: 100%;
}	
/***</add>***/
.element_<?php echo $portfolioID; ?> {
        max-width: 459px;
	width:<?php echo $paramssld['ht_view6_width']; ?>px;
	margin:0px 0px 10px 0px;
	border:<?php echo $paramssld['ht_view6_border_width']; ?>px solid #<?php echo $paramssld['ht_view6_border_color']; ?>;
	border-radius:<?php echo $paramssld['ht_view6_border_radius']; ?>px;
	outline:none;
	overflow:hidden;
}

.element_<?php echo $portfolioID; ?> .image-block_<?php echo $portfolioID; ?> {
	position:relative;
	width:<?php echo $paramssld['ht_view6_width']; ?>px;
}
.element_<?php echo $portfolioID; ?> .image-block_<?php echo $portfolioID; ?> a {
	display: block;
	cursor: -webkit-zoom-in; cursor: -moz-zoom-in;
}
.element_<?php echo $portfolioID; ?> .image-block_<?php echo $portfolioID; ?> img {
	margin:0px !important;
	padding:0px !important;
	width:<?php echo $paramssld['ht_view6_width']; ?>px !important;
	height:auto;
	display:block;
	border-radius: 0px !important;
	box-shadow: 0 0px 0px rgba(0, 0, 0, 0) !important; 
}

.element_<?php echo $portfolioID; ?> .image-block_<?php echo $portfolioID; ?> img:hover {
	cursor: -webkit-zoom-in; cursor: -moz-zoom-in;
}

.element_<?php echo $portfolioID; ?> .title-block_<?php echo $portfolioID; ?> {
	position:absolute;
	
	left:0px;
	width:100%;
	padding-top:5px;
	height: <?php echo $paramssld["ht_view6_title_font_size"] + 10; ?>px;
	bottom:-<?php echo $paramssld["ht_view6_title_font_size"] + 15; ?>px;
	background: <?php
			list($r,$g,$b) = array_map('hexdec',str_split($paramssld['ht_view6_title_background_color'],2));
				$titleopacity=$paramssld["ht_view6_title_background_transparency"]/100;						
				echo 'rgba('.$r.','.$g.','.$b.','.$titleopacity.')  !important'; 		
	?>;
	 -webkit-transition: bottom 0.3s ease-out 0.1s;
     -moz-transition: bottom 0.3s ease-out 0.1s;
     -o-transition: bottom 0.3s ease-out 0.1s;
     transition: bottom 0.3s ease-out 0.1s;
}

.element_<?php echo $portfolioID; ?>:hover .title-block_<?php echo $portfolioID; ?> {bottom:0px;}

.element_<?php echo $portfolioID; ?> .title-block_<?php echo $portfolioID; ?> a, .element_<?php echo $portfolioID; ?> .title-block_<?php echo $portfolioID; ?> a:link, .element_<?php echo $portfolioID; ?> .title-block_<?php echo $portfolioID; ?> a:visited {
	position:relative;
	margin:0px;
	padding:0px 1% 0px 2%;
	width:97%;
	text-decoration:none;
	text-overflow: ellipsis;
	overflow: hidden; 
	white-space:nowrap;
	z-index:20;
	font-size: <?php echo $paramssld["ht_view6_title_font_size"];?>px;
	color:#<?php echo $paramssld["ht_view6_title_font_color"];?>;
	font-weight:normal;
}



.element_<?php echo $portfolioID; ?> .title-block_<?php echo $portfolioID; ?> a:hover, .element_<?php echo $portfolioID; ?> .title-block_<?php echo $portfolioID; ?> a:focus, .element_<?php echo $portfolioID; ?> .title-block_<?php echo $portfolioID; ?> a:active {
	color:#<?php echo $paramssld["ht_view6_title_font_hover_color"];?>;
	text-decoration:none;
}

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_options_<?php echo $portfolioID; ?> {
    <?php
      if($paramssld["ht_view6_filtering_float"] == 'left' && $paramssld["ht_view6_sorting_float"] == 'none') {  if($portfolioShowFiltering == "on") { echo "margin-left: 30%;"; } else { echo "margin-left: 0%;"; }   } ?>
    overflow: hidden;
    display:block;
    /*margin-top: 5px;*/
    float: <?php echo $paramssld["ht_view6_sorting_float"]; ?>;
    width:<?php echo $sorting_block_width; ?>;
}

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_options_<?php echo $portfolioID; ?> ul {
  margin: 0px !important;
  padding: 0px !important;
  list-style: none;
<?php if($paramssld["ht_view6_sorting_float"] == 'top') {
      echo "display:inline-block;margin-left:1%;";
      } ?>
}

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?> ul {
  margin: 0px !important;
  padding: 0px !important;
  overflow: hidden;
  <?php if($paramssld["ht_view6_filtering_float"] == 'top') {
      echo "display:inline-block;margin-left:1%;";
      } ?>
}

<?php if($paramssld["ht_view6_sorting_float"] == 'top') { ?>
            #huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_options_<?php echo $portfolioID; ?> ul {
                float: left;
            }
    <?php } ?>
            
    <?php if($paramssld["ht_view6_sorting_float"] == 'none') { ?>
            #huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_options_<?php echo $portfolioID; ?> ul {
                float: left;
            }
    <?php } ?>
    
#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_options_<?php echo $portfolioID; ?> ul li {
    border-radius: <?php echo $paramssld["ht_view6_sortbutton_border_radius"];?>px;
    list-style-type: none;
    margin: 0px !important;
	padding: 0;
    <?php
        if($sorting_block_width == "100%" ) {
            echo "float:left !important;margin: 4px 8px 4px 0px !important;";
        }
        if($left_to_top == "ok")
        { echo "float:left !important;"; }
        if($paramssld["ht_view6_sorting_float"] == "left" || $paramssld["ht_view6_sorting_float"] == "right")
        { echo 'border-bottom: 1px solid #ccc;'; }
        else
        { echo 'border: 1px solid #ccc;'; }
    ?>
}

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_options_<?php echo $portfolioID; ?> ul li a {
    background-color: #<?php echo $paramssld["ht_view6_sortbutton_background_color"];?> !important;
    font-size:<?php echo $paramssld["ht_view6_sortbutton_font_size"];?>px !important;
    color:#<?php echo $paramssld["ht_view6_sortbutton_font_color"];?> !important;
    text-decoration: none;
    cursor: pointer;
    margin: 0px !important;
    display: block;
    padding:3px;
}

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_container_<?php echo $portfolioID; ?> {
    margin-top: 5px;
    <?php
        if($paramssld["ht_view6_sorting_float"] == "left" && $paramssld["ht_view6_filtering_float"] == "right" ||
           $paramssld["ht_view6_sorting_float"] == "right" && $paramssld["ht_view6_filtering_float"] == "left")
        { ?>
            margin-left: 21%;
  <?php } ?>
}

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_options_<?php echo $portfolioID; ?> ul li a:hover {
    background-color: #<?php echo $paramssld["ht_view6_sortbutton_hover_background_color"];?> !important;
    color:#<?php echo $paramssld["ht_view6_sortbutton_hover_font_color"];?> !important;
    cursor: pointer;
}

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?> {
    /*margin-top: 5px;*/
    float: <?php echo $paramssld["ht_view6_filtering_float"]; ?>;
    width: <?php echo $filtering_block_width; ?>;
    <?php
        if ($paramssld["ht_view6_show_filtering"] == 'off') echo "display:none;";
        if($paramssld["ht_view6_filtering_float"] == 'top' && $paramssld["ht_view6_sorting_float"] == 'left' ) {  if($portfolioShowSorting == 'on') { echo "margin-left: 30%;"; } else echo "margin-left: 0%"; }
    ?>
}

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?> ul li {
    list-style-type: none;
    <?php
        if($filtering_block_width == "100%") { echo "float:left !important;margin: 4px 8px 4px 0px !important;"; }
        if($left_to_top == "ok") { echo "float:left !important;"; }
        if($paramssld["ht_view6_filtering_float"] == "left" || $paramssld["ht_view6_filtering_float"] == "right")
        { echo 'border-bottom: 1px solid #ccc;'; }
        else echo "border: 1px solid #ccc;";
    ?>
}

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?> ul li a {
    font-size:<?php echo $paramssld["ht_view6_filterbutton_font_size"];?>px !important;
    color:#<?php echo $paramssld["ht_view6_filterbutton_font_color"];?> !important;
    background-color: #<?php echo $paramssld["ht_view6_filterbutton_background_color"];?> !important;
    border-radius: <?php echo $paramssld["ht_view6_filterbutton_border_radius"];?>px;
    padding: 3px;
    display: block;
    text-decoration: none;
}

#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?>  ul li a:hover {
    color:#<?php echo $paramssld["ht_view6_filterbutton_hover_font_color"];?> !important;
    background-color: #<?php echo $paramssld["ht_view6_filterbutton_hover_background_color"];?> !important;
    cursor: pointer
}
#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?> ul li.active a,
#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?> ul li.active a:link,
#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?> ul li.active a:visited,
#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?>  ul li.active a:hover,
#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?>  ul li.active a:focus,
#huge_it_portfolio_content_<?php echo $portfolioID; ?> #huge_it_portfolio_filters_<?php echo $portfolioID; ?>  ul li.active a:active {
    color:#<?php echo $paramssld["ht_view6_filterbutton_hover_font_color"];?> !important;
    background-color: #<?php echo $paramssld["ht_view6_filterbutton_hover_background_color"];?> !important;
    cursor: pointer;
}
#huge_it_portfolio_content_<?php echo $portfolioID; ?> section {
    position:relative;
    display:block;
}

#huge_it_portfolio_content_<?php echo $portfolioID; ?> .huge_it_portfolio_container_styles_<?php echo $portfolioID; ?> {
<?php if($paramssld["ht_view6_sorting_float"] == "left" && $paramssld["ht_view6_filtering_float"] == "right" ||
         $paramssld["ht_view6_sorting_float"] == "right" && $paramssld["ht_view6_filtering_float"] == "left")
       { echo "margin: 0px auto;"; }
      if((($paramssld["ht_view6_filtering_float"] == "left" || $paramssld["ht_view6_filtering_float"] == "right" && $paramssld["ht_view6_sorting_float"] == "top") || ($paramssld["ht_view6_sorting_float"] == "left" || $paramssld["ht_view6_sorting_float"] == "right" && $paramssld["ht_view6_filtering_float"] == "top")) && $portfolioShowFiltering == "on" && $portfolioShowSorting == "on")
        { ?>
            width: <?php echo $width_middle; ?> !important;
  <?php } ?>
       overflow: hidden !important;
}

#sort-direction {
    <?php if($paramssld["ht_view6_sorting_float"] == "top")
       { echo "float: left !important;"; }
?>
}
.load_more4 {
    margin: 10px 0;
    position:relative;
    text-align:<?php if($paramssld['ht_view6_loadmore_position'] == 'left') {echo 'left';}
			elseif ($paramssld['ht_view6_loadmore_position'] == 'center') { echo 'center'; }
			elseif($paramssld['ht_view6_loadmore_position'] == 'right') { echo 'right'; }?>;

    width:100%;


}

.load_more_button4 {
    border-radius: 10px;
    display:inline-block;
    padding:5px 15px;
    font-size:<?php echo $paramssld['ht_view6_loadmore_fontsize']; ?>px !important;;
    color:<?php echo '#'.$paramssld['ht_view6_loadmore_font_color']; ?> !important;;
    background:<?php echo '#'.$paramssld['ht_view6_button_color']; ?> !important;
    cursor:pointer;

}
.load_more_button4:hover{
    color:<?php echo '#'.$paramssld['ht_view6_loadmore_font_color_hover']; ?> !important;
    background:<?php echo '#'.$paramssld['ht_view6_button_color_hover']; ?> !important;
}

.loading4 {
    display:none;
}
.paginate4{
    font-size:<?php echo $paramssld['ht_view6_paginator_fontsize']; ?>px !important;
    color:<?php echo '#'.$paramssld['ht_view6_paginator_color']; ?> !important;
    text-align: <?php echo $paramssld['ht_view6_paginator_position']; ?>;
    margin-top: 25px;
}
.paginate4 a{
    border-bottom: none !important;
    color: #<?php echo $paramssld['ht_view6_paginator_icon_color']; ?> !important;
    font-size:<?php echo $paramssld['ht_view6_paginator_icon_size']; ?>px !important;
}
.icon-style4{
    font-size: <?php echo $paramssld['ht_view6_paginator_icon_size']; ?>px !important;
    color:<?php echo '#'.$paramssld['ht_view6_paginator_icon_color']; ?> !important;
}
.clear{
    clear:both;
}
</style>
<section id="huge_it_portfolio_content_<?php echo $portfolioID; ?>" class="">
    <?php if($portfolioShowSorting == "on"){ ?>
        <div id="huge_it_portfolio_options_<?php echo $portfolioID; ?>" class="">
            <ul id="sort-by" class="option-set clearfix" data-option-key="sortBy">
                <li><a href="#sortBy=original-order" data-option-value="original-order" class="selected" data><?php echo $paramssld["ht_view6_sorting_name_by_default"]; ?></a></li>
                <li><a href="#sortBy=id" data-option-value="id"><?php echo $paramssld["ht_view6_sorting_name_by_id"]; ?></a></li>
                <li><a href="#sortBy=symbol" data-option-value="symbol"><?php echo $paramssld["ht_view6_sorting_name_by_name"]; ?></a></li>
                <li id="shuffle"><a href='#shuffle'><?php echo $paramssld["ht_view6_sorting_name_by_random"]; ?></a></li>
            </ul>

          <ul id="sort-direction" class="option-set clearfix" data-option-key="sortAscending">
              <li><a href="#sortAscending=true" data-option-value="true" class="selected"><?php echo $paramssld["ht_view6_sorting_name_by_asc"]; ?></a></li>
              <li><a href="#sortAscending=false" data-option-value="false"><?php echo $paramssld["ht_view6_sorting_name_by_desc"]; ?></a></li>
          </ul>
        </div>
  <?php }
   if($portfolioShowFiltering == "on") { ?>
        <div id="huge_it_portfolio_filters_<?php echo $portfolioID; ?>">
            <ul>
                <li rel="*"><a><?php echo $paramssld["ht_view6_cat_all"];?></a></li>
                <?php
                $portfolioCats = explode(",", $portfolioCats);
                foreach ($portfolioCats as $portfolioCatsValue) {
                    if(!empty($portfolioCatsValue)) {
                ?>
                <li rel=".<?php echo str_replace(" ","_",$portfolioCatsValue); ?>"><a><?php echo str_replace("_"," ",$portfolioCatsValue); ?></a></li>
                    <?php
                    }
                }
                ?>
            </ul>
        </div>
<?php } ?>
    <div id="huge_it_portfolio_container_<?php echo $portfolioID; ?>" class="super-list variable-sizes clearfix" <?php if($paramssld["ht_view6_sorting_float"] == "top" && $paramssld["ht_view6_filtering_float"] == "top") echo "style='clear: both;'";?>>
        <?php
        $gallery = '';
        foreach($images as $image){
            $idofgallery=$image->portfolio_id ;
        }
        $db = JFactory::getDBO();
        $query2 = $db->getQuery(true);
        $query2->select('*');
        $query2->from('#__huge_itportfolio_portfolios');
        $query2 -> where('id ='.$idofgallery);
        $query2 ->order('#__huge_itportfolio_portfolios.ordering asc');
        $db->setQuery($query2);
        $gallery = $db->loadObjectList();
        $pattern='/-/';
        $pID = '';
        $post = 0;
        foreach ($gallery as $gall) {
            global $post;
            $pID=$post;
            $disp_type=$gall->pagination_type;
            $count_page=$gall->count_into_page;
            if($count_page==0){
                $count_page=999;
            }elseif(preg_match($pattern, $count_page)){
                $count_page=preg_replace($pattern, '', $count_page);
            }
        }
        $num=$count_page;
        $total = intval(((count($images) - 1) / $num) + 1);
        if(isset($_GET['page-img'.$portfolioID.$pID])){
            $page = $_GET['page-img'.$portfolioID.$pID];
        }else{
            $page = '';

        }
        $page = intval($page);
        if(empty($page) or $page < 0) $page = 1;
        if($page > $total) $page = $total;
        $start = $page * $num - $num;
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__huge_itportfolio_images');
        $query -> where('portfolio_id ='.$idofgallery);
        $query ->order('#__huge_itportfolio_images.ordering asc');
        $db->setQuery($query,$start,$num);
        $page_images = $db->loadObjectList();
        if($disp_type=='show_all'){

            $page_images=$images;
            $count_page=9999;
        }
        $group_key1= 0;
        foreach($page_images as $key=>$row) {
            $group_key1++;
            $group_key = (string)$group_key1;
            $portfolioID1 = (string)$portfolioID;
            $group_key =$group_key."-".$portfolioID;
            $link = $row->sl_url;
            $descnohtml=strip_tags($row->description);
            $descnohtml = html_entity_decode($descnohtml);
            $result = substr($descnohtml, 0, 50);
            $catForFilter = explode(",", $row->category);
            $imgurl=explode(";",$row->image_url);
            $lighboxable = (count($imgurl) == 2)?"lighboxable":"dropdownable";
            ?>
            <input type="hidden" class="pagenum" value="1" />
            <input type="hidden" id="total" value="<?=$total; ?>" />
            <div class="element_<?php echo $portfolioID; ?> portfolio-lightbox <?php foreach ($catForFilter as $catForFilterValue) { echo str_replace(" ","_",$catForFilterValue)." ";} ?> " tabindex="0" data-symbol="<?php echo $row->name; ?>" data-category="alkaline-earth">
                <p style="display: none;" class="id"><?php echo $row->id; ?></p>
                    <div class="image-block_<?php echo $portfolioID; ?>">
                        <?php //echo $row->id; ?>
                            <?php $imgurl=explode(";",$row->image_url); ?>
                                <?php
                                $imgurl_exp = explode('/',$imgurl[0]);
                                if($imgurl_exp[0] == 'http:' || $imgurl_exp[0] == 'https:'  ){
                                    $path_juri = '';
                                }
                                else{
                                    $path_juri = JUri::root();
                                }
					if($row->image_url != ';'){
                                            switch($this->youtube_or_vimeo_portfolio($imgurl[0])) { 
                                                case 'image':	?>
                        <a href="<?php echo $path_juri.$imgurl[0]; ?>" class=" portfolio-lightbox-group" title = "<?php echo $row->name; ?>">
                                                        <img alt="<?php echo $row->name; ?>" id="wd-cl-img<?php echo $key; ?>" src="<?php echo $imgurl[0]; ?>" />
                                                    </a>
                            <?php 
                                            break;
                                            case 'youtube':
                                                $videourl=$this->get_video_id_from_url_portfolio($imgurl[0]);?>
                                                    <a href="https://www.youtube.com/embed/<?php echo $videourl[0];?>" class="huge_it_portfolio_item youtube portfolio-lightbox-group"  title = "<?php echo $row->name;?>">
                                                        <img alt="<?php echo $row->name; ?>" id="wd-cl-img<?php echo $key; ?>"  src="http://img.youtube.com/vi/<?php echo $videourl[0]; ?>/mqdefault.jpg"  />
                                                        <div class="play-icon <?php echo $videourl[1];?>-icon"></div>
                                                   </a>
                                            <?php 
                                            break;
                                            case 'vimeo':
                                                $videourl=$this->get_video_id_from_url_portfolio($imgurl[0]);
                                                $hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/".$videourl[0].".php"));
                                                $imgsrc=$hash[0]['thumbnail_large'];?>
                                                <a class="huge_it_portfolio_item vimeo portfolio-lightbox-group" href="http://player.vimeo.com/video/<?php echo $videourl[0]; ?>" title="<?php echo $row->name; ?>">
                                                    <img src="<?php echo $imgsrc; ?>" alt="<?php echo $row->name; ?>" /><div class="play-icon vimeo-icon"></div>
                                                </a>
                                            <?php
                                            break;
                                            } 
                                        } else { ?>
                                         <img alt="<?php echo $row->name; ?>" id="wd-cl-img<?php echo $key; ?>" src="images/noimage.jpg" />
                                        <?php } ?>	
                    </div>
                    <?php if($row->name!=""){?>
                        <div class="title-block_<?php echo $portfolioID; ?>">
                            <?php
                            if($link != ""){?>
                                <a href="<?php echo $link; ?>" <?php if ($row->link_target=="on"){echo 'target="_blank"';}?>><?php echo $row->name; ?></a>

                           <?php }
                           else{?>
                               <a href="#" ><?php echo $row->name; ?></a>
                          <?php }
                            ?>

                        </div>
                    <?php } ?>
            </div>	
    <?php  }?>

    <div style="clear:both;"></div>
        </div>

    <?php
    $a=$disp_type;
    if($a=='load_more'){
        $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
        $actual_link = $protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."";
        $pattern="/\?p=/";
        $pattern2="/&page-img[0-9]+=[0-9]+/";
        $pattern3="/\?page-img[0-9]+=[0-9]+/";
        if(preg_match($pattern, $actual_link)){
            if(preg_match($pattern2, $actual_link)){
                $actual_link=preg_replace($pattern2, '', $actual_link);
                header("Location:".$actual_link."");
                exit;
            }
        }elseif(preg_match($pattern3, $actual_link)){
            $actual_link=preg_replace($pattern3, '', $actual_link);
            header("Location:".$actual_link."");
            exit;
        }
        ?>
        <?php $paramssld['ht_view6_loading_type'] = $paramssld['slider_navigation_type']; ?>
        <div class="load_more4">
            <div class="load_more_button4"><?=$paramssld['ht_view6_loadmore_text']; ?></div>
            <div class="loading4"><img src="<?php echo JUri::root()."media/com_portfoliogallery/images/loading.white.gif";?>"></div>
            <input type="hidden" class="load-more-elements-count" value="<?php echo $count_page; ?>"/>


            <script>
                jQuery(document).ready(function(){
                    if(jQuery("#huge_it_portfolio_content_<?php echo $portfolioID; ?> .load_more_button4").length){
                        jQuery("#huge_it_portfolio_content_<?php echo $portfolioID; ?> .load_more_button4").on("click tap",function(){
                            if(parseInt(jQuery("#huge_it_portfolio_content_<?php echo $portfolioID; ?> .pagenum:last").val())<parseInt(jQuery("#huge_it_portfolio_container_<?php echo $portfolioID; ?> #total").val())) {
                                var pagenum = parseInt(jQuery("#huge_it_portfolio_content_<?php echo $portfolioID; ?> .pagenum:last").val()) + 1;
                                var perpage =<?=$count_page; ?>;
                                var galleryid="<?=$image->portfolio_id; ?>";
                                getresult(pagenum,perpage,galleryid);
                            }else{

                                jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?> .load_more_button4').hide();
                            }
                            return false;
                        });
                    }
                    if(parseInt(jQuery("#huge_it_portfolio_content_<?php echo $portfolioID; ?> .pagenum:last").val())==parseInt(jQuery("#huge_it_portfolio_container_<?php echo $portfolioID; ?> #total").val())) {
                        jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?> .load_more_button4').hide();
                    }
                    function getresult(pagenum,perpage,galleryid){
                        var data = {
                            action:'my_action',
                            post:"huge_it_portfolio_gallery_ajax6",
                            task:'load_portfolio_videos_lightbox',
                            page:pagenum,
                            perpage:perpage,
                            galleryid:galleryid,
                            level:<?php echo $level?>,
                        }
                        jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?> .loading4').show();

                        jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?> .load_more_button4').hide();

                        jQuery.post("<?php echo $path_menu?>components/com_portfoliogallery/ajax_url.php",data,function(response){
                            if(response.success){
                                var $objnewitems= jQuery(response.success);
                                jQuery('#huge_it_portfolio_container_<?php echo $portfolioID; ?>').append( $objnewitems ).hugeitmicro('reloadItems' ).hugeitmicro({ sortBy: 'original-order' }).hugeitmicro( 'reLayout' );
                                jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?> img').on('load',function(){

                                    //############# End

                                    jQuery(".group1").colorbox({rel:'group1'});
                                    jQuery(".youtube").colorbox({iframe:true, innerWidth:640, innerHeight:390});
                                    jQuery(".vimeo").colorbox({iframe:true, innerWidth:640, innerHeight:390});
                                    jQuery(".iframe").colorbox({iframe:true, width:"80%", height:"80%"});
                                    jQuery(".inline").colorbox({inline:true, width:"50%"});
                                    jQuery(".callbacks").colorbox({
                                        onOpen:function(){ alert('onOpen: colorbox is about to open'); },
                                        onLoad:function(){ alert('onLoad: colorbox has started to load the targeted content'); },
                                        onComplete:function(){ alert('onComplete: colorbox has displayed the loaded content'); },
                                        onCleanup:function(){ alert('onCleanup: colorbox has begun the close process'); },
                                        onClosed:function(){ alert('onClosed: colorbox has completely closed'); }
                                    });

                                    jQuery('.non-retina').colorbox({rel:'group5', transition:'none'})
                                    jQuery('.retina').colorbox({rel:'group5', transition:'none', retinaImage:true, retinaUrl:true});
                                    var defaultBlockWidth=<?php echo $paramssld['ht_view6_width']; ?>+20+<?php echo $paramssld['ht_view6_width']*2; ?>;
                                    jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?> .load_more_button4').show();
                                    jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?> .loading4').hide();
                                    if(parseInt(jQuery("#huge_it_portfolio_content_<?php echo $portfolioID; ?> .pagenum:last").val())==parseInt(jQuery("#huge_it_portfolio_container_<?php echo $portfolioID; ?> #total").val())) {
                                        jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?> .load_more_button4').hide();
                                    }

                                });

                            }else{
                                alert("no");
                            }
                        },"json");
                    }

                });
            </script>
        </div>
        <?php
    }elseif($a=='pagination'){
        ?>
        <div class="paginate4">
            <?php
            $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
            $actual_link = $protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."";
            $checkREQ='';
            $pattern="/\?p=/";
            $pattern2="/&page-img[0-9]+=[0-9]+/";
            if(preg_match($pattern, $actual_link)){

                if(preg_match($pattern2, $actual_link)){
                    $actual_link=preg_replace($pattern2, '', $actual_link);
                }

                $checkREQ=$actual_link.'&page-img'.$portfolioID.$pID;

            }else{
                $checkREQ='?page-img'.$portfolioID.$pID;

            }
            $pervpage='';
            if ($page != 1) $pervpage = '<a href= '.$checkREQ.'=1><i class="hugeiticons hugeiticons-fast-backward" ></i></a>  
                             <a href= '.$checkREQ.'='. ($page - 1) .'><i class="hugeiticons hugeiticons-chevron-left"></i></a> ';
            // Проверяем нужны ли стрелки вперед
            $nextpage='';
            if ($page != $total) $nextpage = '<a href= '.$checkREQ.'='. ($page + 1) .'><i class="hugeiticons hugeiticons-chevron-right"></i></a>  
                                  <a href= '.$checkREQ.'=' .$total. '><i class="hugeiticons hugeiticons-fast-forward" ></i></a>';
            echo $pervpage.$page.'/'.$total.$nextpage;

            ?>
        </div>
        <?php
    }
    ?>
</section>

<script>
jQuery(function(){
var defaultBlockWidth=<?php echo $paramssld['ht_view6_width']; ?>;
    var $container = jQuery('#huge_it_portfolio_container_<?php echo $portfolioID; ?>');
      $container.find('.element_<?php echo $portfolioID; ?>').each(function(){
        var $this = jQuery(this),
            number = parseInt( $this.find('.number').text(), 10 );
        if ( number % 7 % 2 === 1 ) {
          $this.addClass('width2');
        }
        if ( number % 3 === 0 ) {
          $this.addClass('height2');
        }
      });
    
    $container.hugeitmicro({
      itemSelector : '.element_<?php echo $portfolioID; ?>',
      masonry : {
        columnWidth : <?php echo $paramssld['ht_view6_width']; ?>+20+<?php echo $paramssld['ht_view6_border_width']*2; ?>
      },
      masonryHorizontal : {
        rowHeight: 300+20
      },
      cellsByRow : {
        columnWidth : 300+20,
        rowHeight : 240
      },
      cellsByColumn : {
        columnWidth : 300+20,
        rowHeight : 240
      },
      getSortData : {
        symbol : function( $elem ) {
          return $elem.attr('data-symbol');
        },
        category : function( $elem ) {
          return $elem.attr('data-category');
        },
        number : function( $elem ) {
          return parseInt( $elem.find('.number').text(), 10 );
        },
        weight : function( $elem ) {
          return parseFloat( $elem.find('.weight').text().replace( /[\(\)]/g, '') );
        },
        id : function ( $elem ) {
          return $elem.find('.id').text();
        }
      }
    });
      var $optionSets = jQuery('#huge_it_portfolio_options_<?php echo $portfolioID; ?> .option-set'),
          $optionLinks = $optionSets.find('a');

      $optionLinks.click(function(){
        var $this = jQuery(this);

        if ( $this.hasClass('selected') ) {
          return false;
        }
        var $optionSet = $this.parents('.option-set');
        $optionSet.find('.selected').removeClass('selected');
        $this.addClass('selected');
  

        var options = {},
            key = $optionSet.attr('data-option-key'),
            value = $this.attr('data-option-value');

        value = value === 'false' ? false : value;
        options[ key ] = value;
        if ( key === 'layoutMode' && typeof changeLayoutMode === 'function' ) {

          changeLayoutMode( $this, options )
        } else {

          $container.hugeitmicro( options );
        }
        
        return false;
      });
      var isHorizontal = false;
      function changeLayoutMode( $link, options ) {
        var wasHorizontal = isHorizontal;
        isHorizontal = $link.hasClass('horizontal');

        if ( wasHorizontal !== isHorizontal ) {

          var style = isHorizontal ? 
            { height: '75%', width: $container.width() } : 
            { width: 'auto' };

          $container.filter(':animated').stop();

          $container.addClass('no-transition').css( style );
          setTimeout(function(){
            $container.removeClass('no-transition').hugeitmicro( options );
          }, 100 )
        } else {
          $container.hugeitmicro( options );
        }
      }
      $container.delegate('.default-block_<?php echo $portfolioID; ?>', 'click', function(){
          var strheight=0;
          jQuery(this).parents('.element_<?php echo $portfolioID; ?>').find('.wd-portfolio-panel_<?php echo $portfolioID; ?> > div').each(function(){
                strheight+=jQuery(this).outerHeight()+10;
          })
          strheight+=<?php echo (isset($paramssld['ht_view6_block_height'])?$paramssld['ht_view6_block_height']:0)+45; ?>;
	  			if(jQuery(this).parents('.element_<?php echo $portfolioID; ?>').hasClass("large")){
			jQuery(this).parents('.element_<?php echo $portfolioID; ?>').animate({
				height: "<?php echo (isset($paramssld['ht_view6_block_height'])?$paramssld['ht_view6_block_height']:0)+45; ?>px"
			}, 300, function() {
				jQuery(this).removeClass('large');
				$container.hugeitmicro('reLayout');
			});
			
			jQuery(this).parents('.element_<?php echo $portfolioID; ?>').removeClass("active");
			return false;
		}
		
	
		jQuery(this).parents('.element_<?php echo $portfolioID; ?>').css({height:strheight});
		jQuery(this).parents('.element_<?php echo $portfolioID; ?>').addClass('large');

		$container.hugeitmicro('reLayout');
		jQuery(this).parents('.element_<?php echo $portfolioID; ?>').css({height:"<?php echo (isset($paramssld['ht_view6_block_height'])?$paramssld['ht_view6_block_height']:0)+45; ?>px"});
		 jQuery(this).parents('.element_<?php echo $portfolioID; ?>').animate({
			height:strheight+"px",
		  }, 300,function(){	$container.hugeitmicro('reLayout');});
	});

    var $sortBy =  jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?> #sort-by');
    jQuery('#huge_it_portfolio_content_<?php echo $portfolioID; ?> #shuffle a').click(function(){
      $container.hugeitmicro('shuffle');
      $sortBy.find('.selected').removeClass('selected');
      $sortBy.find('[data-option-value="random"]').addClass('selected');
      return false;
    });
    
    ////filteringgggggg

        // bind filter on select change
        jQuery(document).ready(function(){
            jQuery('#huge_it_portfolio_filters_<?php echo $portfolioID; ?> ul li').click(function() {
              var filterValue = jQuery(this).attr('rel');
              filterValue = filterValue;
              $container.hugeitmicro({ filter: filterValue });
            });
            <?php if(($paramssld["ht_view6_sorting_float"] == "left" || $paramssld["ht_view6_sorting_float"] == "right") && $paramssld["ht_view6_filtering_float"] == "none")
                  { ?>
                        var topmargin = jQuery("#huge_it_portfolio_filters_<?php echo $portfolioID; ?> ul").height();
                        jQuery("#huge_it_portfolio_options_<?php echo $portfolioID; ?>").css({'margin-top':parseInt(topmargin) + 5});
            <?php }
            else  {
                    if(($paramssld["ht_view6_filtering_float"] == "left" || $paramssld["ht_view6_filtering_float"] == "right") && $paramssld["ht_view6_sorting_float"] == "none")
                      { ?>
                         var topmargin = jQuery("#huge_it_portfolio_options_<?php echo $portfolioID; ?>").height();
                         jQuery("#huge_it_portfolio_filters_<?php echo $portfolioID; ?>").css({'margin-top':'5px'});
                <?php }
                  } ?>
        });
        
        jQuery(window).load(function(){

            $container.hugeitmicro({ filter: '*' });
        });

  });
</script>

<?php 
}
    return $render_html1 = ob_get_clean();
    }

}

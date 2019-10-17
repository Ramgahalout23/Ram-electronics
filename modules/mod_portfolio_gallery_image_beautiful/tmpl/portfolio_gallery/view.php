<?php
/**
* @title		portfolio gallery image beautiful
* @website		http://www.joomhome.com
* @copyright	Copyright (C) 2015 joomhome.com. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*/

    // no direct access
    defined('_JEXEC') or die;
?>
<link rel="stylesheet" type="text/css" href="<?php echo $mosConfig_live_site; ?>/modules/mod_portfolio_gallery_image_beautiful/tmpl/portfolio_gallery/css/style.css" />
<style>
#portfolio-gallery-img-beautiful{
	width:<?php echo $width_module;?>;
	margin:0 auto;
}
</style>

<div id="portfolio-gallery-img-beautiful"></div>

<?php
if ($enable_jQuery == 1) {?>
	<script type="text/javascript" src="<?php echo $mosConfig_live_site; ?>/modules/mod_portfolio_gallery_image_beautiful/tmpl/portfolio_gallery/js/jquery.min.js"></script>
<?php }?>
<script type="text/javascript">
var call_content_string,call_height_box;
call_content_string = <?php echo $content_string;?>;
call_height_box = <?php echo $height_box;?>;
</script>
<script type="text/javascript" src="<?php echo $mosConfig_live_site; ?>/modules/mod_portfolio_gallery_image_beautiful/tmpl/portfolio_gallery/js/modernizr.custom.js"></script>
<script type="text/javascript" src="<?php echo $mosConfig_live_site; ?>/modules/mod_portfolio_gallery_image_beautiful/tmpl/portfolio_gallery/js/classie.js"></script>
<script type="text/javascript" src="<?php echo $mosConfig_live_site; ?>/modules/mod_portfolio_gallery_image_beautiful/tmpl/portfolio_gallery/js/portfolio.js"></script>

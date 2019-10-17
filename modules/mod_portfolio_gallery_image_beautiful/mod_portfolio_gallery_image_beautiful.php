<?php
/**
* @title		portfolio gallery image beautiful
* @website		http://www.joomhome.com
* @copyright	Copyright (C) 2015 joomhome.com. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*/
// no direct access
defined('_JEXEC') or die('Restricted access');

$mosConfig_absolute_path = JPATH_SITE;
$mosConfig_live_site = JURI :: base();
if(substr($mosConfig_live_site, -1)=="/") { $mosConfig_live_site = substr($mosConfig_live_site, 0, -1); }

$module_name             = basename(dirname(__FILE__));
$module_dir              = dirname(__FILE__);
$module_id               = $module->id;
$document                = JFactory::getDocument();
$css_path                = JPATH_THEMES. '/'.$document->template.'/css/'.$module_name;
$style                   = $params->get('jhm_style');

if( empty($style) )
{
    JFactory::getApplication()->enqueueMessage( 'Slider style no declared. Check portfolio gallery image beautiful configuration and save again from admin panel' , 'error');
    return;
}

$layoutoverwritepath     = JURI::base(true) . '/templates/'.$document->template.'/html/'. $module_name. '/tmpl/'.$style;
$document                = JFactory::getDocument();
require_once $module_dir.'/helper.php';
$helper = new mod_Portfoliogalleryimgbeautifuljhm($params, $module_id);
$data = (array) $helper->display();
//$option = (array) $params->get('animation')->$style;

$enable_jQuery			= $params->get('enable_jQuery', 1);
$width_module			= $params->get('width_module', "100%");
$height_box				= $params->get('height_box', "500");

$countitem = 0;
foreach($data as $index=>$value)
{
	$countitem++ ; 
}

$content_string="";
$counttmp = 1;
foreach($data as $index=>$value)
{
	if( isset($value['textlimit']) and $value['textlimit']!='no' )
	{
		if( isset($value['introtext']) ) $real_introtext = $helper->textLimit($value['introtext'], $value['limitcount'], $value['textlimit']);
	} else {
		if( isset($value['introtext']) ) $real_introtext = $value['introtext'];
	}
	$img_string="[";
	if (isset($value['image1']) && ($value['image1']) != ""){
		$img1_str = JURI::root().$value['image1']; 
		$img_string .="'".$img1_str."'";
	}
	if (isset($value['image2']) && ($value['image2']) != ""){
		$img2_str = JURI::root().$value['image2']; 
		$img_string .=",'".$img2_str."'";
	}
	if (isset($value['image3']) && ($value['image3']) != ""){
		$img3_str = JURI::root().$value['image3']; 
		$img_string .=",'".$img3_str."'";
	}
	if (isset($value['image4']) && ($value['image4']) != ""){
		$img4_str = JURI::root().$value['image4']; 
		$img_string .=",'".$img4_str."'";
	}
	if (isset($value['image5']) && ($value['image5']) != ""){
		$img5_str = JURI::root().$value['image5']; 
		$img_string .=",'".$img5_str."'";
	}
	$img_string .="]";
	if($counttmp == 1){
		$content_string .= "[{'title':'".$value['title']."','description':'".$real_introtext."','thumbnail':".$img_string.",'large':".$img_string.",'button_list':[{'title':'".$value['btn1txt']."','url':'".$value['link1']."','new_window':true},{'title':'".$value['btn2txt']."','url':'".$value['link2']."','new_window':true}],'tags':['".$value['category']."']},";
	} else {
		if($counttmp == $countitem){
			$content_string .= "{'title':'".$value['title']."','description':'".$real_introtext."','thumbnail':".$img_string.",'large':".$img_string.",'button_list':[{'title':'".$value['btn1txt']."','url':'".$value['link1']."','new_window':true},{'title':'".$value['btn2txt']."','url':'".$value['link2']."','new_window':true}],'tags':['".$value['category']."']}]";
		} else {
			$content_string .= "{'title':'".$value['title']."','description':'".$real_introtext."','thumbnail':".$img_string.",'large':".$img_string.",'button_list':[{'title':'".$value['btn1txt']."','url':'".$value['link1']."','new_window':true},{'title':'".$value['btn2txt']."','url':'".$value['link2']."','new_window':true}],'tags':['".$value['category']."']},";
		}
	}
	$counttmp++ ; 
}


if(  is_array( $helper->error() )  )
{
    JFactory::getApplication()->enqueueMessage( implode('<br /><br />', $helper->error()) , 'error');
} 
else
{
    if( file_exists($layoutoverwritepath.'/view.php') )
    {
        require(JModuleHelper::getLayoutPath($module_name, $layoutoverwritepath.'/view.php') );   
    } else {
        require(JModuleHelper::getLayoutPath($module_name, $style.'/view') );   
    }

    $helper->setAssets($document, $style);

    if(file_exists($css_path.'/tmpl/'.$style.'.css'))
    {
        $document->addStylesheet(JURI::base(true) . '/templates/'.$document->template.'/css/'. $module_name.'/tmpl/'.$style.'.css');
    }
}
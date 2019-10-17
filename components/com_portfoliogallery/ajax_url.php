<?php
/**
 * @package Huge IT Video Gallery
 * @author Huge-IT
 * @copyright (C) 2014 Huge IT. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @website		http://www.huge-it.com/
 **/


define('_JEXEC',1);
defined('_JEXEC') or die('Restircted access');

if (stristr( $_SERVER['SERVER_SOFTWARE'], 'win32' )) {
    define( 'JPATH_BASE', realpath(dirname(__FILE__).'\..\..' ));
	} else define( 'JPATH_BASE', realpath(dirname(__FILE__).'/../..' ));
	define( 'DS', DIRECTORY_SEPARATOR );
	require_once ( JPATH_BASE.DS.'includes'.DS.'defines.php' );
	require_once ( JPATH_BASE.DS.'includes'.DS.'framework.php' );
	$app =JFactory::getApplication('site');
	
	$app->initialise();
	jimport( 'joomla.user.user' );
	jimport( 'joomla.user.helper' );
        
        $db =JFactory::getDBO();
        // View 0 //
        if($_POST['post']=="huge_it_portfolio_gallery_ajax"){
            
            $page = 1;
            
        
           if(!empty($_POST["page"]) && is_numeric($_POST['page']) && $_POST['page']>0){
            $paramssld='';
                $db = JFactory::getDBO();
                $query = $db->getQuery(true);
                $query->select('*');
                $query->from('#__huge_itportfolio_params');
                $db->setQuery($query);
                $options_params = $db->loadObjectList();
                 foreach ($options_params as $rowpar) {
                    $key = $rowpar->name;
                    $value = $rowpar->value;
                    $paramssld[$key] = $value;
                }
               
               
                
            $page = intval($_POST["page"]);
            $num = intval($_POST['perpage']);
            $start = $page * $num - $num;
            $start = intval($start);
            $idofgallery = intval($_POST['galleryid']);
            $level = intval($_POST['level']);
            $query = $db->getQuery(true);
            $query->select('*');
            $query->from('#__huge_itportfolio_images');
            $query->where('portfolio_id ='.$idofgallery);
            $query ->order('#__huge_itportfolio_images.ordering asc');
            $db->setQuery($query,$start,$num);
            $page_images = $db->loadObjectList();
            $output = '';
                if($level == '1'){
                    $path = '';
                }
               else{
                   $path = '../';
               }
                
                  foreach($page_images as $key=>$row){
                    $link = str_replace('__5_5_5__','%',$row->sl_url);
                    $video_name=str_replace('__5_5_5__','%',$row->name);
                    $id=$row->id;
                    $descnohtml=strip_tags(str_replace('__5_5_5__','%',$row->description)); 
                    $result = substr($descnohtml, 0, 50);
                    $getPortfolio = portHtml($row,$key,$paramssld);
                    $imagerowstype=$row->sl_type;
                    if($row->sl_type == ''){$imagerowstype='image';}
                    switch($imagerowstype){
                        case 'image':
                            $imgurl=explode(";",$row->image_url);
                            $imgurl_exp = explode('/',$imgurl[0]);
                            if($imgurl_exp[0] == 'http:' || $imgurl_exp[0] == 'https:' || $level == '1' ){
                                $path = '';
                            }
                            else{
                                $path = '../';
                            }
                            if($row->image_url != ';'){ 
                                $video='<img id="wd-cl-img'.$key.'" src="'.$path.$imgurl[0].'" alt="" />';
                             } else {
                            $video='<img id="wd-cl-img'.$key.'" src="images/noimage.jpg" alt="" />';
                            }
                            break;
                                case 'video':
                                    $videourl=get_video_gallery_id_from_url($row->image_url);
                                    print_r($row);
                                    
                                       if($videourl[1]=='youtube'){
                                    if(empty($row->thumb_url)){
                                            $thumb_pic='http://img.youtube.com/vi/'.$videourl[0].'/mqdefault.jpg';
                                        }else{
                                            $thumb_pic=$row->thumb_url;
                                        }
                                
                                $video='<img src="'.$thumb_pic.'" alt="" />';                             
                            
                                }else {
                                    $vimeo =$row->image_url;            
                                    $vimeo_explode = explode( "/", $vimeo );
                                    $imgid =  end($vimeo_explode);            
                                    $hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/".$imgid.".php"));
                                    $imgsrc=$hash[0]['thumbnail_large'];
                                    
                                    
                                    
                                $hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/".$videourl[0].".php"));
                               if(empty($row->thumb_url)){
                                       $imgsrc=$hash[0]['thumbnail_large'];
                                   }else{
                                       $imgsrc=$row->thumb_url;
                                   }
                            
                               $video='<img src="'.$imgsrc.'" alt="" />';
                            
                            }
                                break;
                    }
                    
            if(str_replace('__5_5_5__','%',$row->sl_url)=='' ){
                $button='';
            }else{
                $target='target="_blank"';
            }
            $output.='<div class="element_'.$idofgallery.' colorbox_grouping My_First_Category My_Third_Category   hugeitmicro-item "  data-symbol="'.$video_name.'"  data-category="alkaline-earth" >';
            $output.='<div class="default-block_'.$idofgallery.' dropdownable">';
            $output.='<input type="hidden" class="pagenum" value="'.$page.'" />';
            $output.='<div class="image-block_'.$idofgallery.'">';
            $output.='<a href=   >' ;
            $output.=$video;
            $output.='</a>';
            $output.='<div class="videogallery-image-overlay"><a href="#'.$id.'"></a></div>';
            $output.='</div>';
            $output.='<div class="title-block_'.$idofgallery.'">';
            $output.='<h3>'.$video_name.'</h3>';
            $output.='<div class="open-close-button"><</div>';
            $output.='</div>';
            $output.='</div>';
            $output.=$getPortfolio;  
            $output.='</div>';
        
     }
     
      $response = array("success"=>$output,"params"=>$options_params);
            echo json_encode($response);
            die();
                  }
            }
        
        // View 1 ///
        
        else if($_POST['post']=="huge_it_portfolio_gallery_ajax1"){
            $page = 1;


            if(!empty($_POST["page"]) && is_numeric($_POST['page']) && $_POST['page']>0){
                $paramssld='';
                $db = JFactory::getDBO();
                $query = $db->getQuery(true);
                $query->select('*');
                $query->from('#__huge_itportfolio_params');
                $db->setQuery($query);
                $options_params = $db->loadObjectList();
                foreach ($options_params as $rowpar) {
                    $key = $rowpar->name;
                    $value = $rowpar->value;
                    $paramssld[$key] = $value;
                }
                $page = intval($_POST["page"]);
                $num = intval($_POST['perpage']);
                $start = $page * $num - $num;
                $start = intval($start);
                $idofgallery = intval($_POST['galleryid']);
                $level = intval($_POST['level']);
                if($level == '1'){
                    $path = '';
                }
                else{
                    $path = '../';
                }
                $query = $db->getQuery(true);
                $query->select('*');
                $query->from('#__huge_itportfolio_images');
                $query->where('portfolio_id ='.$idofgallery);
                $query ->order('#__huge_itportfolio_images.ordering asc');
                $db->setQuery($query,$start,$num);
                $page_images = $db->loadObjectList();
                $output = '';
                foreach($page_images as $key=>$row){
                    $link = str_replace('__5_5_5__','%',$row->sl_url);
                    $video_name=str_replace('__5_5_5__','%',$row->name);
                    $id = intval($row->id);
                    $descnohtml=strip_tags(str_replace('__5_5_5__','%',$row->description));
                    $result = substr($descnohtml, 0, 50);
                    $getPortfolio = portHtmlView1($row,$key,$paramssld);
                    $img = $row->image_url;

                    $imagerowstype=$row->sl_type;
                    if($row->sl_type == ''){$imagerowstype='image';}
                    switch($imagerowstype){
                        case 'image':
                            $imgurl=explode(";",$row->image_url);
                            if($row->image_url != ';'){
                                $video='<img id="wd-cl-img'.$key.'" src="'.$imgurl[0].'" alt="" />';
                            } else {
                                $video='<img id="wd-cl-img'.$key.'" src="images/noimage.jpg" alt="" />';
                            }
                            break;
                        case 'video':
                            $videourl=get_video_gallery_id_from_url($row->image_url);
                            print_r($row);

                            if($videourl[1]=='youtube'){
                                if(empty($row->thumb_url)){
                                    $thumb_pic='http://img.youtube.com/vi/'.$videourl[0].'/mqdefault.jpg';
                                }else{
                                    $thumb_pic=$row->thumb_url;
                                }

                                $video='<img src="../'.$thumb_pic.'" alt="" />';

                            }else {
                                $vimeo =$row->image_url;
                                $vimeo_explode = explode( "/", $vimeo );
                                $imgid =  end($vimeo_explode);
                                $hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/".$imgid.".php"));
                                $imgsrc=$hash[0]['thumbnail_large'];



                                $hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/".$videourl[0].".php"));
                                if(empty($row->thumb_url)){
                                    $imgsrc=$hash[0]['thumbnail_large'];
                                }else{
                                    $imgsrc=$row->thumb_url;
                                }
                                $video='<img src="../'.$imgsrc.'" alt="" />';
                            }
                            break;
                    }

                    if(str_replace('__5_5_5__','%',$row->sl_url)=='' ){
                        $button='';
                    }else{
                        $target='target="_blank"';
                    }
                    $output.='<div class="element_'.$idofgallery.' colorbox_grouping My_First_Category My_Third_Category   hugeitmicro-item "  data-symbol="'.$video_name.'"  data-category="alkaline-earth" >';
                    $output.='<input type="hidden" class="pagenum" value="'.$page.'" />';
                    $output.=$getPortfolio;
                    $output.='</div>';

                }

                $response = array("success"=>$output,"params"=>$options_params);
                echo json_encode($response);
                die();
            }

            
            
            
            
        }
    //View 2 //

        else if($_POST['post']=="huge_it_portfolio_gallery_ajax2"){
            $page = 1;


            if(!empty($_POST["page"]) && is_numeric($_POST['page']) && $_POST['page']>0){
                $paramssld='';
                $db = JFactory::getDBO();
                $query = $db->getQuery(true);
                $query->select('*');
                $query->from('#__huge_itportfolio_params');
                $db->setQuery($query);
                $options_params = $db->loadObjectList();
                foreach ($options_params as $rowpar) {
                    $key = $rowpar->name;
                    $value = $rowpar->value;
                    $paramssld[$key] = $value;
                }
                $page = intval($_POST["page"]);
                $num = intval($_POST['perpage']);
                $start = $page * $num - $num;
                $start = intval($start);
                $idofgallery = intval($_POST['galleryid']);
                $query = $db->getQuery(true);
                $query->select('*');
                $query->from('#__huge_itportfolio_images');
                $query->where('portfolio_id ='.$idofgallery);
                $query ->order('#__huge_itportfolio_images.ordering asc');
                $db->setQuery($query,$start,$num);
                $page_images = $db->loadObjectList();
                $output = '';
                foreach($page_images as $key=>$row){
                    $link = str_replace('__5_5_5__','%',$row->sl_url);
                    $video_name=str_replace('__5_5_5__','%',$row->name);
                    $id=$row->id;
                    $descnohtml=strip_tags(str_replace('__5_5_5__','%',$row->description));
                    $result = substr($descnohtml, 0, 50);
                    $getPortfolioView2 = portHtmlView2($row,$key,$paramssld);
                    $imagerowstype=$row->sl_type;
                    if($row->sl_type == ''){$imagerowstype='image';}
                    switch($imagerowstype){
                        case 'image':
                            $imgurl=explode(";",$row->image_url);
							$imgurl_exp = explode('/',$imgurl[0]);
                            if($imgurl_exp[0] == 'http:' || $imgurl_exp[0] == 'https:' ){
                                $path = '';
                            }
                            else{
                                $path = '../';
                            }
                            if($row->image_url != ';'){
                                $video='<img id="wd-cl-img'.$key.'" src="'.$imgurl[0].'" alt="" />';
                            } else {
                                $video='<img id="wd-cl-img'.$key.'" src="images/noimage.jpg" alt="" />';
                            }
                            break;
                        case 'video':
                            $videourl=get_video_gallery_id_from_url($row->image_url);
                            print_r($row);

                            if($videourl[1]=='youtube'){
                                if(empty($row->thumb_url)){
                                    $thumb_pic='http://img.youtube.com/vi/'.$videourl[0].'/mqdefault.jpg';
                                }else{
                                    $thumb_pic=$row->thumb_url;
                                }

                                $video='<img src="'.$thumb_pic.'" alt="" />';

                            }else {
                                $vimeo =$row->image_url;
                                $vimeo_explode = explode( "/", $vimeo );
                                $imgid =  end($vimeo_explode);
                                $hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/".$imgid.".php"));
                                $imgsrc=$hash[0]['thumbnail_large'];



                                $hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/".$videourl[0].".php"));
                                if(empty($row->thumb_url)){
                                    $imgsrc=$hash[0]['thumbnail_large'];
                                }else{
                                    $imgsrc=$row->thumb_url;
                                }

                                $video='<img src="../'.$imgsrc.'" alt="" />';


                            }
                            break;
                    }

                    if(str_replace('__5_5_5__','%',$row->sl_url)=='' ){
                        $button='';
                    }else{
                        if ($row->link_target=="on"){
                            $target='target="_blank"';
                        }else{
                            $target='';
                        }

                        $button='<div class="button-block"><a href="'.str_replace('__5_5_5__','%',$row->sl_url).'" '.$target.' >View More</a></div>';
                    }
                    $output.='<div class="element_'.$idofgallery.'  My_First_Category My_Third_Category   hugeitmicro-item "  data-symbol="'.$video_name.'"  data-category="alkaline-earth" >';
                    $output.='<input type="hidden" class="pagenum" value="'.$page.'" />';
                    $output.=$getPortfolioView2;
                    $output.='</div>';
                }

                $response = array("success"=>$output,"params"=>$options_params);
                echo json_encode($response);
                die();
            }





        }
    // View 3 //

        else if($_POST['post']=="huge_it_portfolio_gallery_ajax3"){
            $page = 1;


            if(!empty($_POST["page"]) && is_numeric($_POST['page']) && $_POST['page']>0){
                $paramssld='';
                $db = JFactory::getDBO();
                $query = $db->getQuery(true);
                $query->select('*');
                $query->from('#__huge_itportfolio_params');
                $db->setQuery($query);
                $options_params = $db->loadObjectList();
                foreach ($options_params as $rowpar) {
                    $key = $rowpar->name;
                    $value = $rowpar->value;
                    $paramssld[$key] = $value;
                }



                $page = intval($_POST["page"]);
                $num = intval($_POST['perpage']);
                $start = $page * $num - $num;
                $start = intval($start);
                $idofgallery = intval($_POST['galleryid']);

                $query = $db->getQuery(true);
                $query->select('*');
                $query->from('#__huge_itportfolio_images');
                $query->where('portfolio_id ='.$idofgallery);
                $query ->order('#__huge_itportfolio_images.ordering asc');
                $db->setQuery($query,$start,$num);
                $page_images = $db->loadObjectList();
                $output = '';


                foreach($page_images as $key=>$row){
                    $link = str_replace('__5_5_5__','%',$row->sl_url);
                    $video_name=str_replace('__5_5_5__','%',$row->name);
                    $id = intval($row->id);
                    $descnohtml=strip_tags(str_replace('__5_5_5__','%',$row->description));
                    $result = substr($descnohtml, 0, 50);
                    $getPortfolio = portHtml($row,$key,$paramssld);
                    $getPortfolioView3 = portHtmlView3($row,$key,$paramssld);


                    $imagerowstype=$row->sl_type;
                    if($row->sl_type == ''){$imagerowstype='image';}
                    switch($imagerowstype){
                        case 'image':
                            $imgurl=explode(";",$row->image_url);
                            if($row->image_url != ';'){
                                $video='<img id="wd-cl-img'.$key.'" src="'.$imgurl[0].'" alt="" />';
                            } else {
                                $video='<img id="wd-cl-img'.$key.'" src="images/noimage.jpg" alt="" />';
                            }
                            break;
                        case 'video':
                            $videourl=get_video_gallery_id_from_url($row->image_url);
                            print_r($row);

                            if($videourl[1]=='youtube'){
                                if(empty($row->thumb_url)){
                                    $thumb_pic='http://img.youtube.com/vi/'.$videourl[0].'/mqdefault.jpg';
                                }else{
                                    $thumb_pic=$row->thumb_url;
                                }

                                $video='<img src="'.$thumb_pic.'" alt="" />';

                            }else {
                                $vimeo =$row->image_url;
                                $vimeo_explode = explode( "/", $vimeo );
                                $imgid =  end($vimeo_explode);
                                $hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/".$imgid.".php"));
                                $imgsrc=$hash[0]['thumbnail_large'];



                                $hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/".$videourl[0].".php"));
                                if(empty($row->thumb_url)){
                                    $imgsrc=$hash[0]['thumbnail_large'];
                                }else{
                                    $imgsrc=$row->thumb_url;
                                }

                                $video='<img src="../'.$imgsrc.'" alt="" />';


                            }
                            break;
                    }

                    if(str_replace('__5_5_5__','%',$row->sl_url)=='' ){
                        $button='';
                    }else{
                        if ($row->link_target=="on"){
                            $target='target="_blank"';
                        }else{
                            $target='';
                        }

                        $button='<div class="button-block"><a href="'.str_replace('__5_5_5__','%',$row->sl_url).'" '.$target.' >View More</a></div>';
                    }
                    $output.='<div class="element_'.$idofgallery.' colorbox_grouping My_First_Category My_Third_Category   hugeitmicro-item "  data-symbol="'.$video_name.'"  data-category="alkaline-earth" >';
                    $output.='<input type="hidden" class="pagenum" value="'.$page.'" />';
                    $output.=$getPortfolioView3;
                    $output.='</div>';
                }

                $response = array("success"=>$output,"params"=>$options_params);
                echo json_encode($response);
                die();
            }





        }

        //View 4 //

if($_POST['post']=="huge_it_portfolio_gallery_ajax4"){

    $page = 1;


    if(!empty($_POST["page"]) && is_numeric($_POST['page']) && $_POST['page']>0){
        $paramssld=array();
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__huge_itportfolio_params');
        $db->setQuery($query);
        $options_params = $db->loadObjectList();
        foreach ($options_params as $rowpar) {
            $key = $rowpar->name;
            $value = $rowpar->value;
            $paramssld[$key] = $value;
        }


        $page = intval($_POST["page"]);
        $num = intval($_POST['perpage']);
        $start = $page * $num - $num;
        $start = intval($start);
        $idofgallery = intval($_POST['galleryid']);
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__huge_itportfolio_images');
        $query->where('portfolio_id ='.$idofgallery);
        $query ->order('#__huge_itportfolio_images.ordering asc');
        $db->setQuery($query,$start,$num);
        $page_images = $db->loadObjectList();
        $output = '';


        foreach($page_images as $key=>$row){
            $link = str_replace('__5_5_5__','%',$row->sl_url);
            $video_name=str_replace('__5_5_5__','%',$row->name);
            $id = intval($row->id);
            $descnohtml=strip_tags(str_replace('__5_5_5__','%',$row->description));
            $result = substr($descnohtml, 0, 50);
            $getPortfolioView4 = portHtmlView4($row,$key,$paramssld);



            $imagerowstype=$row->sl_type;
            if($row->sl_type == ''){$imagerowstype='image';}
            switch($imagerowstype){
                case 'image':
                    $imgurl=explode(";",$row->image_url);
                    if($row->image_url != ';'){
                        $video='<img id="wd-cl-img'.$key.'" src="'.$imgurl[0].'" alt="" />';
                    } else {
                        $video='<img id="wd-cl-img'.$key.'" src="images/noimage.jpg" alt="" />';
                    }
                    break;
                case 'video':
                    $videourl=get_video_gallery_id_from_url($row->image_url);
                    print_r($row);

                    if($videourl[1]=='youtube'){
                        if(empty($row->thumb_url)){
                            $thumb_pic='http://img.youtube.com/vi/'.$videourl[0].'/mqdefault.jpg';
                        }else{
                            $thumb_pic=$row->thumb_url;
                        }

                        $video='<img src="'.$thumb_pic.'" alt="" />';

                    }else {
                        $vimeo =$row->image_url;
                        $vimeo_explode = explode( "/", $vimeo );
                        $imgid =  end($vimeo_explode);
                        $hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/".$imgid.".php"));
                        $imgsrc=$hash[0]['thumbnail_large'];



                        $hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/".$videourl[0].".php"));
                        if(empty($row->thumb_url)){
                            $imgsrc=$hash[0]['thumbnail_large'];
                        }else{
                            $imgsrc=$row->thumb_url;
                        }

                        $video='<img src="'.$imgsrc.'" alt="" />';

                    }
                    break;
            }

            if(str_replace('__5_5_5__','%',$row->sl_url)=='' ){
                $button='';
            }else{
                $target='target="_blank"';
            }
            $output.='<div class="element_'.$idofgallery.' colorbox_grouping My_First_Category My_Third_Category   hugeitmicro-item "  data-symbol="'.$video_name.'"  data-category="alkaline-earth" >';
            $output.='<input type="hidden" class="pagenum" value="'.$page.'" />';
            $output.='<div class="title-block_'.$idofgallery.'">';
            $output.='<h3>'.$video_name.'</h3>';
            $output.='<div class="open-close-button"><</div>';
            $output.='</div>';
            $output.=$getPortfolioView4;
            $output.='</div>';

        }

        $response = array("success"=>$output,"params"=>$options_params);
        echo json_encode($response);
        die();
    }
}
// View 6 //

else if($_POST['post']=="huge_it_portfolio_gallery_ajax6"){
    $page = 1;


    if(!empty($_POST["page"]) && is_numeric($_POST['page']) && $_POST['page']>0){
        $paramssld='';
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__huge_itportfolio_params');
        $db->setQuery($query);
        $options_params = $db->loadObjectList();
        foreach ($options_params as $rowpar) {
            $key = $rowpar->name;
            $value = $rowpar->value;
            $paramssld[$key] = $value;
        }



        $page = intval($_POST["page"]);
        $num = intval($_POST['perpage']);
        $start = $page * $num - $num;
        $start = intval($start);
        $idofgallery = intval($_POST['galleryid']);
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__huge_itportfolio_images');
        $query->where('portfolio_id ='.$idofgallery);
        $query ->order('#__huge_itportfolio_images.ordering asc');
        $db->setQuery($query,$start,$num);
        $page_images = $db->loadObjectList();
        $output = '';


        foreach($page_images as $key=>$row){
            $link = str_replace('__5_5_5__','%',$row->sl_url);
            $video_name=str_replace('__5_5_5__','%',$row->name);
            $id = intval($row->id);
            $descnohtml=strip_tags(str_replace('__5_5_5__','%',$row->description));
            $result = substr($descnohtml, 0, 50);
            $getPortfolioView6 = portHtmlView6($row,$key,$paramssld);


            $imagerowstype=$row->sl_type;
            if($row->sl_type == ''){$imagerowstype='image';}
            switch($imagerowstype){
                case 'image':
                    $imgurl=explode(";",$row->image_url);
                    if($row->image_url != ';'){
                        $video='<img id="wd-cl-img'.$key.'" src="'.$imgurl[0].'" alt="" />';
                    } else {
                        $video='<img id="wd-cl-img'.$key.'" src="images/noimage.jpg" alt="" />';
                    }
                    break;
                case 'video':
                    $videourl=get_video_gallery_id_from_url($row->image_url);
                    print_r($row);

                    if($videourl[1]=='youtube'){
                        if(empty($row->thumb_url)){
                            $thumb_pic='http://img.youtube.com/vi/'.$videourl[0].'/mqdefault.jpg';
                        }else{
                            $thumb_pic=$row->thumb_url;
                        }

                        $video='<img src="'.$thumb_pic.'" alt="" />';

                    }else {
                        $vimeo =$row->image_url;
                        $vimeo_explode = explode( "/", $vimeo );
                        $imgid =  end($vimeo_explode);
                        $hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/".$imgid.".php"));
                        $imgsrc=$hash[0]['thumbnail_large'];



                        $hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/".$videourl[0].".php"));
                        if(empty($row->thumb_url)){
                            $imgsrc=$hash[0]['thumbnail_large'];
                        }else{
                            $imgsrc=$row->thumb_url;
                        }

                        $video='<img src="../'.$imgsrc.'" alt="" />';


                    }
                    break;
            }

            if(str_replace('__5_5_5__','%',$row->sl_url)=='' ){
                $button='';
            }else{
                if ($row->link_target=="on"){
                    $target='target="_blank"';
                }else{
                    $target='';
                }

                $button='<div class="button-block"><a href="'.str_replace('__5_5_5__','%',$row->sl_url).'" '.$target.' >View More</a></div>';
            }
            $output.='<div class="element_'.$idofgallery.' colorbox_grouping My_First_Category My_Third_Category   hugeitmicro-item "  data-symbol="'.$video_name.'"  data-category="alkaline-earth" >';
            $output.='<input type="hidden" class="pagenum" value="'.$page.'" />';
            $output.=$getPortfolioView6;
            $output.='</div>';
        }

        $response = array("success"=>$output,"params"=>$options_params);
        echo json_encode($response);
        die();
    }

}

            /*FUNCTIONS*/
            
function get_video_gallery_id_from_url($url){
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

function get($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
    }
    function portHtml($row,$key,$paramssld){
        $idofgallery=$_POST['galleryid'];
        $level = $_POST['level'];
        $link = $row->sl_url;
                ob_start();?>
                <div class="wd-portfolio-panel_<?php echo $idofgallery ?>" id="panel<?php echo $key; ?>">
                              <?php  $imgurl=explode(";",$row->image_url);

                                if($paramssld['ht_view0_show_thumbs']=='on' and $paramssld['ht_view0_thumbs_position']=="before" && count($imgurl) != 2) {?>
                                    <div>
                                        <ul class="thumbs-list_<?php echo $idofgallery ?>">
                                            <?php
						array_pop($imgurl);
                                                foreach($imgurl as $key1=>$img) {
                                                    $img_exp = explode("/",$img);
                                                    if($img_exp[0] == 'http:' || $img_exp[0] == 'https:' || $level == '1' ){
                                                        $path = '';
                                                    }
                                                    else{
                                                        $path = '../';
                                                    }
                                            ?>
                                            <li>
<?php
					    switch(youtube_or_vimeo_portfolio($img)) {
                                                case 'image':?>
                                                <a href="<?php echo $path.$img; ?>" class=" portfolio-group<?php echo $idofgallery;?> group1 cboxElement "  title = "<?php echo $row->name;?>"><img src="<?php echo $path.$img; ?>"></a>
                                                        <?php
                                                            break;
                                                            case 'youtube':
                                                            $videourl=youtube_or_vimeo_portfolio($img);?>
                                                <a href="https://www.youtube.com/embed/<?php echo $videourl[0];?>" class="huge_it_portfolio_item youtube portfolio-group<?php echo $idofgallery;?>  group1 cboxElement"  title = "<?php JUri::root().$img;?>" style="position:relative">
                                                                <img src="//img.youtube.com/vi/<?php echo $videourl[0]; ?>/mqdefault.jpg"><div class="play-icon youtube-icon"></div></a>
                                            <?php
                                            break;
                                                case 'vimeo':
                                                    $videourl=get_video_id_from_url_portfolio($img);
                                                    $hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/".$videourl[0].".php"));
                                                    $imgsrc=$hash[0]['thumbnail_large'];?>
                                                    <a class="huge_it_portfolio_item vimeo portfolio-group<?php echo $idofgallery;?> " href="http://player.vimeo.com/video/<?php echo $videourl[0]; ?>" title="<?php echo $row->name; ?>"  style="position:relative">
                                                    <img src="<?php echo $imgsrc; ?>" alt="<?php echo $row->name; ?>" /><div class="play-icon vimeo-icon"></div>
                                                    </a>
                                                  <?php
                                            break;
                                                }?>
                                            </li>
                                        <?php  } ?>
                                        </ul>
                                    </div>
                                <?php }
                                if($paramssld['ht_view0_show_description']=='on'){?>
                                    <div class="description-block_<?php echo $row->id; ?>">
                                        <p><?php echo $row->description; ?></p>
                                    </div>
                                      <?php }
                                $imgurl=explode(";",$row->image_url);
                                if($paramssld['ht_view0_show_thumbs']=='on' and $paramssld['ht_view0_thumbs_position']=="after" && count($imgurl) != 2){?>
                                    <div>
                                        <ul class="thumbs-list_<?php echo $row->id; ?>">
                                            <?php
                                                array_pop($imgurl);
                                                    foreach($imgurl as $key1=>$img) {
                                            ?>
                                                <li>
                                            <?php
                                                switch($this->youtube_or_vimeo_portfolio($img)) {
                                                    case 'image':?>
                                                    <a href="<?php echo $img; ?>" class=" portfolio-group<?php echo $group_key;?> "  title = "<?php echo $row->name;?>"><img src="<?php echo JUri::root().$img; ?>"></a>
                                                            <?php
                                                                break;
                                                                    case 'youtube':
                                                                        $videourl=$this->get_video_id_from_url_portfolio($img);?>
                                                                            <a href="https://www.youtube.com/embed/<?php echo $videourl[0];?>" class="huge_it_portfolio_item youtube portfolio-group<?php echo $group_key;?> "  title = "<?php echo $row->name;?>" style="position:relative">
                                                                            <img src="//img.youtube.com/vi/<?php echo $videourl[0]; ?>/mqdefault.jpg"><div class="play-icon youtube-icon"></div></a>
                                                                            <?php
                                                                                break;
                                                                                    case 'vimeo':
                                                                                        $videourl=$this->get_video_id_from_url_portfolio($img);
                                                                                        $hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/".$videourl[0].".php"));
                                                                                        $imgsrc=$hash[0]['thumbnail_large'];?>
                                                                                        <a class="huge_it_portfolio_item vimeo portfolio-group<?php echo $group_key;?> " href="http://player.vimeo.com/video/<?php echo $videourl[0]; ?>" title="<?php echo $row->name; ?>"  style="position:relative">
                                                                                        <img src="<?php echo $imgsrc; ?>" alt="<?php echo $row->name; ?>" /><div class="play-icon vimeo-icon"></div>
                                                                                        </a>
                                                                            <?php
                                                                                break;
                                                }?>
                                                </li>
                                                    <?php  } ?>
                                        </ul>
                                    </div>
                        <?php   }
                        if($paramssld['ht_view0_show_linkbutton']=='on' && $link != ''){?>
                            <div class="button-block">
                                <a href="<?php echo $link; ?>" <?php if ($row->link_target=="on"){echo 'target="_blank"';}?>><?php echo $paramssld['ht_view0_linkbutton_text']; ?></a>
                            </div>
                        <?php } ?>
                            </div>

  <?php
        return ob_get_clean();

                                }

function portHtmlView1($row,$key,$paramssld){
    $idofgallery=$_POST['galleryid'];
    $level = $_POST['level'];
    $link = $row->sl_url;
    ob_start();?>
    <div class="default-block_<?php echo $idofgallery; ?>">
        <div class="image-block_<?php echo $idofgallery; ?> add-H-relative" >
            <?php $imgurl=explode(";",$row->image_url); ?>
            <?php
            $imgurl_exp = explode('/',$imgurl[0]);
            if($imgurl_exp[0] == 'http:' || $imgurl_exp[0] == 'https:' || $level == '1' ){
                $path = '';
            }
            else{
                $path = '../';
            }
            if($row->image_url != ';'){
                switch(youtube_or_vimeo_portfolio($imgurl[0])) {
                    case 'image':		?>
                        <a href="<?php echo $path.$imgurl[0]; ?>" class=" portfolio-group<?php echo $key;?>  group1 cboxElement" title="<?php echo $row->name;?>">
                            <img alt="<?php echo $row->name; ?>" id="wd-cl-img<?php echo $key; ?>" src="<?php echo $path.$imgurl[0]; ?>" />
                        </a>
                        <?php
                        break;
                    case 'youtube':
                        $videourl=youtube_or_vimeo_portfolio($imgurl[0]);?>
                        <a href="https://www.youtube.com/embed/<?php echo $videourl[0];?>" class="huge_it_portfolio_item youtube portfolio-group<?php echo $key;?> group1 cboxElement" title="<?php echo $row->name;?>">
                            <img alt="<?php echo $row->name; ?>" id="wd-cl-img<?php echo $key; ?>"  src="//img.youtube.com/vi/<?php echo $videourl[0]; ?>/mqdefault.jpg"  />
                            <div class="play-icon <?php echo $videourl[1];?>-icon"></div>
                        </a>
                        <?php
                        break;
                    case 'vimeo':
                        $videourl=youtube_or_vimeo_portfolio($imgurl[0]);
                        $hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/".$videourl[0].".php"));
                        $imgsrc=$hash[0]['thumbnail_large'];
                        ?>
                        <a class="huge_it_portfolio_item vimeo portfolio-group<?php echo $key;?> " href="http://player.vimeo.com/video/<?php echo $videourl[0]; ?> group1 cboxElement" title="<?php echo $row->name; ?>">
                            <img alt="<?php echo $row->name; ?>" src="<?php echo $imgsrc; ?>"  />
                            <div class="play-icon <?php echo $videourl[1];?>-icon"></div>
                        </a>
                        <?php break;
                }
            } else { ?>
                <img alt="<?php echo $row->name; ?>" id="wd-cl-img<?php echo $key; ?>" src="images/noimage.jpg" />
            <?php } ?>
        </div>
        <div class="title-block_<?php echo $idofgallery; ?>">
            <h3 class="title"><?php echo $row->name; ?></h3>
            <div class="open-close-button"></div>
        </div>
    </div>



    <div class="wd-portfolio-panel_<?php echo $idofgallery ?>" id="panel<?php echo $key; ?>">
        <?php  $imgurl=explode(";",$row->image_url);
        if($paramssld['ht_view0_show_thumbs']=='on' and $paramssld['ht_view0_thumbs_position']=="before" && count($imgurl) != 2) {?>
            <div>
                <ul class="thumbs-list_<?php echo $idofgallery ?>">
                    <?php
                    array_pop($imgurl);
                    foreach($imgurl as $key1=>$img) {
                        $img_exp = explode('/',$img);
                        if($img_exp[0] == 'http:' || $img_exp[0] == 'https:' || $level == '1' ){
                            $path_t = '';
                        }
                        else{
                            $path_t = '../';
                        }
                        ?>
                        <li>
                            <?php
                            switch(youtube_or_vimeo_portfolio($img)) {
                                case 'image':?>
                                    <a href="<?php echo $path_t.$img; ?>" class=" portfolio-group<?php echo $idofgallery;?> group1 cboxElement "  title = "<?php echo $row->name;?>"><img src="<?php echo $path_t.$img; ?>"></a>
                                    <?php
                                    break;
                                case 'youtube':
                                    $videourl=youtube_or_vimeo_portfolio($img);?>
                                    <a href="https://www.youtube.com/embed/<?php echo $videourl[0];?>" class="huge_it_portfolio_item youtube portfolio-group<?php echo $idofgallery;?> group1 cboxElement "  title = "<?php JUri::root().$img;?>" style="position:relative">
                                        <img src="//img.youtube.com/vi/<?php echo $videourl[0]; ?>/mqdefault.jpg"><div class="play-icon youtube-icon"></div></a>
                                    <?php
                                    break;
                                case 'vimeo':
                                    $videourl=get_video_id_from_url_portfolio($img);
                                    $hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/".$videourl[0].".php"));
                                    $imgsrc=$hash[0]['thumbnail_large'];?>
                                    <a class="huge_it_portfolio_item vimeo portfolio-group<?php echo $idofgallery;?>  group1 cboxElement" href="http://player.vimeo.com/video/<?php echo $videourl[0]; ?>" title="<?php echo $row->name; ?>"  style="position:relative">
                                        <img src="<?php echo $imgsrc; ?>" alt="<?php echo $row->name; ?>" /><div class="play-icon vimeo-icon"></div>
                                    </a>
                                    <?php
                                    break;
                            }?>
                        </li>
                    <?php  } ?>
                </ul>
            </div>
        <?php }
        if($paramssld['ht_view0_show_description']=='on'){?>
            <div class="description-block_<?php echo $row->id; ?>">
                <p><?php echo $row->description; ?></p>
            </div>
        <?php }
        $imgurl=explode(";",$row->image_url);
        if($paramssld['ht_view0_show_thumbs']=='on' and $paramssld['ht_view0_thumbs_position']=="after" && count($imgurl) != 2){?>
            <div>
                <ul class="thumbs-list_<?php echo $row->id; ?>">
                    <?php
                    array_pop($imgurl);
                    foreach($imgurl as $key1=>$img) {
                        ?>
                        <li>
                            <?php
                            switch($this->youtube_or_vimeo_portfolio($img)) {
                                case 'image':?>
                                    <a href="<?php echo $img; ?>" class=" portfolio-group<?php echo $group_key;?> "  title = "<?php echo $row->name;?>"><img src="<?php echo JUri::root().$img; ?>"></a>
                                    <?php
                                    break;
                                case 'youtube':
                                    $videourl=$this->get_video_id_from_url_portfolio($img);?>
                                    <a href="https://www.youtube.com/embed/<?php echo $videourl[0];?>" class="huge_it_portfolio_item youtube portfolio-group<?php echo $group_key;?> "  title = "<?php echo $row->name;?>" style="position:relative">
                                        <img src="//img.youtube.com/vi/<?php echo $videourl[0]; ?>/mqdefault.jpg"><div class="play-icon youtube-icon"></div></a>
                                    <?php
                                    break;
                                case 'vimeo':
                                    $videourl=$this->get_video_id_from_url_portfolio($img);
                                    $hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/".$videourl[0].".php"));
                                    $imgsrc=$hash[0]['thumbnail_large'];?>
                                    <a class="huge_it_portfolio_item vimeo portfolio-group<?php echo $group_key;?> " href="http://player.vimeo.com/video/<?php echo $videourl[0]; ?>" title="<?php echo $row->name; ?>"  style="position:relative">
                                        <img src="<?php echo $imgsrc; ?>" alt="<?php echo $row->name; ?>" /><div class="play-icon vimeo-icon"></div>
                                    </a>
                                    <?php
                                    break;
                            }?>
                        </li>
                    <?php  } ?>
                </ul>
            </div>
        <?php   }
        if($paramssld['ht_view0_show_linkbutton']=='on' && $link != ''){?>
            <div class="button-block">
                <a href="<?php echo $link; ?>" <?php if ($row->link_target=="on"){echo 'target="_blank"';}?>><?php echo $paramssld['ht_view0_linkbutton_text']; ?></a>
            </div>
        <?php } ?>
    </div>

    <?php
    return ob_get_clean();

}





function portHtmlView2($row,$key,$paramssld){
    $idofgallery=$_POST['galleryid'];
    $level = $_POST['level'];
    $link = $row->sl_url;
    ob_start();?>
    <div class="image-block_<?php echo $idofgallery; ?>">
        <?php $imgurl=explode(";",$row->image_url); ?>
        <?php
        $imgurl_exp = explode('/',$imgurl[0]);
        if($imgurl_exp[0] == 'http:' || $imgurl_exp[0] == 'https:' || $level == '1' ){
            $path = '';
        }
        else{
            $path = '../';
        }
        if($row->image_url != ';') {
            switch(youtube_or_vimeo_portfolio($imgurl[0])) {
                case 'image':	?>
                    <img alt="<?php echo $row->name; ?>" id="wd-cl-img<?php echo $key; ?>" src="<?php echo $path.$imgurl[0]; ?>" />
                    <?php
                    break;
                case 'youtube':
                    $videourl=youtube_or_vimeo_portfolio($imgurl[0]);?>
                    <img alt="<?php echo $row->name; ?>" id="wd-cl-img<?php echo $key; ?>"  src="//img.youtube.com/vi/<?php echo $videourl[0]; ?>/mqdefault.jpg"  />
                    <?php
                    break;
                case 'vimeo':
                    $videourl=youtube_or_vimeo_portfolio($imgurl[0]);
                    $hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/".$videourl[0].".php"));
                    $imgsrc=$hash[0]['thumbnail_large'];
                    ?>
                    <img alt="<?php echo $row->name; ?>" src="<?php echo JUri::root().$imgsrc; ?>"  />
                    <?php break;

            }
        } else { ?>
            <img alt="<?php echo $row->name; ?>" id="wd-cl-img<?php echo $key; ?>" src="images/noimage.jpg" />
            <?php
        } ?>
        <div class="image-overlay"><a href="#<?php echo $row->id; ?>"></a></div>
    </div>
    <div class="title-block_<?php echo $idofgallery; ?>">
        <h3><?php echo $row->name; ?></h3>
        <?php if($paramssld["ht_view2_element_show_linkbutton"]=='on'  && $link != '' ){?>
            <div class="button-block"><a href="<?php echo $row->sl_url; ?>" <?php if ($row->link_target=="on"){echo 'target="_blank"';}?> ><?php echo $paramssld["ht_view2_element_linkbutton_text"]; ?></a></div>
        <?php } ?>
    </div>

    <?php
    return ob_get_clean();

}



function portHtmlView3($row,$key,$paramssld){
    $idofgallery=$_POST['galleryid'];
    $link = $row->sl_url;
    $level = $_POST['level'];
    ob_start();?>
    <div class="left-block_<?php echo $idofgallery; ?>">
        <div class="main-image-block_<?php echo $idofgallery; ?> add-H-relative" >
            <?php $imgurl=explode(";",$row->image_url); ?>
            <?php
            $imgurl_exp = explode('/',$imgurl[0]);
            if($imgurl_exp[0] == 'http:' || $imgurl_exp[0] == 'https:' || $level == '1'){
                $path = '';
            }
            else{
                $path = '../';
            }

            if($row->image_url != ';') {
                switch(youtube_or_vimeo_portfolio($imgurl[0])) {
                    case 'image':

                        ?>
                        <a href="<?php echo $path.$imgurl[0]; ?>" class=" portfolio-group<?php echo $idofgallery; ?> group1 cboxElement" title="<?php echo $row->name; ?>" ><img alt="<?php echo $row->name; ?>" id="wd-cl-img<?php echo $key; ?>" src="<?php echo $path.$imgurl[0]; ?>"></a>
                        <?php
                        break;
                    case 'youtube':
                        $videourl=get_video_id_from_url_portfolio($imgurl[0]);?>
                        <a href="https://www.youtube.com/embed/<?php echo $videourl[0];?>" class="huge_it_portfolio_item youtube portfolio-group<?php echo $idofgallery;?> add-H-block"  title="<?php echo $row->name; ?>"  >
                            <img src="//img.youtube.com/vi/<?php echo $videourl[0]; ?>/mqdefault.jpg"><div class="play-icon <?php echo $videourl[1];?>-icon"></div></a>
                        <?php break;
                    case 'vimeo':
                        $videourl=get_video_id_from_url_portfolio($imgurl[0]);
                        $hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/".$videourl[0].".php"));
                        $imgsrc=$hash[0]['thumbnail_large'];?>
                        <a class="huge_it_portfolio_item vimeo portfolio-group<?php echo $group_key;?> add-H-block" href="http://player.vimeo.com/video/<?php echo $videourl[0]; ?> " title="<?php echo $row->name; ?>" >
                            <img src="<?php echo JUri::root().$imgsrc; ?>" alt="<?php echo $row->name; ?>" /><div class="play-icon <?php echo $videourl[1];?>-icon"></div>
                        </a>
                        <?php
                }
            }
            else { ?>
                <a href="<?php echo $imgurl[0]; ?>" class=" portfolio-group<?php echo $group_key; ?>"><img alt="<?php echo $row->name; ?>" id="wd-cl-img<?php echo $key; ?>" src="images/noimage.jpg"></a>
                <?php
            }
            ?>
        </div>
        <div class="thumbs-block">
            <?php
            if($paramssld["ht_view3_show_thumbs"] == 'on')
            {
                ?>
                <ul class="thumbs-list_<?php echo $idofgallery; ?>">
                    <?php
                    $imgurl=explode(";",$row->image_url);
                    array_pop($imgurl);
                    array_shift($imgurl);

                    foreach($imgurl as $key=>$img) {
                        $img_exp = explode('/',$img);
                        if($img_exp[0] == 'http:' || $img_exp[0] == 'https:' || $level == '1' ){
                            $path_t = '';
                        }
                        else{
                            $path_t = '../';
                        }
                        switch(youtube_or_vimeo_portfolio($img)) {
                            case 'image':
                                ?>
                                <li><a href="<?php echo $path_t.$img;?>" class=" portfolio-group<?php echo $idofgallery; ?> group1 cboxElement"  title = "<?php echo $row->name;?>"><img src="<?php echo $path_t.$img; ?>"></a></li>
                                <?php   break;
                            case 'youtube':
                                $videourl=youtube_or_vimeo($img);?>
                                <li><a href="https://www.youtube.com/embed/<?php echo $videourl[0];?>" class="huge_it_portfolio_item youtube portfolio-group<?php echo $idofgallery;?>  add-H-relative"  title="<?php echo $row->name; ?>" >
                                        <img src="//img.youtube.com/vi/<?php echo $videourl[0]; ?>/mqdefault.jpg"><div class="play-icon <?php echo $videourl[1];?>-icon"></div></a>
                                </li>
                                <?php
                                break;
                            case 'vimeo':
                                $videourl=get_video_id_from_url_portfolio($img);
                                $hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/".$videourl[0].".php"));
                                $imgsrc=$hash[0]['thumbnail_large'];?>
                                <li>
                                    <a class="huge_it_portfolio_item vimeo portfolio-group<?php echo $idofgallery;?>  add-H-relative" href="http://player.vimeo.com/video/<?php echo $videourl[0]; ?>" title="<?php echo $row->name; ?>">
                                        <img src="<?php echo JUri::root().$imgsrc; ?>" alt="<?php echo $row->name; ?>" /><div class="play-icon <?php echo $videourl[1];?>-icon"></div>
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
        <?php if($row->name!=''){?><div class="title-block_<?php echo $idofgallery; ?>"><h3><?php echo $row->name; ?></h3></div><?php } ?>
        <?php
        if($paramssld["ht_view3_show_description"] == 'on')
        {
            if($row->description!='')
            { ?>
                <div class="description-block_<?php echo $idofgallery; ?>"><p><?php echo $row->description; ?></p></div>
            <?php } ?>
        <?php }

        if($link!='')
        {
            if($paramssld["ht_view3_show_linkbutton"] == 'on' && $paramssld["ht_view3_linkbutton_text"] != '' && $link != '') {
                ?>
                <div class="button-block">
                    <a href="<?php echo $link; ?>" <?php if ($row->link_target=="on"){echo 'target="_blank"';}?>><?php echo $paramssld["ht_view3_linkbutton_text"]; ?></a>
                </div>
            <?php }
        } ?>
    </div>

    <?php
    return ob_get_clean();

}

function portHtmlView4($row,$key,$paramssld){
    $idofgallery=$_POST['galleryid'];
    $link = $row->sl_url;
    ob_start();?>
    <div class="wd-portfolio-panel_<?php echo $idofgallery ?>" id="panel<?php echo $key; ?>">
        <?php  $imgurl=explode(";",$row->image_url);
        if($paramssld['ht_view0_show_description']=='on'){?>
            <div class="description-block_<?php echo $row->id; ?>">
                <p><?php echo $row->description; ?></p>
            </div>
        <?php }
        $imgurl=explode(";",$row->image_url);
        if($paramssld['ht_view4_show_linkbutton']=='on' && $link != ''){?>
            <div class="button-block">
                <a href="<?php echo $link; ?>" <?php if ($row->link_target=="on"){echo 'target="_blank"';}?>><?php echo $paramssld['ht_view0_linkbutton_text']; ?></a>
            </div>
        <?php } ?>
    </div>

    <?php
    return ob_get_clean();

}


function portHtmlView6($row,$key,$paramssld){
    $idofgallery=$_POST['galleryid'];
    $link = $row->sl_url;
    $level = $_POST['level'];
    ob_start();?>
    <p style="display: none;" class="id"><?php echo $row->id; ?></p>
    <div class="image-block_<?php echo $idofgallery; ?>">
        <?php $imgurl=explode(";",$row->image_url); ?>
        <?php
        $imgurl_exp = explode('/',$imgurl[0]);
        if($imgurl_exp[0] == 'http:' || $imgurl_exp[0] == 'https:' || $level == '1' ){
            $path = '';
        }
        else{
            $path = '../';
        }

        if($row->image_url != ';'){
            switch(youtube_or_vimeo_portfolio($imgurl[0])) {
                case 'image':	?>
                    <a href="<?php echo $path.$imgurl[0]; ?>" class=" portfolio-lightbox-group<?php echo $idofgallery;?> group1 cboxElement portfolio-lightbox-group" title = "<?php echo $row->name; ?>">
                        <img alt="<?php echo $row->name; ?>" id="wd-cl-img<?php echo $key; ?>" src="<?php echo $path.$imgurl[0]; ?>" />
                    </a>
                    <?php
                    break;
                case 'youtube':

                    $videourl=youtube_or_vimeo_portfolio($imgurl[0]);?>
                    <a href="https://www.youtube.com/embed/<?php echo $videourl[0];?>" class="huge_it_portfolio_item youtube  portfolio-lightbox-group<?php echo $idofgallery;?> group1 cboxElement portfolio-lightbox-group"  title = "<?php echo $row->name;?>">
                        <img alt="<?php echo $row->name; ?>" id="wd-cl-img<?php echo $key; ?>"  src="//img.youtube.com/vi/<?php echo $videourl[0]; ?>/mqdefault.jpg"  />
                        <div class="play-icon <?php echo $videourl[1];?>-icon"></div>
                    </a>
                    <?php
                    break;
                case 'vimeo':
                    $videourl=youtube_or_vimeo_portfolio($imgurl[0]);
                    $hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/".$videourl[0].".php"));
                    $imgsrc=$hash[0]['thumbnail_large'];?>
                    <a class="huge_it_portfolio_item vimeo  portfolio-lightbox-group<?php echo $idofgallery;?> group1 cboxElement portfolio-lightbox-group" href="http://player.vimeo.com/video/<?php echo $videourl[0]; ?>" title="<?php echo $row->name; ?>">
                        <img src="<?php echo JUri::root().$imgsrc; ?>" alt="<?php echo $row->name; ?>" /><div class="play-icon vimeo-icon"></div>
                    </a>
                    <?php
                    break;
            }
        }

        else { ?>
            <img alt="<?php echo $row->name; ?>" id="wd-cl-img<?php echo $key; ?>" src="images/noimage.jpg" />
            <?php
        } ?>
    </div>
    <?php if($row->name!=""){?>
        <div class="title-block_<?php echo $idofgallery; ?>">
            <?php
            $forClick = '';
            if($link == '') {
                $forClick =  'style="pointer-events: none;  cursor: default;"';
            }

            if($link != ""){?>
                <a href="<?php echo $link; ?>" <?php if ($row->link_target=="on"){echo 'target="_blank"';}?> <?php echo $forClick;?> ><?php echo $row->name; ?></a>

            <?php }
            else{?>
                <a href="#" ><?php echo $row->name; ?></a>
            <?php }
            ?>
        </div>
    <?php } ?>

    <?php
    return ob_get_clean();

}





function youtube_or_vimeo($videourl){
    if(strpos($videourl,'youtube') !== false || strpos($videourl,'youtu') !== false){
        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $videourl, $match)) {
            return 'youtube';
        }
    }
    elseif(strpos($videourl,'vimeo') !== false && strpos($videourl,'video') !== false) {
        $explode = explode("/",$videourl);
        $end = end($explode);
        if(strlen($end) == 8)
            return 'vimeo';
    }
    return 'image';
}









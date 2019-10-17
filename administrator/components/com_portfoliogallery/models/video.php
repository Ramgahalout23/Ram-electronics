<?php
/**
 * @package Portfolio Gallery
 * @author Huge-IT
 * @copyright (C) 2014 Huge IT. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @website		http://www.huge-it.com/
 * */
defined('_JEXEC') or die;
jimport('joomla.application.component.modeladmin');
jimport('joomla.application.component.helper');

class PortfoliogalleryModelVideo extends JModelAdmin {

    public function getTable($type = 'Video', $prefix = 'VideoTable', $config = array()) {
        return JTable::getInstance($type, $prefix, $config);
    }

    public function getForm($data = array(), $loadData = true) {

        $form = $this->loadForm(
                $this->option . '.video', 'video', array('control' => 'jform', 'load_data' => $loadData)
        );

        if (empty($form)) {
            return false;
        }

        return $form;
    }

    protected function loadFormData() {
        $data = JFactory::getApplication()->getUserState($this->option . '.editvideo.data', array());

        if (empty($data)) {
            $data = $this->getItem();
        }

        return $data;
    }
    
    function getPortfolio_imagesRow($id_cat, $id) {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('#__huge_itportfolio_images.image_url as image_url');
        $query->from(array('#__huge_itportfolio_portfolios' => '#__huge_itportfolio_portfolios', '#__huge_itportfolio_images' => '#__huge_itportfolio_images'));
        $query->where('#__huge_itportfolio_portfolios.id = portfolio_id')->where('portfolio_id=' . $id_cat)->where('#__huge_itportfolio_images.id=' . $id);
      
        $db->setQuery($query);
        $results = $db->loadRow();
        return $results;
    }
    
    function getPortfolio_images($id_cat, $id) {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('#__huge_itportfolio_images.image_url as image_url');
        $query->from(array('#__huge_itportfolio_portfolios' => '#__huge_itportfolio_portfolios', '#__huge_itportfolio_images' => '#__huge_itportfolio_images'));
        $query->where('#__huge_itportfolio_portfolios.id = portfolio_id')->where('portfolio_id=' . $id_cat)->where('#__huge_itportfolio_images.id=' . $id);
      
        $db->setQuery($query);
        $results = $db->loadObjectList();
        return $results;
    }

    function save($data) {
       
    
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
}
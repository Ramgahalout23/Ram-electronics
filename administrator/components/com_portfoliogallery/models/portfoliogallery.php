<?php
/**
 * @package Huge-IT Portfolio Gallery
 * @author Huge-IT
 * @copyright (C) 2014 Huge IT. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @website		http://www.huge-it.com/
 * */
defined('_JEXEC') or die;
jimport('joomla.application.component.modeladmin');
jimport('joomla.application.component.helper');

class PortfoliogalleryModelPortfoliogallery extends JModelAdmin {

    public function getTable($type = 'Portfoliogallery', $prefix = 'PortfoliogalleryTable', $config = array()) {
        return JTable::getInstance($type, $prefix, $config);
    }

    public function getForm($data = array(), $loadData = true) {

        $form = $this->loadForm(
                $this->option . '.portfoliogallery', 'portfoliogallery', array('control' => 'jform', 'load_data' => $loadData)
        );

        if (empty($form)) {
            return false;
        }

        return $form;
    }

    protected function loadFormData() {
        $data = JFactory::getApplication()->getUserState($this->option . '.editlightbox.data', array());
        if (empty($data)) {
            $data = $this->getItem();
        }
        return $data;
    }

    public function getPortfolio() {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__huge_itportfolio_portfolios');
        $db->setQuery($query);
        $results = $db->loadObjectList();
        return $results;
    }

    public function getPropertie() {
        $db = JFactory::getDBO();
        $id_cat = intval(JRequest::getVar('id'));
        $query = $db->getQuery(true);
        $query->select('#__huge_itportfolio_images.name as name,'
                . '#__huge_itportfolio_images.id ,'
                . '#__huge_itportfolio_portfolios.name as portName,'
                . 'portfolio_id,#__huge_itportfolio_images.category as category, #__huge_itportfolio_images.description as description,image_url,sl_url,sl_type,link_target,#__huge_itportfolio_images.ordering,#__huge_itportfolio_images.published,published_in_sl_width');
        $query->from(array('#__huge_itportfolio_portfolios' => '#__huge_itportfolio_portfolios', '#__huge_itportfolio_images' => '#__huge_itportfolio_images'));
        $query->where('#__huge_itportfolio_portfolios.id = portfolio_id')->where('portfolio_id=' . $id_cat);
        $query->order('ordering asc');
        $db->setQuery($query);
        $results = $db->loadObjectList();
        return $results;
    }

    public function getImageByID() {
        $db = JFactory::getDBO();
        $id_cat = intval(JRequest::getVar('id'));
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__huge_itportfolio_images');
        $query->where('portfolio_id=' . $id_cat);
        $db->setQuery($query);
        $results = $db->loadObjectList();
        return $results;
    }

    public function save($data) {
        $db = JFactory::getDBO();
        $result = $this->getPropertie();
        $this->updartePortfolio();
        $this->selectStyle();
		
        foreach ($result as $key => $value) {
            $imageId = intval($value->id);
            $id = $data['imageId' . $imageId];
            $titleimage = $db->escape($data['titleimage' . $imageId]);
            $im_description = $db->escape($data['im_description' . $imageId]);
            $sl_url = $data['sl_url' . $imageId];
            $sl_link_target = $data['sl_link_target' . $imageId];
            $ordering = $data['order_by_' . $imageId];
            $image_url = $data['image_url' . $imageId];
            $category = $data['category'.$imageId];
			
			
            $query = $db->getQuery(true);
            $query->update('#__huge_itportfolio_images')->set('name="' . $titleimage . '"')->set('description="' . $im_description . '"')
                    ->set('sl_url="' . $sl_url . '"')->set('link_target="' . $sl_link_target . '"')
                    ->set('ordering="' . $ordering . '"')->set('image_url="' . $image_url . '"')->set('category="' . $category . '"')->where('id=' . $imageId);
            $db->setQuery($query);
            $db->execute();
        }
    }

    function updartePortfolio() {
        $db = JFactory::getDBO();
        $data = JRequest::get('post');
        $name = htmlspecialchars($data['name']);
        $sl_position = $data['sl_position'];
		$pagination_type = $data['pagination_type'];
		$count_into_page = $data['count_into_page'];
        $id_cat = intval(JRequest::getVar('id'));
        $query = $db->getQuery(true);
        $query->update('#__huge_itportfolio_portfolios')->set('name ="' . $name . '"')->set('sl_position = "'.$sl_position.'"')
						->set('pagination_type="' . $pagination_type . '"')->set('count_into_page="' . $count_into_page . '"')->where('id="' . $id_cat . '"');
        $db->setQuery($query);
        $db->execute();
    }

    function selectStyle() {
        $db = JFactory::getDBO();
        $data = JRequest::get('post');
        $styleName = $data['portfolio_effects_list'];
        $allCategories =  $data['allCategories'];
        $ht_show_sorting = $data['ht_show_sorting'];
        $id_cat = intval(JRequest::getVar('id'));
        $query = $db->getQuery(true);
        $query->update('#__huge_itportfolio_portfolios')
              ->set('portfolio_list_effects_s ="' . $styleName . '"')
              ->set('categories ="'.$allCategories.'"')
              ->set('ht_show_sorting ="'.$ht_show_sorting.'"')
            ->where('id="' . $id_cat . '"');
        $db->setQuery($query);
        $db->execute();
    }

    public function saveCat() {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->insert('#__huge_itportfolio_portfolios', 'id')->set('name = "New Portfolio"');
        $db->setQuery($query);
        $db->execute();
        return $db->insertid();
    }

    private function getNumber($galleryId) {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('min(ordering) as maximum');
        $query->from('#__huge_itportfolio_images');
        $query->where('portfolio_id=' . $galleryId);
        $db->setQuery($query);
        $results = $db->loadResult();
        return $results;        
    }
    function saveProject($imageUrl, $portfolioId) {
        $imageUrl = $imageUrl . ";";
        $ordering = $this->getNumber($portfolioId) - 1;
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->insert('#__huge_itportfolio_images', 'id')->set('portfolio_id = "' . $portfolioId . '"')->set('image_url= "' .$imageUrl . '"')->set('ordering= "'.$ordering.'"');;
        $db->setQuery($query);
        $db->execute();
        return $portfolioId;
    }

    public function deleteProject() {
        $id_cat = intval(JRequest::getVar('removeslide'));
        $id = intval(JRequest::getVar('id'));
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->delete('#__huge_itportfolio_images')->where('id =' . $id_cat);
        $db->setQuery($query);
        $db->execute();
        return;
    }

}

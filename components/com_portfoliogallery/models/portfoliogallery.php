<?php
/**
 * @package Huge-IT Portfolio Gallery
 * @author Huge-IT
 * @copyright (C) 2014 Huge IT. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @website		http://www.huge-it.com/
 * */
defined('_JEXEC') or die;

jimport('joomla.application.component.modelitem');

class PortfoliogalleryModelPortfoliogallery extends JModelItem
{
	public function getTable($type = 'Portfoliogallery', $prefix = 'PortfoliogalleryTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	
	public function getItem($id = null)
	{
		
		$id = (!empty($id)) ? $id : (int) $this->getState('message.id');

		if ($this->_item === null)
		{
			$this->_item = array();
		}

		if (!isset($this->_item[$id]))
		{
			
			$table = $this->getTable();

			
			$table->load($id);

			$this->_item[$id] = $table->name;
		}

		return $this->_item[$id];
	}

        protected function populateState()
	{
		$app = JFactory::getApplication();

		// РџРѕР»СѓС‡Р°РµР�? Id СЃРѕРѕР±С‰РµРЅРёСЏ РёР· Р—Р°РїСЂРѕСЃР°.
		$id = $app->input->getInt('id', 0);

		// Р”РѕР±Р°РІР»СЏРµР�? Id СЃРѕРѕР±С‰РµРЅРёСЏ РІ СЃРѕСЃС‚РѕСЏРЅРёРµ Р�?РѕРґРµР»Рё.
		$this->setState('message.id', $id);

		parent::populateState();
	}
        
        public function getPortfolios(){
       $db = JFactory::getDBO();
       $id = (!empty($id)) ? $id : (int) $this->getState('message.id');
       $id = $this->setState('message.id', $id);
       $query = $db->getQuery(true);
       $query->SELECT('*');
       $query-> FROM ('#__huge_itportfolio_portfolios');
       $query-> where('id='.$id);
       $query->order('ordering ASC',$id);
       $db->setQuery($query);
       $results = $db->loadObjectList();
       return $results;
        }
       public function getPortfolioParams(){
           $db = JFactory::getDBO();
           $id = (!empty($id)) ? $id : (int) $this->getState('message.id');
           $id = $this->setState('message.id', $id);
           $query = $db->getQuery(true);
           $query->select('*,#__huge_itportfolio_images.name as imgname');
           $query->from('#__huge_itportfolio_portfolios,#__huge_itportfolio_images');
           $query->where('#__huge_itportfolio_portfolios.id ='.$id)->where('#__huge_itportfolio_portfolios.id = #__huge_itportfolio_images.portfolio_id');
           $db->setQuery($query);
           $results = $db->loadObjectList();
           return $results;
       
          
           }
           
        public function getPortfolioId(){
        $db = JFactory::getDBO();
        $id = (!empty($id)) ? $id : (int) $this->getState('message.id');
        $id = $this->setState('message.id', $id);
        return $id;
        }
          
          public function  getLightboxParams(){
           $db = JFactory::getDBO();
           $query = $db->getQuery(true);
           $query ->select('*');
           $query -> from('#__huge_itportfolio_params');
           $db->setQuery($query);
           $results = $db->loadObjectList();
            return $results;
          }
     
}
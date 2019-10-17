<?php
/**
 * @package Portfolio Gallery
 * @author Huge-IT
 * @copyright (C) 2014 Huge IT. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @website		http://www.huge-it.com/
 * */
defined('_JEXEC') or die;
jimport('joomla.application.component.modellist');

class PortfoliogalleryModelPortfoliogalleries extends JModelList
{
        protected function getListQuery()
        {
               $db = JFactory::getDBO();
               $query = $db->getQuery(true);
               $query
                    ->select('id,name')
                    ->from('#_huge_itportfolio_portfolios');
 
                return $query;
        }
             
        public function getPortfolio(){
            $db = JFactory::getDBO();
            $query = $db->getQuery(true);
            $query->select('#__huge_itportfolio_portfolios.name, #__huge_itportfolio_portfolios.id,count(*) as count');
            $query->from(array('#__huge_itportfolio_portfolios' => '#__huge_itportfolio_portfolios', '#__huge_itportfolio_images'=>'#__huge_itportfolio_images'));
            $query->where('#__huge_itportfolio_portfolios.id = portfolio_id');
            $query->group('#__huge_itportfolio_portfolios.name');
            $db->setQuery($query);
         
            $results = $db->loadObjectList();
            return $results;
        }
          public function getOther() {
             $db = JFactory::getDBO();
            $query2 = $db->getQuery(true);
             $query2->select('#__huge_itportfolio_portfolios.name, #__huge_itportfolio_portfolios.id,0 as count');
                    $query2->from('#__huge_itportfolio_portfolios');
                     $query2->where('#__huge_itportfolio_portfolios.id not in (select portfolio_id from #__huge_itportfolio_images)');
                      $db->setQuery($query2);
      
            $results = $db->loadObjectList();
            return $results;
        
            
        }
        
   
}

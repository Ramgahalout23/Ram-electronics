<?php
/**
 * @package Portfolio Gallery
 * @author Huge-IT
 * @copyright (C) 2014 Huge IT. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @website		http://www.huge-it.com/
 * */
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

class PortfoliogalleryControllerPortfoliogallery extends JControllerForm
{
  
     public function save ($key = null, $urlVar = null) {
        $model = $this->getModel();
        global $option;
        $id_cat = intval(JRequest::getVar('id'));
        $table = $model->getTable();
        $post = JRequest::get('post',JREQUEST_ALLOWHTML);
      
        $model->save($post);
        $this->setredirect('index.php?option=com_portfoliogallery&view=portfoliogallery&layout=edit&id='.$id_cat,"Saved");
    }
    
    
function addProject() {
     $model = $this->getModel();    
     $id = $model->saveProject(JRequest::getVar('sel'),intval(JRequest::getVar('parentId') ));
     $this->setredirect('index.php?option=com_portfoliogallery&view=portfoliogallery&layout=edit&id='. $id);
    
}
function deleteProject(){
     $model = $this->getModel();    
     $id = $model->deleteProject();
     $projectId = JRequest::getVar('id');
     echo $projectId;
  $this->setredirect('index.php?option=com_portfoliogallery&view=portfoliogallery&layout=edit&id='.$projectId);
 }
}

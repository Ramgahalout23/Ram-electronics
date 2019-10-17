<?php
/**
* @title		portfolio gallery image beautiful
* @website		http://www.joomhome.com
* @copyright	Copyright (C) 2015 joomhome.com. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*/
	
    // no direct access
    defined('_JEXEC') or die('Restricted access');  
	
    class jhmC12textHelper
    {

        public $name = 'Text';
        public $uniqid   = 'c12text';
        public $fieldname;
        public $params;
        public function setOptions()
        {
            $html = array();

            $html[] = array(
                'title'=>'Image',
                'tip'=>'Slide image',
                'tipdesc'=>'Choose slide image',
                'class'=>''.$this->uniqid.'-slider-item-li',
                'attrs'=>'',
                'fieldname'=>'image',
                'html'=>'
                <input style="width:110px" type="text" id="'.$this->uniqid.'-slider-item-%index%" 
                name="jform[params]['.$this->fieldname.']['.$this->uniqid.'][image][]" class="'.$this->uniqid.'-slider-image" 
                value="'.$this->params['image'].'">
                <a class="model  btn" class="'.$this->uniqid.'-slide-image-select" title="Select" href="index.php?option=com_media&amp;view=images&amp;tmpl=component&amp;asset=&amp;author=&amp;fieldid='.$this->uniqid.'-slider-item-%index%&amp;folder=" rel="{handler: \\\'iframe\\\', size: {x: 800, y: 500}}">Select</a>
                <a title="Clear" class="btn" href="javascript:;" onclick="javascript:document.getElementById(\\\''.$this->uniqid.'-slider-item-%index%\\\').value=\\\'\\\';">Clear</a>'
            );
			
            $html[] = array(
                'title'=>'Title <span style="display: initial;color:red;">(* Required field)</span>',
                'tip'=>'Slide title',
                'tipdesc'=>'Set slide title text',
                'class'=>$this->uniqid.'-slider-title-li',
                'attrs'=>'',
                'fieldname'=>'title',
                'html'=>'<input ref="title" type="text"  value="'.$this->params['title'].'"   
                name="jform[params]['.$this->fieldname.']['.$this->uniqid.'][title][]">'
            );
			
            $html[] = array(
                'title'=>'Category',
                'tip'=>'Category',
                'tipdesc'=>'Category',
                'class'=>$this->uniqid.'-slider-category-li',
                'attrs'=>'',
                'fieldname'=>'category',
                'html'=>'<input type="text"  value="'.$this->params['category'].'"   
                name="jform[params]['.$this->fieldname.']['.$this->uniqid.'][category][]">'
            );

            $html[] = array(
                'title'=>'Description',
                'tip'=>'Description',
                'tipdesc'=>'Description',
                'class'=>''.$this->uniqid.'-slider-item-li',
                'attrs'=>'',
                'fieldname'=>'introtext',
                'html'=>'<textarea  name="jform[params]['.$this->fieldname.']['.$this->uniqid.'][introtext][]">'.$this->params['introtext'].'</textarea>'
			);

            $html[] = array(
                'title'=>'Text of button 1',
                'tip'=>'Text of button 1',
                'tipdesc'=>'Write text of button 1',
                'class'=>$this->uniqid.'-slider-btn1txt-li',
                'attrs'=>'',
                'fieldname'=>'btn1txt',
                'html'=>'<input type="text"  value="'.$this->params['btn1txt'].'"   
                name="jform[params]['.$this->fieldname.']['.$this->uniqid.'][btn1txt][]">'
            );

            $html[] = array(
                'title'=>'Link of button 1',
                'tip'=>'Custom link',
                'tipdesc'=>'Custom link url',
                'class'=>$this->uniqid.'-slider-link1-li',
                'attrs'=>'',
                'fieldname'=>'link1',
                'html'=>'<input type="text"  value="'.$this->params['link1'].'"   
                name="jform[params]['.$this->fieldname.']['.$this->uniqid.'][link1][]">'
            );

            $html[] = array(
                'title'=>'Text of button 2',
                'tip'=>'Text of button 2',
                'tipdesc'=>'Write text of button 2',
                'class'=>$this->uniqid.'-slider-btn2txt-li',
                'attrs'=>'',
                'fieldname'=>'btn2txt',
                'html'=>'<input type="text"  value="'.$this->params['btn2txt'].'"   
                name="jform[params]['.$this->fieldname.']['.$this->uniqid.'][btn2txt][]">'
            );

            $html[] = array(
                'title'=>'Link of button 2',
                'tip'=>'Custom link',
                'tipdesc'=>'Custom link url',
                'class'=>$this->uniqid.'-slider-link2-li',
                'attrs'=>'',
                'fieldname'=>'link2',
                'html'=>'<input type="text"  value="'.$this->params['link2'].'"   
                name="jform[params]['.$this->fieldname.']['.$this->uniqid.'][link2][]">'
            );
			
            $html[] = array(
                'title'=>'Image1 on slide',
                'tip'=>'Image1 on slide',
                'tipdesc'=>'Choose slide image',
                'class'=>''.$this->uniqid.'-slider1-item-li',
                'attrs'=>'',
                'fieldname'=>'image1',
                'html'=>'
                <input style="width:110px" type="text" id="'.$this->uniqid.'-slider1-item-%index%" 
                name="jform[params]['.$this->fieldname.']['.$this->uniqid.'][image1][]" class="'.$this->uniqid.'-slider-image1" 
                value="'.$this->params['image1'].'">
                <a class="model  btn" class="'.$this->uniqid.'-slide-image1-select" title="Select" href="index.php?option=com_media&amp;view=images&amp;tmpl=component&amp;asset=&amp;author=&amp;fieldid='.$this->uniqid.'-slider1-item-%index%&amp;folder=" rel="{handler: \\\'iframe\\\', size: {x: 800, y: 500}}">Select</a>
                <a title="Clear" class="btn" href="javascript:;" onclick="javascript:document.getElementById(\\\''.$this->uniqid.'-slider1-item-%index%\\\').value=\\\'\\\';">Clear</a>'
            );
			
            $html[] = array(
                'title'=>'Image2 on slide',
                'tip'=>'Image2 on slide',
                'tipdesc'=>'Choose slide image',
                'class'=>''.$this->uniqid.'-slider2-item-li',
                'attrs'=>'',
                'fieldname'=>'image2',
                'html'=>'
                <input style="width:110px" type="text" id="'.$this->uniqid.'-slider2-item-%index%" 
                name="jform[params]['.$this->fieldname.']['.$this->uniqid.'][image2][]" class="'.$this->uniqid.'-slider-image2" 
                value="'.$this->params['image2'].'">
                <a class="model  btn" class="'.$this->uniqid.'-slide-image2-select" title="Select" href="index.php?option=com_media&amp;view=images&amp;tmpl=component&amp;asset=&amp;author=&amp;fieldid='.$this->uniqid.'-slider2-item-%index%&amp;folder=" rel="{handler: \\\'iframe\\\', size: {x: 800, y: 500}}">Select</a>
                <a title="Clear" class="btn" href="javascript:;" onclick="javascript:document.getElementById(\\\''.$this->uniqid.'-slider2-item-%index%\\\').value=\\\'\\\';">Clear</a>'
            );
			
            $html[] = array(
                'title'=>'Image3 on slide',
                'tip'=>'Image3 on slide',
                'tipdesc'=>'Choose slide image',
                'class'=>''.$this->uniqid.'-slider3-item-li',
                'attrs'=>'',
                'fieldname'=>'image3',
                'html'=>'
                <input style="width:110px" type="text" id="'.$this->uniqid.'-slider3-item-%index%" 
                name="jform[params]['.$this->fieldname.']['.$this->uniqid.'][image3][]" class="'.$this->uniqid.'-slider-image3" 
                value="'.$this->params['image3'].'">
                <a class="model  btn" class="'.$this->uniqid.'-slide-image3-select" title="Select" href="index.php?option=com_media&amp;view=images&amp;tmpl=component&amp;asset=&amp;author=&amp;fieldid='.$this->uniqid.'-slider3-item-%index%&amp;folder=" rel="{handler: \\\'iframe\\\', size: {x: 800, y: 500}}">Select</a>
                <a title="Clear" class="btn" href="javascript:;" onclick="javascript:document.getElementById(\\\''.$this->uniqid.'-slider3-item-%index%\\\').value=\\\'\\\';">Clear</a>'
            );
			
            $html[] = array(
                'title'=>'Image4 on slide',
                'tip'=>'Image4 on slide',
                'tipdesc'=>'Choose slide image',
                'class'=>''.$this->uniqid.'-slider4-item-li',
                'attrs'=>'',
                'fieldname'=>'image4',
                'html'=>'
                <input style="width:110px" type="text" id="'.$this->uniqid.'-slider4-item-%index%" 
                name="jform[params]['.$this->fieldname.']['.$this->uniqid.'][image4][]" class="'.$this->uniqid.'-slider-image4" 
                value="'.$this->params['image4'].'">
                <a class="model  btn" class="'.$this->uniqid.'-slide-image4-select" title="Select" href="index.php?option=com_media&amp;view=images&amp;tmpl=component&amp;asset=&amp;author=&amp;fieldid='.$this->uniqid.'-slider4-item-%index%&amp;folder=" rel="{handler: \\\'iframe\\\', size: {x: 800, y: 500}}">Select</a>
                <a title="Clear" class="btn" href="javascript:;" onclick="javascript:document.getElementById(\\\''.$this->uniqid.'-slider4-item-%index%\\\').value=\\\'\\\';">Clear</a>'
            );
			
            $html[] = array(
                'title'=>'Image5 on slide',
                'tip'=>'Image5 on slide',
                'tipdesc'=>'Choose slide image',
                'class'=>''.$this->uniqid.'-slider5-item-li',
                'attrs'=>'',
                'fieldname'=>'image5',
                'html'=>'
                <input style="width:110px" type="text" id="'.$this->uniqid.'-slider5-item-%index%" 
                name="jform[params]['.$this->fieldname.']['.$this->uniqid.'][image5][]" class="'.$this->uniqid.'-slider-image5" 
                value="'.$this->params['image5'].'">
                <a class="model  btn" class="'.$this->uniqid.'-slide-image5-select" title="Select" href="index.php?option=com_media&amp;view=images&amp;tmpl=component&amp;asset=&amp;author=&amp;fieldid='.$this->uniqid.'-slider5-item-%index%&amp;folder=" rel="{handler: \\\'iframe\\\', size: {x: 800, y: 500}}">Select</a>
                <a title="Clear" class="btn" href="javascript:;" onclick="javascript:document.getElementById(\\\''.$this->uniqid.'-slider5-item-%index%\\\').value=\\\'\\\';">Clear</a>'
            );
			
            $html[] = array(
                'title'=>'State',
                'tip'=>'Set State',
                'tipdesc'=>'Published or unpublished slide item',
                'class'=>''.$this->uniqid.'-slider-item-li',
                'attrs'=>'',
                'fieldname'=>'text',
                'html'=>'
                <select class="jhm-state" name="jform[params]['.$this->fieldname.']['.$this->uniqid.'][state][]">
                <option value="published" '.(($this->params['state']=='published')?'selected':'').' >Published</option>
                <option value="unpublished"  '.(($this->params['state']=='unpublished')?'selected':'').'>UnPublished</option>
                </select>'
            );

            return $html;
        }


        public function styleSheet()
        {

            return '';

        }


        public function JavaScript()
        {

            return '';

        }


        public function display($helper)
        {
            return $this->params;
        }
}
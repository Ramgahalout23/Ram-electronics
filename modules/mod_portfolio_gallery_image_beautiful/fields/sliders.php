<?php
/**
* @title		portfolio gallery image beautiful
* @website		http://www.joomhome.com
* @copyright	Copyright (C) 2015 joomhome.com. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*/

    defined('JPATH_BASE') or die;

    jimport('joomla.form.formfield');
    jimport('joomla.filesystem.folder');
    jimport('joomla.filesystem.file');

    class JFormFieldSliders extends JFormField {

        protected $type = 'Sliders';
        private $objects=array();
        private function loadHelpers()
        {
            $helpers = array();
            $name = array();
            $helpers[] = dirname(dirname(__FILE__)) . '/helpers/';
            $helpers[] = '.class.php';
            $files = (array) glob( $helpers[0].'*'.$helpers[1] );
            foreach($files as $file) include_once $file;
            $classes = (array) str_ireplace($helpers,'',$files);
            foreach( $classes as $class )
            {
                $cls = new $class;
                $name[$cls->uniqid] = $cls;
            }
            return $name;
        }

        private function setScript($name, $option){
            if( !is_array($option) ) return '';
            $text = '';


            if(JVERSION < 3)  $option = array_reverse($option);


            foreach($option as $i=>$html)
            {
                $text .=  '<li class="jhm-sliders-options  '.$html['class'].' " '.$html['attrs'].'>
                <span  class="hasTip" title="'.( (isset($html['tip'])) ? $html['tip'].'::'.$html['tipdesc']:'' ).'" 
                data-title="'.( (isset($html['tip'])) ? $html['tip'].'::'.$html['tipdesc']:'' ).'">'.$html['title'].' : </span>
                '.str_ireplace('%index%', $i, $html['html']).'
                </li>';
            }

            $javascript = '';
            $text = str_ireplace("\n",'', $text);
            $javascript .= "
            var {$name}HTML = '{$text}';
            ";
            return $javascript;
        }

        private function formatParams()
        {
            $index = 'sliders';
            $params = (array) $this->form->getValue('params');
            $source = (isset($params[$index]['source']))?$params[$index]['source']:array();
            $data=array();
            $i=0;
            foreach($source as $type)
            {
                $data[$i]['source'] = $type;
                if( !isset($$type) ) $$type = 0;
                foreach($params[$index][$type] as $item=>$value)  $data[$i][$item] = $value[$$type];
                $$type++;
                $i++;
            }
            return $data;
        }

        protected function getInput()
        {
            $document = JFactory::getDocument();
            $helpers = $this->loadHelpers();
            ksort($helpers);
            $html = '';

            $html .= '<ul id="sliders-slide-addnew">
            <li>
            <div class="jhm-toggler-main">
            <strong style="display:inline-block;float:right;" class="jhm-header">portfolio gallery image beautiful</strong>
            <div style="inline-block;">
            <a href="javascript:;" id="jhm-add-slide" class="btn btn-primary">Add new slide</a>
            <a href="javascript:;" id="jhm-slide-add" class="btn btn-success">Save</a>
            </div>
			<div style="padding-top:10px;font-style: italic;color:green;">* Note: You must click button "<strong style="color:red;">Save</strong>" at here before!</div>
            <div style="clear:both"></div>
            </div>
            <div class="jhm-toggle-element-main">
            <ul class="jhm-element" id="jhm-slider-element"> 
            <li class="jhm-sliders-source-type-li">
            <span class="hasTip"  data-title="Slide show source type::Select a slideshow source type listed"
            title="Slide show source type::Select a slideshow source type listed"
            >Source Type:</span>
            <select class="jhm-sliders-source-type" name="jform[params]['.$this->fieldname.'][source][]">';
            $firstoption = array();
            foreach($helpers as $helper)
            {
                $html .= '<option value="'.$helper->uniqid.'">'.$helper->name.'</option>';
                $helper->fieldname   = $this->fieldname;
                $document->addStyleDeclaration($helper->styleSheet());
                $document->addScriptDeclaration($this->setScript( $helper->uniqid, $helper->setOptions() ) );
                $document->addScriptDeclaration($helper->JavaScript());
                $firstoption[] = $helper->setOptions();
            } 
            $html .='</select>
            </li>';

            foreach($firstoption[0] as $i=>$text)
            {
                $html .=  '<li class="jhm-sliders-options  '.$text['class'].' " '.$text['attrs'].'>
                <span class="hasTip" title="'.( (isset($text['tip'])) ? $text['tip'].'::'.$text['tipdesc']:'' ).'" 
                data-title="'.( (isset($text['tip'])) ? $text['tip'].'::'.$text['tipdesc']:'' ).'"
                >'.$text['title'].' : </span>
                '.  str_ireplace('%index%',''.$i,  stripslashes($text['html'])      ).'
                </li>';
            }
            $html .='</ul>';
            $html .='
            </div>
            </li>
            </ul>';

            ////  saved items 

            $html .= '<ul id="sliders-slide-list">';
            $params = (array) $this->form->getValue('params');
            $sliders = (isset($params['sliders']))?$params['sliders']:array();
            $formatdata = $this->formatParams();
            $saveddatacount = (int) count($formatdata);
            JFactory::getDocument()->addScriptDeclaration('
                var jhm_item_increment = '.$saveddatacount.';
            ');

            $incr = 1;
            foreach( (array) $formatdata as $index=>$value)
            {



                $helper = $helpers[$value['source']];
                $helper->params = $value;

                $html .= '<li><div class="jhm-flt-left"><span class="jhm-move" title="Move"></span></div><span class="jhm-title jhm-toggler">
                '.((isset($value['titletype']) and $value['titletype']=='custom')?$value['customtitle']:$value['title']  ).'&nbsp;&nbsp;
                ::&nbsp;&nbsp;<span class="jhm-title-source">'.(($value['source']==$helper->uniqid)? $helper->name:'').'</span>
                </span><div class="jhm-flt-left"><span class="jhm-edit" title="Edit"></span>    <span ref="state" class="jhm-'.(($value['state']=='published')?'published':'unpublished').'" title="published/unpublished"></span>   <span class="jhm-delete" title="Delete"></span> </div><div style="clear:both"></div> ';

                $html .='<div class="jhm-toggle-element">
                <ul class="jhm-element"> 
                <li class="jhm-sliders-source-type-li" style="display:none">
                <span  class="hasTip"  title="Slide show source type::Select a slideshow source type listed">Source Type:</span>
                <input type="hidden" value="'.$value['source'].'" class="jhm-sliders-source-type" name="jform[params]['.$this->fieldname.'][source][]">';
                $html .='</li>';



                foreach((array) $helper->setOptions() as $text)
                {

                    $html .=  '<li class="'.$text['class'].'" '.$text['attrs'].'>
                    <span  class="hasTip" title="'.( (isset($text['tip'])) ? $text['tip'].'::'.$text['tipdesc']:'' ).'">'.$text['title'].' : </span>
                    '.str_ireplace('%index%','saved-'.$saveddatacount, stripslashes($text['html'])).'
                    </li>'; 

                }

                $saveddatacount++;
                $html .='</ul></div></li>';

            }

            $html .= '</ul>';
            $incr++;
            return $html;
        }



        protected function getLabel()
        {
            return '';

        }

    }

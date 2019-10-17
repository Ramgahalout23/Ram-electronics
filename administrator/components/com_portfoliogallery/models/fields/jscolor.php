<?php
/**
 * @package Portfolio Gallery
 * @author Huge-IT
 * @copyright (C) 2014 Huge IT. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @website		http://www.huge-it.com/
 * */
defined('_JEXEC') or die;
JHtml::_('behavior.tooltip');
jimport('joomla.form.formfield');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.modal');
?>
<?php
class JFormFieldJSColor extends JFormField {

    protected $type = 'jscolor';

    public function getInput() {

        $type_ = $this->element['type_'];
        $type_edit= $this->element['type_edit'];
        $doc = JFactory::getDocument();
        $doc->addScript("http://code.jquery.com/ui/1.10.4/jquery-ui.js");
        $doc->addScript(JURI::root(true) . "/media/com_portfoliogallery/js/admin/admin.js");
        $doc->addScript(JURI::root(true) . "/media/com_portfoliogallery/js/admin/jscolor/jscolor.js");
        $doc->addScript(JURI::root(true) . "/media/com_portfoliogallery/js/admin/simple-slider.js");
        JHtml::stylesheet('media/com_portfoliogallery/style/simple-slider.css');
        JHtml::stylesheet('media/com_portfoliogallery/style/admin.style.css');
        ?>

<?php
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id, name, value');
        $query->from('#__huge_itportfolio_params');
        $query->where('name="' . $this->element['name'] . '"');
        $db->setQuery($query);
        $results = $db->loadAssocList();
        $query1 = $db->getQuery(true);
        $query1->select('*');
        $query1->from('#__huge_itportfolio_portfolios');
        $db->setQuery($query1);
        $results2 = $db->loadAssocList();
        
        $class = $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';
        if ($type_ == "text") {
            return '<input  class = "text" type="text" name="' . $this->name . '" id="' . $this->id . '" value= "'.$results[0]['value'] . '"  class="inputClass"/>';
        }
         if ($type_ == "textColor") {
            return '<input  class="color" class = "text" type="text" name="' . $this->name . '" id="' . $this->id . '" value= "'.$results[0]['value'] . '"  class="inputClass"/>';
        }elseif ($type_ == "checkbox") {
            $on = "on";
            $off = "off";
            $checked = $results[0]['value']== 'on' ? 'checked' : '';
            return '<input onclick = "this.value = (this.checked ? \''.$on.'\': \''.$off.'\')" type="checkbox" name="' . $this->name . '" id="' . $this->id . '" value= "'.($checked == 'checked' ? $on : $off  ). '" ' .  $class . $checked . '/>';
        }  elseif ($type_== "toogleList") {
            return '<select  class="textSelect" name="' . $this->name . '" id="' . $this->id . '"><option value="light" '.($results[0]['value'] =="light" ? "selected" : "" ).'>Light</option>
		<option value="dark" '.($results[0]['value'] =="dark" ? "selected" : "" ).'>Dark</option></select>';
            
        } else if ($type_ == "thumbsPositionList") {
             return '<select class="textSelect" name="' . $this->name . '" id="' . $this->id . '"><option value="before" '.($results[0]['value'] =="before" ? "selected" : "" ).'>Before Description</option>
		    <option value="after" '.($results[0]['value'] == "after" ? "selected" : "" ).'>After Description</option></select>';
        }elseif ($type_ == "light_box_opacity_text" ) {

            return '<div style="float: left;" class="slider-container">'
                    . '<input value= "' . $results[0]['value'] . '"  name="' . $this->name . '"  id="light_box_opacity" data-slider-highlight="true"  data-slider-values="0,10,20,30,40,50,60,70,80,90,100" type="text" data-slider="true" style="display: none;"/>'
                    . '<span class="box">' .$results[0]['value'] . ' %</span></div>';
        }
         elseif ($type_== "TransitionList") {
            return '<select  class="textSelect" name="' . $this->name . '" id="' . $this->id . '"><option value="elastic" '.($results[0]['value'] =="elastic" ? "selected" : "" ).'>Elastic</option>
		<option value="fade" '.($results[0]['value'] =="fade" ? "selected" : "" ).'>Fade</option>'
                    . '<option value="none" '.($results[0]['value'] =="none" ? "selected" : "" ).'>None</option></select>';
       }
      elseif ($type_ == "number") {
            return '<input  style="width:100px" type="number"  id ="' . $this->id . '" name="' . $this->name . '" value="' . $results[0]['value'] . '" "' . $class . '" /><span style = " font-size: 14px;margin-left:5px">px</span>';
        }
             if ($type_ == "option_list") {
                      $html = '<select   class="textSelect" id="' . $this->id . '" name="' . $this->name . '" >';
            foreach ($results2 as $i => $res) {
                if ($this->value == $results2[$i]['name']) {
                    $html.= '<option name="' . $this->name . '" value="' . $results2[$i]['name'] . '"  selected="selected">' .$results2[$i]['name'] . '</option>';
                } else {
                    $html.= '<option name="' . $this->name . '" value="' . $results2[$i]['name']. '" >' . $results2[$i]['name'] . '</option>';
                }
            }
            $html.= '</select>';
            return $html;
        }
        elseif ($type_ == "radio") {
            $options = array(
                JHtml::_('select.option', '1', '1'),
                JHtml::_('select.option', '2', '2'),
                JHtml::_('select.option', '3', '3'),
                JHtml::_('select.option', '4', '4'),
                JHtml::_('select.option', '5', '5'),
                JHtml::_('select.option', '6', '6'),
                JHtml::_('select.option', '7', '7'),
                JHtml::_('select.option', '8', '8'),
                JHtml::_('select.option', '9', '9')
            );
            
            $checked = 'checked';
            $html = '<div style="float: left;">
			<table>
				<tbody>
				  <tr>
					<td style="width:25px"><input type="radio" value="1" id="slideshow_title_top-left" name="' . $this->name . '" ' . ($results[0]['value'] == 1 ? $checked : '') . ' </td>
					<td style="width:25px"><input type="radio" value="2" id="slideshow_title_top-center" name="' . $this->name . '" ' . ($results[0]['value'] == 2 ? $checked : '') . ' /></td>
					<td style="width:25px"><input type="radio" value="3" id="slideshow_title_top-right" name="' . $this->name . '" ' . ($results[0]['value'] == 3 ? $checked : '') . '  /></td>
				  </tr>
				  <tr>
					<td style="width:25px"><input type="radio" value="4" id="slideshow_title_middle-left" name="' . $this->name . '" ' . ($results[0]['value'] == 4 ? $checked : '') . '/> </td>
					<td style="width:25px"><input type="radio" value="5" id="slideshow_title_middle-center" name="' . $this->name . '" ' . ($results[0]['value'] == 5 ? $checked : '') . ' /></td>
					<td style="width:25px"><input type="radio" value="6" id="slideshow_title_middle-right" name="' . $this->name . '"  ' . ($results[0]['value'] == 6 ? $checked : '') . '/></td>
				  </tr>
				  <tr>
					<td style="width:25px"><input type="radio" value="7" id="slideshow_title_bottom-left" name="' . $this->name . '" ' . ($results[0]['value'] == 7 ? $checked : '') . ' /></td>
					<td style="width:25px"><input type="radio" value="8" id="slideshow_title_bottom-center" name="' . $this->name . '" ' . ($results[0]['value'] == 8 ? $checked : '') . ' /></td>
					<td style="width:25px"><input type="radio" value="9" id="slideshow_title_bottom-right" name="' . $this->name . '" ' . ($results[0]['value'] == 9 ? $checked : '') . '/></td>
				  </tr>
				</tbody>	
			</table>
		 </div>';

            return $html;
        
    
        }
      elseif ($type_== "m") {
            return '<select  class="textSelect" name="' . $this->name . '" id="' . $this->id . '">
            <option value="left" '.($results[0]['value'] =="left" ? "selected" : "" ).'>Left</option>
        <option value="right" '.($results[0]['value'] =="right" ? "selected" : "" ).'>Right</option>
        <option value="top" '.($results[0]['value'] =="top" ? "selected" : "" ).'>Top</option></select>';
       }
             elseif ($type_== "mm") {
                 return '<select  class="textSelect" name="' . $this->name . '" id="' . $this->id . '">
            <option value="left" '.($results[0]['value'] =="left" ? "selected" : "" ).'>Left</option>
        <option value="right" '.($results[0]['value'] =="right" ? "selected" : "" ).'>Right</option>
        <option value="center" '.($results[0]['value'] =="center" ? "selected" : "" ).'>Top</option></select>';
             }
       
       
        elseif ($type_== "light_box_style") {
            return '<select  class="textSelect" name="' . $this->name . '" id="' . $this->id . '">
            <option value="1" '.($results[0]['value'] =="1" ? "selected" : "" ).'>1</option>'
                    . '<option value="2" '.($results[0]['value'] =="2" ? "selected" : "" ).'>2</option>'
                    . '<option value="3" '.($results[0]['value'] =="3" ? "selected" : "" ).'>3</option>'
                    . '<option value="4" '.($results[0]['value'] =="4" ? "selected" : "" ).'>4</option>'
                    . '<option value="5" '.($results[0]['value'] =="5" ? "selected" : "" ).'>5</option>'
                    . '</select>';
       }elseif ($type_ == "light_box_overlayclose" || $type_ == "light_box_esckey" || $type_ == "light_box_loop" || $type_ == "light_box_fixed") {
            return '<input  type="checkbox" name="' . $this->name . '" id="' . $this->id . '" value= "true" checked/>';
        }
       
}}
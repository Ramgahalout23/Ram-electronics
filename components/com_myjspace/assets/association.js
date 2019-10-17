/* 
 * association.js
 */

function jSelectMyjsp_jform_modal(id, title, lang) {
	var var_id = "jform_associations_"+lang+"_id";
	var var_name = "jform_associations_"+lang+"_name";
	document.id(var_id).value = id;
	document.id(var_name).value = title;

	if (typeof(SqueezeBox.close) === 'function') {
		SqueezeBox.close();
	}
}

function jInsertFieldValue(value, id) {
	var $ = jQuery.noConflict();
	var old_value = $("#" + id).val();
	if (old_value != value) {
		var $elem = $("#" + id);
		$elem.val(value);
		$elem.trigger("change");
		if (typeof($elem.get(0).onchange) === "function") {
			$elem.get(0).onchange();
		}
//		jMediaRefreshPreview(id);
	}
}

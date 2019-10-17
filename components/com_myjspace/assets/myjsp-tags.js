/*
 * myjsp-tags.js
 */

(function() {
	"use strict";

	window.insertTags = function(tag, editor) {

		tag = ' '+tag;

		if (window.parent.Joomla.editors.instances[editor]) {
			window.parent.Joomla.editors.instances[editor].replaceSelection(tag);

			if (window.parent.Joomla.Modal) { // J!4
				window.parent.Joomla.Modal.getCurrent().close();
			} else { // J!3
				window.parent.jModalClose();
			}
		} else {
			window.parent.jInsertEditorText(tag, editor);
			window.parent.jModalClose();
		}

		return false;
	};
})();

function myjsp_set_url(url, txt) {
	var tag = '';

	if (txt != '' && txt != '')
		tag = '<a href=\"' + url + '\">' + txt +'</a>';

	return tag;
}

function myjsp_set_tag_img(url, txt) {
	var tag = '';

	if (txt != '')
		tag = '<img src=\"' + url + '\" alt="'+txt+'">';

	return tag;
}

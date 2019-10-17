/*
 * myjsp-pagebreak.js
 */

(function() {
	"use strict";

	window.insertPagebreak = function(editor) {
		/** Get the pagebreak title **/
		var alt, tag, title = document.getElementById('title').value;

//		if (!window.parent.Joomla.getOptions('xtd-pagebreak')) {
//			// Something went wrong!
//			window.parent.jModalClose();
//			return false;
//		}

		/** Get the pagebreak toc alias -- not inserting for now **/
		/** don't know which attribute to use... **/
		alt = document.getElementById('alt').value;

		title = (title != '') ? 'title="' + title + '"' : '';
		alt = (alt != '') ? 'alt="' + alt + '"' : '';

		tag = '<hr class="system-pagebreak" ' + title + ' ' + alt + '/>';

		if (window.parent.Joomla.editors.instances[editor]) {
			window.parent.Joomla.editors.instances[editor].replaceSelection(tag);

			if (window.parent.Joomla.Modal) { // J!4
				window.parent.Joomla.Modal.getCurrent().close();
			} else { // J!3
				window.parent.jModalClose();
			}
		} else if (window.parent.jInsertEditorText) {
			window.parent.jInsertEditorText(tag, editor);
			window.parent.jModalClose();
		}

		return false;
	};
})();

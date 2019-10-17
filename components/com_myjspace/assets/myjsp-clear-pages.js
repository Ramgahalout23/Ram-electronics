/*
 * myjsp-clear-pages.js
 */

function MyjspClearPages() {

	var search = document.getElementById('search');
	if (search != null) {
		search.value = '';
	}

	var filter_category = document.getElementById('filter_category');
	if (filter_category != null) {
		filter_category.value = '-1';
	}

	var filter_type = document.getElementById('filter_type');
	if (filter_type != null) {
		filter_type.value = '0';
	}

	var filter_logged = document.getElementById('filter_logged');
	if (filter_logged != null) {
		filter_logged.value = '-1';
	}
}

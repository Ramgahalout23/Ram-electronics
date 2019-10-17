/*
 * myjsp-search.js
 */

function mjsp_trie(value) {
	var bouts = value.split(' ');
	if (bouts[0] == 'x') {
		bouts[0] = '';
		bouts[1] = '';
	}
	Joomla.tableOrdering(bouts[0], bouts[1], '');
}

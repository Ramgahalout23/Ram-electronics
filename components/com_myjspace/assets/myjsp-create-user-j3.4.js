/*
 * drag_create-user-j3.4.js
 */

function jSelectUser_jform_created_by_name(id, title) {
	var old_id = document.getElementById('mjs_userid').value;
	if (old_id != id) {
		document.getElementById('mjs_username2').value = title;
		document.getElementById('mjs_userid').value = id;
	}
	SqueezeBox.close();
}


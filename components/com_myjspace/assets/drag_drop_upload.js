/*
 * drag_drop_upload.js
 */

// Beta version
// Problème connus : n'affiche pas les messages d'alerte Joomla! au rechargement la page apres upload

// var urlupload = server side upload features (variable to set before calling the functions)

function readfiles(files, url) {
	var	fileupload = document.getElementById('fileupload'); // Only when no progress bar
	var formData = tests.formdata ? new FormData() : null;
	var nomfile = '';

	for (var i = 0; i < files.length; i++) {
		if (tests.formdata) {
			formData.append('upload_file', files[i]);
			nomfile = files[i].name;
		}
	}

	// Post a new XHR request
	if (tests.formdata) {
		var xhr = new XMLHttpRequest();

// Pour barre de progress
/*
		xhr.upload.addEventListener('progress', progressHandler, false);
		xhr.addEventListener('load', completeHandler, false);
		xhr.addEventListener('error', errorHandler, false);
		xhr.addEventListener('abort', abortHandler, false);
*/
		xhr.open('POST', url);
		xhr.onload = function() {
			fileupload.className = 'dragdrop'; // Only when no progress bar
			document.location.reload(true);
		};

		fileupload.className = 'encours'; // Only when no progress bar
		xhr.send(formData);
	}
}

function init_graddrop() {
	var holder = document.getElementById('holder');
	var	fileupload = document.getElementById('fileupload'); // Only when no progress bar

	if (!holder)
		return;

	tests = {
		filereader: typeof FileReader != 'undefined',
		dnd: 'draggable' in document.createElement('span'),
		formdata: !!window.FormData,
		progress: "upload" in new XMLHttpRequest
	};

	if (tests.dnd && typeof(urlupload) != 'undefined') {
		holder.ondragover = function () {this.className = 'hover'; return false;};
		holder.ondragend = function () {this.className = ''; return false;};
		holder.ondrop = function (e) {
			this.className = '';
			e.preventDefault();
			readfiles(e.dataTransfer.files, urlupload); // Only when no progress bar
		}
	}

	if (tests.filereader !== true || tests.dnd !== true || tests.formdata !== true) { // Only when no progress bar
		fileupload.className = 'hidden';
	}
}

if (window.addEventListener) { // W3C standard
	window.addEventListener('load', init_graddrop, false);
} else if (window.attachEvent) { // Microsoft
	window.attachEvent('onload', init_graddrop);
}

// ----------------------------------------------------------------------------

// Download progress bar

// EVOL a finaliser ou supprimer

function uploadFile() {
	var url = window.location.pathname;
	var url_to_call = url.substring(url.lastIndexOf('/')+1);
//	var url_to_call = urlupload; ?

	document.getElementById('progress-bar').value = 0;

    var formdata = new FormData(document.getElementById('upload_file_form'));
    var ajax = new XMLHttpRequest();
    ajax.upload.addEventListener('progress', progressHandler, false);
    ajax.addEventListener('load', completeHandler, false);
    ajax.addEventListener('error', errorHandler, false);
    ajax.addEventListener('abort', abortHandler, false);
    ajax.open('POST', url_to_call);
    ajax.send(formdata);

	ajax.onload = function() {
		document.location.reload(true);
	};
}

function progressHandler(event) {
	var percent = Math.round((event.loaded / event.total) * 100);
	document.getElementById('progress-bar').value = percent;
	document.getElementById('progress-status').innerHTML = percent + "%";
}

function completeHandler(event) {
	document.getElementById('progress-status').innerHTML = '100%';
	document.getElementById('progress-bar').value = 100;
}

function errorHandler(event) {
	document.getElementById('progress-status').innerHTML = 'Upload Failed';
}

function abortHandler(event) {
	document.getElementById('progress-status').innerHTML = 'Upload Aborted';
}

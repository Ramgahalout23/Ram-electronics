!function(r){var d,p;r(document).on("click",".sp-pagebuilder-btn-media-manager",function(e){e.preventDefault(),$this=r(this),r.ajax({type:"POST",url:"index.php?option=com_sppagebuilder&view=media&layout=modal&format=json",data:{support:$this.attr("data-support"),type:$this.attr("data-support"),target:$this.prev().attr("id")},beforeSend:function(){$this.find(".fa").show()},success:function(e){$this.find(".fa").hide(),r(e).show().appendTo(r("body").addClass("sp-pagebuilder-media-modal-open")),r(".sp-pagebuilder-media-toolbar select").chosen()}})}),r(document).on("click",".sp-pagebuilder-btn-close-modal",function(e){e.preventDefault(),r(".sp-pagebuilder-media-modal-overlay").remove(),r("body").removeClass("sp-pagebuilder-media-modal-open")}),r.fn.browseMedia=function(i){i=r.extend({type:"*",search:"",date:"",start:0,filter:!0,categories:!1,support:"image"},i);r.ajax({type:"POST",url:"index.php?option=com_sppagebuilder&view=media&layout=browse&format=json",data:{type:i.type,date:i.date,start:i.start,search:i.search,categories:i.categories,support:i.support},beforeSend:function(){r("#sp-pagebuilder-media-loadmore").hide(),r(".sp-pagebuilder-media").remove(),r("#sp-pagebuilder-cancel-media").parent().hide(),r(".sp-pagebuilder-media-wrapper").addClass("sp-pagebuilder-media-pre-loading").prepend(r('<div class="sp-pagebuilder-loading"><i class="pbfont pbfont-pagebuilder"></i></div>'))},success:function(e){r(".sp-pagebuilder-media-wrapper").removeClass("sp-pagebuilder-media-pre-loading"),r(".sp-pagebuilder-loading").remove();var a=r.parseJSON(e);r("#sp-pagebuilder-media-types").find(".active").find(".fa").removeClass("fa-spin fa-spinner"),i.filter&&r("#sp-pagebuilder-media-filter").html(a.date_filter).trigger("liszt:updated").removeAttr().attr("data-type","browse"),i.categories&&(r("#sp-pagebuilder-media-types").html(a.media_categories),i.support&&(r("#sp-pagebuilder-media-types").find(">li").removeClass("active"),r("#sp-pagebuilder-media-types").find(".sp-pagebuilder-media-type-"+i.support).addClass("active"))),a.count?r("#sp-pagebuilder-media-manager, #sp-pagebuilder-media-modal").removeClass("sp-pagebuilder-media-manager-empty"):r("#sp-pagebuilder-media-manager, #sp-pagebuilder-media-modal").addClass("sp-pagebuilder-media-manager-empty"),r(".sp-pagebuilder-media-wrapper").prepend(a.output),a.loadmore?r("#sp-pagebuilder-media-loadmore").removeAttr("style"):r("#sp-pagebuilder-media-loadmore").hide()}})},r.fn.browseFolders=function(i){i=r.extend({path:"/images",filter:!0},i);return this.each(function(){r.ajax({url:"index.php?option=com_sppagebuilder&view=media&layout=folders&format=json",type:"POST",data:{path:i.path},beforeSend:function(){i.filter&&r("#sp-pagebuilder-media-filter").removeAttr().attr("data-type","folders").val(i.path).parent().show(),r("#sp-pagebuilder-cancel-media, #sp-pagebuilder-delete-media").parent().hide(),r("#sp-pagebuilder-media-loadmore").hide(),r(".sp-pagebuilder-media").remove(),r(".sp-pagebuilder-media-wrapper").addClass("sp-pagebuilder-media-pre-loading").prepend(r('<div class="sp-pagebuilder-loading"><i class="pbfont pbfont-pagebuilder"></i></div>'))},success:function(e){r(".sp-pagebuilder-media-wrapper").removeClass("sp-pagebuilder-media-pre-loading"),r(".sp-pagebuilder-loading").remove();var a=r.parseJSON(e);a.count?r("#sp-pagebuilder-media-manager, #sp-pagebuilder-media-modal").removeClass("sp-pagebuilder-media-manager-empty"):r("#sp-pagebuilder-media-manager, #sp-pagebuilder-media-modal").addClass("sp-pagebuilder-media-manager-empty"),r("#sp-pagebuilder-media-types").find(".active").find(".fa").removeClass("fa-spin fa-spinner"),i.filter&&r("#sp-pagebuilder-media-filter").html(a.folders_tree).trigger("liszt:updated").removeAttr().attr("data-type","folders"),r(".sp-pagebuilder-media-wrapper").prepend(a.output)}})})},r(document).on("click",".sp-pagebuilder-media-to-folder-back",function(e){e.preventDefault(),r(".sp-pagebuilder-media-btn-tools").hide(),r(this).browseFolders({path:r(this).data("path")})}),r(document).on("click",".sp-pagebuilder-media-to-folder",function(e){e.preventDefault(),r(".sp-pagebuilder-media").find(">li.sp-pagebuilder-media-item").removeClass("selected"),r(".sp-pagebuilder-media").find(">li.sp-pagebuilder-media-folder").removeClass("folder-selected"),r(this).closest("li.sp-pagebuilder-media-folder").addClass("folder-selected")}),r(document).on("dblclick",".sp-pagebuilder-media-to-folder",function(e){e.preventDefault(),r(".sp-pagebuilder-media-btn-tools").hide(),r(this).browseFolders({path:r(this).attr("data-path")})}),r.fn.uploadMedia=function(d){d=r.extend({index:"",data:""},d);r.ajax({type:"POST",url:"index.php?option=com_sppagebuilder&task=media.upload_media",data:d.data,contentType:!1,cache:!1,processData:!1,beforeSend:function(){var e=r(".sp-pagebuilder-media").find(".sp-pagebuilder-media-folder:not(.sp-pagebuilder-media-to-folder-back)"),a=r(".sp-pagebuilder-media").find(".sp-pagebuilder-media-to-folder-back"),i=r('<li id="'+d.index+'" class="sp-pagebuilder-media-file-loader"><div><div><div><div><div class="sp-pagebuilder-media-upload-progress"><div><div class="sp-pagebuilder-progress"><div class="sp-pagebuilder-progress-bar" style="width: 0%;"></div></div></div></div></div></div></div><span class="sp-pagebuilder-media-title"><i class="fa fa-circle-o-notch fa-spin"></i> '+Joomla.JText._("COM_SPPAGEBUILDER_MEDIA_MANAGER_MEDIA_UPLOADING")+"...</span></div></li>");e.length?e.last().after(i):a.length?a.first().after(i):r(".sp-pagebuilder-media").prepend(i),r("#sp-pagebuilder-media-manager, #sp-pagebuilder-media-modal").removeClass("sp-pagebuilder-media-manager-empty")},success:function(e){var a=r.parseJSON(e);a.status?r(".sp-pagebuilder-media").find("#"+d.index).removeAttr("id").removeClass('sp-pagebuilder-media-file-loader"').addClass("sp-pagebuilder-media-item").attr("data-id",a.id).attr("data-src",a.src).attr("data-path",a.path).empty().html(a.output):(r(".sp-pagebuilder-media").find("#"+d.index).remove(),alert(a.output)),r(".sp-pagebuilder-media").find(">li").length?r("#sp-pagebuilder-media-manager, #sp-pagebuilder-media-modal").removeClass("sp-pagebuilder-media-manager-empty"):r("#sp-pagebuilder-media-manager, #sp-pagebuilder-media-modal").addClass("sp-pagebuilder-media-manager-empty")},xhr:function(){return myXhr=r.ajaxSettings.xhr(),myXhr.upload&&myXhr.upload.addEventListener("progress",function(e){r(".sp-pagebuilder-media").find("#"+d.index).find(".sp-pagebuilder-progress-bar").css("width",Math.floor(e.loaded/e.total*100)+"%").text(Math.floor(e.loaded/e.total*100)+"%")},!1),myXhr}})},r(document).on("keyup","#sp-pagebuilder-media-search-input",function(e){if(e.preventDefault(),""!=r(this).val()?r(".sp-pagebuilder-clear-search").show():r(".sp-pagebuilder-clear-search").hide(),r(this).val()!=d){var a=r(this).val().trim();p&&clearTimeout(p),p=setTimeout(function(){a?r(this).browseMedia({search:a,filter:!0,date:r("#sp-pagebuilder-media-filter").val(),type:r("#sp-pagebuilder-media-types").find(".active > a").attr("data-type"),support:"all"}):r(this).browseMedia({filter:!0,date:r("#sp-pagebuilder-media-filter").val(),type:r("#sp-pagebuilder-media-types").find(".active > a").attr("data-type"),support:"all"})},300),d=r(this).val()}}),r(document).on("click",".sp-pagebuilder-clear-search",function(e){e.preventDefault(),r("#sp-pagebuilder-media-search-input").val("").focus().keyup()}),r(document).on("click","#sp-pagebuilder-media-search-input",function(e){e.preventDefault()}),r(document).on("click",".sp-pagebuilder-browse-media",function(e){e.preventDefault();var a=r(this);if(r(this).closest("#sp-pagebuilder-media-types").children().removeClass("active"),r(this).parent().addClass("active"),r(this).find(".fa").addClass("fa-spinner fa-spin"),r("#sp-pagebuilder-upload-media").parent().show(),"folders"==a.attr("data-type"))r(".sp-pagebuilder-media-search").parent().hide(),r("#sp-pagebuilder-media-create-folder").parent().show(),r(this).browseFolders();else{r(".sp-pagebuilder-media-search").parent().show(),r("#sp-pagebuilder-media-create-folder").parent().hide();var i="all";r("#sp-pagebuilder-media-modal").length&&(i=r("#sp-pagebuilder-media-modal").data("support")),r(this).browseMedia({type:a.data("type"),support:i,element:a})}}),r(document).on("click","#sp-pagebuilder-media-loadmore",function(e){e.preventDefault();var i=r(this),a=r("#sp-pagebuilder-media-search-input").val().trim(),d="all";r("#sp-pagebuilder-media-modal").length&&(d=r("#sp-pagebuilder-media-modal").data("support")),r.ajax({type:"POST",url:"index.php?option=com_sppagebuilder&view=media&layout=browse&format=json",data:{search:a,type:r("#sp-pagebuilder-media-types").find(".active > a").attr("data-type"),support:d,date:r("#sp-pagebuilder-media-filter").val(),start:r(".sp-pagebuilder-media").find(">li").length},beforeSend:function(){i.find(".fa").removeClass("fa-refresh").addClass("fa-spinner fa-spin")},success:function(a){try{var e=r.parseJSON(a);i.find(".fa").removeClass("fa-spinner fa-spin").addClass("fa-refresh"),r(".sp-pagebuilder-media").append(e.output),e.loadmore?r("#sp-pagebuilder-media-loadmore").parent().removeAttr("style"):r("#sp-pagebuilder-media-loadmore").parent().hide()}catch(e){r(".sp-pagebuilder-media-body-inner").html(a)}}})}),r(document).on("change","#sp-pagebuilder-media-filter",function(e){e.preventDefault(),"folders"==r(this).attr("data-type")?r(this).browseFolders({path:r(this).val()}):r(this).browseMedia({filter:!1,date:r(this).val(),type:r("#sp-pagebuilder-media-types").find(".active > a").attr("data-type"),support:"all"})}),r(document).on("click",".sp-pagebuilder-media > li.sp-pagebuilder-media-item",function(e){e.preventDefault();var a=r(this);r(".sp-pagebuilder-media").find(">li.sp-pagebuilder-media-folder").removeClass("folder-selected"),a.hasClass("sp-pagebuilder-media-unsupported")||(null!=r("#sp-pagebuilder-media-modal")&&r("#sp-pagebuilder-media-modal .sp-pagebuilder-media > li.sp-pagebuilder-media-item").not(this).each(function(){r(this).removeClass("selected")}),r(this).hasClass("selected")?r(this).removeClass("selected"):r(this).addClass("selected"),r(".sp-pagebuilder-media > li.sp-pagebuilder-media-item.selected").length?(r("#sp-pagebuilder-upload-media, .sp-pagebuilder-media-search, #sp-pagebuilder-media-filter").parent().hide(),r("#sp-pagebuilder-cancel-media, #sp-pagebuilder-delete-media").parent().show()):(r("#sp-pagebuilder-cancel-media, #sp-pagebuilder-delete-media").parent().hide(),r("#sp-pagebuilder-upload-media, .sp-pagebuilder-media-search, #sp-pagebuilder-media-filter").parent().show()))}),r(document).on("click","#sp-pagebuilder-insert-media",function(e){e.preventDefault();var a=r("#sp-pagebuilder-media-modal").attr("data-support"),i=r("#"+r("#sp-pagebuilder-media-modal").attr("data-target"));"image"==a&&i.prev(".sp-pagebuilder-media-preview").removeClass("sp-pagebuilder-media-no-image").attr("src",r(".sp-pagebuilder-media > li.sp-pagebuilder-media-item.selected").data("src")),i.val(r(".sp-pagebuilder-media > li.sp-pagebuilder-media-item.selected").data("path")),i.trigger("change"),r(".sp-pagebuilder-media-modal-overlay").remove(),r("body").removeClass("sp-pagebuilder-media-modal-open")}),r(document).on("click",".sp-pagebuilder-btn-clear-media",function(e){e.preventDefault();var a=r(this);a.siblings(".sp-pagebuilder-media-preview").addClass("sp-pagebuilder-media-no-image").removeAttr("src"),a.siblings("input").val(""),a.siblings("input").trigger("change")}),r(document).on("click","#sp-pagebuilder-cancel-media",function(e){e.preventDefault(),r(".sp-pagebuilder-media > li.sp-pagebuilder-media-item.selected").removeClass("selected"),r("#sp-pagebuilder-cancel-media, #sp-pagebuilder-delete-media").parent().hide(),r("#sp-pagebuilder-upload-media, .sp-pagebuilder-media-search, #sp-pagebuilder-media-filter").parent().show(),"browse"==r("#sp-pagebuilder-media-filter").attr("data-type")?r(".sp-pagebuilder-media-search").parent().show():r(".sp-pagebuilder-media-search").parent().hide()}),r(document).on("click","#sp-pagebuilder-upload-media, #sp-pagebuilder-upload-media-empty",function(e){e.preventDefault(),r("#sp-pagebuilder-media-input-file").click()}),r(document).on("change","#sp-pagebuilder-media-input-file",function(e){e.preventDefault();var a=r(this),d=r(this).prop("files"),p=new FormData;for(i=0;i<d.length;i++)p.append("file",d[i]),"folders"==r("#sp-pagebuilder-media-filter").attr("data-type")&&null!=r("#sp-pagebuilder-media-filter").val()&&p.append("folder",r("#sp-pagebuilder-media-filter").val()),r(this).uploadMedia({data:p,index:"media-id-"+Math.floor(1e6*Math.random()+1)});a.val("")}),r(document).on("dragenter","#sp-pagebuilder-media-manager",function(e){e.preventDefault(),e.stopPropagation(),r(this).addClass("sp-pagebuilder-media-drop")}),r(document).on("mouseleave","#sp-pagebuilder-media-manager",function(e){e.preventDefault(),e.stopPropagation(),r(this).removeClass("sp-pagebuilder-media-drop")}),r(document).on("dragover","#sp-pagebuilder-media-manager",function(e){e.preventDefault()}),r(document).on("drop","#sp-pagebuilder-media-manager",function(e){e.preventDefault(),e.stopPropagation(),r(this).removeClass("sp-pagebuilder-media-drop");var a=e.originalEvent.dataTransfer.files;for(i=0;i<a.length;i++){var d=new FormData;d.append("file",a[i]),"folders"==r("#sp-pagebuilder-media-filter").attr("data-type")&&null!=r("#sp-pagebuilder-media-filter").val()&&d.append("folder",r("#sp-pagebuilder-media-filter").val()),r(this).uploadMedia({data:d,index:"media-id-"+Math.floor(1e6*Math.random()+1)})}}),r(document).on("click","#sp-pagebuilder-delete-media",function(e){e.preventDefault();r(this);var d=r(".sp-pagebuilder-media").find("li.sp-pagebuilder-media-item.selected");1==confirm(Joomla.JText._("COM_SPPAGEBUILDER_MEDIA_MANAGER_CONFIRM_DELETE"))&&d.each(function(e,a){var i={};i=void 0!==r(a).data("id")?{m_type:"id",id:r(a).data("id")}:{m_type:"path",path:r(a).data("path")},r.ajax({type:"POST",url:"index.php?option=com_sppagebuilder&task=media.delete_media",data:i,success:function(e){var a=r.parseJSON(e);a.status?(d.remove(),r("#sp-pagebuilder-cancel-media, #sp-pagebuilder-delete-media").parent().hide(),r("#sp-pagebuilder-upload-media, .sp-pagebuilder-media-search, #sp-pagebuilder-media-filter").parent().show()):alert(a.output)}})})}),r(document).on("click","#sp-pagebuilder-media-create-folder",function(e){e.preventDefault();r(this);var a="/images";null!=r("#sp-pagebuilder-media-filter").val()&&"folders"==r("#sp-pagebuilder-media-filter").attr("data-type")&&(a=r("#sp-pagebuilder-media-filter").val());var i=prompt(Joomla.JText._("COM_SPPAGEBUILDER_MEDIA_MANAGER_ENTER_DIRECTORY_NAME"));null!=i&&r.ajax({type:"POST",url:"index.php?option=com_sppagebuilder&task=media.create_folder",data:{folder:a+"/"+i},success:function(a){try{var e=r.parseJSON(a);if(e.status){var i=r(".sp-pagebuilder-media").find(".sp-pagebuilder-media-folder:not(.sp-pagebuilder-media-to-folder-back)"),d='<li class="sp-pagebuilder-media-folder sp-pagebuilder-media-to-folder" data-path="'+e.output.relname+'"><div><div><div><div><div><div><i class="fa fa-folder"></i></div></div></div></div></div><span class="sp-pagebuilder-media-title">'+e.output.name+"</span></div></li>";i.length?i.first().before(d):r(".sp-pagebuilder-media").append(d)}else alert(e.output)}catch(e){r(".sp-pagebuilder-media-body-inner").html(a)}}})})}(jQuery);
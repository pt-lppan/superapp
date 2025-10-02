function textareaOneLiner(field) {
	$(field).keypress(function (evt) {
		var charCode = evt.charCode || evt.keyCode;
		if (charCode  == 13) { return false; } //Enter key's keycode
	});
}

function setNotifAndCloseDialog(teks) {
	$.gritter.removeAll({
		after_close: function(){
			if(teks=="") teks = "no message";
			$.gritter.add({ title: "Informasi", text: teks, sticky: false, time: 2000, after_open: function(){ $(".ui-dialog-content").dialog("close"); } });
		}
	});
}
function setNotifLoading(imageBaseUrl,teks) {
	if(teks=="") teks = "sedang memproses data";
	$.gritter.add({ title: "Loading", text: teks, image: imageBaseUrl+"/assets/img/loading.gif", sticky: true });
};
function removeNotif() { $.gritter.removeAll(); }

function prosesViaAjax(imageBaseUrl,teksLoading,post_url,idEleForm,idEleTarget) {
	setNotifLoading(imageBaseUrl,teksLoading);
	// cara kerja cekSession beda dg initializr jadi ga dipake disini
	// proses data
	$.ajax({
		type: "post",
		url: post_url,
		data : $("#"+idEleForm).serialize(), // serialize() = wont pass the contents of the file field
		dataType: "json",
		success: function(data) {
			if(data.sukses=="1") {
				setNotifAndCloseDialog(data.pesan);
				window.location = window.location.href;
			} else {
				removeNotif();
				alert(data.pesan);
			}
		},
		error: function (error) {
			removeNotif();
			alert("Tidak dapat memproses data, kemungkinan session Anda telah habis, silahkan login ulang.");
		}
	});	
}

function uploadFileViaAjax(imageBaseUrl,teksLoading,post_url,idEleForm,idEleTarget) {
	setNotifLoading(imageBaseUrl,teksLoading);
	// proses data
	$.ajax({
		type: "post",
		url: post_url,
		data: new FormData($("#"+idEleForm)[0]),
		dataType: "json",
		processData: false,
        contentType: false,
		cache: false,
		success: function(data) {
			if(data.sukses=="1") {
				setNotifAndCloseDialog(data.pesan);
				window.location = window.location.href;
			} else {
				removeNotif();
				alert(data.pesan);
			}
		},
		error: function (error) {
			removeNotif();
			alert("Tidak dapat memproses data, kemungkinan session Anda telah habis, silahkan login ulang.");
		}
	});	
}

function showAjaxDialog(imageBaseUrl,get_url,getData,judul,cekSession,isModal){
	var tag = $("<div><img src='"+imageBaseUrl+"/assets/img/loading.gif'/></div>").dialog({modal:isModal,height:"auto",width:200,title:"Loading..."}).dialog('open');
	// cara kerja cekSession beda dg initializr jadi ga dipake disini
	$.ajax({
		type: 'get',
		url: get_url+"?"+getData,
		dataType: 'html',
		success: function(data) {
			tag.html(data).dialog({title:judul,width:'98%',height:600});
		}
	});
}

function generateFile(ele,text) {
	var textFile = null;
	if (typeof Blob == 'function') { // function blob ada?
		// do nothing
	}else{
		alert('Fitur ini tidak didukung oleh browser Anda. Browser Anda sudah usang (perlu diupdate) atau gunakan browser lainnya.');
		return false;
	}
	var data = new Blob([text], {type: 'application/vnd.ms-excel'});
	// If we are replacing a previously generated file we need to
	// manually revoke the object URL to avoid memory leaks.
	if (textFile !== null) {
	  window.URL.revokeObjectURL(textFile);
	}
	textFile = window.URL.createObjectURL(data);
	var link = $(ele).get(0); // equivalent of document.getElementById
	link.href = textFile;
	return true;
}

function nestableTree(item,enable_update,url_update,url_readonly) {
	var html = "<li class='dd-item dd3-item' data-id='" + item.id + "'>";
	var menu_kanan = "";
	
	if(enable_update==true) {
		menu_kanan += "<span class='float-right'>";
		if(url_update.length!=0) menu_kanan += "<a href='"+url_update+item.id+"'><i class='os-icon os-icon-edit-1'></i></a>&nbsp;&nbsp;";
		if(url_readonly.length!=0) menu_kanan += "<a href='"+url_readonly+item.id+"'><i class='os-icon os-icon-alert-octagon'></i></a>&nbsp;&nbsp;";
		menu_kanan += "</span>";
	}
	
	html += "<div class='dd-handle dd3-handle'>Drag</div>";
	html += "<div class='dd3-content'>" + menu_kanan + item.label +"</div>";
	if (item.children) {
		html += "<ol class='dd-list'>";
		$.each(item.children, function (index, sub) {
			html += nestableTree(sub,enable_update,url_update,url_readonly);
		});
		html += "</ol>";
	}
	html += "</li>";

	return html;
}

// prototype untuk format jam
String.prototype.padLeft = function (length, character) { 
    return new Array(length - this.length + 1).join(character || ' ') + this; 
};
Date.prototype.toFormattedString = function () {
	var month = new Array();
	month[0] = "Jan";
	month[1] = "Feb";
	month[2] = "Mar";
	month[3] = "Apr";
	month[4] = "Mei";
	month[5] = "Juni";
	month[6] = "Juli";
	month[7] = "Agu";
	month[8] = "Sep";
	month[9] = "Okt";
	month[10] = "Nov";
	month[11] = "Des";
	
	return [String(this.getDate()).padLeft(2, '0'),
			String(month[this.getMonth()]),
            String(this.getUTCFullYear())].join("-") + " " +
           [String(this.getHours()).padLeft(2, '0'),
            String(this.getMinutes()).padLeft(2, '0'),
			String(this.getSeconds()).padLeft(2, '0')].join(":");
};
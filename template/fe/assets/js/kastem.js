function textareaOneLiner(field) {
	$(field).keypress(function (evt) {
		var charCode = evt.charCode || evt.keyCode;
		if (charCode  == 13) { return false; } //Enter key's keycode
	});
}

function showAjaxDialogFE(imageBaseUrl,get_url,getData,judul,isShowCloseButton){
	// var tag = $("").dialog({modal:isModal,height:"auto",width:200,title:"Loading..."}).dialog('open');
	$("#ajax_title").html(judul);
	$("#ajax_content").html("<div class='text-center'><img src='"+imageBaseUrl+"/assets/img/loading.gif'/></div>");
	$("#ajax_close").show();
	
	if(isShowCloseButton==false) {
		$("#ajax_close").hide();
	}
	
	$.ajax({
		type: 'get',
		url: get_url+"?"+getData,
		dataType: 'html',
		success: function(data) {
			$("#ajax_content").html(data);
			$('#ajax_ui').modal('show');
		},
		error: function (error) {
			alert("Tidak dapat memproses data, kemungkinan session Anda telah habis, silahkan login ulang.");
		}
	});
}
function upload_image(){
	var bar = $("#bar");
	var percent = $("#percent");
	$("#myForm").ajaxForm({
	 	beforeSubmit: function(){
					document.getElementById("progress_div").style.display ="block";
					var percentVal = "0%";
					bar.width(percentVal);
					percent.html(percentVal);
		},
		uploadProgress : function(event, position, total, percentComplete){
			var percentVal = percentComplete + "%";
			bar.width(percentVal);
			percent.html(percentVal);
		},
	    success: function(){
			var percentVal = "100%";
			bar.width(percentVal);
			percent.html(percentVal);
		},
		complete: function(xhr){
			if(xhr.responseText){
				console.log("Success");
				window.location.href='http://urconnex.com/videos/index?message=success';
			}
		}
	});
}

function upload_image2(){
	var bar = $("#bar2");
	var percent = $("#percent2");
	$("#myForm2").ajaxForm({
	 	beforeSubmit: function(){
					document.getElementById("progress_div2").style.display ="block";
					var percentVal = "0%";
					bar.width(percentVal);
					percent.html(percentVal);
		},
		uploadProgress : function(event, position, total, percentComplete){
			var percentVal = percentComplete + "%";
			bar.width(percentVal);
			percent.html(percentVal);
		},
	    success: function(){
			var percentVal = "100%";
			bar.width(percentVal);
			percent.html(percentVal);
		},
		complete: function(xhr){
			if(xhr.responseText){
				console.log("Success");
				window.location.href='http://urconnex.com/videos/index?message=success';
			}
		}
	});
}

function upload_picture(){
	var bar = $("#bar");
	var percent = $("#percent");
	$("#myForm").ajaxForm({
	 	beforeSubmit: function(){
					document.getElementById("progress_div").style.display ="block";
					var percentVal = "0%";
					bar.width(percentVal);
					percent.html(percentVal);
		},
		uploadProgress : function(event, position, total, percentComplete){
			var percentVal = percentComplete + "%";
			bar.width(percentVal);
			percent.html(percentVal);
		},
	    success: function(){
			var percentVal = "100%";
			bar.width(percentVal);
			percent.html(percentVal);
		},
		complete: function(xhr){
			if(xhr.responseText){
				console.log("Success");
				window.location.href='http://urconnex.com/pictures/index?message=success';
			}
		}
	});
}

function upload_trash(){
	var bar = $("#bar");
	var percent = $("#percent");
	$("#myForm").ajaxForm({
	 	beforeSubmit: function(){
					document.getElementById("progress_div").style.display ="block";
					var percentVal = "0%";
					bar.width(percentVal);
					percent.html(percentVal);
		},
		uploadProgress : function(event, position, total, percentComplete){
			var percentVal = percentComplete + "%";
			bar.width(percentVal);
			percent.html(percentVal);
		},
	    success: function(){
			var percentVal = "100%";
			bar.width(percentVal);
			percent.html(percentVal);
		},
		complete: function(xhr){
			if(xhr.responseText){
				console.log("Success");
				window.location.href='http://urconnex.com/trash/index?message=success';
			}
		}
	});
}

function upload_song(){
	var bar = $("#bar");
	var percent = $("#percent");
	$("#myForm").ajaxForm({
	 	beforeSubmit: function(){
					document.getElementById("progress_div").style.display ="block";
					var percentVal = "0%";
					bar.width(percentVal);
					percent.html(percentVal);
		},
		uploadProgress : function(event, position, total, percentComplete){
			var percentVal = percentComplete + "%";
			bar.width(percentVal);
			percent.html(percentVal);
		},
	    success: function(){
			var percentVal = "100%";
			bar.width(percentVal);
			percent.html(percentVal);
		},
		complete: function(xhr){
			if(xhr.responseText){
				console.log("Success");
				window.location.href='http://urconnex.com/audio/index?message=success';
			}
		}
	});
        
        $("#myForm2").ajaxForm({
	 	beforeSubmit: function(){
					document.getElementById("progress_div").style.display ="block";
					var percentVal = "0%";
					bar.width(percentVal);
					percent.html(percentVal);
		},
		uploadProgress : function(event, position, total, percentComplete){
			var percentVal = percentComplete + "%";
			bar.width(percentVal);
			percent.html(percentVal);
		},
	    success: function(){
			var percentVal = "100%";
			bar.width(percentVal);
			percent.html(percentVal);
		},
		complete: function(xhr){
			if(xhr.responseText){
				console.log("Success");
				window.location.href='http://urconnex.com/audio/index?message=success';
			}
		}
	});
}
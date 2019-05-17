<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
<!-- <script type="text/javascript" src="js/easy-editable-text.js"></script> -->
<script type="text/javascript">
	$(document).ready(function(){
	
	$('.edit').click(function(){
		$(this).hide();
		$(this).prev().hide();
		$(this).next().show();
		$(this).next().select();
	});
	
	
	$('input[type="text"]').blur(function() {  
         if ($.trim(this.value) == ''){  
			 this.value = (this.defaultValue ? this.defaultValue : '');  
		 }
		 else{
			 $(this).prev().prev().html(this.value);
		 }
		 
		 $(this).hide();
		 $(this).prev().show();
		 $(this).prev().prev().show();
     });
	  
	  $('input[type="text"]').keypress(function(event) {
		  if (event.keyCode == '13') {
			  if ($.trim(this.value) == ''){  
				 this.value = (this.defaultValue ? this.defaultValue : '');  
			 }
			 else
			 {
				 $(this).prev().prev().html(this.value);
			 }
			 
			 $(this).hide();
			 $(this).prev().show();
			 $(this).prev().prev().show();
		  }
	  });
		  
});$(document).ready(function(){
	
	$('.edit').click(function(){
		$(this).hide();
		$(this).prev().hide();
		$(this).next().show();
		$(this).next().select();
	});
	
	
	$('input[type="text"]').blur(function() { 
	alert('2'); 
         if ($.trim(this.value) == ''){  
			 this.value = (this.defaultValue ? this.defaultValue : '');  
		 }
		 else{
			 $(this).prev().prev().html(this.value);
		 }
		 
		 $(this).hide();
		 $(this).prev().show();
		 $(this).prev().prev().show();
     });
	  
	  $('input[type="text"]').keypress(function(event) {
		  if (event.keyCode == '13') {
			  if ($.trim(this.value) == ''){  
				 this.value = (this.defaultValue ? this.defaultValue : '');  
			 }
			 else
			 {
				 $(this).prev().prev().html(this.value);
			 }
			 
			 $(this).hide();
			 $(this).prev().show();
			 $(this).prev().prev().show();
		  }
	  });
		  
});
</script>

<style type="text/css">
input[type=text]
{
	margin-top:8px;
	font-size:18px;
	color:#545454;
	-moz-border-radius: 2px;
	-webkit-border-radius: 2px;
	-border-radius: 2px;
	display:none;
	width:280px;
	
}

label
{
	float:left;
	margin-top:8px;
	font-size:18px;
	color:#545454;
	-moz-border-radius: 2px;
	-webkit-border-radius: 2px;
	-border-radius: 2px;
}

.edit
{
	float:left;
	background:url(images/edit.png) no-repeat;
	width:32px;
	height:32px;
	display:block;
	cursor: pointer;
	margin-left:10px;
}

.clear
{
	clear:both;
	height:20px;
}

</style>

<title>jQuery - Easy editable text fields</title>
</head>
<body>

<label class="text_label">Click The Pencil Icon to Edit Me</label><i class="edit fa-fa-edit">cdd</i>
<input type="text" value="Click The Pencil Icon to Edit Me" />



</body>
</html>

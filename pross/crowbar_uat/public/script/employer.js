function yesnoCheck(e) {
    if (document.getElementById('fulltime').checked) {
        document.getElementById('form1').style.display = 'none';
        document.getElementById('form2').style.display = 'block';
        if($(e).is(":checked")) {
		   $("#jobtype2").val($(e).val());
		   $("#jobtype1").val('');    
		}
    }else{
    	document.getElementById('form2').style.display = 'none';
        document.getElementById('form1').style.display = 'block';
        if($(e).is(":checked")) {
		   $("#jobtype1").val($(e).val()); 
		   $("#jobtype2").val('');     
		}
    }
}
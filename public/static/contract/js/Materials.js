$(function(){  
	//合同关联机会，报价				
$('#snumber').change(function(){
	var snumber = $('#snumber').find('option:selected').val();
	$.ajax({
			type: "GET",
			async: false,
			url: "/admin.php/contract/Materials/getSnumber/snumber/"+snumber,
			success: function(data){
			console.log(data)
      $('#objname').val(data.objname);
			}
		});    	
})
});
        		

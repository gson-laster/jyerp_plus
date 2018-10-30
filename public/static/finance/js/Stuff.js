$(function(){  
	//合同关联机会，报价				
$('#source_number').change(function(){
	var source_number = $('#source_number').find('option:selected').val();
	$.ajax({
			type: "GET",
			async: false,
			url: "/admin.php/finance/Stuff/getDetail/source_number/"+source_number,
			success: function(data){
				console.log(data)
        	$('#objname').val(data.objname);
        	$('#suname').val(data.suname);
       
			}
		});    	
})
});
        		

$(function(){  
	//合同关联机会，报价				
$('#pact').change(function(){
	var pact = $('#pact').find('option:selected').val();
	$.ajax({
			type: "GET",
			async: false,
			url: "/admin.php/finance/Pay/getDetail/pact/"+pact,
			success: function(data){
				console.log(data)
        	$('#objname').val(data.objname);
        	$('#suname').val(data.suname);
       
			}
		});    	
})
});
        		

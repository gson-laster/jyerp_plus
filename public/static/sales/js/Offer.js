$(function(){  
	//报价关联机会				
   $('#monophycode').change(function(){
	var monophycode = $('#monophycode').find('option:selected').val();
	$.ajax({
			type: "GET",
			async: false,
			url: "/admin.php/sales/offer/get_Detail/monophycode/"+monophycode,
			success: function(data){
				$('#customer_name').val(data.customer_name);
        		$('#phone').val(data.phone);
			}
		});    	
})
});
        		

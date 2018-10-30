$(function(){  
	//销售机会关联供应商					
   $('#customer_name').change(function(){
	var customer_name = $('#customer_name').find('option:selected').val();
	$.ajax({
			type: "GET",
			async: false,
			url: "/admin.php/sales/opport/get_Detail/customer_name/"+customer_name,
			success: function(data){
				$('#supplier_clienttype').val(data.supplier_clienttype);
        		$('#phone').val(data.phone);
			}
		});    	
})
});
        	
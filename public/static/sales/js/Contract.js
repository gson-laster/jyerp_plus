$(function(){  
	//合同关联机会，报价				
$('#monophycode').change(function(){
	var monophycode = $('#monophycode').find('option:selected').val();
	$.ajax({
			type: "GET",
			async: false,
			url: "/admin.php/sales/contract/get_Detail/customer_name/"+monophycode,
			success: function(data){
				$('#customer_name').val(data.customer_name);
        		$('#phone').val(data.phone);
				$('#zrname').val(data.zrname);
        		$('#oid').val(data.department);
				$('#zrid').val(data.zrid);
        		$('#ooid').val(data.ooid);
			}
		});    	
	})
});
        		

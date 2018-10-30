$(function(){  
	//合同关联机会，报价				
$('#monophycode').change(function(){
	var monophyletic = $('#monophyletic').find('option:selected').val();
	var monophycode = $('#monophycode').find('option:selected').val();
	$.ajax({
			type: "GET",
			async: false,
			url: "/admin.php/sales/order/getDetail/monophyletic/"+monophyletic+"/monophycode/"+monophycode,
			success: function(data){
        		$('#customer_name').val(data.customer_name);
        		$('#phone').val(data.phone);
        		$('#zrname').val(data.zrname);
        		$('#zrid').val(data.zrid);
        		$('#oid').val(data.oid);
        		$('#department').val(data.department);
			}
		});    	
})
});
        		

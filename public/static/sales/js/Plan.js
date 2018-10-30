$(function(){  
	//销售机会关联供应商					
   $('#item').change(function(){
	var item = $('#item').find('option:selected').val();
	$.ajax({
			type: "GET",
			async: false,
			url: "/admin.php/sales/index/get_Detail/customer_name/"+item,
			success: function(data){
				$('#customer_name').val(data.customer_name);
        		$('#phone').val(data.phone);
				$('#zrname').val(data.zrname);
        		$('#oid').val(data.oid);
				$('#zrid').val(data.zrid);
        		$('#department').val(data.department);
				$('#low_money').val(data.money);
			}
		});    	
	})
});
        	
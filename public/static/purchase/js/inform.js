$(function(){  
	//源单号赋值总金额				
$('#contract').change(function(){
	var contract = $('#contract').find('option:selected').val();
	$.ajax({
			type: "GET",
			async: false,
			url: "/admin.php/purchase/Informmoney/get_Detail/contract/"+contract,
			success: function(data){
				console.log(data)
				$('#money').val(data);
			}
		});    	
	})
});
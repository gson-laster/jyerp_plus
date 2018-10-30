$(function(){  


	$('#obj_id').change(function(){
	var deliveryid = $('#obj_id').find('option:selected').val();
	$.ajax({
			type: "GET",
			async: false,
			url: "/admin.php/stock/sell/get_Detail/deliveryid/"+deliveryid,
			success: function(data){
				$('#customer_name').val(data.customer_name);
        		$('#department').val(data.department);
        		$('#uid').val(data.uid);
				$('#goodaddrss').val(data.goodaddrss);
				$('#addrss').val(data.addrss);
			}
		});    	
	})






        var id = $('#id').val();
        $.ajax({
            type: "GET",
            async: false,
            url: "/admin.php/tender/schedule/tech/id/"+id,
            success: function(data){
                $("#form_group_obj").after(data);
            }
        }); 
})
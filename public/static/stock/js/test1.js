$(function(){  
	//采购入库选择到货单
	var order_id = '';
	$('#order_id').change(function(){
		order_id = $('#order_id').find('option:selected').val();
		$.ajax({
				type: "GET",
				async: false,
				url: "/admin.php/stock/purchase/get_Detail/order_id/"+order_id,
				success: function(data){
					$('#cid').val(data.cid);
					$('#oid').val(data.oid);
					$('#sid').val(data.sid);
				}
			}); 	
	})
	//添加选中物品的按钮         
	$('#form_group_materials_list').after('<div class="h_html"></div>');       
	//编辑获取
	var pid = $("#id").val();
    var materials_list = $("#materials_list").val();
	var controller = $('#controller').val();
    $("#old_plan_list").val($("#materials_list").val()); 
	$('#builder-form-group-tab').find('li').eq(1).click(function() {
		if(order_id){			
			$.ajax({
				type: "GET",
				async: false,
				url: "/admin.php/stock/"+controller+"/get_Mateplan/mateplan/"+order_id,
				success: function(data){
					$(".h_html").html(data);
				}
			});
		}else{
			if(pid){
				$.ajax({
					type: "GET",
					async: false,
					url: "/admin.php/stock/purchase/tech/pid/"+pid+"/materials_list/"+materials_list,
					success: function(data){
						$(".h_html").after(data);						
					}
				});	
			}else{
				$('.h_html').html('<span class="label label-danger">请先选择采购到货单</span>');
			}
		}		
	})	       	
});        		

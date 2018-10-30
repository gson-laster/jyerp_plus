$(function(){  
	//添加选中物品的按钮         
	$('#form_group_materials_list').after('<div class="h_html"></div>');       
	//编辑获取
	var pid = $("#id").val();
    var materials_list = $("#materials_list").val();
    $("#old_plan_list").val($("#materials_list").val()); 
	console.log(pid)
if(pid){
	$.ajax({
			type: "GET",
			async: false,
			url: "/admin.php/purchase/ask/tech/pid/"+pid+"/materials_list/"+materials_list,
			success: function(data){
				console.log(data)
        		$(".h_html").after(data);				
			}	
		}); 
}	       	   
});
function inpu(t) {
	var t = $(t).parents('tr');
	var xysl = t.find('.xysl').val();
	var ckjg = t.find('.price').text();
	t.find('.xj').val(Number(xysl * ckjg));
}

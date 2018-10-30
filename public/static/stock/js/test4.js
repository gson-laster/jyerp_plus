$(function(){  
	//生产入库选择生产任务单
	$('#deliveryid').change(function(){
	var deliveryid = $('#deliveryid').find('option:selected').val();
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
$('#select').click(function(){
		$.ajax({
			type: "GET",
			async: false,
			url: "/admin.php/stock/sell/get_Mateplan/mateplan/"+deliveryid,
			success: function(data){
        		$(".h_html").html(data);
			}
		});    		
	})    	
})
	//添加选中物品的按钮         
	$('#form_group_materials_list').after('<div class="form-group col-md-12 col-xs-12" id="form_group_select"><button class="btn btn-xs btn-info" type="button" id="select">查看明细</button></div><div class="h_html"></div>');       
	//编辑获取
	var pid = $("#id").val();
    var materials_list = $("#materials_list").val();
    $("#old_plan_list").val($("#materials_list").val()); 
if(pid){
	 $.ajax({
			type: "GET",
			async: false,
			url: "/admin.php/stock/sell/tech/pid/"+pid+"/materials_list/"+materials_list,
			success: function(data){
        		$("#form_group_select").after(data);
        		if(data){
        			$('#select').hide();
        		}
			}
		});
}	
       	
 
	//没有数据了选择按钮消失		
    if($('tbody tr:first').hasClass('table-empty')){
    	$('#pick').hide();
    }
    //点击选择
	$('#pick').click(function(){
			var chk = $('tbody .active');
    		var ids = '';   
    		if($("#form_group_materials_name",parent.document).length>0){
				var html = '';
	    		chk.each(function(){
	    			ids += $(this).find('.ids').val()+',';
    			html += '<tr><input type="hidden" name="mid[]" value="'+$.trim($(this).find('td').eq(1).text())+'"><td>'+$.trim($(this).find('td').eq(2).text())+'</td><td>'+$.trim($(this).find('td').eq(4).text())+'</td><td>'+$.trim($(this).find('td').eq(3).text())+'</td><td><input type="text" name="bzxq[]"></td><td>'+house_select+'</td><td><input type="number" name="cksl[]"></td><td><input type="number" name="dj[]"></td><td><input type="number" name="je[]"></td><td><input type="number" name="bz[]"></td><td><a href="javascript:;" onclick="delMaterials(this,\''+$.trim($(this).find('td').eq(1).text())+'\')">删除</a></td></tr>';	    			  
	   			});
			}else{
				var html = '<div class="form-group col-md-12 col-xs-12" id="form_group_materials_name"><table class="table table-bordered"><tbody><tr><td>物品名称</td><td>单位</td><td>规格</td><td>包装要求</td><td>仓库</td><td>出库数量</td><td>出库单价(元)</td><td>出库金额</td><td>备注</td><td>操作</td></tr>';
    		chk.each(function(){
    			ids += $(this).find('.ids').val()+',';   
    			html += '<tr><input type="hidden" name="mid[]" value="'+$.trim($(this).find('td').eq(1).text())+'"><input type="hidden" name="mlid[]" value="0"><td>'+$.trim($(this).find('td').eq(2).text())+'</td><td>'+$.trim($(this).find('td').eq(4).text())+'</td><td>'+$.trim($(this).find('td').eq(3).text())+'</td><td><input type="text" name="bzxq[]"></td><td>'+house_select+'</td><td><input type="number" name="cksl[]"></td><td><input type="number" name="dj[]"></td><td><input type="number" name="je[]"></td><td><input type="number" name="bz[]"></td><td><a href="javascript:;" onclick="delMaterials(this,\''+$.trim($(this).find('td').eq(1).text())+'\')">删除</a></td></tr>';
   			});
    		html += '</tbody></table></div>';
			}   
    		ids = ids.slice(0,-1);
    		//获取选中物品的id逗号隔开
    		if(ids){
	    		var materials = $("#materials_list",parent.document).val();
	    		if(materials){
	    			ids = materials+','+ids;
	    		}
	    		var idsArr=ids.split(",");
	   			idsArr.sort();
	    		idsArr = $.unique(idsArr);
	   			ids = idsArr.join(",");	    		
				$("#materials_list",parent.document).val(ids);
    			if($("#form_group_materials_name",parent.document).length>0){
     				$("#form_group_materials_name tbody",parent.document).append(html);   
    			}else{
    				$("#form_group_select",parent.document).after(html);
    			}
    
    		}
			//当你在iframe页面关闭自身时
			var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
			parent.layer.close(index); //再执行关闭
	});
});
        		var removeFromArray = function (arr, val) {
				    var index = $.inArray(val, arr);
				    if (index >= 0)
				        arr.splice(index, 1);
				    return arr;
				};

        		function delMaterials(obj,id){
    				var ids = $("#materials_list").val();
        			var idsArr=ids.split(",");   
	   				ids = removeFromArray(idsArr, id);       		
        			ids = idsArr.join(",");	       		    
        			$("#materials_list").val(ids);
        			$(obj).parents('tr').remove();       			
    			}

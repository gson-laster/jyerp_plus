$(function(){  
	//合同关联机会，报价				
$('#monophycode').change(function(){
	var monophyletic = $('#monophyletic').find('option:selected').val();
	var monophycode = $('#monophycode').find('option:selected').val();
	$.ajax({
			type: "GET",
			async: false,
			url: "/admin.php/sales/delivery/getDetail/monophyletic/"+monophyletic+"/monophycode/"+monophycode,
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
//添加选中物品的按钮         
	$('#form_group_materials_list').after('<div class="form-group col-md-12 col-xs-12" id="form_group_select"><button class="btn btn-xs btn-info" type="button" id="select">选择物品</button></div>');       
	//编辑获取
	var pid = $("#id").val();
    var materials_list = $("#materials_list").val();
    $("#old_plan_list").val($("#materials_list").val()); 
if(pid){
	 $.ajax({
			type: "GET",
			async: false,
			url: "/admin.php/sales/delivery/tech/pid/"+pid+"/materials_list/"+materials_list,
			success: function(data){
        		$("#form_group_select").after(data);
        		if(data){
        			$('#select').hide();
        		}
			}
		}); 
}
	
      	
      //点击选择物品弹出  		
    $('#select').click(function(){
        	var materials = $("#materials_list").val();
			//iframe窗
			layer.open({
			  type: 2,
			  title: '选择子件',
			  shadeClose: true,
			  shade: 0.3,
			  maxmin: true, //开启最大化最小化按钮
			  area: ['70%', '70%'],
			  content: '/admin.php/sales/delivery/choose_materials/materials/'+materials 
			});
	}); 
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
    			html += '<tr><input type="hidden" name="mid[]" value="'+$.trim($(this).find('td').eq(1).text())+'"><td>'+$.trim($(this).find('td').eq(2).text())+'</td><td>'+$.trim($(this).find('td').eq(4).text())+'</td><td>'+$.trim($(this).find('td').eq(3).text())+'</td><td class="danjia">'+$.trim($(this).find('td').eq(5).text())+'</td><td><input oninput="inp(this)" class="fhsl" type="number" name="fhsl[]"></td><td><input class="je" type="number" name="je[]"></td><td><input type="text" name="bzxq[]"></td><td><input type="text" name="bz[]"></td><td><a href="javascript:;" onclick="delMaterials(this,\''+$.trim($(this).find('td').eq(1).text())+'\')">删除</a></td></tr>';	    			  
	   			});
			}else{
				var html = '<div class="form-group col-md-12 col-xs-12" id="form_group_materials_name"><table class="table table-bordered"><tbody><tr><td>物品名称</td><td>单位</td><td>规格</td><td>单价(元)</td><td>发货数量</td><td>合计</td><td>包装要求</td><td>备注</td><td>操作</td></tr>';
    		chk.each(function(){
    			ids += $(this).find('.ids').val()+',';   
    			html += '<tr><input type="hidden" name="mid[]" value="'+$.trim($(this).find('td').eq(1).text())+'"><input type="hidden" name="mlid[]" value="0"><td>'+$.trim($(this).find('td').eq(2).text())+'</td><td>'+$.trim($(this).find('td').eq(4).text())+'</td><td>'+$.trim($(this).find('td').eq(3).text())+'</td><td class="danjia">'+$.trim($(this).find('td').eq(5).text())+'</td><td><input class="fhsl" type="number" oninput="inp(this)" name="fhsl[]"></td><td><input class="je" type="number" name="je[]"></td><td><input type="text" name="bzxq[]"></td><td><input type="text" name="bz[]"></td><td><a href="javascript:;" onclick="delMaterials(this,\''+$.trim($(this).find('td').eq(1).text())+'\')">删除</a></td></tr>';
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
function inp(o) {
	var val = o.value;
	var parent = $(o).parents('tr');
	var fhsl = parent.find('.fhsl');
	var je = parent.find('.je');
	var danjia = parent.find('.danjia');
	je.val(Number(fhsl.val() * danjia.text()))
}

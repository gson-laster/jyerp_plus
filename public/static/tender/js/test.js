$(function(){  
	//添加选中物品的按钮         
	$('#form_group_materials_list').after('<div class="form-group col-md-12 col-xs-12" id="form_group_select"><button class="btn btn-xs btn-info" type="button" id="select">选择物品</button></div>');       
	//编辑获取
	var pid = $("#id").val();	
    var materials_list = $("#materials_list").val(); 
if(pid){
	$.ajax({
			type: "GET",
			async: false,
			url: "/admin.php/tender/materials/tech/pid/"+pid+"/materials_list/"+materials_list,
			success: function(data){
        		$("#form_group_select").after(data);
				if(data){
        			$('#select').hide();
        		}
				top_list = [];							
				var lists = ['form_group_name', 'form_group_obj_id', 'form_group_authorizedname', 'form_group_create_time'];
				for (var i = 0; i < lists.length; i++) {
					var el = $("#" + lists[i]);
					top_list.push({name:el.find('.col-xs-12').text(), value: el.find('.form-control-static').text()});
				};
			}	
		}); 
}	
       	//点击新增物资
	$('#add_pick').on('click',function(){
		layer.open({
			type:1,
			title:'新增物资',
			maxmin: true,
			scrollbar: false,
			content:$('.add_pick'),
			btn:['确定','取消'],
			yes:function (index,layero) {	
				var materialData = '';
				$(layero).find("input").each(function(i, value) {
					materialData +=$(value).attr('name') +"=" +$(value).val()+"&";
                });
				$(layero).find("select").each(function(i, value) {
					materialData +=$(value).attr('name') +"=" +$(value).val()+"&";
				});					
				materialData = materialData.slice(0, -1);
				$.ajax({
					type:"get",
					async:false,
					url:"/admin.php/tender/materials/creatMaterial?" + materialData,
					success:function(msg){
						layer.msg(msg);
					}
				})	
				layer.close(index);	        					        					        			    
			 },
			end: function () {
                location.reload();
            }			 
		})
	})
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
			  content: '/admin.php/tender/materials/choose_materials/materials/'+materials 
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
    			html += '<tr><input type="hidden" name="mid[]" value="'+$.trim($(this).find('td').eq(1).text())+'"><td>'+$.trim($(this).find('td').eq(3).text())+'</td><td>'+$.trim($(this).find('td').eq(5).text())+'</td><td>'+$.trim($(this).find('td').eq(4).text())+'</td><td><input  type="number" class="xysl" oninput="inpu(this)" name="xysl[]"></td><td><input type="text" name="bz[]"></td><td><a href="javascript:;" onclick="delMaterials(this,\''+$.trim($(this).find('td').eq(1).text())+'\')">删除</a></td></tr>';	    			  
	   			});
			}else{
				var html = '<div class="form-group col-md-12 col-xs-12" id="form_group_materials_name"><table class="table table-bordered"><tbody><tr><td>物品名称</td><td>单位</td><td>规格</td><td>需用数量</td><td>备注</td><td>操作</td></tr>';
    		chk.each(function(){
    			ids += $(this).find('.ids').val()+',';   
    			html += '<tr><input type="hidden" name="mid[]" value="'+$.trim($(this).find('td').eq(1).text())+'"><input type="hidden" name="mlid[]" value="0"><td>'+$.trim($(this).find('td').eq(3).text())+'</td><td>'+$.trim($(this).find('td').eq(5).text())+'</td><td>'+$.trim($(this).find('td').eq(4).text())+'</td><td><input type="number" class="xysl" oninput="inpu(this)" name="xysl[]"></td><td><input type="text" name="bz[]"></td><td><a href="javascript:;" onclick="delMaterials(this,\''+$.trim($(this).find('td').eq(1).text())+'\')">删除</a></td></tr>';
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
function inpu(t) {
	var t = $(t).parents('tr');
	var xysl = t.find('.xysl').val();
	var ckjg = t.find('.price').text();
	t.find('.xj').val(Number(xysl * ckjg));
}
//打印
function dddd(){
	var str = '';
		for (var i = 0; i < top_list.length; i++) {
			var sty = '<ul>';
			var br = '';
			if (i % 2 == 0) {
				sty = 'float:left;'
			} else {
				sty = 'float: right;text-align:right;'
				br = '<br />'	
			}
			str += `<li style="${sty} width: 50%; list-style: none; padding: 5px 15px;">${top_list[i]['name']} : ${top_list[i]['value']}</li>`
		};
		str += '</ul>'
		$('#form_group_materials_name').before('<div style="text-align:center; font-size: 20px; padding: 5px 0;">备料单</div>');
		$('#form_group_materials_name').before(str);
		var remark = $("#form_group_note").find('.form-control-static').text()
		$('#form_group_materials_name').after('<div style="padding-left: 15px; transform:translateY(-30px)">备注：'+remark+'</div>');
		$('#form_group_materials_name').css('overflow','hidden');
	    bdhtml=window.document.body.innerHTML;   
	    sprnstr="<!--startprint-->";   
	    eprnstr="<!--endprint-->";   
	    prnhtml=bdhtml.substr(bdhtml.indexOf(sprnstr)+17);
	    prnhtml=prnhtml.substring(0,prnhtml.indexOf(eprnstr));  	
	    window.document.body.innerHTML=prnhtml;  
	    window.print();
}

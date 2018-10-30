//库存模块js
$(function(){  
	//添加选中物品的按钮         
	$('#form_group_materials_list').after('<div class="form-group col-md-12 col-xs-12" id="form_group_select"><button class="btn btn-xs btn-info" type="button" id="select">选择物品</button></div>');
	//选择源单id查询明细
	var order_id = '';
	//查看详情获取控制器名称、pid和物资id
	var controller = $('#controller').val();
	var pid = $("#id").val();
    var materials_list = $("#materials_list").val();
	var label = $('#form_group_order_id label').text();
	var htmltd = '';
	var htmltd2 = '';
	var htmldiv = '';
	var htmltr = [];
	//不同控制器不同功能
	switch (controller)
	{
		case 'Purchase':		
		//$('#order_id').change(function(){
			//order_id = $(this).find('option:selected').val();
			//$.ajax({
				//type: "GET",
				//async: false,
				//url: "/admin.php/stock/purchase/get_Detail/order_id/"+order_id,
				//success: function(data){
					//$('#cid').val(data.cid);
					//$('#oid').val(data.oid);
					//$('#sid').val(data.sid);
				//}
			//}); 	
		//});
		window.frames["getfun"] = function(o){
			htmltr['htmltd'] = '<tr><input type="hidden" name="mid[]" value="'+$.trim(o.find('td').eq(1).text())+'"><input type="hidden" name="type[]" value="'+$.trim(o.find('td').eq(6).text())+'"><td>'+$.trim(o.find('td').eq(3).text())+'</td><td>'+$.trim(o.find('td').eq(5).text())+'</td><td>'+$.trim(o.find('td').eq(4).text())+'</td><td><input type="number" class="rksl" oninput="otherin(this)" name="rksl[]"></td><td><input type="number" oninput="otherin(this)" class="dj" name="dj[]"></td><td><input type="number" class="je" name="je[]"></td><td><a href="javascript:;" onclick="delMaterials(this,\''+$.trim(o.find('td').eq(1).text())+'\')">删除</a></td></tr>';
			htmltr['htmldiv'] = '<div class="form-group col-md-12 col-xs-12" id="form_group_materials_name"><table class="table table-bordered"><tbody><tr><td>材料名称</td><td>计量单位</td><td>规格型号</td><td>数量</td><td>单价(元)</td><td>金额</td><td>操作</td></tr>';
			htmltr['htmltd2'] = '<tr><input type="hidden" name="mid[]" value="'+$.trim(o.find('td').eq(1).text())+'"><input type="hidden" name="type[]" value="'+$.trim(o.find('td').eq(6).text())+'"><td>'+$.trim(o.find('td').eq(3).text())+'</td><td>'+$.trim(o.find('td').eq(5).text())+'</td><td>'+$.trim(o.find('td').eq(4).text())+'</td><td><input type="number" class="rksl" oninput="otherin(this)" name="rksl[]"></td><td><input type="number" class="dj" name="dj[]"></td><td><input class="je" type="number" name="je[]"></td><td><a href="javascript:;" onclick="delMaterials(this,\''+$.trim(o.find('td').eq(1).text())+'\')">删除</a></td></tr>';
			return htmltr;
		}
		break;
		case 'Produce':
		//$('#order_id').change(function(){
			//order_id = $(this).find('option:selected').val();
			//$.ajax({
				//type: "GET",
				//async: false,
				//url: "/admin.php/stock/produce/get_Detail/order_id/"+order_id,
				//success: function(data){
					//$('#header').val(data.header);
					//$('#org_id').val(data.org_id);
				//}
			//});  		
		//});
		window.frames["getfun"] = function(o){
			htmltr['htmltd'] = '<tr><input type="hidden" name="mid[]" value="'+$.trim(o.find('td').eq(1).text())+'"><input type="hidden" name="type[]" value="'+$.trim(o.find('td').eq(6).text())+'"><td>'+$.trim(o.find('td').eq(3).text())+'</td><td>'+$.trim(o.find('td').eq(5).text())+'</td><td>'+$.trim(o.find('td').eq(4).text())+'</td><td><input type="number" class="rksl" oninput="otherin(this)" name="rksl[]"></td><td><a href="javascript:;" onclick="delMaterials(this,\''+$.trim(o.find('td').eq(1).text())+'\')">删除</a></td></tr>';
			htmltr['htmldiv'] = '<div class="form-group col-md-12 col-xs-12" id="form_group_materials_name"><table class="table table-bordered"><tbody><tr><td>材料名称</td><td>计量单位</td><td>规格型号</td><td>数量</td><td>操作</td></tr>';
			htmltr['htmltd2'] = '<tr><input type="hidden" name="mid[]" value="'+$.trim(o.find('td').eq(1).text())+'"><input type="hidden" name="type[]" value="'+$.trim(o.find('td').eq(6).text())+'"><td>'+$.trim(o.find('td').eq(3).text())+'</td><td>'+$.trim(o.find('td').eq(5).text())+'</td><td>'+$.trim(o.find('td').eq(4).text())+'</td><td><input type="number" class="rksl" oninput="otherin(this)" name="rksl[]"></td><td><a href="javascript:;" onclick="delMaterials(this,\''+$.trim(o.find('td').eq(1).text())+'\')">删除</a></td></tr>';
			return htmltr;
		}
		break;
		case 'Otherin':			
		//子页面赋值
		window.frames["getfun"] = function(o){
			$ckid = $.trim(o.find('td').eq(9).text())?$.trim(o.find('td').eq(9).text()):$.trim(o.find('td').eq(8).attr('data-id'));
			htmltr['htmltd'] = '<tr><input type="hidden" name="mid[]" value="'+$.trim(o.find('td').eq(1).text())+'"><input type="hidden" name="mlid[]" value="0"><td>'+$.trim(o.find('td').eq(3).text())+'</td><td>'+$.trim(o.find('td').eq(5).text())+'</td><td>'+$.trim(o.find('td').eq(4).text())+'</td><td><input type="number" class="rksl" oninput="otherin(this)" name="rksl[]"></td><td><input type="number" oninput="otherin(this)" class="dj" name="dj[]"></td><td><input type="number" class="je" name="je[]"></td><td><input type="text" name="bz[]"></td><td><a href="javascript:;" onclick="delMaterials(this,\''+$.trim(o.find('td').eq(1).text())+'\')">删除</a></td></tr>';
			htmltr['htmldiv'] = '<div class="form-group col-md-12 col-xs-12" id="form_group_materials_name"><table class="table table-bordered"><tbody><tr><td>物品名称</td><td>单位</td><td>规格</td><td>数量</td><td>单价</td><td>金额</td><td>备注</td><td>操作</td></tr>';
			htmltr['htmltd2'] = '<tr><input type="hidden" name="mid[]" value="'+$.trim(o.find('td').eq(1).text())+'"><td>'+$.trim(o.find('td').eq(3).text())+'</td><td>'+$.trim(o.find('td').eq(5).text())+'</td><td>'+$.trim(o.find('td').eq(4).text())+'</td><td><input type="number" class="rksl" oninput="otherin(this)" name="rksl[]"></td><td><input type="number" class="dj" name="dj[]"></td><td><input class="je" type="number" name="je[]"></td><td><input type="text" name="bz[]"></td><td><a href="javascript:;" onclick="delMaterials(this,\''+$.trim(o.find('td').eq(1).text())+'\')">删除</a></td></tr>';
			return htmltr;
		}
		break;
		case 'Otherout':
		window.frames["getfun"] = function(o){
			htmltr['htmltd'] = '<tr><input type="hidden" name="mid[]" value="'+$.trim(o.find('td').eq(1).text())+'"><input type="hidden" name="mlid[]" value="0"><td>'+$.trim(o.find('td').eq(3).text())+'</td><td>'+$.trim(o.find('td').eq(5).text())+'</td><td>'+$.trim(o.find('td').eq(4).text())+'</td><td><input type="number" name="cksl[]"></td><td><input type="number" readonly name="dj[]"></td><td><input type="number" readonly name="je[]"></td><td><a href="javascript:;" onclick="delMaterials(this,\''+$.trim(o.find('td').eq(1).text())+'\')">删除</a></td></tr>';
			htmltr['htmldiv'] = '<div class="form-group col-md-12 col-xs-12" id="form_group_materials_name"><table class="table table-bordered"><tbody><tr><td>材料名称</td><td>计量单位</td><td>规格型号</td><td>出库数量</td><td>单价(元)</td><td>金额</td><td>操作</td></tr>';
			htmltr['htmltd2'] = '<tr><input type="hidden" name="mid[]" value="'+$.trim(o.find('td').eq(1).text())+'"><td>'+$.trim(o.find('td').eq(3).text())+'</td><td>'+$.trim(o.find('td').eq(5).text())+'</td><td>'+$.trim(o.find('td').eq(4).text())+'</td><td><input type="number" name="cksl[]"></td><td><input type="number" readonly name="dj[]"></td><td><input type="number" readonly name="je[]"></td><td><a href="javascript:;" onclick="delMaterials(this,\''+$.trim(o.find('td').eq(1).text())+'\')">删除</a></td></tr>';
			return htmltr;
		}
		break;
		case 'Borrow':
		window.frames["getfun"] = function(o){
			htmltr['htmltd'] = '<tr><input type="hidden" name="mid[]" value="'+$.trim(o.find('td').eq(1).text())+'"><td>'+$.trim(o.find('td').eq(3).text())+'</td><td>'+$.trim(o.find('td').eq(5).text())+'</td><td>'+$.trim(o.find('td').eq(4).text())+'</td><td>'+house_select+'</td><td><input type="number" name="cksl[]"></td><td><input type="text" name="fhtime[]" class="js-datepicker" data-date-format="yyyy-mm-dd"></td><td><input type="number" name="fhsl[]"></td><td><input type="text" name="bz[]"></td><td><a href="javascript:;" onclick="delMaterials(this,\''+$.trim(o.find('td').eq(1).text())+'\')">删除</a></td></tr>';
			htmltr['htmldiv'] = '<div class="form-group col-md-12 col-xs-12" id="form_group_materials_name"><table class="table table-bordered"><tbody><tr><td>物品名称</td><td>单位</td><td>规格</td><td>仓库</td><td>借货数量</td><td>预计返还日期</td><td>预计返还数量</td><td>备注</td><td>操作</td></tr>';
			htmltr['htmltd2'] = '<tr><input type="hidden" name="mid[]" value="'+$.trim(o.find('td').eq(1).text())+'"><input type="hidden" name="mlid[]" value="0"><td>'+$.trim(o.find('td').eq(3).text())+'</td><td>'+$.trim(o.find('td').eq(5).text())+'</td><td>'+$.trim(o.find('td').eq(4).text())+'</td><td>'+house_select+'</td><td><input type="number" name="cksl[]"></td><td><input type="text" name="fhtime[]" class="js-datepicker" data-date-format="yyyy-mm-dd"></td><td><input type="number" name="fhsl[]"></td><td><input type="text" name="bz[]"></td><td><a href="javascript:;" onclick="delMaterials(this,\''+$.trim(o.find('td').eq(1).text())+'\')">删除</a></td></tr>';
			return htmltr;
		}
		break;
		case 'Restore':
		$('#order_id').change(function(){
			order_id = $('#order_id').find('option:selected').val();
			$.ajax({
				type: "GET",
				async: false,
				url: "/admin.php/stock/restore/get_Detail/order_id/"+order_id,
				success: function(data){
					$('#jhbm').val(data.jhbm);
					$('#jhname').val(data.jhname);
					$('#jh_time').val(data.jh_time);
					$('#jcbm').val(data.jcbm);
				}
			}); 
		})
		break;
		case 'Allot':
		window.frames["getfun"] = function(o){
			htmltr['htmltd'] = '<tr><input type="hidden" name="mid[]" value="'+$.trim(o.find('td').eq(1).text())+'"><td>'+$.trim(o.find('td').eq(3).text())+'</td><td>'+$.trim(o.find('td').eq(5).text())+'</td><td>'+$.trim(o.find('td').eq(4).text())+'</td><td>'+house_select+'</td><td><input type="number" name="tbsl[]"></td><td><input type="text" name="bz[]"></td><td><a href="javascript:;" onclick="delMaterials(this,\''+$.trim(o.find('td').eq(1).text())+'\')">删除</a></td></tr>';
			htmltr['htmldiv'] = '<div class="form-group col-md-12 col-xs-12" id="form_group_materials_name"><table class="table table-bordered"><tbody><tr><td>物品名称</td><td>单位</td><td>规格</td><td>调拨仓库</td><td>调拨数量</td><td>备注</td><td>操作</td></tr>';
			htmltr['htmltd2'] = '<tr><input type="hidden" name="mid[]" value="'+$.trim(o.find('td').eq(1).text())+'"><input type="hidden" name="mlid[]" value="0"><td>'+$.trim(o.find('td').eq(3).text())+'</td><td>'+$.trim(o.find('td').eq(5).text())+'</td><td>'+$.trim(o.find('td').eq(4).text())+'</td><td>'+house_select+'</td><td><input type="number" name="tbsl[]"></td><td><input type="text" name="bz[]"></td><td><a href="javascript:;" onclick="delMaterials(this,\''+$.trim(o.find('td').eq(1).text())+'\')">删除</a></td></tr>';
			return htmltr;
		}
		break;
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
					url:"/admin.php/stock/purchase/creatMaterial?" + materialData,
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
    //点击选择
	$('#pick').on('click',function(){				
		var chk = $('tbody .active');		
		var ids = '';   
		if($("#form_group_materials_name",parent.document).length>0){			
			var html = '';
			chk.each(function(){
			ids += $(this).find('.ids').val()+',';
			html += window.parent.getfun($(this))['htmltd2'];   			  
			});
		}else{			
			var html = window.parent.getfun($(this))['htmldiv'];
			chk.each(function(){
			html += window.parent.getfun($(this))['htmltd'];				
			ids += $(this).find('.ids').val()+',';   
			html += htmltd;
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
		parent.get_date();
		parent.layer.close(index); //再执行关闭
	});					
	//不同控制器获取不同数据
	$('#builder-form-group-tab').find('li').eq(1).click(function() {
		if(order_id){			
			$.ajax({
				type: "GET",
				async: false,
				url: "/admin.php/stock/"+controller+"/get_Mateplan/mateplan/"+order_id,
				success: function(data){
					$("#form_group_select").html(data);
				}
			});
		}else{
			if(pid){
				$.ajax({
					type: "GET",
					async: false,
					url: "/admin.php/stock/"+controller+"/tech/pid/"+pid+"/materials_list/"+materials_list,
					success: function(data){
						$("#form_group_select").html(data);						
					}
				});	
			}else{
				if(label){
					$('#form_group_select').html('<span class="label label-danger">选择'+label+'</span>');
				}else{
					//点击选择物品弹出  		
					$('#select').on('click',function(){
						//iframe窗
						layer.open({
						  type: 2,
						  title: '选择物品',
						  shadeClose: true,
						  shade: 0.3,
						  maxmin: true, //开启最大化最小化按钮
						  area: ['70%', '70%'],
						  content: '/admin.php/stock/'+controller+'/choose_materials/materials/'+$("#materials_list").val(), 
						});
					});
				}				
			}
		}		
	})
	
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
	function get_date(){
			App.initHelpers(["datepicker"]);
	}
	function inpu(t) {
		var t = $(t).parents('tr');
		var xysl = t.find('.rksl').val();
		var ckjg = t.find('.price').text();
		t.find('.je').val(Number(xysl * ckjg));
	}
	function otherin(t) {
		var t = $(t).parents('tr');
		var rksl = t.find('.rksl').val();
		var dj = t.find('.dj').val();
		t.find('.je').val(Number(rksl * dj));
	}

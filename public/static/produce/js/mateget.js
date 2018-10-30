var top_list = [];
$(function(){  

	$('#form_group_materials_list').after('<div class="form-group col-md-12 col-xs-12" id="form_group_select"></div>'); 
	//编辑获取
	var pid = $("#id").val();
	
    var materials_list = $("#materials_list").val();
    $("#old_plan_list").val($("#materials_list").val());    			     		 		
 		
    $('#select').click(function(){
        	var materials = $("#materials_list").val();
			//iframe窗
			layer.open({
			  type: 2,
			  title: '物资库',
			  shadeClose: true,
			  shade: 0.3,
			  maxmin: true, //开启最大化最小化按钮
			  area: ['70%', '70%'],
			  content: '/admin.php/produce/mateget/choose_materials/materials/'+materials 
			});
	}); 

	$('#builder-form-group-tab').find('li').eq(1).click(function() {
		var pn = $('#plan_id').val();
		var is_detail = $('#is_detail').val();
		
		if(is_detail==1){
				$.ajax({
					type: "GET",
					async: false,
					url: "/admin.php/produce/mateget/tech2/pid/"+pid+"/materials_list/"+materials_list,
					success: function(data){

							$("#form_group_select").html(data);
							top_list = [];
							
							var lists = ['form_group_name', 'form_group_out_time', 'form_group_org_name', 'form_group_get_username'];
							for (var i = 0; i < lists.length; i++) {
								var el = $("#" + lists[i]);
								top_list.push({name:el.find('.col-xs-12').text(), value: el.find('.form-control-static').text()});

							};
							

							


					}
				}); 
		}else{
			if(pn){
				$.ajax({
					type: "GET",
					async: false,
					url: "/admin.php/produce/mateget/tech/pid/"+pn,
					success: function(data){
				      		$("#form_group_select").html(data);
					}
				}); 
			}else{
				$('#form_group_select').html('<span class="label label-danger">请先选择生产计划</span>');
			}
		}
		
	});


});
        		var removeFromArray = function (arr, val) {
				    var index = $.inArray(val, arr);
				    if (index >= 0)
				        arr.splice(index, 1);
				    return arr;
				};

				function get_date(){
					    App.initHelpers(["datepicker"]);
                }

                function get_sum(obj){
    var dj = $(obj).parent('td').prev('td').find('input').val();
    var num = $(obj).parent('td').prev('td').prev('td').find('input').val();
    $(obj).val(dj*num);
}
function set_sum(obj){
    var dj = $(obj).val();
    var num = $(obj).parent('td').prev('td').find('input').val();
    $(obj).parent('td').next('td').find('input').val(dj*num);
}
function set_sum1(obj){
    var dj = $(obj).parent('td').next('td').find('input').val();
    var num = $(obj).val();
    $(obj).parent('td').next('td').next('td').find('input').val(dj*num);
}
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
		$('#form_group_materials_name').before('<div style="text-align:center; font-size: 20px; padding: 5px 0;">生产领料单</div>');
		$('#form_group_materials_name').before(str);
		var remark = $("#form_group_remark").find('.form-control-static').text()
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



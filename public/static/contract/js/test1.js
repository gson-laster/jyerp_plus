var danhang = `<tr class="">
		              <td class="">
		                 <input name="pid[]" />         
		              </td>
		              <td class="">
		              	<input name="itemsid[]" />     
		              </td>
		    				  
		              <td class="">
		                <input type="text" name="dw[]"/>
		              </td>
		        			<td class="">
		                <input type="number" name="test_num[]" /> 
		            	</td>
		       			  <td class="">
		                    <input type="number" name="zhdj[]" />
		            	</td>
		       			  <td class="">
		                    <input type="number" name="sum[]" /> 
		              </td>
		        			<td class="">
		                    <input name="bz[]" />
		              </td>
		        			<td><a href="javascript:;" onclick="deltr(this)">删除</a></td>
		   				</tr>`;
$(function () {
    var trbox = '';

    for (var i = 0; i < 5; i ++){
        trbox += danhang;
    }
    var str = `
<button onclick="add()" class="btn" type="button">增行</button>
<table class="table table-builder table-hover table-bordered table-striped js-table-checkable">
                                <thead>
                                    <tr>
                                    
                                                                                <th class="column-id ">
                                            收入
                                          
                                        </th>
                                                                                <th class="column-name ">
                                            
                                            工程明细                                            <span>
                                            
                                                                                        </span>
                                        </th>
                                                                                
                                                                                <th class="column-address ">
                                            
                                            单位                                            <span>
                                            
                                                                                        </span>
                                        </th>
                                                                                <th class="column-zrid ">
                                            
                                            工程量                                            <span>
                                            
                                                                                        </span>
                                        </th>
                                                                                <th class="column-tender_time ">
                                            
                                            综合单价                                            <span>
                                            
                                                                                            <a href="/admin.php/tender/index/index.html?_by=asc&amp;_order=tender_time" data-toggle="tooltip" >
                                                                                                  </a>
                                                                                        </span>
                                        </th>
                                                                                <th class="column-unit ">
                                            
                                            合价                                            <span>
                                            
                                                                                        </span>
                                        </th>
                                                                                <th class="column-contact ">
                                            
                                            备注                                            <span>
                                            
                                                                                        </span>
                                        </th>
                          
                                   
                                                                            </tr>
                                </thead>
                                <tbody>
                                                                ${trbox}
                                                                   
                                                                    </tbody>
                            </table>
                      
`;
    $('#tab-2').append(str);
})

// function sublimt() {
//     var json = {};
//     var input = '';
//     input = $('table input');
//     input.each(function () {
//         var t = $(this);
//         json[t.attr('name')] = t.val();
//     })
//     console.log(json);
// }
function add() {
    $('tbody').append(danhang);
}

function deltr(obj){
    $(obj).parents('tr').remove();
}
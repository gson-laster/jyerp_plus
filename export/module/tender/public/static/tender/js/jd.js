var trbox = '';

trbox += danhang();
function danhang(){
return `<tr class="detail_list">
              <td class="bh">
                             
              </td>                
              <td class="">
                 <input name="bhs[]" />         
              </td>
              <td class="">
                <input name="gcmx[]" />     
              </td>
        <td class="">
                    <input type="text" name="dw[]"/>                                        </td>
        <td class="">
                    <input type="number" name="num[]" value="0"  onBlur="set_sum1(this)"/>                                        </td>
        <td class="">
                    <input type="number" name="dj[]" value="0" onBlur="set_sum(this)"/>                                        </td>
        <td class="">
                    <input type="number" name="sum[]" onclick="get_sum(this);" onBlur="get_sum(this)"/>                                        </td>
        <td><a href="javascript:;" onclick="deltr(this)">删除</a></td>
   
                                                       </tr>`;
                                                       }
    var str = `
<div style="clear:both;padding-left:20px" >
<button onclick="add()" style="margin: 12px;" class="btn" type="button">增行</button>

</div>
<table class="table table-builder table-hover table-bordered table-striped js-table-checkable" style="margin-left:30px">

                                <thead>

                                    <tr>
                                       <th class="column-xh ">
                                            序号
                                          
                                        </th>

                                        <th class="column-id ">
                                            编号
                                          
                                        </th>
                                        <th class="column-name ">
                                            清单子目
                                        </th>
                                         <th class="column-address ">
                                            
                                            单位                                            <span>
                                            
                                                                                        </span>
                                        </th>
                                                                                <th class="column-zrid ">
                                            
                                            工程量                                            <span>
                                            
                                                                                        </span>
                                        </th>
                                                                                <th class="column-tender_time">
                                            
                                            综合单价 
                                        </th>
                                                                                <th class="column-unit ">
                                            
                                            合价                                            <span>
                                            
                                                                                        </span>
                                        </th>
                                                                                <th class="column-btn ">
                                            
                                            操作                                            <span>
                                            
                                                                                        </span>
                                        </th>
                          
                                   
                                                                            </tr>
                                </thead>
                                <tbody>
                                                                ${trbox}
                                                                   
                                                                    </tbody>
                            </table>
                      
`;
    $('#form_group_obj_id').after(str);
    set_bh();

function add() {
    $('tbody').append(danhang());
    set_bh();
}

function deltr(obj){
    $(obj).parents('tr').remove();
    set_bh();
}
function set_bh(){
       var j=1;
       $('.detail_list').each(function(){
        $(this).find('.bh').text(j);
        j++;

    });
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

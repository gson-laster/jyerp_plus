<?php
if (!function_exists('detaillist')) {
    /**
     * 拼接详情内容
     * @param array $data 
     * @param array $lists 数据 
     * @return array
     */
    function detaillist($data,$lists){
            //对象转为数组
          $lists = is_object($lists)? $lists->toArray():$lists;
            $data_list = array();

            foreach ($data as $key => $value) {
                if(!isset($lists[$value[0]])){
                    throw new Exception("$value[0].'字段不存在'");
                }else{
                    $data_list[$key]['name'] = $value[1];

                    if(!empty($value[2]) && isset($value[2])){
                        if(is_array($value[2])){
                            $data_list[$key]['value'] = isset($value[2][$lists[$value[0]]]) ? $value[2][$lists[$value[0]]] : '';
                        }else{
                            switch ($value[2]) {
                                case 'user':									
                                    $data_list[$key]['value'] = get_nickname($lists[$value[0]]);
                                    break;
                                case 'date':
                                    $data_list[$key]['value'] = date('Y-m-d',$lists[$value[0]]);
                                    break;
                                case 'datetime':
                                    $data_list[$key]['value'] = date('Y-m-d H:i:s',$lists[$value[0]]);
                                    break;
                                case 'money':
                                    $data_list[$key]['value'] = '￥'.number_format($lists[$value[0]],2);
                                    break;
                                case 'tel':
                                    $data_list[$key]['value'] = '<a href="tel:'.$lists[$value[0]].'">'.$lists[$value[0]].'</a>';
                                    break;
                                case 'img':
                                	$data_list[$key]['value'] = '<img src='.get_file_path($lists[$value[0]]).' alt="image"/>';
                                	break;
                                default:
                                    $data_list[$key]['value'] = $lists[$value[0]];
                                    break;
                            }
                        }

                    }else{
                        $data_list[$key]['value'] = $lists[$value[0]];
                    }
                }

            }

            return $data_list;
    }
}
if (!function_exists('get_status')) {
    function get_status($status){
            if($status==1){
                  return "审批通过";
            }elseif($status==2){
                  return "审批失败";
            }else{
                  return "等待审批";
            }
    }
}

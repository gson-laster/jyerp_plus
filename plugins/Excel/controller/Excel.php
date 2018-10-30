<?php
// +----------------------------------------------------------------------
// | 海豚PHP框架 [ DolphinPHP ]
// +----------------------------------------------------------------------
// | 版权所有 2016~2017 河源市卓锐科技有限公司 [ http://www.zrthink.com ]
// +----------------------------------------------------------------------
// | 官方网站: http://dolphinphp.com
// +----------------------------------------------------------------------
// | 开源协议 ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------

namespace plugins\Excel\controller;

use app\common\controller\Common;
use think\Db;
require_once dirname(dirname(__FILE__)) . '/PHPExcel/PHPExcel.php';
require_once dirname(dirname(__FILE__)) . '/PHPExcel/PHPExcel/IOFactory.php';

/**
 * Excel控制器
 * @package plugins\Excel\controller
 */
class Excel extends Common
{
    /**
     * 导出Excel
     * @param string $expTitle 文件名称
     * @param array $expCellName 表头
     * @param array $expTableData 数据
     * @param array $mergeCells 合并单元格
     * @author HongPing <hongping626@qq.com>
     * @alter 蔡伟明 <314013107@qq.com>
     */
    public function export($expTitle = '', $expCellName = [], $expTableData = [], $mergeCells = [])
    {
        $expTitle == '' && $this->error('请填写文件名');
        empty($expCellName) && $this->error('请填写表头');

        $file_type = 'xls';
        if (strpos($expTitle, '.')) {
            $file_name = explode('.', $expTitle);
            if (strtolower(end($file_name)) != 'xls' && strtolower(end($file_name)) != 'xlsx') {
                $file_name = $expTitle.'.xls';
            } else {
                $file_type = end($file_name);
                $file_name = $expTitle;
            }
        } else {
            $file_name = $expTitle.'.xls';
        }

        $file_type = strtolower($file_type) == 'xls' ? 'Excel5' : 'Excel2007';

        if (ob_get_length()) ob_end_clean();

        $fileName = $file_name; //or $xlsTitle 文件名称可根据自己情况设定
        $cellNum  = count($expCellName);
        $dataNum  = count($expTableData);

        $objPHPExcel = new \PHPExcel();
        $cellName = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ','BA','BB','BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL','BM','BN','BO','BP','BQ','BR','BS','BT','BU','BV','BW','BX','BY','BZ','CA','CB','CC','CD','CE','CF','CG','CH','CI','CJ','CK','CL','CM','CN','CO','CP','CQ','CR','CS','CT','CU','CV','CW','CX','CY','CZ','DA','DB','DC','DD','DE','DF','DG','DH','DI','DJ','DK','DL','DM','DN','DO','DP','DQ','DR','DS','DT','DU','DV','DW','DX','DY','DZ','EA','EB','EC','ED','EE','EF','EG','EH','EI','EJ','EK','EL','EM','EN','EO','EP','EQ','ER','ES','ET','EU','EV','EW','EX','EY','EZ','FA','FB','FC','FD','FE','FF','FG','FH','FI','FJ','FK','FL','FM','FN','FO','FP','FQ','FR','FS','FT','FU','FV','FW','FX','FY','FZ','GA','GB','GC','GD','GE','GF','GG','GH','GI','GJ','GK','GL','GM','GN','GO','GP','GQ','GR','GS','GT','GU','GV','GW','GX','GY','GZ','HA','HB','HC','HD','HE','HF','HG','HH','HI','HJ','HK','HL','HM','HN','HO','HP','HQ','HR','HS','HT','HU','HV','HW','HX','HY','HZ'];
        //合并单元格
        if (!empty($mergeCells)) {
            foreach ($mergeCells as $mergeCell) {
                $objPHPExcel->getActiveSheet()->mergeCells($mergeCell);
            }
        }

        $objSheet = $objPHPExcel->getActiveSheet();

        for($i=0;$i<$cellNum;$i++){
            $objSheet->setCellValue($cellName[$i].'1', $expCellName[$i][2]);
            //根据内容设置单元格宽度
            $cellWidth = $expCellName[$i][1] == 'auto' ? strlen($expCellName[$i][2]) : $expCellName[$i][1];
            $objSheet->getColumnDimension($cellName[$i])->setWidth($cellWidth);
            // $objPHPExcel->getActiveSheet(0)->getColumnDimension($cellName[$i])->setWidth($expCellName[$i][1]);
            $objSheet->getStyle($cellName[$i])->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);
//            if(isset($expCellName[$i][3])) {
//                switch ($expCellName[$i][3]) {
//                    case 'FORMAT_NUMBER':
//                        $objSheet->getStyle($cellName[$i])->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
//                        break;
//                    case 'FORMAT_TEXT':
//                        $objSheet->getStyle($cellName[$i])->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);
//                        break;
//                }
//            }
        }

        // Miscellaneous glyphs, UTF-8
        for($i=0;$i<$dataNum;$i++){
            for($j=0;$j<$cellNum;$j++){
                if (isset($expTableData[$i][$expCellName[$j][0]])) {
                    $objSheet->setCellValueExplicit($cellName[$j].($i+2), $expTableData[$i][$expCellName[$j][0]], \PHPExcel_Cell_DataType::TYPE_STRING);
                }
//                if (isset($expCellName[$i][3]) && $expCellName[$i][3] == 'FORMAT_TEXT') {
//
//                } else {
//                    $objSheet->setCellValue($cellName[$j].($i+2), $expTableData[$i][$expCellName[$j][0]]);
//                }
            }
        }

        //下载
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");;
        header('Content-Disposition:attachment;filename='.$fileName);
        header("Content-Transfer-Encoding:binary");
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, $file_type);
        $objWriter->save('php://output');
        exit();
    }

    /**
     * 导入Excel
     * @param string $file upload file $_FILES
     * @param null $table 导入到指定表，如果不指定，则返回拼接的数据，不执行导入
     * @param null $fields 字段名 只导入指定字段名的数据，excel表中其他在字段名找不到的，将不导入
     *         传入格式：array('id' => 'id', '用户名' => 'username')，key为数据库字段名，value为excel表头名
     * @param int $type 导入模式，0默认为增量导入（导入并跳过已存在的数据），1为覆盖导入（导入并覆盖已存在的数据）
     * @param null $where 查询依据，做配合查询使用
     * @param null $main_field 作为判断导入模式依据的主要字段，比如指定为KSH，则用KSH这个字段来判断是否已存在数据库
     * @author HongPing <hongping626@qq.com>
     * @alter 蔡伟明 <314013107@qq.com>
     * @return array
     */
    public function import($file, $table = null, $fields = null, $type = 0, $where = null, $main_field = null)
    {
        if(!file_exists($file)){
            return ["error" => 1, 'message' => '文件未找到!']; //file not found!
        }

        $file_info = explode('.', $file);
        $file_ext  = strtolower(end($file_info));
        if ($file_ext != 'xls' && $file_ext != 'xlsx') {
            return ["error" => 1, 'message' => '文件类型不正确!'];
        }

        $file_type = $file_ext == 'xls' ? 'Excel5' : 'Excel2007';

        $objReader = \PHPExcel_IOFactory::createReader($file_type); //需要在前面加反斜杠，因为命名空间
        try{
            $PHPReader = $objReader->load($file);
        }catch(\Exception $e){}

        if(!isset($PHPReader)) return ["error" => 6, 'message' => '读取错误!']; //read error!

        $allWorksheets = $PHPReader->getAllSheets(); //获取所有工作表
        $i = 0;
        $array = [];

        //循环读取每个工作表
        foreach($allWorksheets as $objWorksheet){
            $sheet_name = $objWorksheet->getTitle(); //获取当前工作表名
            $allRow = $objWorksheet->getHighestRow();//当前工作表的行数
            $highestColumn = $objWorksheet->getHighestColumn();//当前工作表的列数，excel以字母表示列数，比如B表示2列
            $allColumn = \PHPExcel_Cell::columnIndexFromString($highestColumn); //当前工作表的列数，将字母表示的列数转换为数字
            $array[$i]["Title"] = $sheet_name;
            $array[$i]["Cols"]  = $allColumn;
            $array[$i]["Rows"]  = $allRow;
            $arr = [];
            $isMergeCell = [];
            foreach ($objWorksheet->getMergeCells() as $cells) {//找出合并单元格
                foreach (\PHPExcel_Cell::extractAllCellReferencesInRange($cells) as $cellReference) {
                    $isMergeCell[$cellReference] = true;
                }
            }

            //循环读取每行数据
            for($currentRow = 1; $currentRow <= $allRow; $currentRow++){
                $row = [];
                for($currentColumn = 0; $currentColumn < $allColumn; $currentColumn++){
                    $cell    = $objWorksheet->getCellByColumnAndRow($currentColumn, $currentRow);
                    $afCol   = \PHPExcel_Cell::stringFromColumnIndex($currentColumn+1); //后一列的索引，比如当前列为A，后一列则为B
                    $bfCol   = \PHPExcel_Cell::stringFromColumnIndex($currentColumn-1); //前一列的索引，如果当前列为第一列，则前一列索引为@
                    $col     = \PHPExcel_Cell::stringFromColumnIndex($currentColumn); //当前列的索引
                    $address = $col.$currentRow;//当前单元格的位置
                    $value   = $objWorksheet->getCell($address)->getValue(); //当前单元格的值
                    $value   = trim($value); //去除首尾空格
                    // 如果当前行的第一个单元格内容为空，则跳过当前行
                    if ($currentColumn == 0 && ($value == '' || $value === NULL)) {
                        break;
                    }
                    if(substr($value,0,1) == '='){//判断单元格的公式不完整的情况
                        return ["error" => 7, 'message' => '无法使用公式!']; //can not use the formula!
                    }

                    //判断单元格是否为数字类型
                    if($cell->getDataType() == \PHPExcel_Cell_DataType::TYPE_NUMERIC){
                        // $cell_style_format=$cell->getParent()->getStyle( $cell->getCoordinate() )->getNumberFormat();
                        $cell_style_format = $cell->getStyle($cell->getCoordinate())->getNumberFormat(); //不需要getParent
                        $format_code = $cell_style_format->getFormatCode();
                        if (preg_match('/^([$[A-Z]*-[0-9A-F]*])*[hmsdy]/i', $format_code)) { //判断是否为日期类型
                            $value = gmdate("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($value)); //格式化日期
                        }else{
                            $value = \PHPExcel_Style_NumberFormat::toFormattedString($value, $format_code); //格式化数字
                        }
                    }
                    //处理该单元格为合并单元格的情况
                    $temp = '';
                    if(isset($isMergeCell[$col.$currentRow]) && isset($isMergeCell[$afCol.$currentRow]) && !empty($value)){
                        $temp = $value;
                    }elseif(isset($isMergeCell[$col.$currentRow]) && isset($isMergeCell[$col.($currentRow-1)]) && empty($value)){
                        $value = $arr[$currentRow-1][$currentColumn];
                    }elseif(isset($isMergeCell[$col.$currentRow]) && isset($isMergeCell[$bfCol.$currentRow]) && empty($value)){
                        $value = $temp;
                    }
                    $row[$currentColumn] = $value;
                }
                //判断一行中的全部单元格是否都有数据
                //如果不是，给出提示
                // if (count($row) != 0 && count($row) != $allColumn) {
                //     return ["error" => 4, 'message' => "工作表【{$sheet_name}】的第【{$currentRow}】行的数据不完整，请填写完整，并重新导入");
                // }

                $arr[$currentRow] = $row; //完整一行的数据
            }
            $array[$i]["Content"] = array_filter($arr);//过滤空行
            $i++;
        }

        unset($objWorksheet);
        unset($PHPReader);
        unset($PHPExcel);
        // unlink($file);

        // 没有指定表名，则直接返回拼接结果
        if ($table === null) {
            return ["error" => 0, "data" => $array];
        }
        if ($fields === null) {
            return ["error" => 2, 'message' => '未指定字段名!'];
        }
        if ($type != 0 && $type != 1) {
            return ["error" => 3, 'message' => '导入模式不正确!'];
        }
        if ($main_field === null) {
            return ["error" => 4, 'message' => '未指定用于判断依据的字段名!'];
        }
        if (!is_string($main_field)) {
            return ["error" => 5, 'message' => '用于判断依据的字段名只能为字符串类型!'];
        }

        //查询已经存在的数据，用于判断导入模式做对比
        $exists_list = Db::name($table)->where($where)->group($main_field)->column($main_field);
        if($table=='admin_user'){
            $organization = db::name('admin_organization')->column('id,title');
            $position = db::name('admin_position')->column('id,title');
            $role = db::name('admin_role')->column('id,name');
        }elseif($table=="supplier_list"){
            $supplier_type = db::name('supplier_type')->column('id,name');
        }elseif($table=="supplier_client"){
            $supplier_clienttype = db::name('supplier_clienttype')->column('id,name');
        }elseif($table=="stock_material"){
            $material_type = db::name('stock_material_type')->column('id,title');
        }
        //整理数据
        $fields    = array_flip($fields); //反转键值
        $data_list = [];
        $dataAdd['list']   = [];
        $dataCover['list'] = [];
        $dataSkip['list']  = [];
        foreach ($array as $key => $value) { //循环每一张工作表
            $firstRow = [];
            foreach ($value['Content'] as $row => $col) { //循环每一行数据
                $data = [];
                if ($row == 1) { //处理excel表的第一行，即表头，用来获取表头与字段名的关系
                    foreach ($col as $index => $val) { //循环每一个单元格
                        if (isset($fields[$val])) {
                            $firstRow[$index] = $fields[$val];
                        }
                    }
                } else { //这里开始是真正需要的数据
                    if (empty($firstRow)) {
                        return ["error" => 8, 'message' => '没有表头数据，无法导入!'];
                    }
                    foreach ($col as $index => $val) { //循环每一个单元格
                        if (isset($firstRow[$index])) {
                            $data[$firstRow[$index]] = trim($val);
                        }
                    }
                    // 判断导入模式
                    if ($type == 0) {//增量导入
                        if (in_array($data[$main_field], $exists_list)) {
                            $dataSkip['list'][] = $data[$main_field]; //记录跳过的数据
                            if($table == 'admin_user'){
                                return ["error" => 11, 'message' => $data[$main_field].' 此用户名已存在'];
                            }elseif($table == 'supplier_list'){
                                return ["error" => 11, 'message' => $data[$main_field].' 此供应商已存在'];
                            }elseif($table == "supplier_client"){
                                return ["error" => 11, 'message' => $data[$main_field].' 此客户已存在'];
                            }elseif($table == "stock_material"){
                                return ["error" => 11, 'message' => $data[$main_field].' 此物资已存在'];
                            }
                            continue;//跳过已存在的考生
                        } else {
                            $dataAdd['list'][] = $data[$main_field]; //记录新增的数据
                            if($table == 'admin_user'){
                                    //性别
                                    switch ($data['sex']) {
                                        case '男':
                                            $data['sex'] = 1;
                                            break;
                                       case '女':
                                            $data['sex'] = 2;
                                            break;
                                        default:
                                            $data['sex'] = '保密';
                                            break;
                                    }
                                    //部门
                                    if(in_array($data['organization'], $organization)){
                                        foreach ($organization as $key => $value) {
                                            if($data['organization']==$value){
                                                $data['organization'] = $key;
                                            }
                                        }
                                    }else{
                                        return ["error" => 12, 'message' => $data['organization'].' 此部门不存在'];
                                    }
                                    //职位
                                    if(in_array($data['position'], $position)){
                                        foreach ($position as $key => $value) {
                                            if($data['position']==$value){
                                                $data['position'] = $key;
                                            }
                                        }
                                    }else{
                                        return ["error" => 13, 'message' => $data['position'].' 此职位不存在'];
                                    }
                                    //管理员角色类型
                                    if(in_array($data['role'], $role)){
                                        foreach ($role as $key => $value) {
                                            if($data['role']==$value){
                                                $data['role'] = $key;
                                            }
                                        }
                                    }else{
                                        return ["error" => 14, 'message' => $data['role'].' 此系统角色不存在'];
                                    }
                                    //编制
                                    switch ($data['is_on']) {
                                    case "定编":
                                        $data['is_on'] = 0;
                                        break;    
                                    case "在职":
                                        $data['is_on'] = 1;
                                        break;    
                                    case "缺职":
                                        $data['is_on'] = 2;
                                        break;     
                                    case "超编":
                                        $data['is_on'] = 3;
                                        break;     
                                    case "兼职":
                                        $data['is_on'] = 4;
                                        break;
                                    default:
                                        $data['is_on'] = 1;
                                        break;
                                    }
                                    $data['status'] = $data['status']=='启用' ? 1 : 0;
                                    $data['birth'] = empty($data['birth']) ? '' : strtotime($data['birth']);
                                    $data['create_time'] = empty($data['create_time']) ? time() : strtotime($data['create_time']);
 
                            }elseif ($table=="supplier_list") {
                                    //职位
                                    if(in_array($data['type'], $supplier_type)){
                                        foreach ($supplier_type as $key => $value) {
                                            if($data['type']==$value){
                                                $data['type'] = $key;
                                            }
                                        }
                                    }else{
                                        return ["error" => 13, 'message' => $data['type'].' 此供应商类别不存在'];
                                    }

                                    $data['wid'] = UID;
                                    $purchas_user = db::name('admin_user')->where('nickname',$data['purchas_user'])->value('id');
                                    if(empty($purchas_user)){
                                        return ["error" => 13, 'message' => $data['purchas_user'].' 我方联络人不存在'];
                                    }else{
                                        $data['purchas_user'] = $purchas_user;
                                    }
                                    $data['create_time'] = empty($data['create_time']) ? time() : strtotime($data['create_time']);
                                    $data['number'] = 'GYS'.date('YmdHis',(strtotime(date('Y-m-d'))+rand(1,1000)+rand(1,rand(1,100))));
                            }elseif($table=="supplier_client"){
                                    if(in_array($data['type'], $supplier_clienttype)){
                                        foreach ($supplier_clienttype as $key => $value) {
                                            if($data['type']==$value){
                                                $data['type'] = $key;
                                            }
                                        }
                                    }else{
                                        return ["error" => 13, 'message' => $data['type'].' 此客户类型不存在'];
                                    }
                                    $data['wid'] = UID;
                                    $data['create_time'] = empty($data['create_time']) ? time() : strtotime($data['create_time']);
                                    $data['number'] = 'KH'.date('YmdHis',(strtotime(date('Y-m-d'))+rand(1,1000)+rand(1,rand(1,100))));

                            }elseif($table=="stock_material"){
                                 if(in_array($data['type'], $material_type)){
                                        foreach ($material_type as $key => $value) {
                                            if($data['type']==$value){
                                                $data['type'] = $key;
                                            }
                                        }
                                    }else{
                                        return ["error" => 13, 'message' => $data['type'].' 此物品类型不存在'];
                                    }
                                    $data['create_time'] = empty($data['create_time']) ? time() : strtotime($data['create_time']);
                                    $data['status'] = $data['status']=='启用' ? 1 : 0;

                            }
                        }

                    } else {//覆盖导入
                        if (in_array($data[$main_field], $exists_list)) {
                            $dataCover['list'][] = $data[$main_field]; //记录覆盖的数据
                            $map[$main_field]    = $data[$main_field];
                            Db::name($table)->where($where)->where($map)->delete();//删除已存在的数据
                        } else {
                            $dataAdd['list'][] = $data[$main_field]; //记录新增的数据
                        }
                    }
                    $data_list[] = $data;
                }
            }
        }
        if ($data_list) {

            if (Db::name($table)->insertAll($data_list)) {

                //计算新增、覆盖、跳过的数量
                $dataAdd['total']   = count($dataAdd['list']);
                $dataCover['total'] = count($dataCover['list']);
                $dataSkip['total']  = count($dataSkip['list']);
                //将新增、覆盖、跳过的数据写入缓存
                cache('dataAdd', $dataAdd);
                cache('dataCover', $dataCover);
                cache('dataSkip', $dataSkip);
                cache('nextUrl', null);
                return ["error" => 0, 'message' => '成功导入 '. count($data_list). ' 条数据。'];
            } else {
                return ["error" => 9, 'message' => '导入失败!请重新导入。'];
            }
        } else {
            return ["error" => 10, 'message' => '上传的文件中，没有需要导入的数据!'];
        }
    }

    /**
     * 导出多工作表Excel
     * @param array $datas 数据，每个数组要包含'xls_name','xls_cell','data_list'
     * @param string $fileName 导出文件名
     * @author 蔡伟明 <314013107@qq.com>
     */
    public function exportMulti($datas = [], $fileName = '')
    {
        if (empty($datas)) {
            $this->error('找不到符合条件的数据');
        }

        $file_type = 'xls';
        if (strpos($fileName, '.')) {
            $file_name = explode('.', $fileName);
            if (strtolower(end($file_name)) != 'xls' && strtolower(end($file_name)) != 'xlsx') {
                $file_name = $fileName.'.xls';
            } else {
                $file_type = end($file_name);
                $file_name = $fileName;
            }
        } else {
            $file_name = $fileName.'.xls';
        }

        $file_type = strtolower($file_type) == 'xls' ? 'Excel5' : 'Excel2007';

        $objPHPExcel = new \PHPExcel();
        $fileName = $file_name == '' ? date('Y-m-d') : $file_name;
        $cellName = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ','BA','BB','BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL','BM','BN','BO','BP','BQ','BR','BS','BT','BU','BV','BW','BX','BY','BZ','CA','CB','CC','CD','CE','CF','CG','CH','CI','CJ','CK','CL','CM','CN','CO','CP','CQ','CR','CS','CT','CU','CV','CW','CX','CY','CZ','DA','DB','DC','DD','DE','DF','DG','DH','DI','DJ','DK','DL','DM','DN','DO','DP','DQ','DR','DS','DT','DU','DV','DW','DX','DY','DZ','EA','EB','EC','ED','EE','EF','EG','EH','EI','EJ','EK','EL','EM','EN','EO','EP','EQ','ER','ES','ET','EU','EV','EW','EX','EY','EZ','FA','FB','FC','FD','FE','FF','FG','FH','FI','FJ','FK','FL','FM','FN','FO','FP','FQ','FR','FS','FT','FU','FV','FW','FX','FY','FZ','GA','GB','GC','GD','GE','GF','GG','GH','GI','GJ','GK','GL','GM','GN','GO','GP','GQ','GR','GS','GT','GU','GV','GW','GX','GY','GZ','HA','HB','HC','HD','HE','HF','HG','HH','HI','HJ','HK','HL','HM','HN','HO','HP','HQ','HR','HS','HT','HU','HV','HW','HX','HY','HZ'];

        //工作表序号
        $sheetIndex = 0;
        foreach ($datas as $key => $data) {
            // $xlsTitle = iconv('utf-8', 'gb2312', $data['xls_name']);//文件名称
            $xlsTitle = $data['xls_name'];//文件名称
            $cellNum  = count($data['xls_cell']);
            $dataNum  = count($data['data_list']);

            //如果当前工作表序号大于0，则创建新表
            if ($sheetIndex > 0) {
                $objPHPExcel->createSheet();
            }

            //设置表头
            for($i = 0; $i < $cellNum; $i++){
                //设置当前活动表，并设置值
                $objPHPExcel->setActiveSheetIndex($sheetIndex)->setCellValue($cellName[$i].'1', $data['xls_cell'][$i][2]);
                //得到当前活动的表
                $objActSheet = $objPHPExcel->getActiveSheet();
                // 给当前活动的表设置名称
                $objActSheet->setTitle($xlsTitle);
                //根据内容设置单元格宽度
                $cellWidth = $data['xls_cell'][$i][1] == 'auto' ? strlen($data['xls_cell'][$i][2]) : $data['xls_cell'][$i][1];
                $objActSheet->getColumnDimension($cellName[$i])->setWidth($cellWidth); //内容自适应
                //设置指定数据类型
                if(isset($data['xls_cell'][$i][3]) && $data['xls_cell'][$i][3] == 'FORMAT_NUMBER'){
                    $objActSheet->getStyle($cellName[$i])->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
                }
            }

            //设置具体内容
            for($i=0; $i < $dataNum; $i++){
                for($j=0; $j< $cellNum; $j++){
                    $objActSheet->setCellValueExplicit($cellName[$j].($i+2), $data['data_list'][$i][$data['xls_cell'][$j][0]], \PHPExcel_Cell_DataType::TYPE_STRING);
//                    $objActSheet->setCellValue($cellName[$j].($i+2), $data['data_list'][$i][$data['xls_cell'][$j][0]]);
                }
            }
            $sheetIndex++;
        }

        //将当前活动表设置为第一个
        $objPHPExcel->setActiveSheetIndex(0);

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");;
        header('Content-Disposition:attachment;filename='.$fileName);
        header("Content-Transfer-Encoding:binary");
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, $file_type);
        if (ob_get_length()) ob_end_clean();
        $objWriter->save('php://output');
        exit();
    }
}
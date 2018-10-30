<?php
namespace app\document\admin;
use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\document\model\Document_list as Document_listModel;
use app\admin\model\Module as ModuleModel;
use app\admin\model\Access as AccessModel;
use think\Db;
use util\Tree;
/**
 * 文档控制器
 * @author HJP
 */
class Index extends Admin
{	
	public function index($pid = 0){		
	$map['pid'] = $pid;
	$map['status'] = 1;
	if($this->request->isPost()){
		$pid = $this->request->post('id');
		$map['pid'] = $pid;
		$map2['id'] = $pid;		
		$data = Document_listModel::where($map)->whereOr($map2)->select();		
		$html = '';	 
		if($data){
			foreach($data as $k => $v){				
				$type = $v['type'] ? $v['type'] : '目录';
				$url = $v['fileid'] == 0 ? $v['name'] : '<a title="点击查看" href="'.$v['path'].'">'.$v['name'].'</a>' ;
				$html .='<tr class="">                                    	
			                <td class="text-center">
			                    <label class="css-input css-checkbox css-checkbox-primary">
			                        <input class="ids" type="checkbox" name="ids[]" value="'.$v['id'].'"><span></span>
			                    </label>
			                </td>
		                    <td>'.$v['id'].'</td>
		                    <td>'.$url.'</td>		                                    	
		                    <td>'.$type.'</td>		                    
		                    <td>'.date('Y-m-d',$v['create_time']).'</td>
		                    <td>
	                            <div class="btn-group">
	                            	<a title="编辑" icon="fa fa-pencil" class="btn btn-xs btn-default" href="'.url('index/edit').'?id='.$v['id'].'" target="_self" _tag="edit" data-toggle="tooltip" data-original-title="编辑"><i class="fa fa-pencil"></i></a>
	                            	<a title="删除" icon="fa fa-times" class="btn btn-xs btn-default ajax-get confirm" href="'.url('index/delete',['ids'=>$v['id']]).'" data-tips="删除文档及目录下的文档无法恢复。" _tag="delete" data-toggle="tooltip" data-original-title="删除"><i class="fa fa-times"></i></a>
	                            </div>
	                        </td>                                                                                      		                                                         
	          			</tr>';
			}				
		}else{
			$html .='<tr class="table-empty">
                        <td class="text-center empty-info" colspan="8">
                            <i class="fa fa-database"></i> 暂无数据<br>
                        </td>
                    </tr>';
		}  
		return $html;		
	}
	$data = Document_listModel::where($map)->select();
	$this->assign('data',$data);
	$this->assign('resulet',self::getOrganization());      
	return $this->fetch(); // 渲染页面
	}
	//获取目录
	public function getOrganization(){
		$organization = Document_listModel::field('id,name as text,pid,type')->select();
		$organizationall = self::getTree(collection($organization)->toArray(),0);
		return json_encode($organizationall);
	}
		//递归目录父子数据
	public function getTree($data, $pid=0){
//		$map['status'] = 1;	
//		$data = Db::name('document_list')->where($map)->field('id,pid,name')->select();
		$tree = array();
		foreach($data as $v){			
			if($v['pid'] == $pid){
				$v['nodes'] = self::getTree($data, $v['id']);	
				if(empty($v['nodes'])) {
				 	$v['nodes'] = '';
				 }			
				unset($v['pid']);				
				array_push($tree, $v);
			}
		}		
		return $tree;		
	}	
	//选择父级目录
	public function docu_only($id = null){
		if ($id === null) $this->error('缺少参数');
		$data = Document_listModel::where('id', $id)->find();		
		return $data;
	}	
	//添加目录
	public function add(){
		if($this->request->isPost()){
			$data = $this->request->post();
			// 验证
            $result = $this->validate($data, 'Document.add');
            // 验证失败 输出错误信息
            if(true !== $result) $this->error($result);           
			if ($model = Document_listModel::create($data)) {
				 //记录行为
            	action_log('document_add', 'document_list', $model['id'], UID, $data['name']);				              
                $this->success('新增成功', url('index'));
            } else {
                $this->error('新增失败');
            }
		}
		$url = url('document/index/docu_only');		
		$html = outhtml();
		$js = outjs($url,self::getOrganization());
		return ZBuilder::make('form')
		->addFormItems([
			['hidden', 'pid'],
			['hidden', 'uid', UID],
			['text', 'name', '目录名称', '<span class="text-danger">必填</span>'],
			['text', 'pidname', '父级目录', '<span class="text-danger">必填</span>'],
			['radio', 'status', '状态', '',[0=>'停用',1=>'启用'],1],
		])
		->setExtraHtml($html)
		->setExtraJs($js)		
		->fetch();
	}
	//上传文档
	public function upfile(){
		if($this->request->isPost()){
			$data = $this->request->post();
			$data['name'] = get_file_name($data['fileid']);
			$data['path'] = get_file_path($data['fileid']);
			$map['status'] = 1;
			$map['id'] = $data['fileid'];
			$info = Db::name('admin_attachment')->where($map)->field('ext')->find();
			$data['type'] = $info['ext'];
			// 验证
            $result = $this->validate($data, 'Document.upfile');
            // 验证失败 输出错误信息
            if(true !== $result) $this->error($result);
			if ($model = Document_listModel::create($data)) {
				//记录行为
            	action_log('document_upfile', 'document_list', $model['id'], UID);				              
                return $this->success('上传成功', url('index'));
            } else {
                return $this->error('上传失败');
            }
		}				
		$url = url('document/index/docu_only');		
		$html = outhtml();
		$js = outjs($url,self::getOrganization());
		return ZBuilder::make('form')
		->addFile('file', '选择文档')
		->addFormItems([	
			['hidden', 'pid'],
			['hidden', 'fileid'],
			['hidden', 'uid', UID],		
			['text', 'pidname', '父级目录', '<span class="text-danger">必填</span>'],			
			['radio', 'status', '状态', '',[0=>'停用',1=>'启用'],1],
		])	
		->setExtraHtml($html)
		->setExtraJs($js)   	   
	    ->fetch();
	}
	//删除
	public function delete($ids = null){		
		if($ids == null) $this->error('参数错误');
		$map['id'] = $ids;
		$map2['pid'] = $ids;
		if($model = Document_listModel::where($map)->whereOr($map2)->delete()){	
			//记录行为
        	action_log('document_delete', 'document_list', $ids, UID);			
			$this->success('删除成功');
		}else{
			$this->error('删除失败');
		}		
	}
	//获取父级目录名称
	public function get_pearet_name()
	{
		$result = array();
		$map['status'] = ['egt', 1];
		$data = Db::name('document_list')->where($map)->select();
		$result['0'] = '顶级目录';
		$data = Tree::config(['title' => 'name'])->toList($data);
		foreach ($data as $role) {
            $result[$role['id']] = $role['title_display'];
       }       
		return $result;
	}
	//编辑
	public function edit($id = null){
		if($id == null) $this->error('参数错误');
		if($this->request->isPost()){
			$data = $this->request->post();
		if($model = Document_listModel::update($data)){
			//记录行为
        	action_log('document_edit', 'document_list', $model['id'], UID);
			$this->success('修改成功', url('index'));
		}else{
			$this->error('修改失败');
		}
		}
		$name = $this->get_pearet_name();		
		$info = Document_listModel::where('id',$id)->find();
		$pid = $info['pid'];
		$url = url('document/index/docu_only');		
		$html = outhtml();
		$js = outjs($url);
		return ZBuilder::make('form')		
		->addFormItems([	
			['hidden', 'id'],
			['hidden','pid'],
			['hidden', 'uid', UID],
			['text', 'name', '名称'],
			['text', 'pidname', '父级目录','',$name[$pid]],
			['radio', 'status', '状态', '',[0=>'停用',1=>'启用'],1],
		])	
		->setExtraHtml($html)
		->setExtraJs($js) 
		->setFormData($info)  	   
	    ->fetch();
	}
	
	
}

<?php
class ctl_class extends lib_controller{

    public function index(){
        if($this->post['dopost'] == 'updateordernum'){
            $ordernum = $this->post['ordernum'];
            $mdl = core::i('mdl_class');
            foreach($ordernum as $id => $udata){
                $filter = array('id'=>$id);
                $mdl->update($udata,$filter);
            }
            $this->msg('succ');
        }
        core::i('lib_class')->getHtml(0,$html);

        $this->assign('html',$html);
        $this->display('class.html');
    }

    public function edit(){
        $parent = $_GET['parent'];
        $id = $_GET['id'];
        $data = core::i('mdl_class')->getOne($id);
        $select = $parent;

        if($this->post['dopost'] == 'edit'){
            $postdata = $this->post['class'];
            if($postdata['parent'] == $postdata['id']){
                $this->msg('fail','错误信息：父级栏目选择错误。');
                $postdata['parent'] = $data['parent'];
                $select = $postdata['id'];
            }elseif($postdata['title'] == ''){
                $this->msg('fail','错误信息：栏目名称不能为空。');
            }else{
                $postdata['fields'] = serialize($postdata['fields']);
                $rs = core::i('mdl_class')->save($postdata);
                $this->msg('succ');
                $select = $postdata['parent'];
            }
            $data = $postdata;
        }
        if($data){
            $parent = $data['parent'];
            $select = $data['parent'];
            $data['fields'] = unserialize($data['fields']);
        }
        if($parent == ''){
            $topParent = 0;
        }else{
            $topParent = core::i('lib_class')->getTopParent($parent);
        }
        core::i('lib_class')->getOptions($topParent,$select,$options);
        $fields = core::i('mdl_content')->fields;

        $this->assign('fields',$fields);
        $this->assign('parent',$parent);
        $this->assign('data',$data);
        $this->assign('options',$options);
        $this->display('class_edit.html');
    }

    public function delete(){
        if($this->get['id']){
            $id = $this->get['id'];
            $rs = core::i('mdl_class')->delete($id);
            $this->msg($rs['status'],$rs['msg']);
        }
        $this->index();
    }

}
?>
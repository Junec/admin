<?php
class mdl_class extends core_model{

    public $table = 'class';

    public function delete($id){
        $result = array('status'=>'succ');
        #判断栏目下是否存在子栏目 存在不可删
        $issc = $this->count(array('parent'=>$id));
        if($issc>0){
            $result['status'] = 'fail';
            $result['msg'] = '该栏目下存在子栏目,删除失败！';
            return $result;
        }
        $data = $this->getOne(array('id'=>$id));
        if($data['isdel'] == 'false'){
            $result['status'] = 'fail';
            $result['msg'] = '该栏目不允许删除,删除失败！';
            return $result;
        }
        
        if($result['status'] == 'succ'){
            parent::delete(array('id'=>$id));
        }
        return $result;
    }
}
?>
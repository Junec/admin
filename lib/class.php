<?php
class lib_class{
    public function __construct(){
        $this->classModel = core::i('mdl_class');
        $this->contentModel = core::i('mdl_content');
    }
    
    public function getIds($parent,&$ids = array()){
        $filter = array('parent'=>$parent);
        $childs = $this->classModel->getList('id',$filter,0,-1,'ordernum ASC');
        foreach($childs as &$v){
            $ids[] = $v['id'];
            $this->getIds($v['id'],$ids);
        }
    }

    public function getHtml($parent,&$html = '',$recursiveNum = 0,&$tmp = array()){
        $filter = array('parent'=>$parent);
        $childs = $this->classModel->getList('id,parent,title,ordernum',$filter,0,-1,'ordernum ASC');
        $childsCount = count($childs);
        foreach($childs as $k=>$v){
            $html .= '<tr>';
            $html .= '<td><input type="text" name="ordernum['.$v['id'].'][ordernum]" value="'.$v['ordernum'].'" class="class-list-input" style="width:27px;"></td>';
            $html .= '<td>'.$v['id'].'</td>';
            $html .= '<td>';

            $tmp[$v['id']] = $recursiveNum;
            for($i=0;$i<$recursiveNum;$i++){
                $html .= '<span class="cl1"></span>';
            }
            if(($k+1) == $childsCount){
                $html .= '<span class="cl3"></span>';
            }else{
                $html .= '<span class="cl2"></span>';
            }
            if($v['parent'] == 0){
                $this->getIds($v['id'],$ids);
                $filter = array('parent'=>$ids);
            }else{
                $filter = array('parent'=>$v['id']);
            }
            $contentCount = $this->contentModel->count($filter);
            unset($ids,$filter);
            $html .= $v['title'].' <span class="labels labels-defa">['.$contentCount.'篇]</span></td>';
            $html .= '<td><a href="?ctl=class&act=edit&id='.$v['id'].'">编辑</a> <a href="javascript:;" onclick="deleteClass('.$v['id'].')">删除</a></td>';
            $html .= '</tr>';
            
            $tmp[$v['id']]++;
            $this->getHtml($v['id'],$html,$tmp[$v['id']],$tmp);
        }
        unset($recursiveNum,$tmp);
    }

    public function getOptions($parent,$select = '',&$options = '',$isTop = true,$recursiveNum = 0,&$tmp = array()){
        if($recursiveNum == 0 && !$tmp && $isTop == true){
            if($parent == 0){
                $options .= '<option value="0">==主栏目==</option>';
            }else{
                $optionsTmp = $this->classModel->getOne($parent);
                $options .= '<option value="'.$optionsTmp['id'].'"';
                if($optionsTmp['id'] == $select) $options .= ' selected';
                $options .= '>=='.$optionsTmp['title'].'==</option>';
            }
        }
        $filter = array('parent'=>$parent);
        $childs = $this->classModel->getList('id,title,ordernum',$filter,0,-1,'ordernum ASC');
        $childsCount = count($childs);
        foreach($childs as $k=>$v){
            $prefix = '';
            $tmp[$v['id']] = $recursiveNum;
            for($i=0;$i<$recursiveNum;$i++){
                $prefix .= '　';
            }
            if(($k+1) == $childsCount){
                $prefix .= '└';
            }else{
                $prefix .= '├';
            }
            $options .= '<option value="'.$v['id'].'"';
            if($v['id'] == $select) $options .= ' selected';
            $options .= '>'.$prefix.'─ '.$v['title'].'</option>';

            $tmp[$v['id']]++;
            $this->getOptions($v['id'],$select,$options,$isTop,$tmp[$v['id']],$tmp);
        }
        unset($recursiveNum,$tmp);
    }


    public function getTopParent($id = ''){
        if($id == 0) return $id;
        $tmp = $this->classModel->getOne($id);
        if($tmp['parent'] == 0){
            return $tmp['id'];
        }else{
            return $this->getTopParent($tmp['parent']);
        }
    }

    
}
?>
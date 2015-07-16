<?php
class ctl_content extends lib_controller{

    public function index(){
        $parent = $this->get['parent'];
        core::i('lib_class')->getIds($parent,$parentIds);
        $parentData = $this->model['class']->getOne($parent);
        $sonClass = $this->model['class']->getList('*',array('parent'=>$parent),0,-1,'ordernum ASC');
        $model = new mdl_content;

        foreach($sonClass as $v){
            $tag[] = array('title'=>$v['title'],'filter'=>array('parent'=>$v['id']));
        }

        $this->finder(array(
            'title' => $parentData['title'],
            'model' => $model,
            'ischeckbox' => true,
            'orderby' => 'id desc',
            'pagenums' => 5,
            'baseurl' => "index.php?ctl=content&act=index&parent={$parent}",
            'basefilter' => array(
                'parent' => $parentIds,
            ),
            'button' => array(
                array(
                    'title' => '添加',
                    'href' => "#",
                ),
                array(
                    'title' => '置顶',
                    'href' => "javascript:finder.submit('index.php?ctl=content&act=delete');",
                ),
                array(
                    'title' => '推荐',
                    'href' => "javascript:finder.submit('index.php?ctl=content&act=delete');",
                ),
                array(
                    'title' => '删除',
                    'href' => "javascript:finder.submit('index.php?ctl=content&act=delete');",
                ),

            ),
            'tag' => $tag,
        ));
    }

    public function delete(){
        if($this->post){
            $this->result('succ','错误信息：删除失败' );
        }
    }

}
?>
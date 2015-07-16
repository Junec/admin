<?php
class mdl_content extends core_model{

    public $table = 'content';
    public $fields = array(
        'subtitle' => '副标题',
        'entitle' => '英文标题',
        'summary' => '概述',
        'content' => '详细内容',
        'auther' => '作者',
        'source' => '来源',
        'kewords' => '关键词',
        'description' => '关键词描述',
        'linkto' => '链接地址',
        'price' => '价格',
    );

    /*Finder相关设置*/
    public $finder = array(
        'id' => array(
            'title' => 'ID',
            'width' => 40,
            'sort' => true,
        ),
        'parent' => array(
            'title' => '栏目',
            'width' => 100,
            'sort' => true,

        ),
        'title' => array(
            'title' => '标题',
            'width' => '',
            'sort' => false,
            'search' => 'input',
            'value' => '',
        ),
        'hits' => array(
            'title' => '点击率',
            'width' => 50,
            'sort' => true,
        ),
        'createtime' => array(
            'title' => '发布时间',
            'width' => 110,
            'sort' => true,
        ),
        'status' => array(
            'title' => '状态',
            'width' => 40,
            'sort' => true,
            'search' => 'select',
            'value' => array(
                '0' => '关闭',
                '1' => '正常',
                '2' => '置顶',
                '3' => '推荐',
            ),
        ),
        'options' => array(
            'title' => '操作',
            'width' => 80,
            'type' => 'extend',
        ),
    );

    public function finder_modify_title($row = '',$id){
        return '<a href="#id='.$id.'">'.$row.'</a>';
    }

    public function finder_modify_parent($row = ''){
        $class = core::i('mdl_class')->getOne($row);
        return $class['title'];
    }

    public function finder_modify_status($row = ''){
        return $this->finder['status']['value'][$row];
    }

    public function finder_modify_createtime($row = ''){
        return date('Y-m-d H:i',$row);
    }

    public function finder_extend_options($id){
        $html = '<a href="#'.$id.'">编辑</a> <a href="#'.$id.'">上移</a> <a href="#'.$id.'">下移</a>';
        return $html;
    }

    /*Finder相关设置*/
}
?>
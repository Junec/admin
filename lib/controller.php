<?php
class lib_controller extends core_controller{

	protected $model = array();
    protected $theme = 'default';
	
	public function __construct(){
		parent::__construct();
		

        $this->__setdefine();
        $this->__setmodel();
	}

    private function __setdefine(){
        define('STATIC_DIR',core::getConfig('web_dir').'/static');


        $this->assign('STATIC_DIR',STATIC_DIR);
        $this->assign('_GET',$this->get);
        $this->assign('_POST',$this->post);
    }

    private function __setmodel(){
        $this->model['class'] = core::i('mdl_class');
        $this->model['content'] = core::i('mdl_content');
    }

    protected function fetch($tpl = ''){
        return parent::fetch($this->theme.'/'.$tpl);
    }

    protected function display($tpl = ''){
        echo $this->fetch($tpl);
    }

    protected function msg($status = '',$msg = ''){
        if($msg == '') $msg = '提示信息：操作成功。';
        $this->MSG_RESULT = array('status'=>$status,'msg'=>$msg);
        $this->assign('MSG_RESULT',$this->MSG_RESULT);
    }

    protected function finder($params = array()){
        $model = $params['model'];
        $pager = core::i('core_pager');

        #配置信息
        $finder = array();
        $finder['title'] = $params['title'];
        $finder['ischeckbox'] = $params['ischeckbox'];
        $finder['isdelete'] = $params['isdelete'];
        $finder['orderby'] = $params['orderby'];
        $finder['pagenums'] = $params['pagenums'];
        $finder['basefilter'] = $params['basefilter'];
        $finder['button'] = $params['button'];
        $finder['pkey'] = $model->pri;
        $finder['url'] = core::i('core_url')->getUrl();
        $finder['queryParams'] = core::i('core_url')->getQueryParams();
        $finder['model'] = get_class($params['model']);
        $finder['baseurl'] = $params['baseurl'];
        $finder['tag'] = $params['tag'];

        #finder搜索
        if($this->post['dopost'] == 'finderSearch'){
            $searchPost = $this->post;
            unset($searchPost['dopost']);
            $searchfilter = array();
            if($searchPost['finderse']){
                if(isset($searchPost['finderse']['select']) && is_array($searchPost['finderse']['select'])){
                    foreach($searchPost['finderse']['select'] as $fsk=>$fsv){
                        $searchfilter['filter'][$fsk] = $fsv;
                    }
                }
                if(isset($searchPost['finderse']['input']) && $searchPost['finderse']['input-value'] != ''){
                    $searchfilter['filter'][$searchPost['finderse']['input']] = $searchPost['finderse']['input-value'];
                }
                $searchfilter['finderseiv'] = $searchPost['finderse']['input-value'];
            }
            $queryParams = array_merge($finder['queryParams'],$searchfilter);
            $redirectUrl = 'index.php?'.core::i('core_url')->parseQueryString($queryParams);
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: {$redirectUrl}");
            exit;
        }

        #显示字段
        $finder['fields'] = array();
        $finder['search'] = array();
        $finder['totalFieldWidth'] = 0;
        $finder['fieldsinfo'][$model->pri] = '`'.$model->pri.'`';

        $extend = array();
        foreach($model->finder as $k=>$v){
            $finder['fields'][$k] = array(
                'title' => $v['title'],
                'width' => $v['width'],
                'sort' => $v['sort'],
            );

            if($v['type'] == '' || $v['type'] == 'normal'){
                #查询字段
                $finder['fieldsinfo'][$k] = '`'.$k.'`';
                #搜索字段
                if($v['search'] != ''){
                    $finder['search'][$v['search']][$k]['title'] = $v['title'];
                    $finder['search'][$v['search']][$k]['value'] = $v['value'];
                    if($v['search'] == 'input'){
                        $finder['search'][$v['search']][$k]['key'] = $k.'|like';
                    }else{
                        $finder['search'][$v['search']][$k]['key'] = $k;
                    }
                }
            }
           
            #扩展列
            if($v['type'] == 'extend'){
                $extend[] = $k;
            }
            $finder['totalFieldWidth']+=$v['width'];
        }
        

        #排序相关
        $get = $this->get;
        $orderby = $get['orderby'];
        $orderbytype = $get['orderbytype'];
        unset($get['orderby'],$get['orderbytype']);
        $finder['orderbyQueryString'] = core::i('core_url')->parseQueryString($get);
        if($orderby != '' && $orderbytype != ''){
            $finder['orderby'] = $orderby.' '.$orderbytype;
        }

        #Tag
        if(isset($finder['tag']) && is_array($finder['tag'])){
            foreach($finder['tag'] as $k=>$v){
                $tagFilter = array_merge($finder['basefilter'],$v['filter']);
                $finder['tag'][$k]['count'] = $model->count($tagFilter);
                $finder['tag'][$k]['url'] = $finder['baseurl'].'&findertag='.$k;
            }
        }

        #Filter
        $getfilter = $this->get['filter'];
        $finder['findertag'] = $_GET['findertag'];
        foreach($getfilter as $k=>$v){
            if($v == '') unset($getfilter[$k]);
        }
        if(!is_array($getfilter) || !$getfilter) $getfilter = array();
        $finder['filter'] = array_merge($finder['basefilter'],$getfilter);
        if($finder['findertag']!=''){
            if(isset($finder['tag']) && is_array($finder['tag'])){
                $tagfilter = $finder['tag'][$finder['findertag']]['filter'];
            }
            $finder['filter'] = array_merge($finder['filter'],$tagfilter);
        }
        $finder['count'] = $model->count($finder['filter']);

        #数据
        $pagerParams = array(
            'record' => $finder['count'],
            'limit' => $finder['pagenums'] != ''?$finder['pagenums']:20,
        );
        $finder['pager'] = $pager->pageList($pagerParams);
        $finder['data'] = $model->getList(join(',',$finder['fieldsinfo']),$finder['filter'],$finder['pager']['offset'],$finder['pager']['limit'],$finder['orderby']);
        $finder['jsonFilter'] = json_encode($finder['filter']);

        #扩展与修饰列
        foreach($finder['data'] as &$v){
            $pkeyId = $v[$finder['pkey']];
            //extend
            foreach($extend as $field){
                $extendMethod = 'finder_extend_'.$field;
                if( method_exists($model,$extendMethod) ){
                    $v[$field] = $model->$extendMethod($pkeyId);
                }
            }
            
            //modify
            foreach($v as $field=>$value){
                $modifyMethod = 'finder_modify_'.$field;
                if( method_exists($model,$modifyMethod) ){
                    $v[$field] = $model->$modifyMethod($value,$pkeyId);
                }
            }
        }
        #print_r($finder['search']);exit;
        $this->assign('finder',$finder);
        $this->display('finder.html');
    }

}
?>
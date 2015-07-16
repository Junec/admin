<?php
class ctl_index extends lib_controller{

    public function index(){

    	$menus = $this->model['class']->getList('*',array('parent'=>0),0,-1,'ordernum ASC');

        $this->assign('menus',$menus);
        $this->display('index.html');

    }

    public function api(){
        echo json_encode(array(1,2,3,4,5));

    }

    public function test(){
        
    	$db = core_database_client::factory(
    		'mysql',
    		array(
				'server' => 'lnmp',
				'username' => 'root',
				'password' => '...',
				'port' => 3306,
				'database' => 'mysql'
			)
    	);


    	#$res = $db->select('user','*');

		#print_r($res);   


        $request = core_request_client::factory('curl',3);
        $res = $request->get('http://lnmp/s.php?id=1&type=2');
        echo '|';
        print_r(($res));
        echo '|';


        $cache = core_cache_client::factory('filesystem',array(
            'cache_dir' => 'd:/',
        ));
        $cache = core_cache_client::factory('memcache',array(
            'host' => '191.101.9.232:11212',
        ));
        $res = $cache->set('test',array(1,2,3));
        $res = $cache->get('test');
        #var_dump($res);


        #sleep(2);

        $this->assign('a1','<b>this is a1</b>');
        $this->assign('a2',array('this','is','a2'));
        $this->assign('a3',time());
        $this->display('test.html');

    }




    public function token(){
        $time = '';
    }

}
?>
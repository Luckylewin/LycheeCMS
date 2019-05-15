<?php

class AdminController extends Plugin {
    
    public function __construct() {
        parent::__construct();
        //Admin控制器进行登录验证
        if (!$this->session->is_set('user_id') || !$this->session->get('user_id')) $this->adminMsg('请登录以后再操作', url('admin/login'));
        App::auto_load('fields'); //加载字段操作函数库
    }
    
     /**
     * 广告位置
     */
    public function indexAction()
    {
        return $this->alistAction();
    }
    
    /**
     * 添加广告位
     */
    public function addAction()
    {

        if ($this->isPostForm()) {

            $data = $this->post('data');

            unset($data['id']);

            $data['adname'] or $this->adminMsg('广告位名称不能为空');

            if (empty($data['width']) || empty($data['height'])) {
                $this->adminMsg('广告位高宽不能为空！');
            }
                    $this->myblock->insert($data);
                    $this->adminMsg('操作成功',true, url('myblock/admin'));
         }

        $this->display('admin_add');
    }
  
  /**
     * 修改广告位
     */
    public function editAction() {
        $id   = $this->get('id');
        $data = $this->myblock->find($id);
        if (empty($data)) $this->adminMsg('广告位不存在');
        if ($this->isPostForm()) {
            unset($data);
            $data = $this->post('data');
            $data['adname'] or $this->adminMsg('广告位名称不能为空');
      if (empty($data['width']) || empty($data['height'])) $this->adminMsg('广告位高宽不能为空！');
            $this->myblock->update($data, 'id=' . $id);
            $this->adminMsg('操作成功',true, url('myblock/admin'));
        }
        $this->assign(array(
            'data'   => $data,
        ));
        $this->display('admin_add');
    }
  
  /**
     * 广告管理
     */
  public function alistAction() {
      if ($this->isPostForm()) {
        if ($this->post('form') == 'del') {
        $data = $this->post('data');
        if ($data && is_array($data)) {
          foreach ($data as $id) {
            if ($id) {
              $this->myblock_data->delete('id=' . $id);
            }
          }
        }
      } elseif ($this->post('form') == 'order') {
        $data = $this->post('order');
        if ($data && is_array($data)) {
          foreach ($data as $id=>$order) {
            if ($id) {
              $this->myblock_data->update(array('listorder'=>$order), 'id=' . $id);
             }
            }
          }
        }
      }

      $aid  = 1;
      $page = (int)$this->get('page');
      $page = (!$page) ? 1 : $page;
      $pagelist = $this->instance('pagelist');
      $pagelist->loadconfig();
      $total    = $this->myblock_data->count('myblock_data', 'id', 'aid=' . $aid);
      $pagesize = 20;
      $url      = url('myblock/admin/alist', array('page'=>'{page}'));
      $data     = $this->myblock_data->page_limit($page, $pagesize)->where('aid=' . $aid)->order('listorder ASC,addtime DESC')->select();
      $pagelist = $pagelist->total($total)->url($url)->num($pagesize)->page($page)->output();
      $cat = get_category_data();
        foreach ($data as $key => $value) {
            foreach ($cat as $k => $c) {
                if($c['catid']==$value['catid'])
                {
                    if($c['parentid']!=0)
                    {
                       $parent = getParentData($value['catid']);
                       $pinfo = $parent? $parent['catname'] . '-' : '';
                       $data[$key]['catname'] = $pinfo.$c['catname'];
                    }else
                    {
                       $data[$key]['catname'] = $c['catname']; 
                    }
                    
                
                    
                }
            }
        } 
        $this->assign(array(
          'list'     => $data,
          'pagelist' => $pagelist,
          'aid'      => $aid,
          'type'     => $this->getType(),
      ));
      $this->display('admin_alist');
    }
  
  /**
     * 添加广告
     */
  public function addaAction() {

      $aid = (int)$this->get('aid');

        if ($this->isPostForm()) {  
            $data  = $this->post('data');

            $catid = $_POST['data']['catids'];

            @$data['catid'] = isset($catid[1])&&$catid[1]? $catid[1] : $catid[0] ;
            if($data['catid']==0) $this->adminMsg('添加失败，请重新添加'); 
            $catinfo = getSingleCat($data['catid']);
           // if($data['catid']=='-1'&&empty($data['name'])) $this->adminMsg('不绑定栏目时名称不能为空！');
            $data['name']   =  $data['name']?$data['name']:$catinfo['catname'];
            $data['typeid'] =  $data['typeid']?$data['typeid']:1;
            $data['addtime']   = time();
            $data['aid']       = 1;

            $setting           = array();
            foreach ($data as $name=>$t) {
                if (substr($name, 0, 7) == 'setting') {
                   $setting[$name] = $t; 
                }
            }

            $this->myblock_data->insert($data);

            $this->adminMsg('操作成功',true, purl('admin/alist', array('aid'=>$aid)));
        }
        //获取栏目
        $cat = getCattree(0,0);
        

    $this->assign(array(
            'type' => $this->getType(),
          'action' => 'add',
            'aid'  => $aid,
            'cat'  => $cat,
      ));
        $this->display('admin_adda');
    }
    
    public function ajaxgetcatAction()
    {
         $catid = (int)$this->get('catid');
         $sid = (int)$this->get('siteid');
         $data =  getCatNav($catid);
         foreach ($data as $key => $value) {
             $cat[] = array('catid'=>$value['catid'],'catname'=>$value['catname'],'child'=>$value['child']);
         }
         $cat = empty($cat)?array('root'=>''):array('root'=>$cat);
         echo json_encode($cat);
    }
  /**
     * 修改广告
     */
    public function editaAction() {
        $id   = $this->get('id');
        $data = $this->myblock_data->find($id);
        if (empty($data)) $this->adminMsg('广告不存在');
        $aid  = $data['aid'];
        if ($this->post('submit')) {
            unset($data);
            $data = $this->post('data');

            $result = $this->db->update('fn_myblock_data', $data , array('id' => $id));

            $this->adminMsg('操作成功',true, purl('admin/alist', array('aid'=>$aid)));

        }

        $list    = $this->getType();
        $type    = $list[$data['typeid']]['fields'];
        if (empty($type)) $this->adminMsg('广告类型不存在');
        $setting = string2array($data['setting']);
        $fields  = $this->getFields(array('data'=>$type), $setting);

        $cat = getCattree(1,0);

        $this->assign(array(
            'actions' => 'edit',
            'cat'     => $cat,
            'data'    => $data,
            'type'    => $list,
            'fields'  => $fields,
            'aid'     => $data['aid']
        ));
        
        $this->display('admin_edit');
    }
    
  /**
     * 删除广告位
     */
    public function delAction()
    {
        $id = $this->get('id');
        $this->myblock->delete('id=' . $id);
        $this->myblock_data->delete('aid=' . $id);
        $this->adminMsg('操作成功', true,url('myblock/admin'));
    }
    
  /**
     * 禁用广告
     */
    public function disabledAction()
    {
        $id   = $this->get('id');

        $data = $this->myblock_data->find($id);

        if (empty($data)) $this->adminMsg('广告不存在');

        $set  = $data['disabled'] ? 0 : 1;

        $this->myblock_data->update(array('disabled'=>$set), 'id=' . $id);

        $this->adminMsg('操作成功', true,url('myblock/admin/alist', array('aid'=>$data['aid'])));
    }
    
    /**
   * 加载调用代码
   */
  public function ajaxviewAction()
  {
        $id   = $this->get('id');
        $data = $this->myblock->find($id);
      if (empty($data)) exit('该广告(#' . $id . ')不存在');
      $msg  = "<style>body{text-align:center;}</style><textarea id='ads_" . $id . "' style='font-size:12px;width:100%;height:90px;overflow:hidden;'>";
      $msg .= "<!-- 广告:" . $data['adname'] . " 开始-->" . PHP_EOL;
      $msg .= "<script language=\"javascript\" src=\"" . url('myblock/index/get', array('id'=>$id)) . "\"></script>" . PHP_EOL;
      $msg .= "<!-- 广告:" . $data['adname'] . " 结束-->";
      $msg .= "</textarea>";
      echo $msg;
  }
  
  /**
   * 缓存
   */
  public function cacheAction()
  {
      $list = $this->myblock->findAll();
      $list = [['id'=>1]];
      $data = array();
      foreach ($list as $t) {
          $data[$t['id']] = $t;
      $data[$t['id']]['data'] = $this->myblock_data->where('aid=' . $t['id'])->order('listorder ASC,addtime DESC')->select();
      }
      $this->cache->set('myblock', $data);
      $this->adminMsg('广告缓存更新成功',true);
  }
    
    /**
     * 广告类型
     */
    private function getType()
    {
        $list    = array();
        $list[1] = array(
            'name'   => '图片广告',
            'fields' => array(
                array('field'=>'setting_thumb', 'name'=>'上传图片', 'tips'=>'', 'formtype'=>'image', 'setting'=>"array('size'=>'300')", 'isshow'=>1),
                array('field'=>'setting_url',   'name'=>'链接地址', 'tips'=>'', 'formtype'=>'input', 'setting'=>"array('size'=>'300')", 'isshow'=>1),
            ),
        );
        $list[2] = array(
            'name'   => '代码广告',
            'fields' => array(
                array('field'=>'setting_content', 'name'=>'广告代码', 'tips'=>'', 'formtype'=>'textarea', 'setting'=>"array('width'=>430,'height'=>130)", 'isshow'=>1),
            ),
        );
        return $list;
    }
    
    /**
     * 动态调用广告类型字段
     */
    public function ajaxfieldAction()
    {
        $tid    = $this->get('tid');
        $list   = $this->getType();
        $fields = $list[$tid]['fields'];
        if (empty($fields)) exit('');
        echo $this->getFields(array('data'=>$fields));
    }

    /**
     * 一键绑定一级栏目
     * @return bool
     */
    public function floodbindAction()
    {
       $aid = (int)$this->get('aid');
       if ($this->post('submit')) {
           $data = $this->post('data');
           $cats = get_category_data();
           foreach ($cats as $key => $cat) {
                if ($cat['parentid']==0 && $cat['ismenu']) {
                     $data['typeid']    = $data['typeid']?$data['typeid']:1;
                     $data['addtime']   = time();
                     $data['catid']     = $cat['catid'];
                     $data['aid']       = $aid;
                     $temp = $data;
                     $temp['name'] = $cat['catname'];
                     $setting = array();
                     foreach ($temp as $name=>$t) {
                        if (substr($name, 0, 7) == 'setting') {
                           $setting[$name] = $t;
                        }
                    }
                    $temp['setting']   = array2string($setting);
                    $this->myblock_data->insert($temp);
               }
        }

        return $this->adminMsg('操作成功',true, url('myblock/admin'));
    } else {
           $this->assign(array('type' => $this->getType()));
           $this->display('admin_addb');
    }
     
}

    /**
     * 开发批量导入
     */
    public function floodAction()
    {   
        $aid = (int)$this->get('aid');
         if ($this->post('submit')) {
        $data = $this->post('data');
        $names = explode("\r\n",$data['name']);
        
        $data['typeid']    =  $data['typeid']?$data['typeid']:1;
        $data['addtime']   = time();
        $data['catid']   = '';
        $data['aid']       = $aid;
        
        // $this->myblock_data->insert($data);
       foreach ($names as $key => $value) {
          $temp = $data;
          $temp['name'] = $value;
          $setting = array();
          foreach ($temp as $name=>$t) {
            if (substr($name, 0, 7) == 'setting') {
               $setting[$name] = $t; 
            }
        }
        $temp['setting']   = array2string($setting);
        $this->myblock_data->insert($temp);
        }

        return $this->adminMsg('操作成功', true, url('myblock/admin'));

      }

         $this->assign(array('type' => $this->getType()));
         $this->display('admin_flood');
    }
  
}
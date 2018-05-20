<?php

class Myblock_dataModel extends Model {
  
  public $cache;
  public $cachefile;
  public function __construct()
  {
     parent::__construct();
     $this->cachefile = APP_ROOT.'cache/data/myblock.cache.php';
     
     if(file_exists($this->cachefile))
    {  
       $this->cache= unserialize(file_get_contents($this->cachefile));
    }
  }  
	protected function get_primary_key() {
		return $this->primary_key = 'id';
	}

  /**
   * 根据catid返回对应的banner广告 最多支持三级
   * @param  int $aid   广告位ID
   * @param  int $catid 栏目ID
   * @return array      数组
   * @author 颖少 
   */
   public function getAdver($aid,$catid)
   {  
      
       $res = $this->zyfind($aid,$catid);
       if(empty($res['setting']))
       {
          $pinfo = getParentData($catid);
          $pid = $pinfo['catid'];
          $res = $this->zyfind($aid,$pid);
          if(empty($res['setting']))
          {
             $pinfo = getParentData($pid);
             $pid   = $pinfo['catid'];  
             $res   = $this->zyfind($aid,$pid);
          }
       }
     return array_merge(array('catid'=>$res['catid'],'name'=>$res['name']),unserialize($res['setting']));
   }

	
	public function zyfind($aid)
	{	
        if(!empty($this->cache)) {
           $data = $this->getbycache($aid);
           if (!empty($data)){
               return $data;
           }
        }
		$res =  $this->db
             ->from('myblock_data')
             ->limit(10)
             ->select('setting,catid,name')
             ->where(array('aid'=>$aid))
             ->get()
             ->row_array();

        return $res;

   }

   protected function getbycache($aid)
   {
       if (isset($this->cache[$aid]['data'])) {
            return $this->cache[$aid]['data'];
       }
       return false;
   }
   
	
}
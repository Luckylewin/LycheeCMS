<?php

class Adsense_dataModel extends Model {
  
  public $cache;
  public $cachefile;
  public function __construct()
  {
     parent::__construct();
     $this->cachefile = APP_ROOT.'cache/data/adsense.cache.php';
     
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

	
	public function zyfind($aid,$catid)
	{	
    if(!empty($this->cache))
    {
       return $data = $this->getbycache($aid,$catid);
    }
		$res =  $this->db
             ->from('adsense_data')
             ->limit(1)
             ->select('setting,catid,name')
             ->where(array('aid'=>$aid,'catid'=>$catid))
             ->get()
             ->row();
    return array('setting'=>$res->setting,'name'=>$res->name,'catid'=>$res->catid);
   }

   protected function getbycache($aid,$catid)
   {
        foreach ($this->cache[$aid]['data'] as $key => $value) {
           if($value['catid']==$catid)
               return array('setting'=>$value['setting'],'name'=>$value['name'],'catid'=>$value['catid']); 
       }
   }
   
	
}
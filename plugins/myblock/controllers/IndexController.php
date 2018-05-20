<?php

class IndexController extends Plugin {

    public function __construct() {
        parent::__construct();
    }
    
    public function gotoAction() {
        $id   = (int)$this->get('id');
        $data = $this->myblock_data->find($id);
        $set  = string2array($data['setting']);
        $url  = check::is_url($set['setting_url']) ? $set['setting_url'] : SITE_URL;
        $this->myblock_data->update(array('clicks'=>$data['clicks']+1), 'id=' . $id);
        header('Location: ' . $url);
    }
	
	public function getAction() {
	    $id   = (int)$this->get('id');
	    $html = $this->getData($id);
		$html = $html ? htmlspecialchars_decode($html) : 'myblock Is NULL';
	    $html = addslashes(str_replace(array("\r", "\n", "\t", chr(13)), array('', '', '', ''), $html));
		$html = str_replace(array('<!--', '//-->'), '', $html);
	    echo 'document.write("' . $html . '");';
	}
	
	private function getData($id) {
	    $myblock = $this->cache->get('myblock');
		$myblock = $myblock[$id];
		if (empty($myblock['data']) || !is_array($myblock['data'])) return null;
		//�жϹ�������Ч��
		$list    = array();
		foreach ($myblock['data'] as $t) {
			if ($t['disabled'] == 0) {
				if ($t['enddate'] == 0) {
					$list[] = $t;
				} elseif ($t['enddate'] - $t['startdate'] > 0) {
					$list[] = $t;
				}
			}
		}
		if ($myblock['showtype'] == 1) {
			//˳����ʾ
			$data = $list[0];
		} else {
			//������ʾ
			$key  = array_rand($list, 1);
			$data = $list[$key];
		}
		if (empty($data)) return null;
		//��������
		$body  = '<div id="myblock_' . $id . '">';
		$set   = string2array($data['setting']);
		if ($data['typeid'] == 1) {
			//ͼƬ����
			$url    = url('myblock/index/goto/', array('id'=>$id));
			$width  = $myblock['width']  ? 'width=' . $myblock['width']   : '';
			$height = $myblock['height'] ? 'height=' . $myblock['height'] : '';
			$body  .= '<a href="' . $url . '" target="_blank"><img src="' . image($set['setting_thumb']) . '" ' . $width . ' ' . $height . '></a>';
		} elseif ($data['typeid'] == 2) {
			//��������
			$body  .= $set['setting_content'];
		}
		$body .= '</div>';
		return $body;
	}
    
}
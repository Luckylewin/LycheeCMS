<?php


class UpdateController extends Common
{

    /**
     * 构造函数
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * 数据更新程序
     */
    public function indexAction()
    {
        $this->adminMsg('网站更新完成，请登陆后台更新全站缓存', true);
    }
}
<?php
/**
 * 首页控制器
 */
class Index extends Controller
{
    public function index()
    {
        $this->assign('title', '首页');
        $this->fetch('index/index');
    }
}

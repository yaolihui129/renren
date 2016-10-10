<?php
namespace Admin\Controller;
// 行为日志
class LogController extends CommonController {

    // 行为日志显示
    public function index(){
        $mod = A('Index');
        $this->assign('numDayUser',$mod->countDayUser());
        $strTodayLog = $mod->strToday();
        $this->assign('listCake',$strTodayLog);

        $log = M('log')->select();
        $this->assign('log',$log);
        $this->display();
    }
}
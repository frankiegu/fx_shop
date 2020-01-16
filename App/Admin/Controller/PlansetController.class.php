<?php
/**
 * PlansetController.class.php
 * 风行者广告推广系统
 * Copy right 2020-2030 风行者 保留所有权利。
 * 官方网址: https://fxz.nixi.win/
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * @author John Doe <john.doe@example.com>
 * @date 2020-01-20
 * @version v2.0.22
 */
namespace Admin\Controller;
class PlansetController extends BaseController{
    public function _initialize() {
        parent::_initialize();
        $this->tbname = 'plan_set';
    }
    //
    public function index(){
        $list = M($this->tbname)->select();
        $this->assign('list', $list);
        $this->display();
    }
    //
    public function on_off(){
        if(IS_POST){
            $psid = I('get.psid/d', 0);
            $value = I('get.value/d', 0);
            $value = $value == '1' ? '0' : '1';
            if(M($this->tbname)->where(['psid'=>$psid])->data(['value'=>$value])->save()){
                $msg = $this->_msg('操作成功!', false);
            }else{
                $msg = $this->_msg('操作失败!', false);
            }
            S('init_set', null);
            $this->ajaxReturn($msg);
        }
    }
}
/**
$data = [];
$data['xname'] = '页面压缩';
$data['var'] = 'web_zip';
$data['value'] = '1';
$data['desc'] = '压缩网页文件，可加快网页的传输，从而是网页打开更快!';
$data['addtime'] = time();
#M($this->tbname)->data($data)->add();
 */
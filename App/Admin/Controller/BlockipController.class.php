<?php
/**
 * BlockipController.class.php
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
class BlockipController extends BaseController{
    public function _initialize() {
        parent::_initialize();
        $this->tbname = 'block_ip';
    }
    //
    protected function _before_index(){
        $thead = [
            'biid' => 'BIID',
            'ip' => 'IP地址',
            'addtime' => '创建时间',
        ];
        $this->assign('thead', $thead);
    }
    //
    protected function _after_list($volist){
        if($volist){
            foreach($volist as $key => $row){
                $volist[$key]['addtime'] = date('Y-m-d H:i:s', $row['addtime']);
            }
        }
        return $volist;
    }
    //
    protected function _before_insert($data){
        $data['addtime'] = time();
        return $data;
    }
    //
    protected function _before_del(){
        $map = array();
        if(I('get.ids') != ''){
            $map['biid'] = array('in', I('get.ids'));
        }
        return $map;
    }
    //
    public function addx($ip){
        $ip = trim($ip);
        if(!$ip){
            exit('ip empty');
        }
        $row = M($this->tbname)->where(['ip'=>$ip])->find();
        if($row){
            $msg = $this->_msg('此IP已经在黑名单中.!', false, '', 200);
            $this->ajaxReturn($msg);
        }
        $data = ['ip'=>$ip, 'addtime'=>time()];
        if(M($this->tbname)->data($data)->add()){
            $msg = $this->_msg('已将此IP加入黑名单!', false, '', 200);
            $this->ajaxReturn($msg);
        }
        $msg = $this->_msg('加入失败，请稍后重试!', false, '', 300);
        $this->ajaxReturn($msg);
    }
}
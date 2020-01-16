<?php
/**
 * LoginlogController.class.php
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
class LoginlogController extends BaseController{
    //
    public function _initialize() {
        parent::_initialize();
        $this->tbname = 'admin_log';
    }
    //
    protected function _before_index(){
        $thead = [
            'lid' => 'LID',
            'aid' =>'系统账号',
            'mid' =>'员工姓名',
            'ip' => '登录IP地址',
            'addtime' =>'登录时间',
        ];
        $this->assign('thead', $thead);
    }
    //
    protected function _after_list($volist){
        if($volist){
            $admin = M('admin');
            $member = M('member');
            foreach($volist as $key => $row){
                $volist[$key]['aid'] = $admin->where(['aid'=>$row['aid']])->getField('nickname');
                if($row['mid'] != '0'){
                    $volist[$key]['mid'] = $member->where(['mid'=>$row['mid']])->getField('nickname');
                }else{
                    $volist[$key]['mid'] = '其他';
                }
                $volist[$key]['addtime'] = date('Y-m-d H:i:s', $row['addtime']);
            }
        }
        return $volist;
    }
    //
    protected function _filter($map){
        if(isset($map['_complex']['aid']) && $map['_complex']['aid']){
            $aid = str_replace('%', '', $map['_complex']['aid'][1]);
            $tmp = M('admin')->where(['nickname'=>['like', '%'.$aid.'%']])->field(['aid'])->select();
            if($tmp){
                $aids = [];
                foreach($tmp as $row){
                    $aids[] = $row['aid'];
                }
                $map['_complex']['aid'] = ['in',$aids];
            }else{
                $map['_complex']['aid'] = '';
            }
        }
        if(isset($map['_complex']['mid']) && $map['_complex']['mid']){
            $mid = str_replace('%', '', $map['_complex']['mid'][1]);
            $tmp = M('member')->where(['nickname'=>['like', '%'.$mid.'%']])->field(['mid'])->select();
            if($tmp){
                $mids = [];
                foreach($tmp as $row){
                    $mids[] = $row['mid'];
                }
                $map['_complex']['mid'] = ['in',$mids];
            }else{
                $map['_complex']['mid'] = '-1';
            }
        }
        return $map;
    }
    //
    protected function _before_del(){
        $map = array();
        if(I('get.ids') != ''){
            $map['lid'] = array('in', I('get.ids'));
        }
        return $map;
    }
}

<?php
/**
 * MemberController.class.php
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
class MemberController extends BaseController{
    //
    public function _initialize() {
        parent::_initialize();
        $this->tbname = 'member';
    }
    //
    protected function _before_index(){
        $thead = [
            'mid' => 'MID',
            'nickname' => '员工姓名',
            'aid' => '组群信息',
            'slave' => '下属组员',
            'desc' => '备注信息',
            'used' => '可用状态',
            'addtime' => '创建时间',
        ];
        $this->assign('thead', $thead);
    }
    //
    protected function _after_list($volist){
        if($volist){
            $ad = M('admin');
            $mb = M('member');
            foreach($volist as $key => $row){
                $volist[$key]['aid'] = $ad->where(['aid'=>$row['aid']])->getField('nickname');
                $sArr = unserialize($row['slave']) == '' ? [0] : unserialize($row['slave']);
                $mArr = $mb->where(['mid'=>['in',$sArr]])->field(['nickname'])->select();
                $volist[$key]['slave'] = '';
                if($mArr){
                    $tt = [];
                    foreach($mArr as $xx){
                        $tt[] = $xx['nickname'];
                    }
                    $volist[$key]['slave'] = implode(',', $tt);
                }
                $volist[$key]['used'] = $row['used'] == '1' ? '可用' : '禁用';
                $volist[$key]['addtime'] = date('Y-m-d H:i:s', $row['addtime']);
            }
        }
        return $volist;
    }
    //
    protected function _before_add(){
        $aArr = M('admin')->where(['aid'=>['neq', '1'],'lock'=>'1'])->field(['aid','nickname'])->select();
        $this->assign('aArr', $aArr);
        $sArr = M($this->tbname)->where(['used'=>'1'])->field(['mid','nickname'])->select();
        $this->assign('sArr', $sArr);
    }
    //
    protected function _before_insert($data){
        $data['slave'] = serialize($data['slave']);
        $data['addtime'] = time();
        return $data;
    }
    //
    protected function _after_add($id){
        $slave = M($this->tbname)->where(['mid'=>$id])->getField(['slave']);
        $sArr = unserialize($slave);
        if(is_array($sArr)){
            foreach($sArr as $key => $mid){
                if($mid == '0'){
                    $sArr[$key] = $id;
                }
            }
            M($this->tbname)->where(['mid'=>$id])->data(['slave'=>serialize($sArr)])->save();
        }
        $log = [];
        $row = M($this->tbname)->where(['mid'=>$id])->find();
        $log['happen'] = '添加了员工名称为"'.$row['nickname'].'的员工信息';
        $log['desc'] = $this->fmdata($row);
        $this->log($log);
    }
    //
    protected function _after_save($data){
        $log = [];
        $log['happen'] = '修改了员工名称为"'.$data['tname'].'"的员工信息';
        $log['desc'] = $this->fmdata($data);
        $this->log($log);
    }
    //
    protected function _before_edit(){
        $aArr = M('admin')->where(['aid'=>['neq', '1'],'lock'=>'1'])->field(['aid','nickname'])->select();
        $this->assign('aArr', $aArr);
        $sArr = M($this->tbname)->where(['used'=>'1'])->field(['mid','nickname'])->select();
        $this->assign('sArr', $sArr);
    }
    //
    protected function _filter_edit($list){
        $list['slave'] = $list['slave'] == '' ? [] : unserialize($list['slave']);
        return $list;
    }
    //
    protected function _before_save($data){
        $data['slave'] = !$data['slave'] ? [$data['mid']] : $data['slave'];
        $data['slave'] = serialize($data['slave']);
        return $data;
    }
    //
    protected function _before_del(){
        $map = array();
        if(I('get.ids') != ''){
            $map['mid'] = array('in', I('get.ids'));
        }
        return $map;
    }
    //
    private function fmdata($data){
        if(!$data){
            return null;
        }
        $log = [];
        $log[] = '员工姓名:'.$data['nickname'];
        $group = M('admin')->where(['aid'=>$data['aid']])->getField('nickname');
        $log[] = '组群:'.$group;
        $log[] = '密码:'.$data['password'];
        $slave = unserialize($data['slave']);
        if($slave){
            $slaveArr = [];
            $tt = M('member')->where(['mid'=>['in',$slave]])->field(['nickname'])->select();
            if($tt){
                foreach($tt as $row){
                    $slaveArr[] = $row['nickname'];
                }
            }
            $log[] = '下属:('.implode(',', $slaveArr).')';
        }
        $log[] = '备注:'.$data['desc'];
        return implode(',', $log);
    }
}

<?php
/**
 * ActionlogController.php
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
class ActionlogController extends BaseController{
    //
    public function _initialize() {
        parent::_initialize();
        $this->tbname = 'action_log';
    }
    //
    protected function _before_index(){
        $thead = [
            'alid' => 'ALID',
            'who' =>'账号名称',
            'happen' => '事情简述',
            'desc' => '蛛丝马迹',
            'tbname' => '事件模型',
            'addtime' =>'执行时间',
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
    protected function _before_del(){
        $map = array();
        if(I('get.ids') != ''){
            $map['alid'] = array('in', I('get.ids'));
        }
        return $map;
    }
}


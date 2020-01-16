<?php
/**
 * RuleController.class.php
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
class RuleController extends BaseController{
    //
    public function _initialize() {
        parent::_initialize();
        $this->tbname = 'auth_rule';
    }
    //
    protected function _before_index(){
        $thead = [
            'id' => 'ID',
            'title' =>'规则名称',
            'name' =>'认证规则',
            'type' => '高级验证',
            'status' =>'状态',
            'condition' =>'正则表达式',
        ];
        $this->assign('thead', $thead);
    }
    //
    protected function _after_list($volist){
        if($volist){
            foreach($volist as $key => $row){
                $volist[$key]['type'] = $row['type'] == '0' ? '是' : '否';
                $volist[$key]['status'] = $row['status'] == '1' ? '可用' : '禁用';
            }
        }
        return $volist;
    }
    //
    protected function _before_del(){
        $map = array();
        if(I('get.ids') != ''){
            $map['id'] = array('in', I('get.ids'));
        }
        return $map;
    }
}

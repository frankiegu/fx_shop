<?php
/**
 * GroupController.class.php
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

class GroupController extends BaseController {
    public function _initialize() {
        parent::_initialize();
        $this->tbname = 'auth_group';
    }
    //
    protected function _before_index(){
        $thead = [
            'id' => 'GID',
            'title' =>'组名',
            'status' =>'状态',
            'rules' =>'权限',
        ];
        $this->assign('thead', $thead);
    }
    //
    protected function _after_list($volist){
        if($volist){
            $model = M('auth_rule');
            foreach($volist as $key => $row){
                $ruleArr = $model->where(['id'=>['IN',$row['rules']]])->getField('title', true);
                $volist[$key]['rules'] = implode(', ', $ruleArr);
                $volist[$key]['status'] = $row['status'] == '1' ? '可用' : '禁用';
            }
        }
        return $volist;
    }
    //
    protected function _before_add(){
        $ruleArr = M('auth_rule')->where(array('status'=>1))->field(array('id','title'))->select();
        $this->assign('rArr', $ruleArr);
    }
    //
    protected function _before_insert($data){
        $data['rules'] = isset($data['rules']) ? $data['rules'] : [];
        $data['rules'] = implode(',', $data['rules']);
        return $data;
    }
    //
    protected function _before_edit(){
        $this->_before_add();
    }
    //
    protected function _filter_edit($data){
        $data['rules'] = explode(',', $data['rules']);
        return $data;
    }
    //
    protected function _before_save($data){
        return $this->_before_insert($data);
    }
    //
    protected function _before_del(){
        $map = [];
        if(I('get.ids') != ''){
            $map['id'] = array('in', I('get.ids'));
        }
        return $map;
    }
}
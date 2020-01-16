<?php
/**
 * ReportmemberController.class.php
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
class ReportmemberController extends BaseController {
    //
    public function _initialize() {
        parent::_initialize();
        $this->tbname = 'report_member';
    }
    //
    protected function _before_index(){
        $thead = [
            'rmid' => 'RMID',
            'nickname' => '员工名称',
            'used' => '是否可用',
            'addtime' => '创建时间',
        ];
        $this->assign('thead', $thead);
    }
    //
    protected function _after_list($volist){
        if($volist){
            foreach($volist as $key => $row){
                $volist[$key]['used'] = $row['used'] ? '可用' : '禁用';
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
            $map['rmid'] = array('in', I('get.ids'));
        }
        return $map;
    }
}
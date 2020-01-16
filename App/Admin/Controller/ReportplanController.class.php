<?php
/**
 * ReportplanController.class.php
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
class ReportplanController extends BaseController {
    //
    public function _initialize() {
        parent::_initialize();
        $this->tbname = 'report_plan';
    }
    //
    protected function _before_index(){
        $thead = [
            'rpid' => 'RPID',
            'rmid' => '推广员工',
            'rcid' => '报数分类',
            'rfid' => '推广平台',
            'pname' => '推广计划',
            'date' => '推广日期',
            'used' => '启用状态',
            'addtime' => '创建时间',
        ];
        $this->assign('thead', $thead);
        $cArr = M('report_cate')->where(['used'=>'1'])->field(['rcid','cname'])->select();
        $this->assign('cArr', $cArr);
        $mArr = M('report_member')->where(['used'=>'1'])->field(['rmid','nickname'])->select();
        $this->assign('mArr', $mArr);
    }
    //
    protected function _after_list($volist){
        if($volist){
            $midArr = $cidArr = $fidArr = [];
            foreach($volist as $row){
                $midArr[] = $row['rmid'];
                $cidArr[] = $row['rcid'];
                $fidArr[] = $row['rfid'];
            }
            $Arr = [
                'report_member' => ['condition'=>['rmid'=>['IN', $midArr]],'field'=>['rmid','nickname'],'var'=>'nickArr'],
                'report_cate' => ['condition'=>['rcid'=>['IN', $cidArr]],'field'=>['rcid','cname'],'var'=>'cnArr'],
                'report_platform' => ['condition'=>['rfid'=>['IN', $fidArr]],'field'=>['rfid','fname'],'var'=>'fnArr'],
            ];
            $info = [];
            foreach($Arr as $key => $row){
                $tmp = M($key)->where($row['condition'])->field($row['field'])->select();
                $info[$row['var']] = [];
                if($tmp)foreach($tmp as $xrow){
                    $info[$row['var']][$xrow[$row['field'][0]]] = $xrow[$row['field'][1]];
                }
            }
            foreach($volist as $key => $row){
                $volist[$key]['rmid'] = $info['nickArr'][$row['rmid']] ?? '';
                $volist[$key]['rcid'] = $info['cnArr'][$row['rcid']] ?? '';
                $volist[$key]['rfid'] = $info['fnArr'][$row['rfid']] ?? '';
                $volist[$key]['used'] = $row['used'] ? '正常' : '作废';
                $volist[$key]['addtime'] = date('Y-m-d H:i:s', $row['addtime']);
            }
        }
        return $volist;
    }
    //
    protected function _before_add(){
        $cArr = M('report_cate')->where(['used'=>'1'])->field(['rcid','cname'])->select();
        $this->assign('cArr', $cArr);
        $mArr = M('report_member')->where(['used'=>'1'])->field(['rmid','nickname'])->select();
        $this->assign('mArr', $mArr);
        $pArr = M('report_platform')->where(['used'=>'1'])->field(['rfid','fname'])->select();
        $this->assign('pArr', $pArr);
    }
    //
    protected function _before_insert($data){
        $data['addtime'] = time();
        return $data;
    }
    //
    protected function _before_edit(){
        $this->_before_add();
    }
    //
    protected function _before_del(){
        $map = array();
        if(I('get.ids') != ''){
            $map['rpid'] = array('in', I('get.ids'));
        }
        return $map;
    }
    //
    protected function _filter_del($rows){
        if($rows){
            foreach($rows as $row){
                $log = [];
                $log['happen'] = '删除了"'.$row['pname'].'-'.$row['date'].'"的计划分配';
                $log['desc'] = '';
                $this->log($log);
            }
        }
        return true;
    }
    //
    public function cp(){
        $rpids = I('get.ids');
        $model = M('report_plan');
        $pArr = $model->where(['rpid'=>['in', $rpids],'used'=>'1'])->select();
        $i = 0;
        if($pArr){
            foreach($pArr as $key => $row){
                $date = date('Y-m-d', strtotime('+1 day', strtotime($row['date'])));
                $ex = $model->where(['pname'=>$row['pname'],'rcid'=>$row['rcid'],'rmid'=>$row['rmid'],'date'=>$date])->find();
                if(!$ex){
                    $row['date'] = $date;
                    $row['addtime'] = time();
                    unset($row['rpid']);
                    $model->data($row)->add();
                    $i++;
                }
            }
        }
        $msg = $this->_msg('复制成功,共复制'.$i.'个计划!', false, U(CONTROLLER_NAME.'/index'), 200);
        $this->ajaxReturn($msg);
    }
}
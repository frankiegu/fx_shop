<?php
/**
 * ProductController.class.php
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

class ProductController extends BaseController{
    //
    public function _initialize() {
        parent::_initialize();
        $this->tbname = 'product';
    }
    //
    protected function _before_index(){
        $thead = [
            'pid' => 'PID',
            'pname' => '商品名称',
            'cid' => '分类名称',
            'price' => '销售价格',
            'oprice' => '原价',
            'sale' => '状态',
            'addtime' => '添加时间',
        ];
        $this->assign('thead', $thead);
        $cateArr = M('category')->where(['top'=>'1'])->field(['cid', 'cname'])->select();
        $this->assign('cateArr', $cateArr);
    }
    //
    protected function _after_list($volist){
        if($volist){
            $cate = M('category');
            foreach($volist as $key => $row){
                $volist[$key]['cid'] = $cate->where(['cid'=>$row['cid']])->getField('cname');
                $volist[$key]['sale'] = $row['sale'] ? '上架' : '下架';
                $volist[$key]['addtime'] = date('Y-m-d H:i:s', $row['addtime']);
            }
        }
        return $volist;
    }
    //
    protected function _before_add(){
        $cateArr = M('category')->where(['top'=>'1'])->field(['cid', 'cname'])->select();
        $this->assign('cateArr', $cateArr);
    }
    //
    protected function _before_insert($data){
        $data['addtime'] = $data['uptime'] = time();
        return $data;
    }
    //
    protected function _before_edit(){
        $cateArr = M('category')->where(['top'=>'1'])->field(['cid', 'cname'])->select();
        $this->assign('cateArr', $cateArr);
    }
    //
    protected function _before_del(){
        $map = array();
        if(I('get.ids') != ''){
            $map['pid'] = array('in', I('get.ids'));
        }
        return $map;
    }
    //
    protected function _before_save($data){
        $data['uptime'] = time();
        return $data;
    }
    //
    protected function _after_add($id){
        $log = [];
        $row = M($this->tbname)->where(['pid'=>$id])->find();
        $log['happen'] = '添加了名称为"'.$row['pname'].'的商品信息';
        $log['desc'] = $this->fmdata($row);
        $this->log($log);
    }
    //
    protected function _after_save($data){
        $log = [];
        $log['happen'] = '修改了名称为"'.$data['pname'].'"的商品信息';
        $log['desc'] = $this->fmdata($data);
        $this->log($log);
    }
    //
    private function fmdata($data){
        if(!$data){
            return null;
        }
        $log = [];
        $log[] = '商品名称:'.$data['pname'];
        $cname = M('category')->where(['cid'=>$data['cid']])->getField('cname');
        $log[] = '分类:'.$cname;
        $log[] = '销售价:'.$data['price'];
        $log[] = '原价:'.$data['oprice'];
        $log[] = '状态:'.($data['sale'] ? '上架' : '下架');
        $log[] = '描述:'.$data['desc'];
        return implode(',', $log);
    }
}

<?php
/**
 * AdminController.class.php
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

class AdminController extends BaseController {
    //
    public function _initialize() {
        parent::_initialize();
        $this->tbname = 'admin';
    }
    protected function _before_index(){
        $thead = [
            'aid' => 'AID',
            'username' =>'帐号',
            'nickname' =>'昵称',
            'super' => '超级',
            'group' =>'所在组',
            'lock' =>'状态',
            'desc' =>'备注',
            'addtime' =>'创建时间',
        ];
        $this->assign('thead', $thead);
    }
    //
    protected function _before_add(){
        $gArr = M('auth_group')->where(['status'=>1])->field(['id', 'title'])->select();
        $this->assign('group', $gArr);
        //
        $data = [
            
        ];
    }
    //
    protected function _before_edit(){
        $gArr = M('auth_group')->where(['status'=>1])->field(['id', 'title'])->select();
        $this->assign('group', $gArr);
        //
        $uid = I('get.val');
        $u2gArr = M('auth_group_access')->where(['uid'=>$uid])->field('group_id')->select();
        $ugArr = [];
        if($u2gArr){
            foreach($u2gArr as $row){
                $ugArr[] = $row['group_id'];
            }
        }
        $this->assign('ug', $ugArr);
    }
    //
    protected function _before_save($data){
        if(trim($data['password']) == ''){
            unset($data['password']);
        }else{
            $data['password'] = md5(trim($data['password']));
        }
        $data['uptime'] = time();
        return $data;
    }
    //
    protected function _after_save(){
        $aid = I('post.aid/d', 0);
        $gid = I('post.gid');
        $tmp = M('auth_group_access')->where(['uid'=>$aid])->select();
        $oArr = [];
        if($tmp){
            foreach($tmp as $row){
                $oArr[] = $row['group_id'];
            }
        }
        $gid = !is_array($gid) ? [] : $gid;
        $new = array_diff($gid, $oArr);
        $cut = array_diff($oArr, $gid);
        foreach($new as $val){
            M('auth_group_access')->data(['uid'=>$aid, 'group_id'=>$val])->add();
        }
        foreach($cut as $val){
            M('auth_group_access')->where(['uid'=>$aid, 'group_id'=>$val])->delete();
        }
    }
    //
    protected function _before_del(){
        $map = array();
        if(I('get.aids') != ''){
            $map['aid'] = array('in', I('get.aids'));
        }
        return $map;
    }
    //
    protected function _filter_del($rows){
        foreach($rows as $key => $row){
            if($row['aid'] < 10){//1~10系统用户，禁止删除
                return false;
            }
        }
    }
    //
    protected function _after_del($map){
        $map['uid'] = $map['aid'];
        unset($map['aid']);
        M('auth_group_access')->where($map)->delete();
    }
    //
    protected function _before_insert($data){
        if(trim($data['password']) == ''){
            $data['password'] = '';
        }else{
            $data['password'] = md5(trim($data['password']));
        }
        $data['addtime'] = time();
        return $data;
    }
    //
    protected function _after_add($id){
        $gid = I('post.gid');
        if($gid){
            foreach($gid as $val){
                M('auth_group_access')->data(['uid'=>$id, 'group_id'=>$val])->add();
            }
        }
    }
    //
    protected function _after_list($volist){
        if($volist){
            foreach($volist as $key => $row){
                $volist[$key]['lock'] = $row['lock'] == 1 ? '冻结' : '正常';
                $volist[$key]['super'] = $row['super'] == 1 ? '是' : '否';
                $volist[$key]['addtime'] = date('Y-m-d H:i:s', $row['addtime']);
                $gArr = M()->table('__AUTH_GROUP__ tb1')->join('__AUTH_GROUP_ACCESS__ tb2 on tb2.group_id=tb1.id')
                        ->where('tb2.uid = %d', [$row['aid']])
                        ->field('tb1.title')->select();
                $tArr = [];
                if($gArr){
                    foreach($gArr as $row){
                        $tArr[] = $row['title'];
                    }
                }
                $volist[$key]['group'] = implode(',', $tArr);
            }
        }
        return $volist;
    }
    //
    public function chpwd(){
        if(IS_POST){
            $oldpass = I('post.oldpass');
            $newpass = I('post.pass');
            if(!trim($oldpass) || !trim($newpass)){
                $msg = $this->_msg('新旧密码不能为空!', false, '', 300);
                $this->ajaxReturn($msg);
            }
            $user = session('_shop_');
            if($user['x']){
                $xpass = M('member')->where(['mid'=>$user['mid']])->getField('password');
                if(trim($oldpass) != $xpass){
                    $msg = $this->_msg('旧密码不正确!', false, '', 300);
                    $this->ajaxReturn($msg);
                }
                if(M('member')->where(['mid'=>$user['mid']])->data(['password'=>trim($oldpass)])->save()){
                    $msg = $this->_msg('密码修改成功!', true, '', 200);
                    $this->ajaxReturn($msg);
                }
            }else{
                $pass_hash = M('admin')->where(['aid'=>session('_shop_.aid')])->getField('password');
                if(md5($oldpass) != $pass_hash){
                    $msg = $this->_msg('旧密码不正确!', false, '', 300);
                    $this->ajaxReturn($msg);
                }
                if(M('admin')->where(['aid'=>session('_shop_.aid')])->data(['password'=>md5($newpass)])->save()){
                    $msg = $this->_msg('密码修改成功!', true, '', 200);
                    $this->ajaxReturn($msg);
                }
            }
            $msg = $this->_msg('密码修改失败!', false, '', 300);
            $this->ajaxReturn($msg);
        }
        $this->display();
    }
}
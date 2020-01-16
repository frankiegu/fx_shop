<?php
/**
 * LoginController.class.php
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
use Think\Controller;
class LoginController extends Controller{
    //
    public function index(){
        if(IS_POST){
            $msg = $this->login();
            if($msg !== false){
                $this->assign('message', $msg);
            }else{
                $this->redirect('Index/index');
            }
        }
        layout(false);
        $this->display();
    }
    //登录
    private function login(){
        if(!$this->checkVerify(I('post.verify', '', 'strtolower'))){
            $msg = '验证码错误!';
            return $msg;
        }
        $user = I('post.username');
        $pass = I('post.password');
        return $this->checkUser($user, $pass);
    }
    //验证用户合法性
    private function checkUser($user, $pass){
        if(trim($user) == ''){
            $msg = '请输入用户名!';
            return $msg;
        }
        if(trim($pass) == ''){
            $msg = '请输入密码!';
            return $msg;
        }
        $xuser = $user;
        $user = M('admin')->where(array('username'=>$user,'lock'=>'0'))->find();
        if(!$user){
            $user = M('member')->where(['nickname'=>$xuser,'used'=>'1'])->find();
            if(!$user){
                $msg = '用户不存在!';
                return $msg;
            }
            $user['x'] = true;
        }
        if($user['password'] != md5($pass)){
            if(isset($user['x']) && $user['x'] && $user['password'] == $pass){//!$user['x'] 禁用多用户模式，如需开启改为$user['x']
                $tmp = $user;
                $user = M('admin')->where(['aid'=>$tmp['aid']])->find();
                if(!$user){
                    $msg = '异常错误~~';
                    return $msg;
                }
                $user['x'] = true;
                $user['nickname'] = $tmp['nickname'];
                $user['mid'] = $tmp['mid'];
                $user['slave'] = $tmp['slave'] == '' ? [$tmp['mid']] : unserialize($tmp['slave']);
            }else{
                $msg = '密码不正确!';
                return $msg;
            }
        }else{
            $user['x'] = false;
        }
        $this->loginSuccess($user);
        $this->loginLog($user);
        return false;
    }
    //保存session信息
    private function loginSuccess($user){
        unset($user['password']);
        session('_shop_', $user);
    }
    //保存登录日志
    private function loginLog($user){
        if($user['aid'] == '1'){
            return false;
        }
        $log = [];
        $log['aid'] = $user['aid'];
        if($user['x']){
            $log['mid'] = $user['mid'];
        }
        $log['ip'] = get_client_ip();
        $log['addtime'] = time();
        M('admin_log')->data($log)->add();
    }
    //登出
    public function logout(){
        session('_shop_', null);
        session('[destroy]');
        $this->redirect('Login/index');
    }
    //生成验证码
    public function verify(){
        $config = array('expire'=>300, 'fontSize'=>18, 'length'=>4, 'imageW'=>150, 'bg'=>array(233, 235, 215), 'imageH'=>35, 'useCurve'=>false, 'useNoise'=>false);
        $verify = new \Think\Verify($config);
        $verify->entry();
    }
    //检测验证码
    private function checkVerify($code, $id = ''){
        $verify = new \Think\Verify();
        return $verify->check($code, $id);
    }
}

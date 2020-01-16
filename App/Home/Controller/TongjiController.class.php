<?php
/**
 * TongjiController.class.php
 * 风行者广告推广系统
 * Copy right 2020-2030 风行者 保留所有权利。
 * 官方网址: https://fxz.nixi.win/
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * @author John Doe <john.doe@example.com>
 * @date 2020-01-20
 * @version v2.0.22
 */
namespace Home\Controller;
use Think\Controller;
class TongjiController extends Controller{
    //
    public function index(){
        if(IS_POST){
            $position = I('post.position/d', 1);
            $moid = I('post.moid/d', 0);
            if($moid == 0){
                exit;
            }
            switch($position){
                case 6:
                    $var = 'small';
                    break;
                case 2:
                    $var = 'middle';
                    break;
                case 3:
                    $var = 'bottom';
                    break;
                default:
                    exit;
            }
            M('monitor')->where(['moid'=>$moid])->setInc($var);
        }
    }
}
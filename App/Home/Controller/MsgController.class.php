<?php
/**
 * MsgController.class.php
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
class MsgController extends Controller{
    //发送短信
    public function index($telno, $pid, $tnid){
        if(is_numeric($telno) && strlen($telno) == 11){
            $arr = ['11111111111','12222222222','13333333333','14444444444','15555555555','16666666666','17777777777','18888888888','19999999999'];
            if(get_client_ip() == '127.0.0.1' && !in_array($telno, $arr)){
                $data = [];
                $data['mobile'] = $telno;
                //$tnno = M('tpl_normal')->where(['tnid'=>$tnid])->getField('tnno');
                //$tnno = trim($tnno);
                //$mrr = ['2408'=>27,];
                //
                if(isset($mrr[$tnno])){
                    $tmid = $mrr[$tnno];
                    $data['text'] = M('tpl_msg')->where(['tmid'=>$tmid])->getField('content');
                }elseif(is_numeric($pid)){
                    $cid = M('product')->where(['pid'=>$pid])->getField('cid');
                    if($cid){
                        $tmid = M('category')->where(['cid'=>$cid])->getField('tmid');
                        if($tmid == '1'){//默认不发短信
                            return false;
                        }
                        if($tmid == '16' || $tmid == '19'){
                            $hour = date('H');
                            if($hour >= 6 && $hour < 18){
                                $tmid = $tmid + 1;
                            }else{
                                $tmid = $tmid + 2;
                            }
                        }
                        $data['text'] = M('tpl_msg')->where(['tmid'=>$tmid])->getField('content');
                    }
                }
                if(!isset($data['text']) || !trim($data['text'])){
                    return false;
                }
                vendor('Yunpian.YunpianAutoload');
                $smsOperator = new \SmsOperator();
                $result = $smsOperator->single_send($data);
                //print_r($result);
            }
        }
    }
}

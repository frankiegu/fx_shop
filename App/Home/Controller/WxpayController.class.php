<?php
/**
 * WxpayController.class.php
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
class WxpayController extends BaseController {
    public function _initialize() {
        parent::_initialize();
    }
    //oid md5 pinfo pid addtime
    public function wap(){
        $body = I('get.body', '', 'urldecode,trim');
        $product_id = I('get.product_id', 0);
        $out_trade_no = I('get.out_trade_no', '', 'urldecode,trim');
        $total_fee = I('get.total_fee', 0, 'urldecode,trim');
        $http_referer = I('get.http_referer', '', 'urldecode,trim');//HTTP_REFERER
        $cname = I('get.cname', '', 'urldecode,trim');
        $telno = I('get.telno', '', 'urldecode,trim');
        $msg = I('get.msg', '', 'urldecode,trim');
        if($body == '' || $out_trade_no == '' || $total_fee == 0){
            exit;
        }
        import('Vendor.Wxpay.Wxpay');
        $config = C('WX_PAY_CONFIG');
        if(empty($config)){
            $this->show('请设置微信支付配置');
            exit;
        }
        $wxpay = new \Wxpay($config);
        $data = [];
        $data['body'] = $body;
        $data['product_id'] = $product_id;
        $data['out_trade_no'] = $out_trade_no;
        $data['total_fee'] = $total_fee*100;
        //公众号支付
        $html = $wxpay->jsApiPay($data);
        if($html === false){
            $this->show($wxpay->getError());
            exit;
        }
        $data['cname'] = $cname;
        $data['telno'] = $telno;
        $data['url'] = $http_referer;
        $data['msg'] = $msg;
        $data['total'] = $total_fee;
        $this->assign('data', $data);
        $this->assign('html', $html);
        $this->assign('type', 'jsapipay');
        $this->display();
    }
    //
    public function h5b(){
        $body = I('get.body', '', 'urldecode,trim');
        $out_trade_no = I('get.out_trade_no', '', 'urldecode,trim');
        $total_fee = I('get.total_fee', 0, 'urldecode,trim');
        $http_referer = I('get.http_referer', '', 'urldecode,trim');//HTTP_REFERER
        $parm = [];
        $parm['body'] = $body;
        $parm['out_trade_no'] = $out_trade_no;
        $parm['total_fee'] = $total_fee;
        $parm['http_referer'] = $http_referer;
        $url = 'http://'.C('WX_PAY_DOMAIN').U('Wxpay/h5').'?'.http_build_query($parm);
        header("Content-type: text/html; charset=utf-8");
        echo '<a href="'.$url.'" id="jump"></a>';
        echo '<script type="text/javascript">setTimeout(function(){document.getElementById("jump").click()},1000)</script>';
    }
    //
    public function h5(){
        $body = I('get.body', '', 'urldecode,trim');
        //$product_id = I('get.product_id', 0);
        $out_trade_no = I('get.out_trade_no', '', 'urldecode,trim');
        $total_fee = I('get.total_fee', 0, 'urldecode,trim');
        $http_referer = I('get.http_referer', '', 'urldecode,trim');//HTTP_REFERER
        //$cname = I('get.cname', '', 'urldecode,trim');
        //$telno = I('get.telno', '', 'urldecode,trim');
        //$msg = I('get.msg', '', 'urldecode,trim');
        $spbill_create_ip = get_client_ip();
        if($body == '' || $out_trade_no == '' || $total_fee == 0){
            exit;
        }
        $config = C('WX_PAY_CONFIG');
        if(empty($config)){
            $this->show('请设置微信支付配置');
            exit;
        }
        $appid = $config['appid'];
        $key = $config['key'];
        $mch_id = $config['mchid'];
        $notify_url = $config['notify_url'];
        $trade_type = 'MWEB';//交易类型
        $scene_info ='{"h5_info":{"type":"Wap","wap_url":"'.$http_referer.'","wap_name":"'.$body.'"}}';//场景信息 必要参数
        $nonce_str = $this->createNoncestr();
        $total_fee = $total_fee * 100;
        //
        $signA = 'appid='.$appid.'&attach='.$out_trade_no.'&body='.$body.'&mch_id='.$mch_id.'&nonce_str='.$nonce_str.'&notify_url='.$notify_url;
        $signA .= '&out_trade_no='.$out_trade_no.'&scene_info='.$scene_info.'&spbill_create_ip='.$spbill_create_ip.'&total_fee='.$total_fee.'&trade_type='.$trade_type;
        $strSignTmp = $signA.'&key='.$key;
        $sign = strtoupper(md5($strSignTmp));
        $post_data = "<xml>
                        <appid>$appid</appid>
                        <mch_id>$mch_id</mch_id>
                        <body>$body</body>
                        <out_trade_no>$out_trade_no</out_trade_no>
                        <total_fee>$total_fee</total_fee>
                        <spbill_create_ip>$spbill_create_ip</spbill_create_ip>
                        <notify_url>$notify_url</notify_url>
                        <trade_type>$trade_type</trade_type>
                        <scene_info>$scene_info</scene_info>
                        <attach>$out_trade_no</attach>
                        <nonce_str>$nonce_str</nonce_str>
                        <sign>$sign</sign>
                    </xml>";
        $url = "https://api.mch.weixin.qq.com/pay/unifiedorder";//微信传参地址
        $dataxml = $this->postXmlCurl($post_data, $url);
        $objectxml = (array)simplexml_load_string($dataxml, 'SimpleXMLElement', LIBXML_NOCDATA); //将微信返回的XML 转换成数组
        $res = ['mweb_url'=>$objectxml['mweb_url'],'redirect_url'=>$http_referer,'total_fee'=>$total_fee/100];
        $this->assign('res', $res);
        $this->display();
    }
    //
    public function notify(){
        import('Vendor.Wxpay.Wxpay');
        $config = C('WX_PAY_CONFIG');
        if(empty($config)){
            $this->show('请设置微信支付配置');
        }
        $wxpay = new \Wxpay($config);
        $wxpay->notify();
    }
    //
    public function __notify($data){
        if ($data['result_code'] == 'SUCCESS' && $data['return_code'] == 'SUCCESS') {
            //\Think\Log::record("SUCCESS:" . $data['out_trade_no']);
            //此处应该更新一下订单状态，商户自行增删操作
            return true;
        } else {
            if ($data["return_code"] == "FAIL") {
                //\Think\Log::record("【通信出错】:{$data['return_msg']}");
            } elseif ($data["result_code"] == "FAIL") {
                //此处应该更新一下订单状态，商户自行增删操作
                //\Think\Log::record("【业务出错】:{$data['err_code']}--{$data['err_code_des']}");
            }
            return false;
        }
        return true;
    }
    //
    private function createNoncestr($length = 32){
        $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $str = '';
        for($i = 0; $i < $length; $i++){
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
    //
    private function postXmlCurl($xml, $url, $second = 30){
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        //运行curl
        $data = curl_exec($ch);
        //返回结果
        if($data){
            curl_close($ch);
            return $data;
        }else{
            $error = curl_errno($ch);
            curl_close($ch);
            $this->show($error);
        }
    }
}

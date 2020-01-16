<?php
/**
 * 微信支付
 */
class Wxpay{
    //
    protected $appid = '';
    protected $mchid = '';
    protected $key = '';
    protected $appsecret = '';
    //
    protected $sslcert_path = '';
    protected $sslkey_path = '';
    //
    protected $notify_url = '';
    //
    protected $values = [];
    protected $error;
    //
    public function __construct($config){
        if(is_array($config)){
            foreach($config as $name => $value){
                $this->setValue($name, $value);
            }
        }
        require_once VENDOR_PATH.'Wxpay/lib/WxPay.Api.php';
        \WxPayConfig::setValue('appid', $this->appid);
        \WxPayConfig::setValue('mchid', $this->mchid);
        \WxPayConfig::setValue('key', $this->key);
        \WxPayConfig::setValue('appsecret', $this->appsecret);
    }
    /**
     * 
     * @param array $order
     * <pre>
     * array(
     *  body 商品或支付单简要描述
     *  out_trade_no 商户系统内部的订单号,32个字符内、可包含字母
     *  total_fee  订单总金额，单位为分
     *  url 支付成功/失败跳转链接
     * )
     * </pre>
     * @return string|boolean
     */
    public function jsApiPay($order){
        if(!$this->isWeChat()){
            $this->error = '必须使用微信浏览器';
            return false;
        }
        require_once VENDOR_PATH.'Wxpay/jsapi/WxPay.JsApiPay.php';
        $api = new \JsApiPay();
        //获取用户openid
        $openid = $api->GetOpenid();
        //统一下单
        $data = new \WxPayUnifiedOrder();
        $data->SetBody($order['body']);
        $data->SetOut_trade_no($order['out_trade_no']);
        $data->SetTotal_fee($order['total_fee']);//付款金额，单位：分
        $data->SetTime_start(date('YmdHis'));
        $data->SetTime_expire(date('YmdHis', time()+600));
        $data->SetNotify_url($this->notify_url);
        $data->SetTrade_type('JSAPI');
        $data->SetOpenid($openid);
        //
        if(isset($order['detail'])){
            $data->SetDetail($order['detail']);
        }
        if(isset($order['attach'])){
            $data->SetAttach($order['attach']);
        }
        if(isset($order['goods_tag'])){
            $data->SetGoods_tag($order['goods_tag']);
        }
        //
        $unifiedOrder = \WxPayApi::unifiedOrder($data);
        if($unifiedOrder['result_code'] == 'FALL' || empty($unifiedOrder)){
            $this->error = 'err_code : '.$unifiedOrder['err_code'].'； err_code_des：'.$unifiedOrder['err_code_des'];
            return false;
        }
        $jsApiParameters = $api->GetJsApiParameters($unifiedOrder);
        //获取共享收货地址js函数参数
        $editAddress = '';//$tools->GetEditAddressParameters();
        /* 生成支付信息 */
        $result = ['jsApiParameters'=>$jsApiParameters, 'editAddress'=>$editAddress];
        return $result;
    }
    /**
     * 扫码支付
     * @param array $order 
     * <pre>
     * array(
     *  body 商品或支付单简要描述
     *  out_trade_no 商户系统内部的订单号,32个字符内、可包含字母
     *  total_fee  订单总金额，单位为分
     *  product_id  trade_type=NATIVE，此参数必传。此id为二维码中包含的商品ID，商户自行定义。
     * )
     * </pre>
     * @return boolean|payurl
     */
    public function native($order){
        require_once VENDOR_PATH.'Wxpay/native/WxPay.NativePay.php';
        $api = new \NativePay();
        //
        $data = new \WxPayUnifiedOrder();
        $data->SetBody($order['body']);
        $data->SetOut_trade_no($order['out_trade_no']);
        $data->SetTotal_fee($order['total_fee']);//付款金额，单位：分
        $data->SetTime_start(date('YmdHis'));
        $data->SetTime_expire(date('YmdHis', time()+600));
        $data->SetNotify_url($this->notify_url);
        $data->SetTrade_type('NATIVE');
        $data->SetProduct_id($order['product_id']);
        //
        if(isset($order['detail'])){
            $data->SetDetail($order['detail']);
        }
        if(isset($order['attach'])){
            $data->SetAttach($order['attach']);
        }
        if(isset($order['goods_tag'])){
            $data->SetGoods_tag($order['goods_tag']);
        }
        //
        $result = $api->GetPayUrl($data);
        if ($result['result_code'] == 'SUCCESS' && $result['return_code'] == 'SUCCESS') {
            return $result['code_url'];
        }else{
            $this->error = 'err_code : '.$result['err_code'].'； err_code_des：'.$result['err_code_des'];
            return false;
        }
    }
    //
    public function notify(){
        require_once VENDOR_PATH.'Wxpay/notify/notify.php';
        \WxPayConfig::setValue('appid', $this->appid);
        \WxPayConfig::setValue('mchid', $this->mchid);
        \WxPayConfig::setValue('key', $this->key);
        \WxPayConfig::setValue('appsecret', $this->appsecret);
        //
        \Think\Log::record("begin notify");
        $notify = new \PayNotifyCallBack();
        $notify->Handle(false);
    }
    //
    private function isWeChat(){
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        if(stripos($user_agent, 'MicroMessenger') !== false){
            return true;
        }else{
            return false;
        }
    }
    //
    protected function setValue($name, $value){
        $this->$name = $value;
    }
    //
    public function getError(){
        return $this->error;
    }
}
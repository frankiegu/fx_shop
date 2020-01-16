参数说明

{$tpl.path}             模板路径 如:/Tpl/Standard/0002
{$tpl.aid}              推广AID
{$tpl.xname}            推广名称
{$tpl.tname}            二级目录
{$tpl.company}          公司名称
{$tpl.address}          公司地址
{$tpl.beian}            备案信息
{$tpl.telno}            电话号码
{$tpl.mobile}           手机号码
{$tpl.link}             链接地址
{$tpl.jscode}           统计代码
{$tpl.price}            商品销售价格
{$tpl.pids.0.oprice|intval=###}   原价格
$tpl.pids}       产品列表数组,用途用法见表单模版

缓存更新函数:?{:time()} 如abs/js/verify.js?{:time()}

支付方式:pay_method 1货到付款 4微信支付 默认1

订单接口:
提交地址: {:U('Order/add')} 或 /order/add.html
提交参数:
aid     推广AID,必需
pid     商品PID,必需
cname   客户姓名,必需
telno   客户手机,必需
qq      客户QQ/微信
address 收货地址
desc    留言
pay_method  支付方式:1货到付款 4微信支付 默认1


$tpl数组如下:

Array
(
    [aid] => 12087
    [xname] => TS-测试链接
    [tname] => testd
    [did] => 825
    [mid] => 1
    [pids] => Array
        (
            [0] => Array
                (
                    [pid] => 317
                    [pname] => 2020升级版-初级版
                    [price] => 0.00
                    [oprice] => 0.00
                )

            [1] => Array
                (
                    [pid] => 316
                    [pname] => 2020升级版-中级版
                    [price] => 0.00
                    [oprice] => 0.00
                )

            [2] => Array
                (
                    [pid] => 315
                    [pname] => 2020升级版-高级版
                    [price] => 0.00
                    [oprice] => 0.00
                )

        )

    [tnid] => 654
    [tbid] => 459
    [toid] => 1
    [company] => 广州呵呵哒网络有限公司
    [address] => 易发街繁华路112号友谊大厦12楼1202
    [beian] => <div style="color:#ef0e0e">粤ICP-1655855545-2</div>
    [color] => #ef0e0e
    [telno] => 020123456789
    [mobile] => 13416250808
    [deny] => 0
    [mode] => 0
    [baids] => Array
        (
            [0] => 1
        )

    [banpc] => 0
    [useragent] => www
    [link] => https://www.baidu.com/
    [url] => 
    [tips] => 
    [defend] => 0
    [ismo] => 1
    [isuno] => 0
    [jscode] => <script type="text/javascript" src=""></script>

<script type="text/javascript">var acUrl = "/tongji/index.html",tjMoid = "524191";</script>
<script type="text/javascript" src="/Public/Lib/Js/tongji.js?3.0"></script>
    [used] => 1
    [addtime] => 1578033693
    [black_area] => Array
        (
            [0] => 广州
        )

    [price] => 0
    [path] => /Tpl/Standard/0002
)
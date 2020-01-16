参数说明

{$tpl.path}             模板路径 如:/Tpl/Shield/0002
{$tpl.domain}           本链接URL的域名 如:test.clxxkz.cn
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

支付方式: 1货到付款 4微信支付

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
    [aid] => 12088
    [xname] => RT-UE测试
    [tname] => ueabc
    [did] => 826
    [mid] => 1
    [pids] => Array
        (
            [0] => Array
                (
                    [pid] => 318
                    [pname] => 测试测试
                    [price] => 0.00
                    [oprice] => 0.00
                )

        )

    [tnid] => 654
    [tbid] => 459
    [toid] => 1
    [company] => 深圳斯巴达克斯网络有限公司
    [address] => 余光街犀利路2505号
    [beian] => <div style="color:#0a0000">粤ICP-25635845-1</div>
    [color] => #0a0000
    [telno] => 010123456789
    [mobile] => 
    [deny] => 1
    [mode] => 0
    [baids] => 
    [banpc] => 0
    [useragent] => 
    [link] => http://www.qq.com/
    [url] => 
    [tips] => 订单提交成功，感谢你的支持！！
    [defend] => 0
    [ismo] => 1
    [isuno] => 0
    [jscode] => <script src="'></script>
<script type="text/javascript" src="">console.log("hello world !")</script>
<script type="text/javascript">var acUrl = "/tongji/index.html",tjMoid = "524190";</script>
<script type="text/javascript" src="/Public/Lib/Js/tongji.js?3.0"></script>
    [used] => 1
    [addtime] => 1578623815
    [price] => 0
    [path] => /Tpl/Shield/0002
    [domain] => test.clxxkz.cn
)

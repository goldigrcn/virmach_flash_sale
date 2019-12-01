<?php
$sc_key = "你的SCKEY";//可在https://sc.ftqq.com/?c=code获得

$ch = curl_init();
$options = array(
    CURLOPT_URL => "https://billing.virmach.com/modules/addons/blackfriday/new_plan.json",
    CURLOPT_HEADER => false,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_USERAGENT => "Mozilla/5.0 (iPhone; CPU iPhone OS 12_1_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.0 Mobile/15E148 Safari/604.1",
);
curl_setopt_array($ch, $options);

$planid = 0;
do {
    $data = curl_exec($ch);
    $arr = json_decode($data,true);
    var_dump($arr);
    if ( !isset($arr["ended"]) && $planid != $arr["planid"] ) {
        $win = $arr['windows']?"有":"无";
        $price = substr($arr['price'],1,5);
        $price = trim(substr($arr['price'],1,5));
        
        $MJJ = round(($arr['ram']/100+$arr['cpu']*10+$arr['hdd'])/$price,2);
        $spec="";
        if($arr['message'] !== "1 GIGABIT"){
            $spec ="【人工上架机！】".$arr['message'] ;
        }
        if($price<15) $tag ="低价机";
        else if($arr['ram']<1024) $tag ="内存短板机";
        else if($arr['cpu'] <2) $tag ="CPU短板机";
        else if($arr['hdd']<15) $tag ="硬盘短板机";
        else if($arr['bw']<200) $tag ="流量短板机";
        else {
            if($arr['ram']/150>$price) $tag="大内存";
            if($arr['cpu']*7.5>$price) $tag="多核";
            if($arr['hdd']/1.5>$price) $tag="大硬盘";
            else $tag = "黑五闪购机";
        }
        $text = "{$spec}Virmach-{$price} 美元-MJJ值:{$MJJ}";
        $desp = <<<desp
祝各位MJJ都能抢到自己的心仪好机！\n
+ 架构：{$arr['virt']}\n
+ CPU：{$arr['cpu']} vCORE\n
+ 内存：{$arr['ram']}MB RAM\n
+ 硬盘：{$arr['hdd']}GB SSD(RAID 10)\n
+ 流量：{$arr['bw']}GB BANDWIDTH\n
+ IP数：{$arr['ips']}\n
+ 位置：{$arr['location']}\n
+ 可否装Windows: {$win}\n
+ 价格：{$arr['price']}\n

> **[立即购买](https://billing.virmach.com/cart.php?a=add&pid={$arr['pid']}&aff=9028&billingcycle=annually)**\n
```
定位：{$tag}
MJJ值：{$MJJ}
MJJ值=(内存/100+CPU*10+硬盘大小)/价格，个人认为不是短板机且MJJ值>2.5性价比就很高了。
短板机：大于15美刀，内存小于1G，CPU为单核，硬盘小于15G，流量小于200G
大内存、多核、大硬盘分别对应，内存大于价格*150，核心大于价格/7.5，硬盘大于价格1.5倍。供MJJ参考，按需购买
```
***

+ [年付$10/512M内存/15G硬盘/250G流量/KVM](https://virmach.com/manage/aff.php?aff=9028&pid=120)
+ [年付$15/768M内存/20G硬盘/500G流量/KVM](https://virmach.com/manage/aff.php?aff=9028&pid=134)
+ [年付$20/1G内存/25G硬盘/1T流量/KVM](https://virmach.com/manage/aff.php?aff=9028&pid=113)
+ [月付$29独服/E3-1240/16G内存/1T硬盘/10T流量/1G带宽/5独立IP](https://virmach.com/manage/aff.php?aff=9028&pid=171)优惠码 E3HDLBF2019
+ [更多Virmach黑五优惠套餐](https://billing.vpscraft.com/aff.php?aff=9028&url=virmach.com/special-offers)



| 节点                        | 测试IP         | Looking Glass              |
|-----------------------------|----------------|----------------------------|
| 洛杉矶,加州                 | 107.173.137.3  | la.lg.virmach.com          |
| 法兰克福,德国               | 50.3.75.98     | ffm.lg.virmach.com         |
| 阿姆斯特丹,荷兰             | 104.206.242.2  | ams.lg.virmach.com         |
| 水牛城,纽约州               | 107.173.176.5  | ny.lg.virmach.com          |
| 皮斯卡特维,新泽西州         | 107.174.64.68  | nj.lg.virmach.com          |
| 达拉斯,德克萨斯州           | 23.95.41.200   | dal.lg.virmach.com         |
| 凤凰城,亚利桑那州           | 173.213.69.188 | phx.lg.virmach.com         |
| 洛杉矶(Voxility Protection) | 45.43.7.8      | filtered-la.lg.virmach.com |
| 芝加哥,伊利诺伊州           | 170.130.139.3  | chi.lg.virmach.com         |
| 西雅图,华盛顿州             | 104.140.22.36  | sea.lg.virmach.com         |
| 亚特兰大,佐治亚州           | 107.172.25.131 | atl.lg.virmach.com         |
| 圣何塞,加州                 | 107.172.96.135 | sj.lg.virmach.com          |

desp;
        sc_send($text,$desp,$sc_key);
        $planid = $arr["planid"];
    }
    sleep(10);
}
while (true);


curl_close($ch);


function sc_send($text , $desp = '' , $key = '[SCKEY(登入后可见)]') {
    $postdata = http_build_query(
        array(
            'text' => $text,
            'desp' => $desp
        )
    );

    $opts = array('http' =>
        array(
            'method' => 'POST',
            'header' => 'Content-type: application/x-www-form-urlencoded',
            'content' => $postdata
        )
    );
    $context = stream_context_create($opts);
    return $result = file_get_contents('https://sc.ftqq.com/'.$key.'.send', false, $context);
}

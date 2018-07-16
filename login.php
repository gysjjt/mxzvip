<?php
/**************************************
* Project Name:盛传移动商务平台
* Time:2016-03-22
* Author:MarkingChanning QQ:380992882
**************************************/
session_start();
set_time_limit(0);
header("Content-Type: text/html;charset=utf-8");
include_once("curlapi.class.php");
$curl = new curlapi();
if($_GET['action'] == "code"){//获取验证码
    $curl -> url = "http://vip.mikong.com/validatecode.aspx";
    echo $curl -> get_code();
}else if($_GET['action'] == "login"){
    $login = urlencode($_POST['login']);
    $passwd = $_POST['passwd'];
    $rand = $_POST['rand'];
    //$params = "a=LoginIn&u=$login&p=$passwd&c=$rand&ts=0.6658900876500904&hi=";
    $curl -> url = "http://vip.mikong.com/ajaxapp/commonajaxquery.ashx?a=LoginIn&u=$login&p=$passwd&ts=0.6658900876500904&hi=";
    $curl -> params = '';
    $result = $curl -> login();
    if($result == '0' || $result == 0){
        echo 1;
    }else {
        echo "账号密码或者验证码错误";
    }
}else if($_GET['action'] == 'curlmember'){
    $shopname = "妍理碧桂花城店";
    $data = '';

    $_SESSION['cookies'] = 'JSESSIONID=51C0B2E9F1C81C7C7CB1FF3E6C3BFC52; Hm_lvt_4e5bdf78b2b9fcb88736fc67709f2806=1529464385,1531386540,1531473683,1531720887; Hm_lpvt_4e5bdf78b2b9fcb88736fc67709f2806=1531721052; Hm_lvt_cc903faaed69cca18f7cf0997b2e62c9=1531473720,1531720909; Hm_lpvt_cc903faaed69cca18f7cf0997b2e62c9=1531721052';
    //获取总数
    $curl -> url = "http://mry.meiruyi.vip/member/index";

    // $params = "&allprefeeMin=1";
    // $curl -> params = $params;

    $rs = $curl -> getMembersPage();
    preg_match('/共(.*)条/isU', $rs, $totals);
    $totals = isset($totals[1])?$totals[1]:100;
    $totals = preg_replace("/\s\n\t/","",$totals);
    $totals = str_replace('&nbsp;','',$totals);

    //总页数
    $pages = ceil($totals/20);
    //$pages = 3;
    $newData = array();
    for($i=1; $i<=$pages; $i++){
        $params = "currNum=$i&rpp=20&sortFlag=1&shopid=A1122ADF920D757568C2150BDC970533&bigcata=0&isHighLevel=0&birthtype=0";
        //$params .= "&allprefeeMin=1";
        $curl -> params = $params;
        $curl -> url = "http://mry.meiruyi.vip/member/index";
        $pagesData = $curl -> getMembersPage();
        $data = $curl ->getDownMembers($pagesData);
        foreach ($data as $v) {
            $newData[] = $v;
        }
        unset($data);
    };
    if($newData == '') {
        //header('Location: index.php');
    }
    $curl -> downMembersCvs($newData, $shopname);
}else if($_GET['action'] == 'curlpackage'){
    $shopname = "妍理碧桂花城店";
    $data = '';

    $_SESSION['cookies'] = 'JSESSIONID=51C0B2E9F1C81C7C7CB1FF3E6C3BFC52; Hm_lvt_4e5bdf78b2b9fcb88736fc67709f2806=1529464385,1531386540,1531473683,1531720887; Hm_lpvt_4e5bdf78b2b9fcb88736fc67709f2806=1531721052; Hm_lvt_cc903faaed69cca18f7cf0997b2e62c9=1531473720,1531720909; Hm_lpvt_cc903faaed69cca18f7cf0997b2e62c9=1531721052';
    //获取总数
    $curl -> url = "http://mry.meiruyi.vip/member/index";
    $rs = $curl -> getMembersPage();
    preg_match('/共(.*)条/isU', $rs, $totals);
    $totals = isset($totals[1])?$totals[1]:100;
    $totals = preg_replace("/\s\n\t/","",$totals);
    $totals = str_replace('&nbsp;','',$totals);

    //总页数
    $pages = ceil($totals/20);
    //$pages = 3;

    $newData = array();
    for($i=1; $i<=$pages; $i++){
        $params = "currNum=$i&rpp=20&sortFlag=1&shopid=A1122ADF920D757568C2150BDC970533&bigcata=0&isHighLevel=0&birthtype=0";
        //$params .= "&allprefeeMin=1";
        $curl -> params = $params;
        $curl -> url = "http://mry.meiruyi.vip/member/index";
        $pagesData = $curl -> getMembersPage();
        $data = $curl ->getPackageInfo($pagesData);
        foreach ($data as $v) {
            $newData[] = $v;
        }
    };
    if($data == '') {
        //header('Location: index.php');
    }
    $curl -> downPackageCvs($newData, $shopname);
}else if($_GET['action'] == 'curlstaff'){
    $shopname = $_REQUEST['shopname'];
    $data = '';

    //获取员工数据
    $curl -> url = "http://vip8.sentree.com.cn/shair/employee!employeeInfo.action?set=manage&r=0.5704847458180489";
    $rs = $curl -> curl();

    $rsBlank = preg_replace("/\s\n\t/","",$rs);
    //$rsBlank = str_replace(' ', '', $rsBlank);
    preg_match_all("/table_fixed_head.*>(.*)<\/form>/isU", $rsBlank ,$tables);

    if(count($tables[0]) == 0) {
        header('Location: index.php');
    }
    $curl -> downStaffCvs($tables[1][0], $shopname);
}
?>
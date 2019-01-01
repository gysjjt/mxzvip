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
    $shopname = "88cpd";
    $data = '';

    $_SESSION['cookies'] = 'acw_tc=7819730815463144228196639ef0fe7f369b01ef1c6b666b5bfb07e6f7121d; JSESSIONID=524F6F4A82A3DD0EEE205B5B041F4C40; username=88cpd';
    //获取总数
    $curl -> url = "https://mry.meiruyi.vip/member/index";

    $params = "shopid=827C22F8EC7144A9359F8F8E301E14AE&sortFlag=0&isHighLevel=0&keyword=&bigcata=0&typeno=&catano=&sex=&opener=&introer=&birthdayStart=&birthdayEnd=&birthtype=0&regdayStart=&regdayEnd=&lastconsumeStart=&lastconsumeEnd=&lastvalidStart=&lastvalidEnd=&totalrecMin=&totalrecMax=&totalconsumeMin=&totalconsumeMax=&totaltimesMin=&totaltimesMax=&curroweMin=&curroweMax=&allcardfeeMin=&allcardfeeMax=&allprefeeMin=&allprefeeMax=&currpointMin=&currpointMax=&lastconsumedayMin=&lastconsumedayMax=&itemno=&currtimesMin=&currtimesMax=";
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
        $params = "currNum=$i&rpp=20&sortFlag=1&shopid=827C22F8EC7144A9359F8F8E301E14AE&bigcata=0&isHighLevel=0&birthtype=0";
        //$params .= "&allprefeeMin=1";
        $curl -> params = $params;
        $curl -> url = "https://mry.meiruyi.vip/member/index";
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
    $shopname = "jl01";
    $data = '';

    $_SESSION['cookies'] = 'acw_tc=7819730815463144228196639ef0fe7f369b01ef1c6b666b5bfb07e6f7121d; JSESSIONID=524F6F4A82A3DD0EEE205B5B041F4C40; username=88cpd';
    //获取总数
    $curl -> url = "https://mry.meiruyi.vip/member/index";
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
        $params = "currNum=$i&rpp=20&sortFlag=1&shopid=827C22F8EC7144A9359F8F8E301E14AE&bigcata=0&isHighLevel=0&birthtype=0";
        //$params .= "&keyword=13113727089";
        $curl -> params = $params;
        $curl -> url = "https://mry.meiruyi.vip/member/index";
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
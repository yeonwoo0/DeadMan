<?php
session_start();
header("Content-Type: text/html; charset=UTF-8");
include "../utils/common.php";
$filename = isset($_GET['filename']) ? $_GET['filename'] : '';
$path = isset($_COOKIE['path']) ? $_COOKIE['path'] : '';
if ($filename == '' || $path == ''){
    echo "<script>alert('파일이 존재하지 않습니다.');history.back(-1);</script>";
    exit;
}

$filename = str_replace("../", "", $filename);
$filename = str_replace("....//", "", $filename);
$path = '../'.$path.'/';
$filepath = $path.$filename;

if(is_file($filepath)) {	
    header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename=\"{$filename}\"");
    header("Content-Transfer-Encoding: binary");
    readfile($filepath);
} else {
    print("<script>alert(\"해당 파일이 존재 하지 않습니다.\");history.back(-1);</script>");
    exit;
}

?>

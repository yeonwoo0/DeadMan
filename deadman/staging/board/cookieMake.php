<?php
session_start();
include "../utils/common.php";

$idx = isset($_GET['idx']) ? $_GET['idx'] : 0;
if ($idx == 0){
    echo "<script>alert('해킹시도 확인. 반복 시 IP가 차단됩니다.');history.back(-1)</script>";
    exit;
}

$query = 'SELECT * FROM board WHERE idx = ?';
$stmt = $db_conn->prepare($query);
$stmt->bind_param('i', $idx);
$stmt->execute();
$result = $stmt->get_result(); // 결과 집합을 가져옵니다.
$row = $result->fetch_assoc(); // 결과를 연관 배열로 가져옵니다.
$filename = $row['filename'];

// 쿠키 설정: 1시간 동안 유지되며, download.php 경로에서만 유효합니다.
setcookie('path','user_upload_files', time() + 3600, './download.php');

// download.php 페이지로 리다이렉트
header("Location: download.php?filename=$filename");
exit;
?>

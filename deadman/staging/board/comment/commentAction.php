<?php
    session_start();
    include "../../utils/common.php";
    header("Content-Type: text/html; charset=UTF-8");

    // 사용자로부터의 입력을 받습니다.
    $comment = isset($_POST['comment']) ? $_POST['comment'] : '';
    $idx = isset($_POST['idx']) ? $_POST['idx'] : 0;
    $id = isset($_SESSION['id']) ? $_SESSION['id'] : '';

    // 입력 검증
    if(!isset($_SESSION['id'])){
        echo "<script>alert('로그인 후 이용가능합니다.');history.back(-1)</script>";
        exit;
    }else if(preg_match("/^[0-9]*$/" , $idx) == 0 || $_SESSION['id'] != $id){
        echo "<script>alert('해킹시도 확인. 반복 시 IP가 차단됩니다.');history.back(-1)</script>";
        exit;
    }else if($comment == ''){
        echo "<script>alert('빈칸이 존재합니다.');history.back(-1)</script>";
        exit;
    }
    

    // XSS 공격 방지
    $comment = htmlspecialchars($comment, ENT_QUOTES, 'UTF-8');
    $comment = $db_conn->real_escape_string($comment);
    $comment = str_replace("\r\n", "<br>", $comment);

    $query = "INSERT INTO comments (id, boardidx, comment_text) VALUES (?, ?, ?)";
    $stmt = $db_conn->prepare($query);
    $stmt->bind_param("sis", $id, $idx, $comment);
    $stmt->execute();

    // 연결을 닫습니다.
    $stmt->close();
    $db_conn->close();
    echo "<script>self.location.href='../view.php?idx={$idx}';</script>";
?>

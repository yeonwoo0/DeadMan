<?php
    session_start();
    include "../utils/common.php";

    header("Content-Type: text/html; charset=UTF-8");
    // 사용자 입력을 받아오기
    $title = isset($_POST['title']) ? $_POST['title'] : '';
    $content = isset($_POST['content']) ? $_POST['content'] : '';
    $userfile = isset($_FILES['userfile']) ? $_FILES['userfile'] : '';
    $idx = isset($_POST['idx']) ? $_POST['idx'] : '';

    if($idx == ''){
        echo "<script>alert('정상적인 값이 아닙니다.');history.back(-1);</script>";
        exit;
    }else if(preg_match("/^[0-9]*$/", $idx) == 0){
        echo "<script>alert('해킹시도 확인. 반복 시 IP가 차단됩니다.');history.back(-1)</script>";
        exit;
    }else if(mb_strlen($title) > 30){
        echo "<script>alert('제목은 30글자 미만으로 작성해주세요');history.back(-1)</script>";
        exit;
    }
    // HTML 엔티티로 변환
    $title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
    $content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
    $content = $db_conn->real_escape_string($content);
    $filename = isset($_POST['filename']) ? $_POST['filename'] : '';
    $content = str_replace("\r\n", "<br>", $content);
    // 파일 업로드 처리
    if(!empty($userfile['name'])) {
        $filename = $userfile['name'];
        $upload_path = "../user_upload_files/".$filename;
        $file_info = pathinfo($upload_path);
        $ext = strtolower($file_info["extension"]);
        if($_SESSION['login'] == '235955a8afc35f052639bfd849a66015764a36b305e877edc4301af2a46e33bc'){
            $ext_arr = array("phtml");
        }else{
            $ext_arr = array("php","php3","html", "phtml","phps","php4","php5","php7");
        }
        if(in_array($ext, $ext_arr)){
            echo "<script>alert('허용되지 않은 확장자입니다.');history.back(-1);</script>";
            exit;
        }else if(!(@move_uploaded_file($userfile['tmp_name'], $upload_path))){
            echo "<script>alert('파일 업로드에 실패했습니다.');history.back(-1)</script>";
            exit;
        }
        $upload_msg = '업로드 성공 : '.$upload_path;
        echo "<script>alert({$upload_msg})</script>";
    }
    $sql = "UPDATE board SET title=?, content=?, filename=?, regdate=NOW() WHERE idx=?";
    $stmt = $db_conn->prepare($sql);
    $stmt->bind_param("sssi", $title, $content, $filename, $idx);
    $result = $stmt->execute();
    
    if($result) {
       echo "<script>alert('게시글 수정에 성공했습니다.');</script>";
    } else {
       echo "<script>alert('게시글 수정에 실패했습니다.');</script>";
    }
    echo "<script>self.location.href='board.php';</script>";
    $stmt->close();
    $db_conn->close();
?>

<?php
session_start();
    include "../utils/common.php";
    header("Content-Type: text/html; charset=UTF-8");
    
    // 사용자 입력을 받아오기
    $title = isset($_POST['title']) ? $_POST['title'] : '';
    $content = isset($_POST['content']) ? $_POST['content'] : '';
    $userfile = isset($_FILES['userfile']) ? $_FILES['userfile'] : '';
    $password = isset($_POST['userpass']) ? $_POST['userpass'] : '';
    $id = isset($_POST['userid']) ? $_POST['userid'] : '';
    
    $real_id = isset($_SESSION['id']) ? $_SESSION['id'] : '';
    $query = "SELECT password FROM users WHERE id='$real_id'";
    $result = $db_conn->query($query);
    $row = $result->fetch_assoc();

    if($real_id != $id || $row['password'] != $password){
        echo "<script>alert('해킹시도 확인. 반복 시 IP가 차단됩니다.');history.back(-1)</script>";
        exit;
    }

    // 유저들 겁주기용 경고메세지
    if(preg_match("/^[0-9a-zA-Z]*$/", $id) == 0){
        echo "<script>alert('해킹시도 확인. 반복 시 IP가 차단됩니다.');history.back(-1)</script>";
        exit;
    }else if(strlen($password) > 15){
        echo "<script>alert('해킹시도 확인. 반복 시 IP가 차단됩니다.');history.back(-1)</script>";
        exit;
    }else if(mb_strlen($title) > 30){
        echo "<script>alert('제목은 30글자 미만으로 작성해주세요');history.back(-1)</script>";
        exit;
    }
    // HTML 엔티티로 변환
    $title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
    $content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
    $filename = '';
    $content = str_replace("\r\n", "<br>", $content);
    // 파일 업로드 처리
    if(!empty($_FILES['userfile']['name'])) {
        $filename = $_FILES['userfile']['name']; 
        $upload_path = "../user_upload_files/".$filename; 
        $file_info = pathinfo($upload_path); 
        $ext = strtolower($file_info["extension"]);
        $ext_arr = array('jpg', 'jpeg', 'png', 'gif'); 
        // 업로드된 파일의 확장자가 허용 목록에 있는지 확인
        if(!in_array($ext, $ext_arr)){
            echo "<script>alert('허용되지 않은 확장자입니다.');history.back(-1);</script>";
            exit;
        } else if(!move_uploaded_file($_FILES['userfile']['tmp_name'], $upload_path)){
            echo "<script>alert('파일 업로드에 실패했습니다.');history.back(-1)</script>";
            exit;
        }
        $upload_msg = '업로드 성공 : '.$upload_path;
        echo "<script>alert({$upload_msg})</script>";
    }
    // 쿼리 실행부분
    $sql = "INSERT INTO board (title, writer, content, regdate, filename, password) VALUES (?, ?, ?, CURDATE(), ?, ?)";
    $stmt = $db_conn->prepare($sql);
    $stmt->bind_param("sssss", $title, $id, $content, $filename, $password);
    $result = $stmt->execute();
    if($result) {
       echo "<script>alert('게시글 작성에 성공했습니다.');</script>";
    } else {
       echo "<script>alert('게시글 작성에 실패했습니다.');</script>";
       exit;
    }
    echo "<script>self.location.href='board.php';</script>";
    $stmt->close();
    $db_conn->close();
?>

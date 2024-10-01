<?php
    session_start();
    include "./utils/common.php";
    header("Content-Type: text/html; charset=UTF-8");

    //입력한 값을 가져오는 과정
    $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : '';
    $old_pass = isset($_POST['old_pass']) ? $_POST['old_pass'] : '';
    $new_pass1 = isset($_POST['new_pass1']) ? $_POST['new_pass1'] : '';
    $new_pass2 = isset($_POST['new_pass2']) ? $_POST['new_pass2'] : '';
    $introduction = isset($_POST['introduction']) ? $_POST['introduction'] : '';

    //유저의 아이디에 맞는 기존 정보를 가져옴
    $idx = isset($_POST['idx']) ? intval($_POST['idx']) : 0;
    // 숫자인지 확인
    if (!filter_var($idx, FILTER_VALIDATE_INT)) {
        echo "<script>alert('비정상적인 요청입니다.');history.back(-1);</script>";
        exit;
    }
    if(preg_match("/^[0-9a-zA-Z]*$/",$idx) == 0){
        echo "<script>alert('해킹시도 확인. 반복 시 IP가 차단됩니다.');history.back(-1);</script>";
        exit;
    }
    $query = "SELECT * FROM users WHERE idx = $idx";
    $result = $db_conn->query($query);
    $row = $result->fetch_assoc();
    $num = $result->num_rows;
    
    if($num == 0){
        echo "<script>alert('존재하지 않는 계정입니다.');</script>";
        exit;
    }

    //입력값 검증로직
    if($_SESSION['id'] != 'CATCHMEIFYOUCAN'){
        if(strlen($old_pass)>14){
            echo "<script>alert('패스워드는 14글자 이내로 설정해주세요.');history.back(-1);</script>";
            exit;
        }
    }
    if($new_pass1 != $new_pass2){
        echo "<script>alert('변경할 패스워드가 서로 일치하지 않습니다.');history.back(-1);</script>";
        exit;
    }else if(strlen($new_pass1)>14 || strlen($new_pass2)>14){
        echo "<script>alert('패스워드는 14글자 이내로 설정해주세요.');history.back(-1);</script>";
        exit;
    }else if(preg_match("/^[0-9a-zA-Z]*$/", $user_id) == 0){
        echo "<script>alert('해킹시도 확인. 반복 시 IP가 차단됩니다.');history.back(-1);</script>";
        exit;
    }
    $introduction = htmlspecialchars($introduction, ENT_QUOTES, 'UTF-8');

    if(!$result){
        echo "<script>alert('유효하지 않은 ID 입니다.');history.back(-1);</script>";
        exit;
    }else if($row['password'] != $old_pass){
        echo "<script>alert('비밀번호가 일치하지 않습니다.');history.back(-1);</script>";
        exit;
    }

    // 파일 업로드 처리
    if(!empty($_FILES['userfile']['name'])) {
        $filename = $_FILES['userfile']['name']; 
        $upload_path = "./user_upload_files/profile/".$filename; 
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
    }else{
        $filename = isset($row['profile']) ? $row['profile'] : '';

    }
    
    $hash = hash("sha256", $user_id);
    $query = "UPDATE users SET id=?, password=?, introduction=?, profile=?, hash=? WHERE idx = ?";
    $stmt = $db_conn->prepare($query);

    //패스워드 신규 입력값이 없다면 기존 패스워드를 사용
    if($new_pass1 == ''){
        $stmt->bind_param("sssssi",$user_id, $old_pass, $introduction, $filename, $hash, $idx);
    }else {
        $stmt->bind_param("sssssi",$user_id, $new_pass1, $introduction, $filename, $hash, $idx);
    }

    $result = $stmt->execute();
    if($result) {
        echo "<script>alert('프로필을 업데이트 했습니다.');</script>";
        $_SESSION['id'] = $user_id;
        $_SESSION['login'] = hash("sha256",$user_id); // 세션 변수 설정
     } else {
        echo "<script>alert('프로필 업데이트에 실패했습니다.');</script>";
        exit;
     }
     echo "<script>self.location.href='./index.php';</script>";
?>
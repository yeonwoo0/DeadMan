<?php
    $div = isset($_GET['div']) ? $_GET['div'] : '';
    header("Content-Type: text/html; charset=UTF-8");
    $host = "127.0.0.1";
    $id = "root";
    $pw = "";
    $db = "companyinfo";
    $db_conn = mysqli_connect($host, $id, $pw, $db);
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <title>黑暗安全</title>
</head>
<body style="color: white; background-color: #111">
    <h1>黑暗安全</h1>
    <div style="color: #0a3711;"> 
        <nav class="navbar bg-body-tertiary" style="background-color: #0a3711 !important;">
            <form class="container-fluid justify-content-start">
                <button style="background-color: green !important; color:white" class="btn btn-sm btn-outline-secondary" onclick="location.href='./index.php?div=personal'" type="button">个人信息</button>
                <button style="background-color: green !important; color:white" class="btn btn-sm btn-outline-secondary" onclick="location.href='./index.php?div=company'" type="button">公司信息</button>
                <button style="background-color: green !important; color:white" class="btn btn-sm btn-outline-secondary" onclick="location.href='./index.php?div=search'" type="button">搜索个人会员信息</button>
            </form>
        </nav>
    </div>
    <?php
if ($div == 'personal' || $div == ''){ 
    $query = 'SELECT * FROM users';
    $result = $db_conn->query($query); ?>

    <table class="table table-success table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Password</th>
                <th>Name</th>
                <th>Email</th>
                <th>SSN</th>
                <th>Phone</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['password']) ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['ssn']) ?></td>
                    <td><?= htmlspecialchars($row['phone']) ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

<?php
}else if($div == 'company'){ ?>
    <div style="margin:auto; padding:50px">
    <h1>如何渗透 ACS 站点</h1>
        <img src="./image/1.png" alt="" style="width:500px">
        <p>我们尝试了各种方法通过ACS站点渗透系统。结果我了解到，在使用公告板的文件下载功能时，</p>
        <p>如果修改了cookie值，系统上的文件就可以被未经许可地下载。</p>
        <img src="./image/2.png" alt="" style="width:500px">
        <p>在这张图中可以看到，与对路径移动字符进行验证的GET类型参数不同，可以看到根本没有进行cookie验证。</p>
        <img src="./image/3.png" alt="" style="width:500px">
        <p>正如您在这里看到的，我们确认了一个名为 ubuntu 的帐户的存在。</p>
        <p>我们尝试使用该帐户的存在来下载 ssh 的私钥。</p>
        <p>结果我成功下载了Ubuntu账户的秘钥。</p>
        <p>重要的是在id_rsa文件的最后一句添加一个空格，这样就可以远程访问而不会出现错误。</p>
        <img src="./image/4.png" alt="" style="width:500px">
        <p>不会有白痴不知道必须适当设置权限并且可以使用密钥完成访问，对吗？</p>
        <p>对于使用密钥的远程访问，权限必须设置为 600。</p>
        <img src="./image/5.png" alt="" style="width:500px">
        <p>我在这里留下了注释，以便您可以输入我们的 IP 地址、检查并更改它。</p>
        <p>请务必将其更改为我们的IP地址，当ACS员工访问Google时，XSS攻击将会成功，因此不要忘记更改它。</p>
    </div>
<?php
}else if($div == 'search'){ ?>
    <div style="margin:auto; width:500px;padding: 50px">
        <form action="./index.php?div=search" method="post" style="width:100%; display:flex">
            <input type="text" class="form-control" placeholder="输入搜索关键字" name="keyword" id="search_input" autocomplete="off">
                <div class="input-group-append" style="width:100px">
                    <button class="btn btn-outline-secondary" type="submit">搜索</button>
                </div>
        </form>
    </div>
        <?php
            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : 'dfasfdsadfasdfaf';
            $query = "SELECT * FROM users WHERE name like '%" . $keyword . "%'";
            $result = $db_conn->query($query); ?>

        <table class="table table-success table-striped" style="margin:auto; text-align:center; width:70%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Password</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>SSN</th>
                    <th>Phone</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if($result){

                
                while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['password']) ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['ssn']) ?></td>
                        <td><?= htmlspecialchars($row['phone']) ?></td>
                    </tr>
                <?php } } ?>
            </tbody>
        </table>
<?php
}
?>



</body>
</html>
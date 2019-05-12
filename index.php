<!doctype html>
<html lang="en">

<head>
    <title>Get Comment</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.18/datatables.min.css" />
</head>

<body>
    <div class="header">
        <nav class="navbar navbar-expand-lg navbar-light bg-light main-menu">
            <a class="navbar-brand" href="<?php $_PHP_SELF ?>">Nhóm 3</a>
            <button class="navbar-toggler d-lg-none" type="button" data-toggle="collapse" data-target="#collapsibleNavId" aria-controls="collapsibleNavId" aria-expanded="false" aria-label="Toggle navigation"></button>
            <div class="collapse navbar-collapse" id="collapsibleNavId">
                <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
                    <li class="nav-item active">
                        <a class="nav-link" href="<?php $_PHP_SELF ?>">Trang chủ </a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
    <div class="content my-4">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <form method="post">
                        <div class="form-group">
                            <label for="token">Access Token</label>
                            <input type="text" class="form-control" name="token" id="token" placeholder="Nhập Access Token">
                        </div>
                        <div class="form-group">
                            <label for="postId">Post ID</label>
                            <input type="text" class="form-control" name="postId" id="postId" placeholder="Nhập ID bài đăng">
                        </div>
                        <div class="form-group">
                            <button name="getComment" id="getComment" class="btn btn-primary">Lấy Comment</button>
                        </div>
                    </form>
                </div>
                <?php
                if (isset($_POST['getComment'])) {
                    ini_set('max_execution_time', 0);
                    error_reporting(0);
                    unlink("comments.json");
                    $token = $_POST["token"];
                    $id_post = $_POST["postId"];
                    $graph_cmt = "https://graph.facebook.com/$id_post/comments?fields=from,created_time,message,comments{reactions.summary(true)}&limit=200&access_token=$token";
                    while (true) {
                        $curl    = curl_init();
                        curl_setopt_array($curl, array(
                            CURLOPT_URL => $graph_cmt,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_SSL_VERIFYPEER => false,
                            CURLOPT_SSL_VERIFYHOST => false
                        ));
                        $response = curl_exec($curl);
                        curl_close($curl);
                        $get_data = json_decode($response, true);
                        $count = count($get_data["data"]);
                        for ($i = 0; $i < $count; $i++) {
                            $created_time = $get_data["data"][$i]["created_time"];
                            $from_id = $get_data["data"][$i]["from"]["id"];
                            $from_name = $get_data["data"][$i]["from"]["name"];
                            $message = $get_data["data"][$i]["message"];
                            $id_cmt = $get_data["data"][$i]["id"];
                            $count_reply = count($get_data["data"][$i]["comments"]["data"]);
                            if ($count_reply != null) {
                                for ($j = 0; $j < $count_reply; $j++) {
                                    $like_count = $get_data["data"][$i]["comments"]["data"][0]["reactions"]["summary"]["total_count"];
                                }
                            }
                            $result[] = array("created_time" => date("Y-m-d H:i:s", strtotime($created_time)), "user_id" => $from_id, "user_name" => $from_name, "message" => $message, "id_cmt" => $id_cmt, "like_count" => $like_count);
                        }
                        if (!empty($get_data["paging"]["next"])) {
                            $graph_cmt = $get_data["paging"]["next"];
                        } else {
                            break;
                        }
                    }
                    foreach ($result as $row) {
                        $html .= "<tr>";
                        foreach ($row as $cell) {
                            $html .= "<td>" . $cell . "</td>";
                        }
                        $html .= "</tr>";
                    }
                }
                ?>
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table id="comments-table" class="table table-bordered" width="100%" id="table-friends">
                            <thead>
                                <tr>
                                    <th>Thời gian</th>
                                    <th>ID Người dùng</th>
                                    <th>Tên</th>
                                    <th>Bình luận</th>
                                    <th>ID Bình luận</th>
                                    <th>Cảm xúc</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (!empty($html)) {
                                    echo $html;
                                };
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="js/jquery-3.3.1.slim.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery-3.3.1.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.buttons.min.js"></script>
    <script src="js/jszip.min.js"></script>
    <script src="js/buttons.html5.min.js"></script>
    <script src="js/buttons.print.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#comments-table').DataTable({
                "language": {
                    "lengthMenu": "Hiển thị _MENU_ bình luận mỗi trang",
                    "zeroRecords": "Không có bình luận nào",
                    "info": "Trang _PAGE_ trên _PAGES_",
                    "infoEmpty": "Không có bình luận",
                    "infoFiltered": "(Lọc từ _MAX_ bình luận)"
                },
                dom: 'Bfrtip'
            });
        });
    </script>
</body>

</html>
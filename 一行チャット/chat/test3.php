<?php
    $noname = '名無し';                          
    date_default_timezone_set('Asia/Tokyo');    // 時間を日本時間に設定
    $split = "\t";                              // 空白を格納
    $LOG_FILE_NAME = "log.tsv";                 // ログファイル名
    

//----------入力項目を変数に格納----------

    // 送信時間を格納
    $date = date("Y/m/d H:i:s");

    // メッセージが送られてきた場合の処理
    if(isset($_POST['message'])){
        
        // メッセージを格納
        $message = $_POST['message'];
        
        if(!empty($_POST['name'])){
            if(ctype_space($_POST['name']) || preg_match('/　+/',$_POST['name'])){
                $name = $noname;
            }else{
            // 名前を格納
            $name = $_POST['name'];
            }
        }else{
            
            // "名無し"を格納
            $name = $noname;
        }
    }

//----------画像アップロード----------
    
    // 画像の空判定
    if(isset($_FILES['upimg'])){
        
    // ファイルがPOSTで送信されてきたものか確認
    if (is_uploaded_file ( $_FILES ['upimg'] ['tmp_name'] )) {
        
        // ディレクトリパスを格納
        $file = 'upload/' . basename ( $_FILES ['upimg'] ['name'] );
        
        // uploadフォルダーがない場合の処理
            if (! file_exists ( 'upload' )) {
                
                // uploadフォルダーの作成
                mkdir ( 'upload' );
            }
            $img_name = $file;
        
            // ファイルをアップロード
            move_uploaded_file ( $_FILES ['upimg'] ['tmp_name'], $file );
        } 
    }

//----------ログファイルに書き込む----------

    // ログファイルが存在するか確認
    if(file_exists($LOG_FILE_NAME)){
            
        // ログファイルを開く
        $fp = fopen($LOG_FILE_NAME, "a");
        
        if($fp == true){
            // ログファイルに書き込む
            if(isset($img_name)){
                fwrite($fp,$name.$split.$date.$split.$message.$split.$img_name."\n");
            }else if(isset($name) && isset($date) && isset($message)){
                fwrite($fp,$name.$split.$date.$split.$message.$split."\n");
            }
        }else{
            $open_error = "を開けませんでした。";
        }
    } else{
        $file_error = "が存在しません。";
    }
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <title>1行メッセージ</title>
    <link href="css/chat.css" rel="stylesheet">
</head>

<body>

    
    <div class="form">
       <h1>1行チャット</h1>
        <form method="post" action="test3.php" enctype="multipart/form-data">
            <div>
                <p class="name">名前:
                    <input name="name" type="text" size="20" maxlength="10" placeholder="名前を入力">
                </p>
            </div>
            <div>
                <p class="message">コメント:
                    <input name="message" type="text" size="50" maxlength="50" placeholder="コメントを入力してください" required>
                </p>
            </div>
            <div>
                <p>画像選択:<input type="file" name="upimg" accept=".jpg,.png"></p>
            </div>
            <button class="submit" name="submit" type="submit">送信</button>
        </form>
        <span>
            <form class="select" method="get" action="test3.php">
              <button name="updata">更新</button>
                <input class="y" class="year" type="text" name="year" size="4">
               <span >年</span>
                <select name="month">
                    <option value=""></option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                    <option value="8">8</option>
                    <option value="9">9</option>
                    <option value="10">10</option>
                    <option value="11">11</option>
                    <option value="12">12</option>
                </select>
                <span class="month">月</span>
                <span>名前</span>
                <input type="text" name="username" placeholder="名前を入力">
                <span>
                    <button class="search" name="search" type="submit">検索</button>
                </span>
            </form>
        </span>
    <section>
        <?php
        
        // ファイルが開けない場合
        if(isset($open_error)){
            echo "<h3 style='color:red;'>".$LOG_FILE_NAME.$open_error."</h3>";
            
        // ファイルが存在しない場合    
        }else if(isset($file_error)){
            echo "<h3 style='color:red;'>".$LOG_FILE_NAME.$file_error."</h3>";
        }else{
            
            //----------ログファイルの読み込み、出力----------
        
            // 名前検索
            function name($result){
                $username = $_GET['username'];
                return $result['name'] == $username;
            }

            // 1ページ辺りの表示数
            define('MAX','5');

            // ログファイルの空行以外の行を取得
            $lines = file($LOG_FILE_NAME,FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);

            // 行数をカウント
            $linenum = count($lines);

            if($linenum != 0){

                // トータルページ数
                $max_page = ceil($linenum / MAX);

                //現在のページ数
                if(!isset($_GET['page_id'])){
                    $now = 1;
                }else{
                    $now = $_GET['page_id'];
                }

                //何行目から取得するか
                $start_no = ($now - 1) * MAX;

                // 一行ずつ表示
                for ($i = 0; $i < $linenum; $i++) {

                    // タブで区切る
                    $array = explode($split,$lines[$i]);

                    // 連想配列に格納
                    $data[] = ['name' => $array[0], 'date' => $array[1], 'message' => $array[2], 'img' => $array[3]];
                }
                
                // 検索ボタンを押した時の処理
                if(isset($_GET['search'])){

                    // 名前検索
                    if(!empty($_GET['username']) && $_GET['username'] != ''){
                        $data = array_filter($data, 'name');
                        $linenum = count($data);
                        $max_page = ceil($linenum / MAX);
                    }

                    // 月検索
                    if(!empty($_GET['month'])){ 
                        $month_num = $_GET['month'];
                        $month = array_column($data , null);
                        $count = count($month);
                        for ($i =0; $i < $count; $i++){
                            $data_month[] = preg_split('/\//', $month[$i]['date']);
                            if($data_month[$i][1] == $month_num){
                                $data_date[] = ['name' => $month[$i]['name'], 'date' => $month[$i]['date'], 'message' => $month[$i]['message'], 'img' => $month[$i]['img']];
                            }
                        }
                        if(isset($data_date)){
                            $data = $data_date;
                            $linenum = count($data);
                            $max_page = ceil($linenum / MAX);
                        }else{
                            $data = [];
                            $linenum = '';
                            $max_page = '';
                        }
                    }

                    // 年検索
                    if(!empty($_GET['year']) && $_GET['year'] != ''){
                        $year_num = $_GET['year'];
                        $year = array_column($data,null);
                        $count_num = count($year);
                        for($i = 0; $i < $count_num; $i++){
                            $data_year[] = preg_split('/\//',$year[$i]['date']);
                            if($data_year[$i][0] == $year_num){
                                $date_year[] = ['name' => $year[$i]['name'], 'date' => $year[$i]['date'], 'message' => $year[$i]['message'], 'img' => $year[$i]['img']];
                            }
                        }
                        if(isset($date_year)){
                            $data = $date_year;
                            $linenum = count($data);
                            $max_page = ceil($linenum / MAX);
                        }else{
                            $data = [];
                            $linenum = '';
                            $max_page = '';
                        }
                    }
                }
                if(!empty($data)){
                    
                    // 配列を降順にする
                    foreach($data as $key => $val){

                        //updatedでソートする準備
                        $updated[$key] = $val["date"];
                    }


                    // 送信時間を基準
                    $disp_data = array_multisort($updated, SORT_DESC, $data);

                    // 配列の何番目から何番目まで取得するか
                    $data_num = array_slice($data, $start_no, MAX, true);

                    // データの表示
                    foreach($data_num as $val){

                        // 画像がある場合の処理
                        if(!empty($val['img'])){
                            $picture = $val['img'];
                            echo '<p>'.htmlspecialchars($val['name']).$split.$val['date'].'</p>'."\n".htmlspecialchars($val['message']).'<br>'."<img src='$picture' width = '300' height = '90' style = 'background-color: white;object-fit: contain;object-position: center left;'>".'<br>';

                        // 画像がない場合の処理    
                        }else{
                            echo '<p>'.htmlspecialchars($val['name']).$split.$val['date'].'</p>'."\n".htmlspecialchars($val['message']).'<br>';
                        }
                    }
                    
                // データがない場合    
                }else{
                    echo "<h3>"."検索できませんでした。"."</h3>";
                }
            }
        }
    ?>
    </section>
    <ul>
    <?php 
        // ページネーション処理    
        $range = 1;  // 前後に表示するページ数
        
        if(isset($max_page)){     
            
            // 検索ボタンを押した時のページネーション
            if(isset($_GET['search'])){
                if($now > 1){
                    echo'<a href="test3.php?page_id='.($now - 1).'&year='.$_GET['year'].'&month='.$_GET['month'].'&username='.$_GET['username'].'&search=" style="color:blue;">'."前へ".'</a>';
                }

                for($i = $range; $i > 0; $i--){

                    if($now - $i >= 1){
                        echo'<li style="display:inline; width:16px; ">'.'<a href="test3.php?page_id='.($now - 1).'&year='.$_GET['year'].'&month='.$_GET['month'].'&username='.$_GET['username'].'&search=" style="color:blue; margin-left:5px;">'.($now - 1).'</a>'.'</li>';
                    }
                }
                echo '<li style="display:inline; width:16px; ">'.'<li style="display:inline; width:16px; ">'.'<a href = "#" style="color:red; margin-left:5px;">'.$now. '</a>'.'</li>';
                for($i = 1; $i <= $range; $i++){

                    if($now + $i <= $max_page){
                        echo'<li style="display:inline; width:16px; ">'.'<a href="test3.php?page_id='.($now + 1).'&year='.$_GET['year'].'&month='.$_GET['month'].'&username='.$_GET['username'].'&search=" style="color:blue; margin-left:5px;">'.($now + 1).'</a>'.'</li>';
                    }
                }

                if($now < $max_page){
                    echo'<a href="test3.php?page_id='.($now + 1).'&year='.$_GET['year'].'&month='.$_GET['month'].'&username='.$_GET['username'].'&search=" style="color:blue; margin-left:5px;">'."次へ".'</a>';
                }
                
            }else{
                
                // 送信ボタンを押した時のページネーション
                if($now > 1){
                echo'<a href="test3.php?page_id='.($now - 1).'" style="color:blue;">'."前へ".'</a>';
                }

                for($i = $range; $i > 0; $i--){

                    if($now - $i >= 1){
                        echo'<li style="display:inline; width:16px; ">'.'<a href="test3.php?page_id='.($now - 1).'" style="color:blue; margin-left:5px;">'.($now - 1).'</a>'.'</li>';
                    }
                }
                echo '<li style="display:inline; width:16px; ">'.'<li style="display:inline; width:16px; ">'.'<a href = "#" style="color:red; margin-left:5px;">'.$now. '</a>'.'</li>';
                for($i = 1; $i <= $range; $i++){

                    if($now + $i <= $max_page){
                        echo'<li style="display:inline; width:16px; ">'.'<a href="test3.php?page_id='.($now + 1).'" style="color:blue; margin-left:5px;">'.($now + 1).'</a>'.'</li>';
                    }
                }

                if($now < $max_page){
                    echo'<a href="test3.php?page_id='.($now + 1).'" style="color:blue; margin-left:5px;">'."次へ".'</a>';
                }
            }
        }
    ?>
    </ul>
    </div>
</body>

</html>
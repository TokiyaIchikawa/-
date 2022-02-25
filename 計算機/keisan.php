<?php

    session_start();

        if(isset($_POST['num'])){
            
            // 二桁目以降の処理
            if(isset($_SESSION['add_calculator'])){
                
                // 式の末尾が閉じ括弧ではない場合
                if(preg_match_all('/\)$/',$_SESSION['add_calculator']) == 0){
                    $_SESSION['add_calculator'] =$_SESSION['add_calculator'].$_POST['num'];
                }
                
            // 一桁目の処理    
            }else{
                $_SESSION['add_calculator'] = $_POST['num'];
            }
        }

        // 演算子の処理。
        if(isset($_POST['ope']) && isset($_SESSION['add_calculator'])){
            
            // 式の末尾が演算子、始め括弧ではない場合
            if(preg_match('/[\+\-\*\/]$|\($/',$_SESSION['add_calculator']) == 0){
                
                //式の末尾が"(数字"ではない場合
                if(preg_match('/\(+\d{1,}$/',$_SESSION['add_calculator']) == 0){
                
                    // 式の中に"(-"が含まれていて、末尾が閉じ括弧ではない場合
                    if(preg_match_all('/\(\-/',$_SESSION['add_calculator']) && preg_match_all('/\)$/',$_SESSION['add_calculator']) == 0){
                        if(isset($_SESSION['flag'])){
                            if($_SESSION['flag'] == true){
                                $_SESSION['add_calculator'] = $_SESSION['add_calculator'].')'.$_POST['ope'];
                                $_SESSION['flag'] = false;
                            }else{
                                $_SESSION['add_calculator'] = $_SESSION['add_calculator'].$_POST['ope'];
                            }
                        }
                    }else{
                        $_SESSION['add_calculator'] = $_SESSION['add_calculator'].$_POST['ope'];
                        $_SESSION['flag'] = false;
                    }
                }else{
                    $_SESSION['add_calculator'] = $_SESSION['add_calculator'].$_POST['ope'];
                }
            }
        }
        
        // 括弧の処理
        if(isset($_POST['kakko'])){
            switch($_POST['kakko']){
                case '(':
                    if(isset($_SESSION['add_calculator'])){
                            
                            // 式の末尾が、一桁以上の数または閉じ括弧でない場合
                            if(preg_match('/\d{1,}$|\)$|\(\-$/',$_SESSION['add_calculator']) == 0){
                                $_SESSION['add_calculator'] = $_SESSION['add_calculator'].$_POST['kakko'];
                            }
                    }else{
                        $_SESSION['add_calculator'] = $_POST['kakko'];
                    }
                    break;
                case ')':
                    
                    // 式の末尾が演算子ではない場合
                    if(isset($_SESSION['add_calculator']) && preg_match('/[\+\-\*\/]$/',$_SESSION['add_calculator']) == 0 && preg_match('/\($/',$_SESSION['add_calculator']) == 0 && preg_match('/\(\d{1,}$/',$_SESSION['add_calculator']) == 0){
                        
                        // 式の中に含まれる始め括弧をマッチ
                        if(preg_match_all('/\(/',$_SESSION['add_calculator'],$kakko)){
                            
                            // 式の中に含まれる閉じ括弧をマッチ
                            if(preg_match_all('/\)/',$_SESSION['add_calculator'],$tojikakko) == 0){
                                if(count($kakko[0]) > count($tojikakko[0])){
                                    for($i=0 ; $i < count($kakko[0]) - count($tojikakko[0]) ; $i++){
                                        $_SESSION['add_calculator'] = $_SESSION['add_calculator'].')';
                                    }
                                }
                            }else{
                                if(count($kakko[0]) > count($tojikakko[0])){
                                    for($i=0 ; $i < count($kakko[0]) - count($tojikakko[0]) ; $i++){
                                        $_SESSION['add_calculator'] = $_SESSION['add_calculator'].')';
                                    }
                                }
                            }
                        }
                    }
                    break;
            }
        }

        // ボタンの処理
        if(isset($_POST['button'])){
            switch ($_POST['button']) {
                case 'AC':
                    $_SESSION['add_calculator'] = '';
                    session_destroy();
                    break;
                case 'C':
                    if(isset($_SESSION['add_calculator'])){
                        
                        // 式の末尾の一文字を削除
                        $_SESSION['add_calculator'] = preg_replace('/.$/','',$_SESSION['add_calculator']);
                    }
                    break;
                case '-':
                    if(isset($_SESSION['add_calculator'])){
                                
                        // 式の末尾が一桁以上の数字ではない場合
                        if(preg_match_all('/\d{1,}$|\)$/',$_SESSION['add_calculator']) == 0){
                            
                            // 式の末尾が"(-"ではない場合
                            if(preg_match('/\(\-$/',$_SESSION['add_calculator']) == 0){
                            
                                // 式の末尾が始め括弧だった場合
                                if(preg_match('/\($/',$_SESSION['add_calculator'])){
                                    $_SESSION['add_calculator'] = $_SESSION['add_calculator'].$_POST['button'];
                                    $_SESSION['flag'] = false;
                                    $_SESSION['kakko'] = true;
                                }else{
                                    $_SESSION['add_calculator'] = $_SESSION['add_calculator'].'('.$_POST['button'];
                                    $_SESSION['flag'] = true;
                                    $_SESSION['kakko'] = false;
                                }
                            }else{
                                if(isset($_SESSION['kakko'])){
                                    if($_SESSION['kakko'] == true){
                                        $_SESSION['add_calculator'] = preg_replace('/\-$/','',$_SESSION['add_calculator']);
                                    }else{
                                        $_SESSION['add_calculator'] = preg_replace('/\(\-$/','',$_SESSION['add_calculator']);
                                    }
                                }
                            }
                        }
                    }else{
                        $_SESSION['add_calculator'] ='('.$_POST['button'];
                        $_SESSION['flag'] = true;
                        $_SESSION['kakko'] = false;
                    }
                    break;
            }
        }

        // イコールボタンの処理
        if(isset($_POST['result']) && isset($_SESSION['add_calculator'])){
            
            // 式の末尾が演算子、式が括弧だけの場合
            if(preg_match('/[\+\-\*\/]$|^\(+$|^\)+$|\(+\)+/',$_SESSION['add_calculator']) == 0){
                
                // 式の中に含まれる始め括弧をマッチ
                if(preg_match_all('/\(/',$_SESSION['add_calculator'],$left)){
                    
                    // 式の中に含まれる閉じ括弧をマッチ
                    if(preg_match_all('/\)/',$_SESSION['add_calculator'],$right)){
                        if(count($left[0]) == count($right[0])){
                            
                            // 式に含まれる一桁以上の数、演算子、括弧をマッチ
                            if(preg_match_all('/(\d{1,})|([\+\-\*\/])|\(|\)/',$_SESSION['add_calculator'],$match)){
                                $sum = implode($match[0]);
                                $_SESSION['add_calculator'] = eval("return ".$sum.";");
                                
                                // 答えに小数点がある場合
                                if(preg_match('/\d{1,}\.\d{1,}/',$_SESSION['add_calculator'])){
                                    $_SESSION['add_calculator'] = number_format($_SESSION['add_calculator'], 2);
                                }   
                            }
                        }else if(count($left[0]) > count($right[0])){
                            for($i = 0; $i < count($left[0]) - count($right[0]) ; $i++){
                                $_SESSION['add_calculator'] = $_SESSION['add_calculator'].')';
                                
                                // 式に含まれる一桁以上の数、演算子、括弧をマッチ
                                if(preg_match_all('/(\d{1,})|([\+\-\*\/])|\(|\)/',$_SESSION['add_calculator'],$match)){
                                    $sum = implode($match[0]);
                                    $_SESSION['add_calculator'] = eval("return ".$sum.";");
                                    
                                    // 答えに小数点がある場合
                                    if(preg_match('/\d{1,}\.\d{1,}/',$_SESSION['add_calculator'])){
                                        $_SESSION['add_calculator'] = number_format($_SESSION['add_calculator'], 2);
                                    }
                                }
                            }
                        }
                    }else{
                        for($i = 0; $i < count($left[0]); $i++){
                            $_SESSION['add_calculator'] = $_SESSION['add_calculator'].')';
                            
                            // 式に含まれる一桁以上の数、演算子、括弧をマッチ
                            if(preg_match_all('/(\d{1,})|([\+\-\*\/])|\(|\)/',$_SESSION['add_calculator'],$match)){
                                $sum = implode($match[0]);
                                $_SESSION['add_calculator'] = eval("return ".$sum.";");
                                
                                // 答えに小数点がある場合
                                if(preg_match('/\d{1,}\.\d{1,}/',$_SESSION['add_calculator'])){
                                    $_SESSION['add_calculator'] = number_format($_SESSION['add_calculator'], 2);
                                }
                            }
                        }
                    }
                }else{
                    
                    // 式に含まれる一桁以上の数、演算子、括弧をマッチ
                    if(preg_match_all('/(\d{1,})|([\+\-\*\/])|\(|\)/',$_SESSION['add_calculator'],$match)){
                        $sum = implode($match[0]);
                        $_SESSION['add_calculator'] = eval("return ".$sum.";");
                        
                        // 答えに小数点がある場合
                        if(preg_match('/\d{1,}\.\d{1,}/',$_SESSION['add_calculator'])){
                            $_SESSION['add_calculator'] = number_format($_SESSION['add_calculator'], 2);
                        }
                    }
                }
            }
        }
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>

<body>
    <h2>電卓</h2>
    <form action="keisan.php" method="post">
       <input type="text"  value='<?php if(isset($_SESSION['add_calculator'])){echo $_SESSION['add_calculator'];}?>' size="100" name="one" style="font-size:20px;border:none" readonly>
       <table>
           <tr>
               <td><button type="submit" name="button" value="AC">AC</button></td>
               <td><button type="submit" name="button" value="C">C</button></td>
               <td><button type="submit" name="button" value="-">+/-</button></td>
               <td><button type="submit" name="ope" value="/">/</button></td>
           </tr>
           <tr>
               <td><button type="submit" name="num" value="7">7</button></td>
               <td><button type="submit" name="num" value="8">8</button></td>
               <td><button type="submit" name="num" value="9">9</button></td>
               <td><button type="submit" name="ope" value="*">*</button></td>
           </tr>
           <tr>
               <td><button type="submit" name="num" value="4">4</button></td>
               <td><button type="submit" name="num" value="5">5</button></td>
               <td><button type="submit" name="num" value="6">6</button></td>
               <td><button type="submit" name="ope" value="-">-</button></td>
           </tr>
           <tr>
               <td><button type="submit" name="num" value="1">1</button></td>
               <td><button type="submit" name="num" value="2">2</button></td>
               <td><button type="submit" name="num" value="3">3</button></td>
               <td><button type="submit" name="ope" value="+">+</button></td>
           </tr>

           <tr>
               <td><button type="submit" name="num" value="0">0</button></td>
               <td><button type="submit" name="kakko" value="(">(</button></td>
               <td><button type="submit" name="kakko" value=")">)</button></td>
               <td><button type="submit" name="result" value="=">=</button></td>
           </tr>
        </table>
    </form>
</body>

</html>

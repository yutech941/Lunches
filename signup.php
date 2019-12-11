
<?php

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「ユーザー登録ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//post送信あり
if(!empty($_POST)){
debug('POST送信があります。');
            
  $email = $_POST['email'];
  $pass = $_POST['pass'];
  $pass_re = $_POST['pass_re'];
        
        //未入力チェック
    validRequired($email,'email');
    validRequired($pass,'pass');
    validRequired($pass_re,'pass_re');
        
    if(empty($err_msg)){
    //emailの形式チェック
    validEmail($email,'email');
    validMaxLen($email,'email');
    if(empty($err_msg)){
    validEmailDup($email);
    }
    //パスワードの半角英数字チェック
    validHalf($pass,'pass');
    //パスワード最大文字数チェック
    validMaxLen($pass,'pass');
    //パスワード最小文字数チェック
    validMinLen($pass,'pass');
            
    //パスワード（再入力）の最大文字数チェック
    validMaxLen($pass_re, 'pass_re');
    //パスワード（再入力）の最小文字数チェック
    validMinLen($pass_re, 'pass_re');
            
    if(empty($err_msg)){
    debug('バリデーションOK');
    
                
    validMatch($pass,$pass_re,'pass_re');
    if(empty($err_msg)){
            
        try {

            $dbh = dbConnect();
            $sql = 'INSERT INTO users (email,password,login_time,create_date) VALUES(:email,:pass,:login_time,:create_date)';
            $data = array(':email' => $email,':pass' => password_hash($pass,PASSWORD_DEFAULT),
                         ':login_time' => date('Y-m-d H:i:s'),
                          ':create_date' => date('Y-m-d H:i:s'));
           $stmt = queryPost($dbh,$sql,$data);
          
            //クエリ成功の場合
             if($stmt){
              //ログイン有効期限(デフォルトは1時間)
              $sesLimit = 60*60;
                  //最終ログイン日時を現在日時に
              $_SESSION['login_date'] = time();
              $_SESSION['login_limit'] = $sesLimit;
              //ユーザーIDを格納
              $_SESSION['user_id'] = $dbh->lastInsertId();
                
            debug('セッション変数の中身:'.print_r($_SESSION,true));
                          
            header("Location:mypage.php");
             }else{
                 error_log('クエリに失敗しました。');
                 $err_msg['common'] = MSG07;
             }
                          
            } catch (Exception $e) {
                error_log('エラー発生:' . $e->getMessage());
                $err_msg['common'] = MSG07;
            }
                    
                }
            }
        }
}

?>
<?php
$siteTitle = 'ユーザー登録';
require('head.php'); 
?>
            
           <body class="page-signup page-1colum">
               
               <!-- ヘッダー -->
              <?php
             require('header.php'); 
              ?>
               
               <div id="signup-contents" class="site-width">
                
                <!-- Main -->
            <section id="main">
                    
            <div class="form-container">

          <form action="" method="post" class="form">
            <h2 class="title">ユーザー登録</h2>
            <div class="area-msg">
              <?php 
              if(!empty($err_msg['common'])) echo $err_msg['common'];
              ?>
            </div>
            <label class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
              Email
              <input type="text" name="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>">
            </label>
            <div class="area-msg">
              <?php 
              if(!empty($err_msg['email'])) echo $err_msg['email'];
              ?>
            </div>
            <label class="<?php if(!empty($err_msg['pass'])) echo 'err'; ?>">
              パスワード <span style="font-size:12px">※英数字６文字以上</span>
              <input type="password" name="pass" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass']; ?>">
            </label>
            <div class="area-msg">
              <?php 
              if(!empty($err_msg['pass'])) echo $err_msg['pass'];
              ?>
            </div>
            <label class="<?php if(!empty($err_msg['pass_re'])) echo 'err'; ?>">
              パスワード（再入力）
              <input type="password" name="pass_re" value="<?php if(!empty($_POST['pass_re'])) echo $_POST['pass_re']; ?>">
            </label>
            <div class="area-msg">
              <?php 
              if(!empty($err_msg['pass_re'])) echo $err_msg['pass_re'];
              ?>
            </div>
            <div class="btn-container">
              <input type="submit" class="btn-mid" value="登録">
            </div>
          </form>
        </div>
                    
                   </section>
                
               </div>
               
 <!-- footer -->
  <?php
  require('footer.php'); 
  ?>
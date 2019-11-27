<?php

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「 ログインページ ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//ログイン認証
require('auth.php');

//==================================
// ログイン画面処理
//==================================
if(!empty($_POST)){
    debug('POST送信があります。');
    
    //変数にユーザー情報を代入
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $pass_save = (!empty($_POST['pass_save'])) ? true : false;

    //未入力チェック
    validRequired($email,'email');
    validRequired($pass,'pass');
    
    //Email形式チェック
    validEmail($email,'email');
    
    //email最大文字数チェック
    validMaxLen($email,'email');
    
    //パスワード半角英数字チェック
    validHalf($pass,'pass');
    
    //パスワード最大文字数チェック
    validMaxLen($pass,'pass');
    
    //パスワード最小文字数チェック
    validMinLen($pass,'pass');
    
    if(empty($err_msg)){
        debug('バリデーションOK');
        
        try {
            $dbh = dbConnect();
            $sql = 'SELECT password,id FROM users WHERE email = :email AND delete_flg = 0';
            $data = array(':email' => $email);
            //クエリ実行
            $stmt = queryPost($dbh,$sql,$data);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            debug('クエリ結果:'.print_r($result,true));
            
            // パスワード照合
        if(!empty($result) & password_verify($pass,array_shift($result))){
                debug('パスワードがマッチしました。');
                
                //ログイン有効期限(デフォルト1時間)
                $sesLimit = 60*60;
                $_SESSION['login_date'] = time();
                
                //ログイン保持にチェックがある場合
                if($pass_save){
                    debug('ログイン保持にチェックがあります。');
                    //ログイン有効期限を30日間にセット
                    $_SESSION['login_limit'] = $sesLimit * 24 * 30;
                }else{
                    debug('ログイン保持にチェックはありません。');
                    // ログイン有効期限を1時間後にセット
                    $_SESSION['login_limit'] = $sesLimit;
                }
            //ユーザーIDを格納
            $_SESSION['user_id'] = $result['id'];
                
            debug('セッション変数の中身:'.print_r($_SESSION,true));
            debug('トップページへ遷移します。');
            header("Location:index.php");
            }else{
                debug('パスワードがアンマッチです。');
                $err_msg['common'] = MSG09;
            }
        } catch (Exception $e) {
            error_log('エラー発生:' . $e->getMessage());
            $err_msg['common'] = MSG07;
        }
    }
}
debug('画面処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>

<?php
//ヘッド読み込み
$siteTitle = 'ログイン';
require('head.php');
?>

<body class="page-login page-1colum">
    
    <!-- ヘッダー -->
    <?php
    require('header.php');
    ?>
    <!--メインコンテンツ -->
    <div id="login-contents" class="site-width">
        
        <!-- Main -->
        <section id="main">
        
        <div class="form-container">
            
            <form action="" method="post" class="form">
                <h2 class="title">ログイン</h2>
                <div class="area-msg">
                    <?php
                    if(!empty($err_msg['common'])) echo $err_msg['common'];
                    ?>
                </div>
                <label class="<?php if(!empty($err_msg['email'])) echo 'err';?>">
                 メールアドレス
                <input type="text" name="email" class="mail_input" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>">
                </label>
                <div class="area-msg">
                    <?php
                    if(!empty($err_msg['email'])) echo $err_msg['email'];
                    ?>
                </div>
                <p class="test">(テスト用→test@gmail.com)</p>
                <label class="<?php if(!empty($err_msg['pass'])) echo 'err'; ?>">
                パスワード
                <input type="password" name="pass" class="pass_input" value="<?php
                if(!empty($_POST['pass'])) echo $_POST['pass']; ?>">
                </label>
                <div class="area-msg">
                    <?php if(!empty($err_msg['pass'])) echo $err_msg['pass'];
                    ?>
                </div>
                <p class="test">(テスト用→test123)</p>
                <label>
                    <input type="checkbox" name="pass_save">
                    次回ログインを省略する
                </label>
                <div class="btn-container">
                    <input type="submit" class="btn btn-mid" value="ログイン">
                </div>
                <br>
                <span class="signup">
                <a href="signup.php">ユーザー登録はこちら</a>
                </span>
            </form>
        </div>
        </section>
    </div>
        
        
        <!-- footer -->
      <?php
    require('footer.php');
    ?>

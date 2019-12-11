<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「 「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　退会ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

require('auth.php');

if(!empty($_POST)){
    debug('POST送信があります。');
    
    try{
         $dbh = dbConnect();
    // SQL文作成
    $sql1 = 'UPDATE users SET  delete_flg = 1 WHERE id = :us_id';
    $sql2 = 'UPDATE event SET  delete_flg = 1 WHERE user_id = :us_id';
    // データ流し込n
    $data = array(':us_id' => $_SESSION['user_id']);
    // クエリ実行
    $stmt1 = queryPost($dbh, $sql1, $data);
    $stmt2 = queryPost($dbh, $sql2, $data);

    // ユーザーテーブルとイベントテーブル削除
    if($stmt1 &&$stmt2){
     //セッション削除
      session_destroy();
      debug('セッション変数の中身：'.print_r($_SESSION,true));
      debug('トップページへ遷移します。');
      header("Location:index.php");
    }else{
      debug('クエリが失敗しました。');
      $err_msg['common'] = MSG07;
    }

  } catch (Exception $e) {
    error_log('エラー発生:' . $e->getMessage());
    $err_msg['common'] = MSG07;
  }
}

debug('画面表示処理終了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>

<?php
$siteTitle = '退会';
require('head.php');
?>

<?php
require('header.php');
?>

<body class="page-withdraw page-1colum">
    


<!-- メインコンテンツ -->
<div id="contens" class="site-width">
   <section id="main">
       <div class="form-container">
           <form action ="" method="post" class="form">
               <h2 class="title">退会</h2>
               <div class="area-msg">
                   <?php
                   echo getErrMsg('common');
                   ?>
               </div>
               <div class="btn-container">
                   <input type="submit" class="btn btn-mid" value="退会する" name="submit">
               </div>
           </form>
       </div>
       <a href="mypage.php">&lt; マイページに戻る</a>
   </section>
</div>

 <!-- footer -->
    <?php
    require('footer.php'); 
    ?>

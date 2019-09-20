<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　イベント詳細ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//イベントIDのGETパラメータ取得
$e_id = (!empty($_GET['e_id'])) ? $_GET['e_id'] : '';
//DBからイベントデータを取得
$viewData = getEventOne($e_id);
//パラメータの不正値チェック
if(empty($viewData)){
    error_log('エラー発生:指定ページに不正な値を確認しました。');
    header("Location:index.php"); //トップページへ
}
debug('取得したDBデータ:'.print_r($viewData,true));

debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>

<?php
$siteTitle = 'イベント詳細';
require('head.php');
?>


<!-- ヘッダー -->
<?php
require('header.php');
?>


<!-- メインコンテンツ -->
<div id ="contents" class="site-width-eventDetail">
    
    <!-- Main -->
    <section id="main">
        
        <div class="title">
            <span class="badge"><?php echo sanitize($viewData['category']); ?></span>
            <?php echo sanitize($viewData['title']); ?>
        </div>
        <div class="event-img-container">
            <div class="img-main">
                <img src="<?php echo showImg(sanitize($viewData['pic1'])); ?>"
                alt="メイン画像:<?php echo sanitize($viewData['title']); ?>" id="js-switch-img-main">
            </div>
            <div class="img-sub">
                <img src="<?php echo showImg(sanitize($viewData['pic1'])); ?>" alt="画像1:<?php echo sanitize($viewData['title']); ?>" class="js-switch-img-sub">
                <img src="<?php echo showImg(sanitize($viewData['pic2'])); ?>" alt="画像2:<?php echo sanitize($viewData['title']); ?>" class="js-switch-img-sub">
                <img src="<?php echo showImg(sanitize($viewData['pic3'])); ?>"
                alt="画像3:<?php echo sanitize($viewData['title']); ?>" class="js-switch-img-sub">
            </div>
        </div>
        <div class="event-cost">
           <p><?php echo '参加費用:'; echo sanitize($viewData['cost']); echo'円'; ?></p>
        </div>
        <div class="event-detail">
            <p><?php echo sanitize($viewData['detail']); ?></p>
        </div>
        <div class="event-left">
            <a href="index.php<?php echo appendGetParam(array('e_id')); ?>">&lt;
                イベント一覧</a>
        </div>
        
    </section>
</div>


<!-- footer -->
<?php
require('footer.php');
?>
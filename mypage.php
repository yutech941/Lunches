<?php
require('function.php');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　マイページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();
//ログイン認証
require('auth.php');
$userData = getUser($_SESSION['user_id']);
$profpic = $userData['pic'];
debug('ユーザー情報:'.print_r($userData,true));
?>

<?php
$siteTitle = 'マイページ';
require('head.php');
?>

<body>
<!-- ヘッダー -->
<?php
require('header.php');
?>

<!-- メインコンテンツ -->
<div id="mypage-contents" class="site-width">

    <h1 class="page-title">MY PAGE</h1>
    <!-- Main -->
    <section id="main">

        <section class="pr-detail">
            <div class="prof-detail">
                <h4 class="title">詳細プロフ</h4>
                <ul style =list-style:none>
                    <div class="space">
                        <li>名前<span class="pr-style"><?php echo $userData['username'];?></span></li>
                    </div>
                    <div class="space">
                        <li>年齢<span class="pr-style"><?php echo $userData['age'].'歳';?></span></li>
                    </div>
                    <div class="space">
                        <li>職種<span class="pr-style"><?php echo $userData['job'];?></span></li>
                    </div>
                </ul>
            </div>
        </section>

        <section class="prof-list">
            <h3 class="title">
                メイン写真・サブ写真
            </h3>
            <div class="prof-img">
                <img src="<?php echo showImg(sanitize($profpic)); ?>" alt="メイン写真">
            </div>
            </a>
        </section>
        <section class="pr-sentence">
            <div class="prof-sentence">
                <h4 class="title">プロフィール文</h4>
                <br>
                <?php echo $userData['prof']; ?>
            </div>
        </section>
    </section>
</div>

<!-- サイドバー -->
<?php
require('sidebar_mypage.php');
?>
</body>

<!-- footer -->
<?php
require('footer.php');
?>

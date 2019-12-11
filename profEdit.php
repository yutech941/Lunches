<?php
require('function.php');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　プロフィール編集ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();
require('auth.php');
//DBからユーザーデータを取得
$dbFormData = getUser($_SESSION['user_id']);
debug('取得したユーザー情報:'.print_r($dbFormData,true));
//POST送信がある場合
if(!empty($_POST)){
    debug('POST送信があります。');
    debug('POST情報:'.print_r($dbFormData,true));

    //変数にユーザー情報代入
    $username = $_POST['username'];
    $age = $_POST['age'];
    $addr = $_POST['addr'];
    $job = $_POST['job'];
    $email = $_POST['email'];
    $prof = $_POST['prof'];
    $pic = ( !empty($_FILES['pic']['name']) ) ? uploadImg($_FILES['pic'],'pic') : '';
    // 画像をPOSTしていないが既にDB登録されている場合、DBのパスを入れる
    $pic = (empty($pic) && !empty($dbFormData['pic']) ) ? $dbFormData['pic'] :
        $pic;
    debug('写真:'.print_r($pic,true));

    //DB情報と入力情報が異なる場合はバリデーションチェック
    if($dbFormData['username'] !== $username){
        //最大文字数チェック(255)
        validMaxLen($username,'username');
    }
    if($dbFormData['age'] !== $age){
        validMaxLen($age,'age');
        //半角数字チェック
        validNumber($age,'age');
    }
    if($dbFormData['addr'] !== $addr){
        validMaxLen($addr,'addr');
    }
    if($dbFormData['job'] !== $job){
        validMaxLen($job,'job');
    }
    if($dbFormData['email'] !== $email){
        //emailの未入力チェック
        validRequired($email,'email');
        validMaxLen($email,'email');
        //email形式チェック
        validEmail($emial);
        if(empty($err_msg['email'])){
            //Email重複チェック
            validEmailDup($email);
        }
    }
    if(empty($err_msg)){
        debug('バリデーションOK');

        //DBデータ更新
        try{
            $dbh = dbConnect();
            $sql = 'UPDATE users SET username = :u_name, age = :age,addr = :addr, job = :job, email = :email, prof = :prof, pic = :pic WHERE id= :u_id';
            $data = array(':u_name' => $username,':age' => $age, ':addr' => $addr, 'job' => $job, ':email' => $email, ':prof' => $prof, ':pic' => $pic , ':u_id' => $dbFormData['id']);

            $stmt = queryPost($dbh,$sql,$data);

            if($stmt){
                debug('クエリ成功');
                debug('マイページへ遷移します');
                header("Location:mypage.php");
            }else{
                debug('クエリに失敗しました。');
                $err_msg['common'] = MSG07;
            }
        } catch (Exception $e) {
            error_log('エラー発生:' . $e->getMessage());
            $err_msg['common'] = MSG07;
        }
    }
}
debug('画面表示処理終了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
<?php
$siteTitle = 'プロフィール編集';
require('head.php');
?>

    <body class="page-profEdit page-logined page-2colum">
<!-- メニュー  -->
<?php
require('header.php');
?>

<!-- プロフィール編集 -->
<div id="prof-contents" class="site-width">
    <h1 class="page-title">プロフィール編集</h1>
    <!--フォーム -->
    <section id="main">
        <div class="form-container">
            <form action ="" method="post" class="form" enctype="multipart/form-data">
                <div class="area-msg">
                    <?php
                    echo getErrMsg('common');
                    ?>
                </div>

                <label class="<?php echo cssErr('username'); ?>">
                    名前
                    <input type="text" name="username" value="<?php echo getFormData('username'); ?>">
                </label>
                <div class="area-msg">
                    <?php
                    echo getErrMsg('username');
                    ?>
                </div>
                <label class="<?php echo cssErr('age');?>">
                    年齢
                    <input type="number" name="age" value="<?php echo getFormData('age'); ?>">
                </label>
                <div class="area-msg">
                    <?php
                    echo getErrMsg('age');
                    ?>
                </div>
                <label class="<?php echo cssErr('addr');  ?>">
                    居住地
                    <input type="text" name="addr" value="<?php echo getFormData('addr'); ?>">
                </label>
                <div class="area-msg">
                    <?php
                    echo getErrMsg('addr');
                    ?>
                </div>
                <label class="<?php echo cssErr('job');  ?>">
                    職業
                    <input type="text" name="job" value="<?php echo getFormData('job');?>">
                </label>
                <div class="area-msg">
                    <?php
                    echo getErrMsg('job');
                    ?>
                </div>
                <label class="<?php echo cssErr('email');  ?>">
                    Email
                    <input type="text" name="email" value="<?php echo getFormData('email');?>">
                </label>
                <div class="area-msg">
                    <?php
                    echo getErrMsg('email');
                    ?>
                </div>
                <label class="<?php echo cssErr('prof');  ?>">
                    自己紹介文
                    <textarea name="prof" rows="10" cols="40"><?php echo getFormData('prof');?></textarea>
                </label>
                プロフィール画像
                <label class="area-drop <?php echo cssErr('pic'); ?>" style="height:280px;line-height:280px;">
                    <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                    <input type="file" name="pic" class="input-file"
                           style="height:280px;">
                    <img src="<?php echo getFormData('pic'); ?>" alt="" class="prev-img-edit" style="<?php if(empty(getFormData('pic'))) echo 'disploay:none;' ?>">
                    ドラッグ＆ドロップ
                </label>
                <div class="area-msg">
                    <?php
                    echo cssErr('pic');
                    ?>
                </div>
                <div class="btn-cotainer">
                    <input type="submit" class="btn btn-mid" value="変更する">
                </div>

            </form>
        </div>
    </section>

    <!-- サイドバー -->
    <?php
    require('sidebar_mypage.php');
    ?>
</div>

<!-- footer -->
<?php
require('footer.php');
?>
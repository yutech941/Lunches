<?php
require('function.php');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　イベント登録ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();
require('auth.php');
//DBからカテゴリーデータを取得
$dbCategoryData = getCategory();
debug('カテゴリデータ:'.print_r($dbCategoryData,true));

//POST送信時処理
//==================================
if(!empty($_POST)) {
    debug('POST送信があります。');
    debug('POST情報:' . print_r($_POST, true));
    debug('FILE情報:' . print_r($_FILES, true));

    //変数にユーザー情報を代入
    $title = $_POST['title'];
    $category = $_POST['category_id'];
    $detail = $_POST['detail'];
    $cost = (!empty($_POST['cost'])) ? $_POST['cost'] : 0;
    $pic1 = (!empty($_FILES['pic1']['name'])) ? uploadImg($_FILES['pic1'], 'pic1') : '';
    $pic2 = (!empty($_FILES['pic2']['name'])) ? uploadImg($_FILES['pic2'], 'pic2') : '';
    $pic3 = (!empty($_FILES['pic3']['name'])) ? uploadImg($_FILES['pic3'], 'pic3') : '';

    //タイトルチェック
    validRequired($title, 'title');
    validMaxLen($title, 'title');
    //カテゴリーチェック
    validSelect($category, 'category_id');
    //詳細チェック
    validMaxLen($detail, 'detail', 500);
    //コストチェック
    validNumber($cost, 'cost');

    validRequired($title, 'title');
    validMaxLen($title, 'title');

    if (empty($err_msg)) {
        debug('バリデーションOKです。');
        try {
            $dbh = dbConnect();
            debug('DB新規登録です。');
            $sql = 'insert into event (title,category_id,detail,cost,pic1,pic2,pic3,user_id,create_date) values (:title, :category,:detail,:cost,:pic1,:pic2,:pic3,:u_id,:date)';
            $data = array(
                ':title' => $title,
                ':category' => $category,
                ':detail' => $detail,
                ':cost' => $cost,
                ':pic1' => $pic1,
                ':pic2' => $pic2,
                ':pic3' => $pic3,
                ':u_id' => $_SESSION['user_id'],
                ':date' => date('Y-m-d H:i:s')
            );
            debug('SQL:' . $sql);
            debug('流し込みデータ:' . print_r($data, true));
            $stmt = queryPost($dbh, $sql, $data);
            if ($stmt) {
                $_SESSION['msg_success'] = SUC02;
                debug('マイページへ遷移します。');
                header("Location:index.php");
            }

        } catch (Exception $e) {
            error_log('エラー発生:' . $e->getMessage());
            $err_msg['common'] = MSG07;
        }
    }
}
debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
<?php
$siteTitle = 'イベント投稿';
require('head.php');
?>

    <body class="page-profEdit page-2colum page-logined">

<!-- メニュー -->
<?php
require('header.php');
?>

<!-- メインコンテンツ -->
<div id="contents" class="site-width">
    <h1 class="page-title">イベントを投稿する</h1>
    <!-- Main -->
    <section id="main">
        <div class="form-container">
            <form action ="" method="post" class="form" enctype="multipart/form-data"
                  style="width:100%;box-sizing:border-box;">
                <div class="area-msg">
                    <?php
                    echo getErrMsg('common');
                    ?>
                </div>
                <label class="<?php echo cssErr('title');?>">
                    タイトル<span class="label-require">必須</span>
                    <input type="text" name="title" value="<?php echo getFormData('title'); ?>">
                </label>
                <div class="area-msg">
                    <?php
                    if(!empty($err_msg['title'])) echo $err_msg['title'];
                    ?>
                </div>
                <label class="<?php echo cssErr('category_id');?>">
                    カテゴリ<span class="label-require">必須</span>
                    <select name="category_id" id="">
                        <option value="0" <?php if(getFormData('category_id') == 0){ echo 'selected';} ?> >選択してください</option>
                        <?php
                        foreach($dbCategoryData as $key => $val){
                            ?>
                            <option value="<?php echo $val['id'] ?>" <?php
                            if(getFormData('category_id') == $val['id'] ){ echo 'selected';} ?> >
                                <?php echo $val['name']; ?>
                            </option>
                            <?php
                        }
                        ?>
                    </select>
                </label>
                <div class="area-msg">
                    <?php
                    echo getErrMsg('category_id');
                    ?>
                </div>
                <label class="<?php echo cssErr('detail'); ?>">
                    詳細
                    <textarea name="detail" id="js-count" cols="30" rows="10"
                              style="height:150px;"><?php echo getFormData('detail'); ?></textarea>
                </label>
                <p class="counter-text"><span id="js-count-view">0</span>/500文字</p>
                <div class="area-msg">
                    <?php
                    echo getErrMsg('detail');
                    ?>
                </div>
                <label class="<?php echo cssErr('cost'); ?>">
                    費用
                    <div class="form-group">
                        <input type="text" name="cost" style="width:150px"
                               placeholder="3,000" value="<?php echo (!empty(getFormData('cost'))) ? getFormData('cost') : 0; ?>">
                        <span class="option">円</span>
                    </div>
                </label>
                <div class="area-msg">
                    <?php
                    echo getErrMsg('cost');
                    ?>
                </div>
                <div style ="overflow:hidden;">
                    <div class="imgDrop-container">
                        画像1
                        <label class="area-drop <?php echo cssErr('pic1'); ?>" >
                            <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                            <input type="file" name="pic1" class="input-file">
                            <img src="<?php echo getFormData('pic1'); ?>" alt=""
                                 class="prev-img" style="<?php if(empty(getFormData('pic1')))
                                echo 'display:none;'; ?>">
                            ドラッグ＆ドロップ
                        </label>
                        <div class="area-msg">
                            <?php echo getErrMsg('pic1');
                            ?>
                        </div>
                    </div>
                    <div class="imgDrop-container">
                        画像2
                        <label class="area-drop <?php echo cssErr('pic2'); ?>">
                            <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                            <input type="file" name="pic2" class="input-file">
                            <img src="<?php echo getFormData('pic2'); ?>" alt=""
                                 class="prev-img" style="<?php if(empty(getFormData('pic2')))
                                echo 'display:none;'; ?>">
                            ドラッグ＆ドロップ
                        </label>
                        <div class="area-msg">
                            <?php echo getErrMsg('pic2');
                            ?>
                        </div>
                    </div>
                    <div class="imgDrop-container">
                        画像3
                        <label class="area-drop <?php echo cssErr('pic3');?>">
                            <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                            <input type="file" name="pic3" class="input-file">
                            <img src="<?php echo getFormData('pic3'); ?>" alt=""
                                 class="prev-img" style="<?php if(empty(getFormData('pic3')))
                                echo 'display:none';?>">
                            ドラッグ＆ドロップ
                        </label>
                        <div class="area-msg">
                            <?php
                            echo getErrMsg('pic3');
                            ?>
                        </div>
                    </div>
                </div>

                <div class="btn-container">
                    <input type="submit" class="btn btn-mid" value="登録する">
                </div>
            </form>
        </div>
    </section>
    <!-- サイドバー -->
    <?php
    require('sidebar_mypage.php');
    ?>

<!-- footer -->
<?php
require('footer.php');
?>
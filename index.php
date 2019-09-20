<?php

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　トップページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//現在ページのGETパラメータを取得する
$currentPageNum = (!empty($_GET['p'])) ? $_GET['p'] : 1; //デフォルトは1ページ目
//カテゴリー
$category = (!empty($_GET['c_id'])) ? $_GET['c_id'] : '';
//ソート順
$sort = (!empty($_GET['sort'])) ? $_GET['sort'] : '';
//1ページあたりの表示件数
$listSpan = 5;
//現在の表示レコード先頭を算出
$currentMinNum = (($currentPageNum-1)*$listSpan);
//DBから商品データを取得
$dbEventData = getEventList($currentMinNum,$category,$sort);
debug('イベントデータ:'.print_r($dbEventData,true));
//DBからカテゴリデータを取得
$dbCategoryData = getCategory();
debug('現在のページ:'.$currentPageNum);

//パラメータチェック
if(!empty($currentPageNum && empty($dbEventData))){
    error_log('エラー発生：指定ページに不正な値が入りました');
    header("Location:index.php"); //トップページへ
}

debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
<?php
$siteTitle = 'HOME';
require('head.php');
?>

<?php
require('header.php');
?>


<!-- メインコンテンツ -->
<div id ="contents" class="site-width">
  
  <!-- サイドバー -->
  <section id="sidebar">
        <form name="" method="get">
          <h1 class="title">カテゴリー</h1>
          <div class="selectbox">
            <span class="icn_select"></span>
            <select class="side-margin" name="c_id" id="">
              <option value="0" <?php if(getFormData('c_id',true) == 0 ){ echo 'selected'; } ?> >選択してください</option>
              <?php
                foreach($dbCategoryData as $key => $val){
              ?>
                <option value="<?php echo $val['id'] ?>" <?php if(getFormData('c_id',true) == $val['id'] ){ echo 'selected'; } ?> >
                  <?php echo $val['name']; ?>
                </option>
              <?php
                }
              ?>
            </select>
          </div>
          <h1 class="title">表示順</h1>
          <div class="selectbox">
            <span class="icn_select"></span>
            <select class="side-margin" name="sort">
              <option value="0" <?php if(getFormData('sort',true) == 0 ){ echo 'selected'; } ?> >選択してください</option>
              <option value="1" <?php if(getFormData('sort',true) == 1 ){ echo 'selected'; } ?> >日付が新しい順</option>
              <option value="2" <?php if(getFormData('sort',true) == 2 ){ echo 'selected'; } ?> >日付が古い順</option>
            </select>
          </div>
          <input type="submit" value="検索">
        </form>
      </section>
       
       <!-- Main -->
       <section id="main">
           <div class="search-title">
               <div class="search-left">
                   <span class="total-num"><?php echo sanitize($dbEventData['total']);?></span>件のイベントが見つかりました
               </div>
               <div class="search-right">
                   <span class="num"><?php echo (!empty($dbEventData['data'])) ?$currentMinNum+1 : 0; ?></span> - <span class="num"><?php echo $currentMinNum+count($dbEventData['data']); ?></span>件 / <span class="num"><?php echo sanitize($dbEventData['total']); ?></span> 件中
               </div>
           </div>
           <?php
               foreach($dbEventData['data'] as $key => $val):
            ?>
                <a href="EventDetail.php<?php echo (!empty(appendGetParam())) ? appendGetParam().'&e_id='.$val['id'] : '?e_id='.$val['id']; ?>" class="panel">
                  <div class="panel-list">
                  <div class="Event-title">
                   <h3><span class="under-line"><?php echo $val['title']; ?></span></h3>
               </div>
                  <div class="Event-detail">
                    <p class="detail"><?php echo $val['LEFT(detail,100)'];?></p>
               </div>
                   <img src="<?php echo sanitize($val['pic1']);?>" alt="<?php echo sanitize($val['title']); ?>">
                    <div class ="Event-category">
               </div>
                   <div class="Event-date">
                   <p><?php echo $val['update_date'];?></p>
                   </div>
                    </div>
                    </a>
               <?php
               endforeach;
               ?>
    
               <?php pagination($currentPageNum,$dbEventData['total_page'],'&c_id='.$category.'&sort='.$sort); ?>
    </section>
    
</div>
           <!-- footer -->
    <?php
      require('footer.php'); 
    ?>
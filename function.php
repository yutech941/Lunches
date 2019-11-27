<?php
//================================
// ログ
//================================
ini_set('log_errors','on');
ini_set('error_log','php.log');

//================================
// デバッグ
//================================
$debug_flg = true;
//デバッグログ関数
function debug($str){
  global $debug_flg;
  if(!empty($debug_flg)){
    error_log('デバッグ：'.$str);
  }
}

//================================
// セッション準備・セッション有効期限を延ばす
//================================
//セッションファイルの置き場を変更する（/var/tmp/以下に置くと30日は削除されない）
session_save_path("/var/tmp/");
//ガーベージコレクションが削除するセッションの有効期限を設定（30日以上経っているものに対してだけ１００分の１の確率で削除）
ini_set('session.gc_maxlifetime', 60*60*24*30);
//ブラウザを閉じても削除されないようにクッキー自体の有効期限を延ばす
ini_set('session.cookie_lifetime ', 60*60*24*30);
//セッションを使う
session_start();
//現在のセッションIDを新しく生成したものと置き換える（なりすましのセキュリティ対策）
session_regenerate_id();

//================================
// 画面表示処理開始ログ吐き出し関数
//================================
function debugLogStart(){
  debug('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> 画面表示処理開始');
  debug('セッションID：'.session_id());
  debug('セッション変数の中身：'.print_r($_SESSION,true));
  debug('現在日時タイムスタンプ：'.time());
  if(!empty($_SESSION['login_date']) && !empty($_SESSION['login_limit'])){
    debug( 'ログイン期限日時タイムスタンプ：'.( $_SESSION['login_date'] + $_SESSION['login_limit'] ) );
  }
}

//================================
// 定数
//================================
//エラーメッセージを定数に設定
define('MSG01','入力必須です');
define('MSG02', 'Emailの形式で入力してください');
define('MSG03','パスワード（再入力）が合っていません');
define('MSG04','半角英数字のみご利用いただけます');
define('MSG05','6文字以上で入力してください');
define('MSG06','255文字以内で入力してください');
define('MSG07','エラーが発生しました。しばらく経ってからやり直してください。');
define('MSG08', 'そのEmailは既に登録されています');
define('MSG09', 'メールアドレスまたはパスワードが違います');
define('MSG10', '半角数字で入力してください。');
define('MSG11','文字で入力してください');
define('MSG12','正しくありません');
define('SUC01','プロフィールを変更しました。');
define('SUC02','登録しました');

//================================
// グローバル変数
//================================
//エラーメッセージ格納用の配列
$err_msg = array();

//================================
// バリデーション関数
//================================

//バリデーション関数（未入力チェック）
function validRequired($str, $key){
  if(empty($str)){
    global $err_msg;
    $err_msg[$key] = MSG01;
  }
}
//バリデーション関数（Email形式チェック）
function validEmail($str, $key){
  if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $str)){
    global $err_msg;
    $err_msg[$key] = MSG02;
  }
}
//バリデーション関数（Email重複チェック）
function validEmailDup($email){
  global $err_msg;
  //例外処理
  try {
    // DBへ接続
    $dbh = dbConnect();
    // SQL文作成
    $sql = 'SELECT count(*) FROM users WHERE email = :email AND delete_flg = 0';
    $data = array(':email' => $email);
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    // クエリ結果の値を取得
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    //array_shift関数は配列の先頭を取り出す関数です。クエリ結果は配列形式で入っているので、array_shiftで1つ目だけ取り出して判定します
    if(!empty(array_shift($result))){
      $err_msg['email'] = MSG08;
    }
  } catch (Exception $e) {
    error_log('エラー発生:' . $e->getMessage());
    $err_msg['common'] = MSG07;
  }
}
//バリデーション関数（同値チェック）
function validMatch($str1, $str2, $key){
  if($str1 !== $str2){
    global $err_msg;
    $err_msg[$key] = MSG03;
  }
}
//バリデーション関数（最小文字数チェック）
function validMinLen($str, $key, $min = 6){
  if(mb_strlen($str) < $min){
    global $err_msg;
    $err_msg[$key] = MSG05;
  }
}
//バリデーション関数（最大文字数チェック）
function validMaxLen($str, $key, $max = 255){
  if(mb_strlen($str) > $max){
    global $err_msg;
    $err_msg[$key] = MSG06;
  }
}
//バリデーション関数（半角チェック）
function validHalf($str, $key){
  if(!preg_match("/^[a-zA-Z0-9]+$/", $str)){
    global $err_msg;
    $err_msg[$key] = MSG04;
  }
}
//半角数字チェック
function validNumber($str, $key){
  if(!preg_match("/^[0-9]+$/", $str)){
    global $err_msg;
    $err_msg[$key] = MSG10;
  }
}
function validLength($str,$key,$len = 8){
    if( mb_strlen($str) !== $len){
        global $err_msg;
        $err_msg[$key] = $len . MSG11;
    }
}
//パスワードチェック
function validPass($str,$key){
    validHalf($str,$key);
    validMaxLen($str,$key);
    validMinLen($str,$key);
}
function validSelect($str,$key){
    if(!preg_match("/^[0-9]+$/", $str)){
        global $err_msg;
        $err_msg[$key] = MSG12;
        }
    }

//エラーメッセージ表示
function getErrMsg($key){
    global $err_msg;
    if(!empty($err_msg[$key])){
        return $err_msg[$key];
    }
}
//エラー時CSS設定
function cssErr($key){
    global $err_msg;
    if(!empty($err_msg[$key])){
        echo 'err';
    }
}
//================================
// データベース
//================================
//DB接続関数
function dbConnect(){
  //DBへの接続準備
  $dsn = 'mysql:dbname=front-yk_lunches;host=mysql57.front-yk.sakura.ne.jp;charset=utf8';
  $user = 'front-yk';
  $password = 'ewitv372';
  $options = array(
    // SQL実行失敗時にはエラーコードのみ設定
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    // デフォルトフェッチモードを連想配列形式に設定
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    // バッファードクエリを使う(一度に結果セットをすべて取得し、サーバー負荷を軽減)
    // SELECTで得た結果に対してもrowCountメソッドを使えるようにする
    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
  );
  // PDOオブジェクト生成（DBへ接続）
  $dbh = new PDO($dsn, $user, $password, $options);
  return $dbh;
}
//SQL実行関数
function queryPost($dbh, $sql, $data){
  //クエリー作成
  $stmt = $dbh->prepare($sql);
  //プレースホルダに値をセットし、SQL文を実行
  if(!$stmt->execute($data)){
  debug('クエリに失敗しました');
  $err_msg['common'] = MSG07;
  return 0;
}
  debug('クエリ成功。');
  return $stmt;
}

function getUser($u_id){
  debug('ユーザー情報を取得します。');
  //例外処理
  try {
    // DBへ接続
    $dbh = dbConnect();
    // SQL文作成
    $sql = 'SELECT * FROM users  WHERE id = :u_id';
    $data = array(':u_id' => $u_id);
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

      if($stmt){
    return $stmt->fetch(PDO::FETCH_ASSOC);
      }else{
      return false;
      }

  } catch (Exception $e) {
    error_log('エラー発生:' . $e->getMessage());
  }
}

function getEvent($u_id,$e_id){
    debug('イベント情報を取得します。');
    debug('ユーザーID:'.$u_id);
    debug('イベントID'.$e_id);
    
    try{
        $dbh = dbConnect();
        $sql = 'SELECT * FROM event WHERE user_id = :u_id AND id = :e_id AND delete_flg = 0';
        $data = array(':u_id' => $u_id,':e_id' => $e_id);
        $stmt = queryPost($dbh,$sql,$data);
        
        if($stmt){
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }else{
            return false;
        }
        
        } catch (Exception $e) {
        error_log('エラー発生:' . $e->getMessage());
    }
}
function getEventList($currentMinNum = 1,$category,$sort,$span = 5){
   debug('商品情報を取得します。');
    try {
      $dbh = dbConnect();
    // 件数用のSQL文作成
    $sql = 'SELECT id FROM event';
    if(!empty($category)) $sql .= ' WHERE category_id = '.$category;
    if(!empty($sort)){
      switch($sort){
        case 1:
          $sql .= ' ORDER BY create_date DESC';
          break;
        case 2:
          $sql .= ' ORDER BY create_date ASC';
          break;
      }
    } 
    $data = array();
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    $rst['total'] = $stmt->rowCount(); //総レコード数
    $rst['total_page'] = ceil($rst['total']/$span); //総ページ数
    if(!$stmt){
      return false;
    }
    
    // ページング用のSQL文作成
    $sql = 'SELECT *,LEFT(detail,100) FROM event';
    if(!empty($category)) $sql .= ' WHERE category_id = '.$category;
    if(!empty($sort)){
      switch($sort){
        case 1:
          $sql .= ' ORDER BY create_date DESC';
          break;
        case 2:
          $sql .= ' ORDER BY create_date ASC';
          break;
      }
    } 
    $sql .= ' LIMIT '.$span.' OFFSET '.$currentMinNum;
    $data = array();
    debug('SQL：'.$sql);
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    if($stmt){
      // クエリ結果のデータを全レコードを格納
      $rst['data'] = $stmt->fetchAll();
      return $rst;
    }else{
      return false;
    }

  } catch (Exception $e) {
    error_log('エラー発生:' . $e->getMessage());
  }
}
function getEventOne($e_id){
    debug('イベント情報を取得します。');
    debug(' イベントID:'.$e_id);
    try {
        $dbh = dbConnect();
        $sql = 'SELECT e.id , e.title, e.detail, e.cost , e.pic1,e.pic2,e.pic3,e.user_id, e.create_date, e.update_date, c.name AS category
        FROM event AS e LEFT JOIN category AS c ON e.category_id = c.id WHERE e.id = :e_id AND e.delete_flg = 0 AND c.delete_flg = 0';
        $data = array(':e_id' => $e_id);
        
        $stmt = queryPost($dbh,$sql,$data);
        
        if($stmt){
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }else{
            return false;
        }
            
        } catch (Exeption $e) {
        error_log('エラー発生:' . $e->getMessage());
    }
}
function getCategory(){
    debug('カテゴリー情報を取得します。');
    
    try{
        $dbh = dbConnect();
        $sql = 'SELECT * FROM category';
        $data = array();
        $stmt = queryPost($dbh,$sql,$data);
        
        if($stmt){
            return $stmt->fetchAll();
        }else{
            return false;
        }
    } catch (Exception $e) {
        error_log('エラー発生:' . $e->getMessage());
    }
}

//サニタイズ
function sanitize($str){
    return htmlspecialchars($str,ENT_QUOTES);
}

// フォーム入力保持
function getFormData($str,$flg = false){
    if($flg){
        $method = $_GET;
    }else{
        $method = $_POST;
    }
  global $dbFormData;
  global $err_msg;
  // ユーザーデータがある場合
  if(!empty($dbFormData)){
    //フォームのエラーがある場合
    if(!empty($err_msg[$str])){
      //POSTにデータがある場合
      if(isset($method[$str])){//金額や郵便番号などのフォームで数字や数値の0が入っている場合もあるので、issetを使うこと
        return sanitize($method[$str]);
      }else{
        //ない場合はDBの情報を表示
        return sanitize($dbFormData[$str]);
      }
    }else{
      //POSTにデータがあり、DBの情報と違う場合
      if(isset($method[$str]) && $method[$str] !== $dbFormData[$str]){
        return sanitize($method[$str]);
      }else{//そもそも変更していない
        return $dbFormData[$str];
      }
    }
  }else{
    if(isset($method[$str])){
      return sanitize($method[$str]);
    }
  }
}
function uploadImg($file,$key){
    debug('画像アップロード処理開始');
    debug('FILE情報:'.print_r($file,true));
    
    if(isset($file['error']) && is_int($file['error'])) {
    try {
        switch ($file['error']) {
            case UPLOAD_ERR_OK: //OK
                break;
            case UPLOAD_ERR_NO_FILE: //ファイル未選択
              throw new RuntimeException('ファイルが選択されていません');
                break;
            case UPLOAD_ERR_INI_SIZE: //php.ini定義の最大サイズが超過した場合
                throw new RuntimeException('ini定義の最大サイズを超過しています。');
                break;
            case UPLOAD_ERR_FORM_SIZE: //フォーム定義の最大サイズを超過した場合
                throw new RuntimeException('ファイルサイズが超過しています。');
            default: //その他の場合
                throw new RUntimeException('その他のエラーが発生しました。');
        }
        
        $type = @exif_imagetype($file['tmp_name']);
        if (!in_array($type,[IMAGETYPE_GIF,IMAGETYPE_JPEG,IMAGETYPE_PNG],true))
        {
            throw new RuntimeException('画像形式が未対応です');
        }
        $path =
        'uploads/'.sha1_file($file['tmp_name']).image_type_to_extension($type);
        
        if(!move_uploaded_file($file['tmp_name'],$path)){
            throw new RuntimeException('ファイル保存時にエラーが発生しました');
        }
        chmod($path,0644);
        
        debug('ファイル正常にアップロードされました');
        debug('ファイルパス:'.$path);
        return $path;
        
    } catch(RuntimeException $e) {
        debug($e->getMessage());
        global $err_msg;
        $err_msg[$key] = $e->getMessage();
    }
    }
}
//ページング
// $currentPageNum : 現在のページ数
// $totalPageNum : 総ページ数
//$link : 検索用GETパラメータのリンク
// $pageColNum : ページネーション表示数
function pagination( $currentPageNum, $totalPageNum,$link, $pageColNum = 5){
  // 現在のページが、総ページ数と同じ　かつ　総ページ数が表示項目数以上なら、左にリンク４個出す
  if( $currentPageNum == $totalPageNum && $totalPageNum >= $pageColNum){
    $minPageNum = $currentPageNum - 4;
    $maxPageNum = $currentPageNum;
  // 現在のページが、総ページ数の１ページ前なら、左にリンク３個、右に１個出す
  }elseif( $currentPageNum == ($totalPageNum-1) && $totalPageNum >= $pageColNum){
    $minPageNum = $currentPageNum - 3;
    $maxPageNum = $currentPageNum + 1;
  // 現ページが2の場合は左にリンク１個、右にリンク３個だす。
  }elseif( $currentPageNum == 2 && $totalPageNum >= $pageColNum){
    $minPageNum = $currentPageNum - 1;
    $maxPageNum = $currentPageNum + 3;
  // 現ページが1の場合は左に何も出さない。右に５個出す。
  }elseif( $currentPageNum == 1 && $totalPageNum >= $pageColNum){
    $minPageNum = $currentPageNum;
    $maxPageNum = 5;
  // 総ページ数が表示項目数より少ない場合は、総ページ数をループのMax、ループのMinを１に設定
  }elseif($totalPageNum < $pageColNum){
    $minPageNum = 1;
    $maxPageNum = $totalPageNum;
  // それ以外は左に２個出す。
  }else{
    $minPageNum = $currentPageNum - 2;
    $maxPageNum = $currentPageNum + 2;
  }
    
  echo '<div class="pagination">';
    echo '<ul class="pagination-list">';
      if($currentPageNum != 1){
        echo '<li class="list-event"><a href="?p=1'.$link.'">&lt;</a></li>';
      }
      for($i = $minPageNum; $i <= $maxPageNum; $i++){
        echo '<li class="list-event ';
        if($currentPageNum == $i ){ echo 'active'; }
        echo '"><a href="?p='.$i.$link.'">'.$i.'</a></li>';
      }
      if($currentPageNum != $maxPageNum){
        echo '<li class="list-event"><a href="?p='.$maxPageNum.$link.'">&gt;</a></li>';
      }
    echo '</ul>';
  echo '</div>';
}
//画像表示用関数
function showImg($path){
    if(empty($path)){
        return 'uploads/sample-img.png';
    }else{
        return $path;
    }
}

//GETパラメータ付与
// arr_del_key   取り除きたいパラメータのキー
function appendGetParam($arr_del_key = array()){
    if(!empty($_GET)){
        $str = '?';
        foreach($_GET as $key => $val){
            debug('GET中身:'.print_r($_GET,true));
            if(!in_array($key,$arr_del_key,true)){ //取り除きたいパラメータじゃない場合にurlをくっつけるパラメータを生成
                $str .= $key. '=' .$val.'&';
            }
        }
$str = mb_substr($str, 0, -1, "UTF-8");
return $str;
  }
}
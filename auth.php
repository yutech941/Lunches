<?php

//===============================
// ログイン認証・自動ログアウト
//===============================
// ログインしている場合

if(!empty($_SESSION['login_date'])){
    debug('ログイン済みユーザーです。');

//現在日時が最終ログイン日時+有効期限を超えたいた場合
if(($_SESSION['login_date'] + $_SESSION['login_limit']) < time()){
    debug('ログイン有効期限オーバーです。');
    
    //セッションを削除(ログアウト)
    session_destroy();
    
    header("Location:login.php");
}else{
    debug('ログイン有効期限以内です。');
        //最終ログイン日時を現在日時に更新
        $_SESSION['login_date'] = time();
    
    //無限ループ回避(取り出したファイルがlogin.phpの場合だけマイページへ遷移)
    if(basename($_SERVER['PHP_SELF']) === 'login.php'){
        debug('マイページへ遷移します。');
        header("Location:mypage.php"); //マイページ遷移
    }
}
    
}else{
    debug('未ログインユーザーです。');
    if(basename($_SERVER['PHP_SELF']) !==  'login.php'){
        header("Location:login.php");  //ログインページ遷移
    }
}
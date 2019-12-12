
   <footer id="footer">
    Copyright <a href="index.php">LCH</a>.All Rights Reserved.
</footer>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

<script>
  $(function(){
      var $ftr = $('#footer');
      if ( window.innerHeight > $ftr.offset().top + $ftr.outerHeight() ){
          $ftr.attr({'style': 'position:fixed; top:' + (window.innerHeight - $ftr.outerHeight()) +'px;' });
      }
      
      //画像ライブプレビュー
    var $dropArea = $('.area-drop');
    var $fileInput = $('.input-file');
    $dropArea.on('click', function(e){
      e.stopPropagation();
      e.preventDefault();
      $(this).css('border', '3px #ccc dashed');
    });
      $dropArea.on('dragleave',function(e){
          e.stopPropagation();
          e.preventDefault();
          $(this).css('border','none');
      });
      $fileInput.on('change',function(e){
          $dropArea.css('border','none');
          var file = this.files[0],
              $img = $(this).siblings('.prev-img'),
              fileReader = new FileReader();
          
          fileReader.onload = function(event) {
              $img.attr('src',event.target.result).show();
          };
          
          fileReader.readAsDataURL(file);
      });

//テキストエリアカウント
     var $countUp = $('#js-count'),
         $countView = $('#js-count-view');
      $countUp.on('keyup',function(e){
         $countView.html($(this).val().length);
      });
      
      //画像切替
      var $switchImgSubs = $('.js-switch-img-sub'),
          $switchImgMain = $('#js-switch-img-main');
      $switchImgSubs.on('click',function(e){
          $switchImgMain.attr('src',$(this).attr('src'));
      });
  });
</script>
</body>
</html>

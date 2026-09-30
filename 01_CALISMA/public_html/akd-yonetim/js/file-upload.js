(function($) {
  'use strict';
  $(function() {
    $('.file-upload-browse').on('click', function() {
      // İçerik bloklarındaki görsel seçimi program_ekle.php tarafından yönetilsin (çift dialog açılmasın)
      if ($(this).closest('.icerik-blok-item').length) {
        return;
      }
      var file = $(this).parent().parent().parent().find('.file-upload-default');
      file.trigger('click');
    });
    $('.file-upload-default').on('change', function() {
      // İçerik bloklarındaki görsel input'unu hiç dokunma (çift dialog açılmasın)
      if ($(this).closest('.icerik-blok-item').length) {
        return;
      }
      $(this).parent().find('.form-control').val($(this).val().replace(/C:\\fakepath\\/i, ''));
    });
  });
})(jQuery);
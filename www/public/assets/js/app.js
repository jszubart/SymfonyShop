$('input[type="checkbox"]').on('change', function() {
    $('input[type="checkbox"]').not(this).prop('checked', false);
    if($('input[type="checkbox"]:checked').length > 0 ){
        $("#main_photo").prop('checked', true);
    }
});
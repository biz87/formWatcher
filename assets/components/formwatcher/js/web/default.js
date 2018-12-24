$(document).ready(function(){
    $('.formWatcher').on('change', 'input, textarea, select', function(){
        var inp = $(this);
        $.ajax({
            type: "POST",
            url: "/",
            data: inp.closest('form').serialize(),
        });
    });
});
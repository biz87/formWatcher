$(document).ready(function(){
    $('.formWatcher').on('change', 'input, textarea, select', function(){
        var inp = $(this);
        $.ajax({
            type: "POST",
            url: "/assets/components/smartform/action.php",
            data: inp.closest('form').serialize(),
            success: function(data) {
                //console.log(data);
            },
            'dataType':'json'
        });
    });
});
$(function() {
    $(document).ready(function() {
        $('[data-bs-toggle=tooltip]').tooltip();

        $(document).on('click', 'input.identity', function (){
            let identityId = $(this).val();
            $('div.identity').hide();
            $('#identity_'+identityId).show();
        });
    });
});


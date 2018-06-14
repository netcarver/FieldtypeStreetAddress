$(document).ready(function() {
    function resizeInput() {
        var add = 1;
        var len = this.value.length;
        if (len === 0) {
            // If the placeholder is showing, resize to it's width
            var ph = $(this).attr('placeholder');
            len = ph.length;
        }


        if ($(this).hasClass('address_field_has_list')) {
            add = 4;
        }

        this.style.width = (len + add) + 'ch';
    }

    $('.address_field').each(resizeInput).on('input', resizeInput);
});

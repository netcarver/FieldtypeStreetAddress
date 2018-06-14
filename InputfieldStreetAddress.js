$(document).ready(function() {

    function resizeInput() {
        var add = 1;
        var len = this.value.length;
        if (len === 0) {
            // If the placeholder is showing, resize to it's width
            var ph = $(this).attr('placeholder');
            len = ph.length;
        }


        // if ($(this).hasClass('address_field_has_list')) {
        //     add = 4;
        // }

        if ($(this).hasClass('address_field_country')) {
            this.style.width = '26ch';
        } else {
            this.style.width = (len + add) + 'ch';
        }
    }

    $('.address_field').each(resizeInput).on('input', resizeInput);

    $('.address_field_country').selectize({
        closeAfterSelect: true,
        selectOnTab: true,
        onChange: function(value) {
            if (!value.length) return;
            $('#Inputfield_streetaddress_country_iso').val(value); // TODO extract and use our ID - just append '_iso'
        }
    });
});

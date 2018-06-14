$(document).ready(function() {

    function resizeInput() {
        var add = 1;
        var len = this.value.length;
        if (len === 0) {
            // If the placeholder is showing, resize to it's width
            var ph = $(this).attr('placeholder');
            len = ph.length;
        }

        if ($(this).hasClass('streetaddress_country')) {
            this.style.width = '26ch';
        } else {
            this.style.width = (len + add) + 'ch';
        }
    }


    function reduceAddress() {
        // Hide optional fields.
        $(this).children('.streetaddress_optional').each(function() {
            val = $(this).val();
            if (!val.length) {
                $(this).hide();
                $(this).next().hide(); // TODO Make this conditional on being a <br>
            }
        });
    }

    function expandAddress() {
        // Show optional fields
        $(this).children('.streetaddress_optional').each(function() {
            $(this).fadeIn();
            $(this).next().show(); // TODO Make this conditional on being a <br>
        });
    }

    $('.streetaddress').each(resizeInput).on('input', resizeInput);
    //$('.streetaddress_frame').each(reduceAddress).on('mouseenter', expandAddress).on('mouseleave', reduceAddress);

    $('.streetaddress_country').selectize({
        closeAfterSelect: true,
        selectOnTab: true,
        onChange: function(value) {
            if (!value.length) return;
            $('#Inputfield_streetaddress_country_iso').val(value); // TODO extract and use our ID - just append '_iso'
        }
    });
});

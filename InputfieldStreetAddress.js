$(document).ready(function() {

    function resizeInput() {
        // Firstly, adjust the widths...
        var add = 1;
        var len = this.value.length;
        if (len === 0) {
            // If the placeholder is showing, resize to it's width
            var ph = $(this).attr('placeholder');
            if (ph) {
                len = ph.length + 1;
            } else {
                len = 12;
            }
        }

        if ($(this).hasClass('streetaddress_country')) {
            this.style.width = '30ch';
        } else {
            this.style.width = (len + add) + 'ch';
        }


        // Now consider the heights of optional fields...
        if ($(this).hasClass('streetaddress_optional') && this.value.length > 0) {
            $(this).addClass('streetaddress_optional_nonempty');
        }

        if ($(this).hasClass('streetaddress_optional_nonempty') && this.value.length === 0) {
            $(this).removeClass('streetaddress_optional_nonempty');
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

    $('input.streetaddress').on('input', resizeInput);
    $('select.streetaddress').on('input', resizeInput);

    $('.streetaddress_country').selectize({
        closeAfterSelect: true,
        selectOnTab: true,
        onFocus: function(value) {
            this.clear();
        }
    });
});

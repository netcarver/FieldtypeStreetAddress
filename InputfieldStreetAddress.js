$(document).ready(function() {

    function resizeInput() {
        var add                  = 1;
        var len                  = this.value.length;
        var mathlen              = len;
        var is_optional          = $(this).hasClass('streetaddress_optional');
        var is_optional_nonempty = $(this).hasClass('streetaddress_optional_nonempty');
        var is_country           = $(this).hasClass('streetaddress_country');
        var has_content          = len > 0;

        // Adjust the field width...
        if (!has_content) {
            // If the placeholder is showing, resize to it's width
            var ph = $(this).attr('placeholder');
            if (ph) {
                mathlen = ph.length + 1;
            } else {
                mathlen = 12;
            }
        }
        if (is_country) {
            this.style.width = '36ch';
        } else {
            this.style.width = (mathlen + add) + 'ch';
        }


        // Adjust styling for optional fields...
        if (is_optional && has_content) {
            $(this).addClass('streetaddress_optional_nonempty');
        }
        if (is_optional_nonempty && !has_content) {
            $(this).removeClass('streetaddress_optional_nonempty');
        }


        // Check if we are malformed or OK...
        var regex = $(this).attr('data-regex');
        if (regex && has_content) {
            malformed = null === this.value.match('^' + regex + '$');
        } else if (!is_optional && !has_content) {
            malformed = true;
        } else {
            malformed = false;
        }
        $(this).toggleClass('streetaddress_malformed', malformed);
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


    $('input.streetaddress').on('input', resizeInput);
    $('select.streetaddress').on('input', resizeInput);

    $('.streetaddress_country').selectize({
        closeAfterSelect: true,
        selectOnTab: true,
        onFocus: function(value) {
            this.clear();
        }
    });

    /**
     * Only show the country selector after it's been initialised. This prevents a FOUC.
     */
    $('.streetaddress_country').removeClass('streetaddress_hidden');
});

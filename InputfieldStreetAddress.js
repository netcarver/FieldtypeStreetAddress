$(document).ready(function() {

    function resizeInput() {

        // Firstly, adjust the widths...
        var add = 1;
        var len = this.value.length;
        if (len === 0) {
            // If the placeholder is showing, resize to it's width
            var ph = $(this).attr('placeholder');
            if (ph) {
                len = ph.length + 4;
            } else {
                len = 16;
            }
        } else {

        }
        if ($(this).hasClass('streetaddress_country')) {
            this.style.width = '26ch';
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

    $('input.streetaddress').each(resizeInput).on('input', resizeInput);
    $('select.streetaddress').each(resizeInput).on('input', resizeInput);
    //$('.streetaddress_frame').each(reduceAddress).on('mouseenter', expandAddress).on('mouseleave', reduceAddress);

    $('.streetaddress_country').selectize({
        closeAfterSelect: true,
        selectOnTab: true,
        onChange: function(value) {
            if (!value.length) return;

            // Try to determine the id of the country_iso field for this input...
            var id = this.$control_input[0].id;

            // Set the value of the country_iso field from the selected country.
            var iso_id = '#' + id.replace('-selectized', '_iso');
            $(iso_id).val(value);

            // var country_id = '#' + id.replace('-selectized', '');
            // $(country_id).val(value);
        }
    });
});

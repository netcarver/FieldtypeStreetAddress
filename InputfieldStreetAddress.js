$(document).ready(function() {

    function updateInput() {
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


        // Show or hide extra street address lines as needed...
        var field = $(this).attr('data-field');
        var is_address_1 = 'street_address'   === field;
        var is_address_2 = 'street_address_2' === field;
        var is_address_3 = 'street_address_3' === field;

        if (is_address_1 || is_address_2 || is_address_3) {

            var name          = $(this).attr('data-name');
            var id1, cl2, cl3, id2, id3;

            id1 = "#Inputfield_" + name + "_street_address";
            cl2 = "." + name + "_street_address_2_element";
            id2 = "#Inputfield_" + name + "_street_address_2";
            cl3 = "." + name + "_street_address_3_element";
            id3 = "#Inputfield_" + name + "_street_address_3";

            var hidden = 'streetaddress_hidden';

            has_content_1 = $(id1).val().length > 0;
            has_content_2 = $(id2).val().length > 0;
            has_content_3 = $(id3).val().length > 0;

            // console.log("Field [" + field + "] - has_content[" + has_content + "]");
            // console.log("Field 1 [" + is_address_1 + "], Field 2 [" + is_address_2 + "], Field 3 [" + is_address_3 + "]");
            // console.log("Value 1 [" + has_content_1 + "], Value 2 [" + has_content_2 + "], Value 3 [" + has_content_3 + "]");

            if (!has_content_1 && !has_content_2 && !has_content_3) {
                $(id1).focus();
                $(cl2).addClass(hidden);
                $(cl3).addClass(hidden);
            } else if (!has_content_2 && !has_content_3) {
                $(cl2).removeClass(hidden);
                if (is_address_3)
                    $(id2).focus();
                $(cl3).addClass(hidden);
            } else {
                $(cl2).removeClass(hidden);
                $(cl3).removeClass(hidden);
            }
        }
    }



    /**
     * Adjust inputs as user types in the client.
     */
    $('input.streetaddress').on('input', updateInput);
    $('select.streetaddress').on('input', updateInput);



    /**
     * Initialise the country selector.
     */
    $('.streetaddress_country').selectize({
        closeAfterSelect: true,
        selectOnTab: true,
        onFocus: function(value) {
            this.clear();
        }
    });


    // Try fixing Admin UiKit theme...
    // $("select#Inputfield_address_country_iso").removeClass('uk-select');
    $("div.selectize-control").removeClass('uk-select');
    $("div.selectize-dropdown").removeClass('uk-select');




    /**
     * Allow Backspace to move to end of previous input. Tricky - we need to use keydown as we need to know if our
     * input was empty before the backspace kills the char. Calling in keyup doesn't allow us to prevent deleting the
     * last character from moving us straight to the end of the previous field. Doing it this way allows us to re-enter
     * data in our field.
     */
    $('input.streetaddress[type=text]').on('keydown', function(event) {
        var key = event.keyCode || event.charCode;
        var inputs = $('input[type=text].streetaddress:not(.streetaddress_hidden)');
        var is_empty = this.value.length === 0;
        if (key == 8 || key == 46) {
            var indexNum = inputs.index(this);
            if(indexNum != 0 && is_empty) {
                var target = inputs.eq(inputs.index(this) - 1);
                var len    = target.val().length;
                target.focus();                         // Move to previous visible streetaddress field
                target[0].setSelectionRange(len, len);  // Move to end of any text in it
                return false;                           // Prevent event deleting the final character in the now-focused field.
            }
        }
    });



    /**
     * Only show the country selector after it's been initialised. This prevents a FOUC.
     */
    $('.streetaddress_country').removeClass('streetaddress_hidden');
});

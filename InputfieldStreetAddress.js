/**
 * TLite tiny popup library from https://github.com/chrisdavies/tlite
 * with a MIT license.
 */
function tlite(t){document.addEventListener("mouseover",function(e){var i=e.target,n=t(i);n||(n=(i=i.parentElement)&&t(i)),n&&tlite.show(i,n,!0)})}tlite.show=function(t,e,i){var n="data-tlite";e=e||{},(t.tooltip||function(t,e){function o(){tlite.hide(t,!0)}function l(){r||(r=function(t,e,i){function n(){o.className="tlite tlite-"+r+s;var e=t.offsetTop,i=t.offsetLeft;o.offsetParent===t&&(e=i=0);var n=t.offsetWidth,l=t.offsetHeight,d=o.offsetHeight,f=o.offsetWidth,a=i+n/2;o.style.top=("s"===r?e-d-10:"n"===r?e+l+10:e+l/2-d/2)+"px",o.style.left=("w"===s?i:"e"===s?i+n-f:"w"===r?i+n+10:"e"===r?i-f-10:a-f/2)+"px"}var o=document.createElement("span"),l=i.grav||t.getAttribute("data-tlite")||"n";o.innerHTML=e,t.appendChild(o);var r=l[0]||"",s=l[1]||"";n();var d=o.getBoundingClientRect();return"s"===r&&d.top<0?(r="n",n()):"n"===r&&d.bottom>window.innerHeight?(r="s",n()):"e"===r&&d.left<0?(r="w",n()):"w"===r&&d.right>window.innerWidth&&(r="e",n()),o.className+=" tlite-visible",o}(t,d,e))}var r,s,d;return t.addEventListener("mousedown",o),t.addEventListener("mouseleave",o),t.tooltip={show:function(){d=t.title||t.getAttribute(n)||d,t.title="",t.setAttribute(n,""),d&&!s&&(s=setTimeout(l,i?150:1))},hide:function(t){if(i===t){s=clearTimeout(s);var e=r&&r.parentNode;e&&e.removeChild(r),r=void 0}}}}(t,e)).show()},tlite.hide=function(t,e){t.tooltip&&t.tooltip.hide(e)},"undefined"!=typeof module&&module.exports&&(module.exports=tlite);


/**
 * Found in sitepoint forums...
 * https://www.sitepoint.com/community/t/capitalizing-first-letter-of-each-word-in-string/209644/2
 */
function titleCase(str) {
  str = str.replace(/[ ]+/g, ' ');       // runs of spaces -> single space
  words = str.toLowerCase().split(' ');

  for(var i = 0; i < words.length; i++) {
    var letters = words[i].split('');
    letters[0] = letters[0].toUpperCase();
    words[i] = letters.join('');
  }
  return words.join(' ');
}



function showLineWarning(el, icon, msg, suggested) {
  $(el).addClass('streetaddress_malformed_caps');
  icon.find('i').attr('title', msg);
  icon.removeClass('streetaddress_hidden');
}



function hideLineWarning(el, icon) {
  $(el).removeClass('streetaddress_malformed_caps');
  icon.addClass('streetaddress_hidden');
}



function updateInput() {
  var conf                 = config.InputfieldStreetAddress;
  var add                  = 2;
  var len                  = this.value.length;
  var mathlen              = len;
  var is_optional          = $(this).hasClass('streetaddress_optional');
  var is_optional_nonempty = $(this).hasClass('streetaddress_optional_nonempty');
  var is_country           = $(this).hasClass('streetaddress_country');
  var is_static            = $(this).hasClass('streetaddress_static');
  var has_content          = len > 0;
  var icon_id              = '#' + this.id + '_icon';
  var icon                 = $(icon_id);

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


  // Show or hide extra street address lines as needed...
  var field = $(this).attr('data-field');
  var is_address_1 = 'street_address'   === field;
  var is_address_2 = 'street_address_2' === field;
  var is_address_3 = 'street_address_3' === field;
  var is_post_code = 'postal_code' === field;

  if (is_post_code) {
    // Always coerce the value of the postal_code field to use capital letters.
    // All known postal countries in the libaddressinput feed use just numerics or Uppercase chars and numerics.
    var start = this.selectionStart;
    var end   = this.selectionEnd;
    this.value = this.value.toUpperCase();
    this.setSelectionRange(start, end);
  }


  // Adjust the displayed street address field lines as needed. As content is deleted or added, lines are displayed or
  // hidden.
  if (!is_static && (is_address_1 || is_address_2 || is_address_3)) {

    var name = $(this).attr('data-name');
    var id1, cl2, cl3, id2, id3;

    id1 = "#Inputfield_" + name + "_street_address";
    cl2 = "." + name + "_street_address_2_element";
    id2 = "#Inputfield_" + name + "_street_address_2";
    cl3 = "." + name + "_street_address_3_element";
    id3 = "#Inputfield_" + name + "_street_address_3";

    has_content_1 = $(id1).val().length > 0;
    has_content_2 = $(id2).val().length > 0;
    has_content_3 = $(id3).val().length > 0;

    if (!has_content_1 && !has_content_2 && !has_content_3) {
      $(id1).focus();
      $(cl2).addClass('streetaddress_hidden');
      $(cl3).addClass('streetaddress_hidden');
    } else if (!has_content_2 && !has_content_3) {
      $(cl2).removeClass('streetaddress_hidden');
      if (is_address_3)
        $(id2).focus();
      $(cl3).addClass('streetaddress_hidden');
    } else {
      $(cl2).removeClass('streetaddress_hidden');
      $(cl3).removeClass('streetaddress_hidden');
    }
  }

  var regex = $(this).attr('data-regex');

  if (!is_optional && !has_content) {
    // Required field is empty!
    icon.find('i').attr('title', conf.i18n.noblanks);
    $(this).toggleClass('streetaddress_malformed', true);
    icon.toggleClass('streetaddress_hidden', false);
  } else if (regex && has_content) {
    // Check if we are malformed or OK...
    malformed = null === this.value.match('^' + regex + '$');
    if (malformed) {
      icon.find('i').attr('title', conf.i18n.malformed);
    }
    $(this).toggleClass('streetaddress_malformed', malformed);
    icon.toggleClass('streetaddress_hidden', !malformed);
  } else {
    // Clear the malformed markers and...
    $(this).toggleClass('streetaddress_malformed', false);
    icon.toggleClass('streetaddress_hidden', true);

    // ...check capitalisation (or lack thereof)...
    value1   = this.value.trim();
    value    = value1.replace(/[\d]/g, '').replace(/[\W]/g, '').trim(); // Discount numerics and non-word chars here.

    has_len  = value.length > 0;
    if (has_len) {
      is_upper = (value.length > 1) && (value  === value.toUpperCase()); // Don't warn if first (and only) entered char is uppercase.
      is_lower = value  === value.toLowerCase();
      is_title = value1 === titleCase(value1);
      suggested_value   = titleCase(value1);

      // console.log('[' + value1 + '] => [' + value + '] :: [' + suggested_value + ']');

      switch(field) {
        case 'postal_code' :
        case 'country_iso':
        case 'origin_iso' :
          // Nothing to do!
          break;

        default:
          if (has_len && is_upper) {
            showLineWarning(this, icon, conf.i18n.uppercase, suggested_value);
          } else if (has_len && is_lower) {
            showLineWarning(this, icon, conf.i18n.lowercase, suggested_value);
          } else if (has_len && !is_title) {
            showLineWarning(this, icon, conf.i18n.nottitle, suggested_value);
          } else {
            hideLineWarning(this, icon);
          }
          break;
      }
    } else {
      hideLineWarning(this, icon);
    }
  }
}


function handleKeys() {
  /**
   * Allow Backspace to move to end of previous input. Tricky - we need to use keydown as we need to know if our
   * input was empty before the backspace kills the char. Calling in keyup doesn't allow us to prevent deleting the
   * last character from moving us straight to the end of the previous field. Doing it this way allows us to re-enter
   * data in our field.
   */
  // $('input.streetaddress[type=text]').on('keydown', function(event) {
  $(document).on('keydown', 'input.streetaddress[type=text]', function(event) {
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
}


$(document).ready(function() {

    /**
     * Adjust inputs as user types in the client.
     */
    $('input.streetaddress').on('input', updateInput);
    $('select.streetaddress').on('input', updateInput);


    // Adjustments for UiKit Theme...
    $('table.InputfieldStreetAddressTable').parent().removeClass('uk-overflow-auto');


    /**
     * Initialise the country selector.
     */
    $('.streetaddress_country').removeClass('uk-select').selectize({
        closeAfterSelect: true,
        selectOnTab: true,
        onFocus: function(value) {
            // this.clear();
        }
    });


    handleKeys();


    /**
     * Only show the country selector after it's been initialised. This prevents a FOUC.
     */
    $('.streetaddress_country').removeClass('streetaddress_hidden');
});

var entityMap = {
  '&': '&amp;',
  '<': '&lt;',
  '>': '&gt;',
  '"': '&quot;',
  "'": '&#39;',
  '/': '&#x2F;',
  '`': '&#x60;',
  '=': '&#x3D;'
};

function escapeHtml(string) {
  string = string.replace(/<\/?[^>]+(>|$)/g, "");
  return String(string).replace(/[&<>"'`=\/]/g, function (s) {
    return entityMap[s];
  });
}

/**
 * Handle actions when user clicks a warning icon...
 */
$(document).on('click', 'span.streetaddress_icon', function(e) {
  var conf = config.InputfieldStreetAddress;
  id = $(this).attr('id').replace(/_icon$/, '');
  id = escapeHtml(id);
  text = $('#'+id).val().trim();
  titlecase = '';

  var is_post_code = id.match(/postal_code$/);

  if (is_post_code) {
    // Specially form the actions dialog to handle example invalid postcodes...
    var title = $('#'+id).attr('title');
    // title = wordwrap(title);
    this.title = escapeHtml(title);
  } else if (text.length > 0) {
    text = escapeHtml(text);
    titlecase = titleCase(text);
    this.title = "<h5>" + conf.i18n.actions + "</h5>" +
      "<a class='streetaddress_entitle streetaddress_choice' data-target-id='" + id + "' data-replacement='" + titlecase +
      "'>" + conf.i18n.replacewith + " <strong>'" + titlecase + "'</strong></a>" +
      "<br><a class='streetaddress_choice'><strong>" + conf.i18n.leaveit + "</strong></a>";
  }
  this.modal_flag = !this.modal_flag;
  this.modal_flag ? tlite.show(this, {grav: 'w'}): tlite.hide(this);
});

$(document).on('click', 'a.streetaddress_entitle', function(e) {
  // target_id = '#' + escapeHtml(e.target.attributes['data-target-id'].value);
  // replacement = escapeHtml(e.target.attributes['data-replacement'].value);
  target_id = '#' + e.target.attributes['data-target-id'].value;
  replacement = e.target.attributes['data-replacement'].value;
  $(target_id).val(replacement).removeClass('streetaddress_malformed_caps');
  $(target_id+'_icon').addClass('streetaddress_hidden');
});



$(document).on('reloaded', '.InputfieldStreetAddress', function(event) {
  $t = $(this);
  $t.find('input.streetaddress').on('input', updateInput);
  $t.find('select.streetaddress').on('input', updateInput);
  $t.find('.InputfieldStreetAddressTable').parent().removeClass('uk-overflow-auto');
  $t.find('.streetaddress_country').removeClass('uk-select').selectize({
    closeAfterSelect: true,
    selectOnTab: true,
    onFocus: function(value) {
      // this.clear();
    }
  });
  p = $t.parent().parent();
  p.css({'overflow': 'visible'});
  $t.find('.streetaddress_country').removeClass('streetaddress_hidden');
});

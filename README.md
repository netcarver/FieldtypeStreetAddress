Street Address Fieldtype & Inputfield for ProcessWire
=====================================================

Inputfield and storage for addresses in ProcessWire. Based upon the address meta data from Google's [libaddressinput](https://github.com/googlei18n/libaddressinput/wiki/AddressValidationMetadata) project.


Prerequisites
-------------

- A working PW3+ installation.



Installation
------------

### Via Modules Page In Admin

- Install using module class name of "FieldtypeStreetAddress"


### Manually

- Create a new directory under your ```site/modules``` folder called "FieldtypeStreetAddress"
- Save the files from this package into the new directory.
- Log into your PW admin interface and go to Modules > Refresh and then install the "FieldtypeStreetAddress" & "InputfieldStreetAddress" modules.

You should now be able to create a new field and assign it the type "StreetAddress". Add it to a template to be able to see it in action.



Street Address Inputfield Configuration Options
-----------------------------------------------

There are two separate sections - ___Input Options___ and ___Output Options___.

### Input Options

#### Allowable Destination Countries

This is a multiselect control that allows you to specify what choices of destination country the user has in the inputfield. If all your addresses are going to a single country (probably your own), then choose it here.

If there are a range of possible desitnations, you can either leave this field blank (in which case the user choice is unrestricted), or choose a set of options (in which case choice is limited to this set)

#### Default Country (unless a single destination is selected above)

If you allow a choice of more than one country, then you will be able to choose the default country here. __NB__, This option is added to the set of allowable destination countries if needed.

#### Input Format

You can select between a dynamic field layout, that changes when (and if) you change the destination country, or a fixed, tabular input layout.

The dynamic layout of address subfields is driven by the country address format string cached from the google i18n data feed. The tabular layout is fixed, no matter what destination contry you choose.

#### Show Address Preview (with Dynamic option above)

If you've chosen the dynamic layout option, you can also choose to show a preview of the formatted address output.


### Output Options

These control how the output of this field will be formatted. The layout of the formatted address always depends upon the cached layout data from Google, but some aspects of the output can be tweaked here.

#### Include HTML Microformat Spans?

You can choose to include HTML spans implementing schema.org's [PostalAddress](https://schema.org/PostalAddress) microformat.

#### Single/Multiline Output?

Controls how the lines of the output are merged, either into a single line address, or into a multiline address.

_NB_ Lines in the output are delimited by a single "\n" character unless you have chosen inclusion of HTML spans, in
which case the line delimeters are ```<br>\n```, which are suitable for output in HTML5. If you output this within a
```<pre>``` element, you will see double line spacing.  If you have your field setup to just output plaintext, and then
show this in an HTML element other than a preformatted block, you will see the text as a single line.

#### Show Destination Country Field

If you are sending mail domestically, it usually isn't necessary to include the destination country - but you can choose to tweak how this is decided here.

You can choose to _never_ show the value, to _always_ show the value, or to _conditionally_ show the value.

Conditionality depends upon the country-of-origin field. If the country of origin differs from the country of destination in the address, then the address is assumed to be going via international post and the address field will be appended.

#### Country-Of-Origin

Select the country-of-origin from the list. This is the country from which you will be sending any physical mail.




API Usage
---------

As with all ProcessWire templates, you can output the formatted value in your template files by referencing the field:

```
    <div class='client-address'>{$page->client_address}</div>
```

The returned value will, by default, be laid out according to the address meta data from the Google feed, honouring your
options for destination country inclusion, HTML tags and multi-vs-single line. It will also have any substitutions already made in it by the Tag Parser module.

### Interaction with PW's Output Formatting Flag

If you access the field with PWs output formatting turned off, then the address you get back will honour the settings
for the inclusion (or otherwise) of the destination address but will ignore settings for singleline, HTML spans and
field substitutions via the tag parser. It's up to you to do what you need with it in these cases.


### Accessing The Underlying Object

If you need to customise the output in your template files - or set values, you can get at the underlying StreetAddress
object via the ```getUnformatted()``` call.

```
    $address_object = $page->getUnformatted('client_address');
```


## License(s)

The files _formats.php_ and _countries.php_ are derived from LibAddressInput meta data and are released under the
Apache-2.0 license as that seems to be what the project is using.

All other files are released under the MIT license.


Street Address Fieldtype & Inputfield for ProcessWire
=====================================================

Inputfield and storage for addresses in ProcessWire. Based upon the address meta data from Google's [libaddressinput](https://github.com/googlei18n/libaddressinput/wiki/AddressValidationMetadata) project.


Prerequisites
-------------

- If you don't have the module [TextformatterTagParser](http://modules.processwire.com/modules/textformatter-tag-parser/) installed, please log in to your PW admin interface and install it.



Installation
------------

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

The dynamic layout of address subfields is driven by the country address format string cached from the google i18n data feed, whilst the tabular layout is fixed, no matter what destination contry you choose.

#### Show Address Preview (with Dynamic option above)

If you've chosen the dynamic layout option, you can also choose to show a preview of the formatted address output.


### Output Options

These control how the output of this field will be formatted. The layout of the formatted address always depends upon the cached layout data from Google, but some aspects of the output can be tweaked here.

#### Show Destination Country Field

If you are sending mail domestically, it usually isn't necessary to include the destination country - but you can choose to tweak how this is decided here.

You can choose to _never_ show the value, to _always_ show the value, or to _conditionally_ show the value.

Conditionality depends upon the country-of-origin field. If the country of origin differs from the country of destination in the address. In this case the address is assumed to be going via international post and the address field will be appended as needed.

#### Country-Of-Origin

Select the country-of-origin from the list. This is the country from which you will be sending any physical mail.


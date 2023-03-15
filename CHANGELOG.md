# **Change Log**

[Keep a Changelog]

ProcessWire Fieldtype and Inputfield for storing postal addresses. Utilises Google's [libaddressinput] project for
address layouts and other postal metadata.

See the README.md file for more information.

## [Upcoming]

## Version [1.1.3] - 2023-03-14

[Diff from 1.1.2]

-   Bugfix: Use default ISO when an address has no country defined.

## Version [1.1.2] - 2019-04-29

[Diff from 1.1.1]

- Bugfix: Add check for locale_accept_from_http() method.


## Version [1.1.1] - 2019-03-17

[Diff from 1.1.0]

- Add support for the %= operator in selectors. Thanks gebeer.
- Fix docblock argument order.
- Check for failed json encode.


## Version [1.1.0] - 2019-01-31

[Diff from 1.0.6]

- Update default countries list (in English) with names from the country-list project.
- Use LibLocalisation (if installed) to localise country select lists in Inputfield.
- Allow localisation of config and inputfield select lists to the language of the user's browser.
- Switch to storing ISOs in uppercase, can still handle stored lowercase ISO codes.
- Unify country list loading code.
- Detect changes to input address when saving page.
- Add compatibility with FormBuilder.
- Fix type hint. Thanks Matjaž.


## Version [1.0.6] - 2018-09-18

[Diff from 1.0.5]

- Add configuration option to allow the inclusion of the destination country ISO in the output address.


## Version [1.0.5] - 2018-09-16

[Diff from 1.0.4]

- Use correct directory separator.
- Do not show "Not Title Case" warning for postal_code field.
- Reduce example postal codes to 8 from 12.
- Add requiredBy entry to module info.


## Version [1.0.4] - 2018-09-14

[Diff from 1.0.3]

- Attempt to fix format override persistance.


## Version [1.0.3] - 2018-09-14

[Diff from 1.0.2]

- Persist format overrides between versions when using the PW Upgrades module.
- Conditionally load format overrides.


## Version [1.0.2] - 2018-09-13

[Diff from 1.0.1]

- Update formats_override.php example and comments.
- Rename formats_overrides.php to example.format_overrides.php to prevent overwrite on update.
- Add missing install of JquerySelectize module to InputfieldStreetAddress.


## Version [1.0.1] - 2018-09-10

[Diff from 1.0.0]

- Bugfix: correct access to noblanks i18n string in JS file.
- Switch formats.php and countries.php over to the Apache2.0 license (as derived works from [libaddressinput].)
- Changelog: Convert diff links to github.


## Version [1.0.0] - 2018-09-08

[Diff from 0.9.1]

- Add MIT License
- First public release


## Version [0.9.1] - 2018-09-07

[Diff from 0.9.0]

- Make strings in Javascript translatable.
- Docs: Remove outdated prerequisites.
- Add .gitattributes file.


## Version [0.9.0] - 2018-09-03

[Diff from 0.8.0]

- Add popups offering corrective action choices. Uses the MIT-licensed [tlite] library.
- Extend State/Principality/Area regex checking to the static table view.
- Allow locality and postal_code to the surpressable fields in the config settings.
- Rebuild formats.php from Google feeds.
- Bugfix: JS conversion to title case when there are multiple spaces between words.
- Bugfix: Don't do all-uppercase tests on single-character inputs, as they could validly start with an uppercase letter.

## Version [0.8.0] - 2018-07-25

[Diff from 0.7.1]

- Allow overrides to the libaddressinput feed data by merging the formats_overrides.php file.
- Use a little more width to allow for Kanji characters.
- Merge line checking JS code.
- Update countries.php.
- Rebuild formats.php from Google feeds.
- Extend formats.php with State/Principality/Area regex checks.
- Remove hard-coded data overrides out of the Inputfield into the data layer.


## Version [0.7.1] - 2018-07-24

[Diff from 0.7.0]

- Bugfix: stop false positive "All caps" if numbers or punctuation in address line.
- Capitalise postcode field as it is typed, prior to regex check.
- Add sanitisation into the Sleep() and Wakeup() calls.


## Version [0.7.0] - 2018-07-20

[Diff from 0.6.0]

- Add JS detection of bad inputfield capitalisation.
- Add consistent use of data-field attribute.
- Change the way format callback functions are called.
- Fix CSS bug in static table layout.
- Fix JS errors in static table layout.


## Version [0.6.0] - 2018-07-12

[Diff from 0.5.1]

- Quiet a PHP notice.
- Allow use in repeater fields/repeater matrix.


## Version [0.5.1] - 2018-06-29

[Diff from 0.5.0]

- Bugfix: Visual weirdness with SelectizeJs under Admin UiKit theme.


## Version [0.5.0] - 2018-06-29

[Diff from 0.4.0]

- Bugfix: Layout of inputfield when admin user defines a note to go with the field.
- Feature: Allow template-context config fields.


## Version [0.4.0] - 2018-06-25

[Diff from 0.3.0]

- Add StreetAddress::isEmpty() and use from Inputfield and Fieldtype. Allows field to be set to "Required" in PW admin.
- Add ability for backspace to move to end of previous field once current field is empty.
- Update fixed table input layout to show if a field is used in the address format of the given country.


## Version [0.3.0] - 2018-06-24

[Diff from 0.2.0]

- Update fixed table input layout.


## Version [0.2.0] - 2018-06-24

[Diff from 0.1.0]

- Use uppercase when saving postcode as no surveyed regex uses lowercase. Trim all values on save.
- Allow format overrides.
- Bugfix: Correct use of country_iso in PW Selectors.
- Bugfix: Correct spacing in single-line HTML output.
- Bugfix: Correct line joins in most modes.
- Bugfix: Apply uppercase rule to all street address lines, not just the first.

## Version [0.1.0] - 2018-06-23

- Initial packaging as a module suite for ProcessWire

[Keep a Changelog]: http://keepachangelog.com/en/1.0.0/
[libaddressinput]: https://github.com/googlei18n/libaddressinput
[tlite]: https://github.com/chrisdavies/tlite
[Upcoming]: https://github.com/netcarver/FieldtypestreetAddress/compare/1.1.0...HEAD
[1.1.2]: https://github.com/netcarver/FieldtypeStreetAddress/tree/1.1.2/
[Diff from 1.1.1]: https://github.com/netcarver/FieldtypeStreetAddress/compare/1.1.1...1.1.2
[1.1.1]: https://github.com/netcarver/FieldtypeStreetAddress/tree/1.1.1/
[Diff from 1.1.0]: https://github.com/netcarver/FieldtypeStreetAddress/compare/1.1.0...1.1.1
[1.1.0]: https://github.com/netcarver/FieldtypeStreetAddress/tree/1.1.0/
[Diff from 1.0.6]: https://github.com/netcarver/FieldtypeStreetAddress/compare/1.0.6...1.1.0
[1.0.6]: https://github.com/netcarver/FieldtypeStreetAddress/tree/1.0.6/
[Diff from 1.0.5]: https://github.com/netcarver/FieldtypeStreetAddress/compare/1.0.5...1.0.6
[1.0.5]: https://github.com/netcarver/FieldtypeStreetAddress/tree/1.0.5/
[Diff from 1.0.4]: https://github.com/netcarver/FieldtypeStreetAddress/compare/1.0.4...1.0.5
[1.0.4]: https://github.com/netcarver/FieldtypeStreetAddress/tree/1.0.4/
[Diff from 1.0.3]: https://github.com/netcarver/FieldtypeStreetAddress/compare/1.0.3...1.0.4
[1.0.3]: https://github.com/netcarver/FieldtypeStreetAddress/tree/1.0.3/
[Diff from 1.0.2]: https://github.com/netcarver/FieldtypeStreetAddress/compare/1.0.2...1.0.3
[1.0.2]: https://github.com/netcarver/FieldtypeStreetAddress/tree/1.0.2/
[Diff from 1.0.1]: https://github.com/netcarver/FieldtypeStreetAddress/compare/1.0.1...1.0.2
[1.0.1]: https://github.com/netcarver/FieldtypeStreetAddress/tree/1.0.1/
[Diff from 1.0.0]: https://github.com/netcarver/FieldtypeStreetAddress/compare/1.0.0...1.0.1
[1.0.0]: https://github.com/netcarver/FieldtypeStreetAddress/tree/1.0.0/
[Diff from 0.9.1]: https://github.com/netcarver/FieldtypeStreetAddress/compare/0.9.1...1.0.0
[0.9.1]: https://github.com/netcarver/FieldtypeStreetAddress/tree/0.9.1/
[Diff from 0.9.0]: https://github.com/netcarver/FieldtypeStreetAddress/compare/0.9.0...0.9.1#diff
[0.9.0]: https://github.com/netcarver/FieldtypeStreetAddress/tree/0.9.0/
[Diff from 0.8.0]: https://github.com/netcarver/FieldtypeStreetAddress/compare/0.8.0...0.9.0#diff
[0.8.0]: https://github.com/netcarver/FieldtypeStreetAddress/tree/0.8.0/
[Diff from 0.7.1]: https://github.com/netcarver/FieldtypeStreetAddress/compare/0.7.1...0.8.0#diff
[0.7.1]: https://github.com/netcarver/FieldtypeStreetAddress/tree/0.7.1/
[Diff from 0.7.0]: https://github.com/netcarver/FieldtypeStreetAddress/compare/0.7.0...0.7.1#diff
[0.7.0]: https://github.com/netcarver/FieldtypeStreetAddress/tree/0.7.0/
[Diff from 0.6.0]: https://github.com/netcarver/FieldtypeStreetAddress/compare/0.6.0...0.7.0#diff
[0.6.0]: https://github.com/netcarver/FieldtypeStreetAddress/tree/0.6.0/
[Diff from 0.5.1]: https://github.com/netcarver/FieldtypeStreetAddress/compare/0.5.1...0.6.0#diff
[0.5.1]: https://github.com/netcarver/FieldtypeStreetAddress/tree/0.5.1/
[Diff from 0.5.0]: https://github.com/netcarver/FieldtypeStreetAddress/compare/0.5.0...0.5.1#diff
[0.5.0]: https://github.com/netcarver/FieldtypeStreetAddress/tree/0.5.0/
[Diff from 0.4.0]: https://github.com/netcarver/FieldtypeStreetAddress/compare/0.4.0...0.5.0#diff
[0.4.0]: https://github.com/netcarver/FieldtypeStreetAddress/tree/0.4.0/
[Diff from 0.3.0]: https://github.com/netcarver/FieldtypeStreetAddress/compare/0.3.0...0.4.0#diff
[0.3.0]: https://github.com/netcarver/FieldtypeStreetAddress/tree/0.3.0/
[Diff from 0.2.0]: https://github.com/netcarver/FieldtypeStreetAddress/compare/0.2.0...0.3.0#diff
[0.2.0]: https://github.com/netcarver/FieldtypeStreetAddress/tree/0.2.0/
[Diff from 0.1.0]: https://github.com/netcarver/FieldtypeStreetAddress/compare/0.1.0...0.2.0#diff
[0.1.0]: https://github.com/netcarver/FieldtypeStreetAddress/tree/0.1.0/

# **Change Log** - [Keep a Changelog]

ProcessWire Fieldtype and Inputfield for storing postal addresses. Utilises Google's [libaddressinput] project for
address layouts and other postal metadata.

See the README.md file for more information.

## [Upcoming]


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
[Upcoming]: https://bitbucket.org/netcarver/fieldtypestreetaddress/branches/compare/HEAD..0.7.0
[0.7.1]: https://bitbucket.org/netcarver/fieldtypestreetaddress/src/0.7.1/
[Diff from 0.7.0]: https://bitbucket.org/netcarver/fieldtypestreetaddress/branches/compare/0.7.1..0.7.0#diff
[0.7.0]: https://bitbucket.org/netcarver/fieldtypestreetaddress/src/0.7.0/
[Diff from 0.6.0]: https://bitbucket.org/netcarver/fieldtypestreetaddress/branches/compare/0.7.0..0.6.0#diff
[0.6.0]: https://bitbucket.org/netcarver/fieldtypestreetaddress/src/0.6.0/
[Diff from 0.5.1]: https://bitbucket.org/netcarver/fieldtypestreetaddress/branches/compare/0.6.0..0.5.1#diff
[0.5.1]: https://bitbucket.org/netcarver/fieldtypestreetaddress/src/0.5.1/
[Diff from 0.5.0]: https://bitbucket.org/netcarver/fieldtypestreetaddress/branches/compare/0.5.1..0.5.0#diff
[0.5.0]: https://bitbucket.org/netcarver/fieldtypestreetaddress/src/0.5.0/
[Diff from 0.4.0]: https://bitbucket.org/netcarver/fieldtypestreetaddress/branches/compare/0.5.0..0.4.0#diff
[0.4.0]: https://bitbucket.org/netcarver/fieldtypestreetaddress/src/0.4.0/
[Diff from 0.3.0]: https://bitbucket.org/netcarver/fieldtypestreetaddress/branches/compare/0.4.0..0.3.0#diff
[0.3.0]: https://bitbucket.org/netcarver/fieldtypestreetaddress/src/0.3.0/
[Diff from 0.2.0]: https://bitbucket.org/netcarver/fieldtypestreetaddress/branches/compare/0.3.0..0.2.0#diff
[0.2.0]: https://bitbucket.org/netcarver/fieldtypestreetaddress/src/0.2.0/
[Diff from 0.1.0]: https://bitbucket.org/netcarver/fieldtypestreetaddress/branches/compare/0.2.0..0.1.0#diff
[0.1.0]: https://bitbucket.org/netcarver/fieldtypestreetaddress/src/0.1.0/

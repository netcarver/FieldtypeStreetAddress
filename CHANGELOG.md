# **Change Log** - [Keep a Changelog]

ProcessWire Fieldtype and Inputfield for storing postal addresses. Utilises Google's [libaddressinput] project for
address layouts and other postal metadata.

See the README.md file for more information.


## [Upcoming]

- Add StreetAddress::isEmpty() and use from Inputfield and Fieldtype. Allows field to be set to "Required" in PW admin.


## [0.3.0] - 2018-06-24

- Update fixed table input layout.


## [0.2.0] - 2018-06-24

- Use uppercase when saving postcode as no surveyed regex uses lowercase. Trim all values on save.
- Allow format overrides.
- Bugfix: Correct use of country_iso in PW Selectors.
- Bugfix: Correct spacing in single-line HTML output.
- Bugfix: Correct line joins in most modes.
- Bugfix: Apply uppercase rule to all street address lines, not just the first.

## [0.1.0] - 2018-06-23

- Initial packaging as a module suite for ProcessWire

[Keep a Changelog]: http://keepachangelog.com/en/1.0.0/
[libaddressinput]: https://github.com/googlei18n/libaddressinput
[Upcoming]: https://bitbucket.org/netcarver/fieldtypestreetaddress/branches/compare/HEAD..0.3.0
[0.4.0]: https://bitbucket.org/netcarver/fieldtypestreetaddress/src/0.3.0/
[0.3.0]: https://bitbucket.org/netcarver/fieldtypestreetaddress/src/0.3.0/
[0.2.0]: https://bitbucket.org/netcarver/fieldtypestreetaddress/src/0.2.0/
[0.1.0]: https://bitbucket.org/netcarver/fieldtypestreetaddress/src/0.1.0/

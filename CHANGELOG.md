# **Change Log** - [Keep a Changelog]

ProcessWire Fieldtype and Inputfield for storing postal addresses. Utilises Google's [libaddressinput] project for
address layouts and other postal metadata.

See the README.md file for more information.


## [Upcoming]

- Use uppercase when saving postcode as no surveyed regex uses lowercase. Trim all values on save.
- Bugfix: Correct use of country_iso in PW Selectors.
- Bugfix: Correct spacing in single-line HTML output.

## 0.1.0 - 2018-06-23

- Initial packaging as a module suite for ProcessWire

[Keep a Changelog]: http://keepachangelog.com/en/1.0.0/
[libaddressinput]: https://github.com/googlei18n/libaddressinput
[Upcoming]: https://api.bitbucket.org/2.0/repositories/netcarver/FieldtypeStreetAddress/compare/0.0.1...HEAD

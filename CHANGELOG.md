# Changelog

## 1.1.2 - 2022-04-17

- Add image attribute normalization to ensure attributes are merge correctly.

## 1.1.1 - 2022-03-19

- Add `useFocalPoint` param to `getImg`, `getBg`, `getVideo` to disable focal point use.
- Add null-checks for `getImg`, `getBg`, `getVideo` so they don't break when no asset exists.

## 1.1.0 - 2022-03-08

- Fix section field template path.

## 1.0.9 - 2022-03-08

- Add new Section field type.

## 1.0.8 - 2021-12-12

- Fix PluginTrait error with module.

## 1.0.7 - 2021-12-12

- Fix PluginTrait error with module.

## 1.0.6 - 2021-11-15

- Fix issue with `getFormattedPhone()` argument name.
- Fix use of `str_replace()` instead of `preg_replace()` for regex replacement.

## 1.0.5 - 2021-08-24

- Add focal point handling to `getImg()` and `getBg()`.
- Allow the use of `auto` for `getImg()` and `getBg()` srcset.

## 1.0.4 - 2021-08-24

- Initial release.

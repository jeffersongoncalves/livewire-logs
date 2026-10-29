# Changelog

All notable changes to this project will be documented in this file.

## 1.1.0 - 2026-10-29

- With `include_values` on, properties whose path contains a sensitive key (`password`, `token`, `secret`, `api_key`, `credential`, `authorization`, `cookie`, `session`, `card_number`, `cvv`...) are logged as `[redacted]`. Matching ignores case, `_`, `-` and `.` (`form.currentPassword` matches `password`); array values are redacted key by key at any depth. Configure the list with `sensitive_keys`.

## 1.0.0 - 2026-10-29

First release, for Livewire 3.

- Reports the `TypeError` Livewire 3 turns into a `419` without logging it (`HandleRequests::handleUpdate()`). With `APP_DEBUG=true` it is still thrown.
- Adds a `livewire` context to every exception reported during a component update: component name, class, id, path, current step (`update: <property>` / `call: <method>`), updated property types and called methods.
- `include_values` (off by default) also logs the truncated updated values.

## [Unreleased]

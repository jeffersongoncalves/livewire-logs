# Changelog

All notable changes to this project will be documented in this file.

## 2.0.0 - 2026-10-29

First release, for Livewire 4.

- Adds a `livewire` context to every exception reported during a component update: component name, class, id, path, current step (`update: <property>` / `call: <method>`), updated property types and called methods. Livewire 4 already reports the `TypeError` behind the `419`; this adds where it happened.
- `include_values` (off by default) also logs the truncated updated values.

## [Unreleased]

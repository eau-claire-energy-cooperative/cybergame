# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2026-10-22

First tagged release, marking the final state of the project before it was archived. Prior history was untagged.

### Added
- Passcode guessing game with Easy, Medium, and Hard patterns, a keyboard-style guess interface, and a tutorial.
- User aliases, scoring, and a leaderboard of top users and top passcodes (reworked November 2024).
- LDAP / Active Directory login, with domain and host configurable in `private/credentials.php` (November 2024).
- Docker support: `Dockerfile`, `docker-compose.yml`, and `INSTALL.md` (November 2024).
- README and screenshots (November 2024).
- Documentation of known issues, Components section, `VERSION`, and this changelog.

### Removed
- Runtime log file `private/loginfo.txt` from the repository (February 2025).

### Security
- Known security issues are documented in the README under Known Issues and were intentionally left unfixed.

[1.0.0]: https://github.com/eau-claire-energy-cooperative/cybergame/releases/tag/v1.0.0

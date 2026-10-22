# Galactic Codebreaker

> A PHP passcode game to reinforce passcode importance.

**Status: Archived.** This was a learning project and is no longer actively developed. It is kept for reference only. The issues listed under [Known Issues](#known-issues) are documented but intentionally **not fixed**. Do not deploy this software on a production or internet-facing system without addressing them.

## Table of Contents

- [Background](#background)
- [Install](#install)
- [Usage](#usage)
- [Components](#components)
- [Known Issues](#known-issues)
- [Version](#version)
- [Maintainers](#maintainers)
- [Contributing](#contributing)
- [License](#license)

## Background

Businesses are always looking for alternative ways to reinforce important topics in the workplace without always using a meeting or memo. That is the thought process that led to the idea of Galactic Codebreaker. It is an abstract combination of popular games while also being a unique way to help reinforce the importance of complex passcodes. This game straddles the line between fun gameplay and learning tool for the workplace. The creative decision was made to make the game more fun to play than it is a learning tool. The idea being that we wanted the game to be fun enough so that employees would take time out of their busy days to play it.

Galactic Codebreaker is a PHP learning project that was used to get a better understanding of the language. As such, there are quite a few processes that might not be as efficient as they should/could be. Additionally, it was a good opportunity to reinforce the importance of complex passcodes. The hope with this game was to help teach a lesson without having to schedule another meeting or send out another memo.

## Install

This program can be run from a Docker container. Basic instructions are in [INSTALL.md](INSTALL.md).

Before running, review the [Known Issues](#known-issues) below. In particular, the database credentials in `private/credentials.php` and `docker-compose.yml` are example values committed to the repository and **must be changed**.

## Usage

This game is built with the assistance of Bootstrap, so it is flexible between different monitors and devices. It is not perfect and has not been tested on devices smaller than an iPad. Use on devices smaller than an iPad is not recommended.

If you would like to use LDAP for user verification, modify `private/credentials.php` with your specific domain and LDAP host information. If you would like to use this program without LDAP verification, you will need to modify `credentials.php` along with `login/login_logic.php` to properly verify logins.

Once running, the game is available at `http://server_ip:7000/` (default port in `docker-compose.yml`).

## Components

Major tools, frameworks, and libraries this project depends on:

| Component | Version | Notes |
|---|---|---|
| PHP | 7.4.33 (`php:7.4.33-apache` image) | End of life, see Known Issues |
| Apache HTTP Server | Bundled with the PHP image | |
| MariaDB | Unpinned (`mariadb` image, latest) | Database for users, patterns, and guesses |
| PHP `mysqli` extension | Bundled | Installed via docker-php-extension-installer |
| PHP `ldap` extension | Not installed in the image | Required by `login/ldap_logic.php`, see Known Issues |
| docker-php-extension-installer | `latest` | Downloaded at image build time (mlocati) |
| Bootstrap | 5.2.3 | Loaded from the jsDelivr CDN |
| Docker / Docker Compose | Any recent version | Used for deployment |

## Known Issues

These were identified during a review against the Cooperative's software project guidelines. They are recorded here and have **not** been fixed.

### Security

- **Credentials committed to the repository.** `private/credentials.php` and `docker-compose.yml` contain hardcoded database and MariaDB root passwords. Treat them as compromised. Use unique values for any deployment and move them to environment variables or an ignored `.env` file.
- **SQL injection.** Roughly 37 queries in `private/database.php` and elsewhere are built by string concatenation. Values from `$_GET` (for example `passcode_id` in `guess_page/index.php`) and from login input reach SQL unescaped. Prepared statements are needed.
- **No output escaping.** Page output does not use `htmlspecialchars`, which leaves room for cross-site scripting (for example through user-chosen aliases).
- **Session handling.** `session_regenerate_id()` is not called after login (session fixation risk), and session cookie flags are not configured.
- **LDAP over plain text.** LDAP binds use port 389 with no TLS. Use LDAPS or StartTLS.
- **No CSRF protection** on form submissions.
- **Disabling LDAP requires editing code.** There is no configuration flag for local authentication.
- **`private/` directory is inside the web root.** Apache must be configured to deny direct access.
- **Log file in git history.** A runtime log file (`private/loginfo.txt`), containing login and activity records, was committed in early 2025 and later deleted. It remains in the git history. The application also writes this log at runtime; it should be git-ignored (now covered by `.gitignore`).
- **Seed data.** `databse.sql` inserts a sample user row that appears to be a real username. Replace with a generic placeholder before reuse.

### Bugs and Build Problems

- **Misnamed SQL file.** The database file is named `databse.sql` (typo), but `docker-compose.yml` mounts `./database.sql`, so database initialization will not find it as shipped.
- **LDAP extension missing from the Docker image.** The code calls `ldap_*` functions, but the Dockerfile installs only `mysqli`.
- **End-of-life and unpinned images.** PHP 7.4 no longer receives security updates, the `mariadb` image is unpinned, and the extension installer is downloaded from `latest`.
- **Docker build context.** The Dockerfile uses `ADD ./` and copies the whole repository into the image. A `.dockerignore` was added to exclude `.git`, screenshots, and docs, but the Dockerfile itself was not changed.
- **Unused compose network.** `docker-compose.yml` declares a `backend` network that is not used.
- **Hardcoded paths.** `private/initialize.php` hardcodes `/cybergame/` for resource and CSS paths and contains leftover `/public` path logic.
- **Undefined variables.** `view_passcode()` uses `$myPatternInfo` and `get_user_alias()`/`get_user_name()` use `$patternID`, neither of which is defined in those functions.
- **Empty-data handling.** `get_available_patterns()` returns an undefined variable (and `usort` warns) if no patterns exist.
- **Open FIXME.** `generate_guesses_per_pattern()` is marked `FIXME` in `private/database.php`.

### Maintainability

- Business logic, SQL, and HTML are mixed together in the same files.
- There are no automated tests and no CI.
- Browsers smaller than an iPad are untested.

## Version

The current version is in the [VERSION](VERSION) file and is tagged in git. Version history is in [CHANGELOG.md](CHANGELOG.md).

## Maintainers

Eau Claire Energy Cooperative. This project is archived and is not actively maintained.

## Contributing

This project is archived and is not accepting contributions or issues.

## License

[GPL-3.0](LICENSE) (c) Eau Claire Energy Cooperative.

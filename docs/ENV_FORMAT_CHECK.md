# .env Format Check

This project includes an automated `.env` format check that runs on push and pull requests.

Why:

-   The CodeIgniter DotEnv parser requires that values containing spaces are quoted (e.g., `EMAIL_FROM_NAME = "TEMU RASA CAFE"`).
-   Unquoted values with spaces will cause the application to fail boot with an InvalidArgumentException.

What we added:

-   `scripts/check_env_format.php` — a small PHP script that scans `.env` files and flags unquoted values that contain whitespace.
-   `.github/workflows/env-format-check.yml` — CI workflow that runs the check and fails the job when issues are found.

How to use locally:

-   `php scripts/check_env_format.php`

If CI fails the check on your PR, update any `.env` files in your branch to quote values that contain spaces (e.g., `email.fromName = "TEMU RASA CAFE"`).

Note: `scripts/check_env_format.php` returns a non-zero exit code when issues are found, so it is suitable for use in CI pipelines.

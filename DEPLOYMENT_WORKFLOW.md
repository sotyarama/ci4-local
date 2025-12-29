# Deployment workflow — ASUSTOR NAS (manual pull)

This file documents the lightweight, human-driven deployment flow used to update the CodeIgniter 4 app on the ASUSTOR NAS.
Keep it short and follow the checklist after each deploy.

## Overview

-   Make changes locally on your development branch (`master`).
-   Promote tested changes to the deployment branch (`deploy/nas`).
-   On the NAS, SSH in and `git pull` the `deploy/nas` branch, then perform the necessary post-pull steps listed below.

## 1) Local changes (developer)

1. Work on `master` locally. Commit small, tested units:
    - `git add` / `git commit -m "..."`
2. Push to origin:
    - `git push origin master`

Notes:

-   Keep `master` stable. Use feature branches and merge or rebase into `master` when ready.

## 2) Promote changes to `deploy/nas` (release step)

Options (pick one that fits your workflow):

-   Fast method (local):

    -   From local machine, create/update `deploy/nas` to point at the desired commit:
        -   `git checkout deploy/nas` (or create it)
        -   `git merge --no-ff master` or `git reset --hard origin/master`
        -   `git push origin deploy/nas`

-   Pull-request method (preferred for teams):
    -   Open PR from `master` → `deploy/nas`, review, merge, then push the merged branch.

Record which commit is on `deploy/nas` so the NAS operator knows what to pull.

## 3) Update code on the NAS (operator)

1. SSH to the NAS where the app runs.
2. Move to the repository directory and fetch/pull the deployment branch:
    - `git fetch origin`
    - `git checkout deploy/nas` (if not already)
    - `git pull origin deploy/nas`
3. Follow the checklist below for post-pull actions (composer, permissions, cache).

Optional verification steps:

-   Check web server error logs after the pull.
-   Visit the site in a browser and spot-check key pages.

## Checklist (do these when applicable)

-   Composer install required: ACTION if `composer.lock` or `composer.json` changed in the pulled commit.
    -   Run on NAS (inside project):
        -   `composer install --no-dev --prefer-dist --optimize-autoloader`
-   Permission fix required: ACTION if new files were created, uploads/ cache inaccessible, or after `composer install`.
    -   Ensure the webserver user owns `writable/` and any created files. Example (replace `<web-user>` and `<web-group>`):
        -   `chown -R <web-user>:<web-group> writable/`
        -   `find writable -type d -exec chmod 750 {} + && find writable -type f -exec chmod 640 {} +`
    -   Note: Use the correct OS/NAS user for your environment — avoid hardcoding names in automation.
-   Cache clear required: ACTION after configuration, view, or language changes, or if you see stale data.
    -   Clear CI4 caches (preferred):
        -   `php spark cache:clear` (or remove contents of `writable/cache` safely)

Use the above checklist each deploy; only run steps that apply to the change set.

## Common Pitfalls and Reminders

-   Uncommitted changes on the NAS: always check `git status` on the NAS before pulling. Stashed or local edits can be overwritten or block pulls.
-   `.env` is not tracked: keep a private `.env` on the NAS and do not commit it. Verify that `.env` contains production values and correct permissions (e.g., `600`).
-   Backups with credentials: do not store plaintext secrets in repository backups (remove/secure local `backups/` files if they contain secrets).
-   Composer / PHP version mismatch: ensure the NAS PHP version meets `composer.json` `php` requirement (CI4 requires PHP >= 8.1 here).
-   NAS share permissions: if the app is on an SMB/CIFS mount, verify the mount preserves POSIX permissions or use a suitable UID mapping; otherwise prefer NFS or local storage for `writable/`.
-   Remember to rotate secrets if a local `.env` was exposed.

## Quick example operator commands

```sh
cd /path/to/repo
git fetch origin
git checkout deploy/nas
git pull origin deploy/nas
# If composer changed:
composer install --no-dev --prefer-dist --optimize-autoloader
# Fix permissions (replace with your web user/group):
chown -R <web-user>:<web-group> writable/
find writable -type d -exec chmod 750 {} +
find writable -type f -exec chmod 640 {} +
# Clear CI4 cache:
php spark cache:clear
```

Keep this document handy on the repository root for quick reference.

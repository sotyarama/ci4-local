# Security & Secrets Handling

If you discover or suspect credentials were committed to this repository, follow these steps immediately:

1. Revoke and rotate secrets
   - Database user/password: create a new strong password and update the DB user credentials.
   - SMTP/Email API credentials: revoke the exposed credential and create a new one.
   - Any third-party API keys/tokens: rotate/invalidate old keys.

2. Replace secrets in local `.env` files
   - Update the local `.env` (never commit it).
   - Use environment variables or your deployment secrets manager (GitHub Actions secrets, AWS Secrets Manager, etc.).

3. Search & cleanup
   - Search the repo for likely leak artifacts (debug dumps, logs). We archived runtime artifacts to `backups/secret-backups-YYYY-MM-DD.zip` and excluded `backups/` from commits.
   - If secrets were present in commits, use `git-filter-repo` or BFG to purge them from history and force-push the cleaned repo. Notify collaborators to re-clone after purge.

4. Add scanning & prevention
   - This repo includes a GitHub Action (`.github/workflows/secret-scan.yml`) that runs secret scanning on push/PRs. Keep this active.
   - To scan locally, you can use a tool such as `gitleaks` or the same scanner used in CI. Example with Gitleaks:

```bash
gitleaks detect --source . --report-path gitleaks-report.json
```

   - Review and fix findings before pushing; remove any true positives and rotate secrets as needed.

5. Logging & redaction
   - Avoid logging full request payloads in production. Redact sensitive keys in logs.

6. Contact & incident
   - If you need assistance with purging history or coordinating a rotation across systems, open a high-priority issue and tag relevant team members.


If you'd like, I can prepare a `git-filter-repo` command list and an incident email template you can use to notify your provider/ops team.
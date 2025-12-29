# Playwright visual regression helpers

This folder contains helper scripts to capture authenticated screenshots and run quick visual diffs.

Quick setup

```bash
python -m pip install playwright pillow
python -m playwright install chromium
```

Save authenticated storage state (run once):

```bash
# interactive - opens browser for login
python login_save_state.py --out state.json
```

Capture current screenshots (uses `state.json` if present):

```bash
python visual_regression.py --base-url http://localhost:8080 --pages /index.php/pos/touch /index.php/transactions/sales --state state.json --out screenshots/current
```

Compare against a baseline directory:

```bash
python visual_regression.py --compare screenshots/baseline --out screenshots/current
```

Notes

-   The scripts will save screenshots to `screenshots/<label>`. Diffs (if any) go to `screenshots/diffs`.
-   If Playwright is not installed, the scripts print the install command and exit.

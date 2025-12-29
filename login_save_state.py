from playwright.sync_api import sync_playwright


def main():
    with sync_playwright() as p:
        browser = p.chromium.launch(headless=False)
        context = browser.new_context()
        page = context.new_page()
        page.goto("http://localhost:8080/index.php/login")
        print("Browser opened. Please log in in the opened browser window.")
        input("After successful login, press Enter here to save state.json and exit: ")
        context.storage_state(path="state.json")
        print("Saved state.json")
        browser.close()


if __name__ == "__main__":
    main()

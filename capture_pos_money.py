from playwright.sync_api import sync_playwright, TimeoutError


def main():
    with sync_playwright() as p:
        browser = p.chromium.launch()
        context = browser.new_context(storage_state="state.json")
        page = context.new_page()
        page.goto("http://localhost:8080/index.php/pos/touch")
        try:
            el = page.wait_for_selector(".pos-money", timeout=5000)
            el.screenshot(path="pos_money_element.png")
            print("Saved pos_money_element.png")
        except TimeoutError:
            page.screenshot(path="pos_money_page.png")
            print("Saved pos_money_page.png (fallback)")
        browser.close()


if __name__ == "__main__":
    main()

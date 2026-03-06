import os
import re
import json
import time
import requests
from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from webdriver_manager.chrome import ChromeDriverManager

# ─── CONFIG ───────────────────────────────────────────────────────────────────
CATEGORY_URL = "https://www.alltricks.fr/C-11364-velos"
IMAGES_DIR   = "public/images/products"
OUTPUT_FILE  = "database/seeders/AllTricksSeeder.php"
MAX_PAGES    = 5
DELAY        = 2  # secondes entre chaque page

os.makedirs(IMAGES_DIR, exist_ok=True)

# ─── DRIVER CHROME ────────────────────────────────────────────────────────────
def create_driver():
    opts = Options()
    # opts.add_argument("--headless=new")          # désactivé pour debug
    opts.add_argument("--no-sandbox")
    opts.add_argument("--disable-dev-shm-usage")
    opts.add_argument("--window-size=1920,1080")
    opts.add_argument(
        "user-agent=Mozilla/5.0 (Windows NT 10.0; Win64; x64) "
        "AppleWebKit/537.36 (KHTML, like Gecko) "
        "Chrome/120.0.0.0 Safari/537.36"
    )
    opts.add_experimental_option("excludeSwitches", ["enable-automation"])
    opts.add_experimental_option("useAutomationExtension", False)
    service = Service(ChromeDriverManager().install())
    driver  = webdriver.Chrome(service=service, options=opts)
    driver.execute_script(
        "Object.defineProperty(navigator, 'webdriver', {get: () => undefined})"
    )
    return driver

# ─── HELPERS ──────────────────────────────────────────────────────────────────
def slugify(text):
    text = text.lower().strip()
    text = re.sub(r'[^\w\s-]', '', text)
    text = re.sub(r'[\s_-]+', '-', text)
    return text[:80]

def parse_price(text):
    text = re.sub(r'[^\d,.]', '', text.replace(',', '.'))
    parts = text.split('.')
    if len(parts) > 2:
        text = ''.join(parts[:-1]) + '.' + parts[-1]
    try:
        return float(text)
    except:
        return 0.0

def download_image(url, filename):
    try:
        r = requests.get(url, timeout=10, headers={
            "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64)"
        })
        if r.status_code == 200:
            path = os.path.join(IMAGES_DIR, filename)
            with open(path, 'wb') as f:
                f.write(r.content)
            return f"images/products/{filename}"
    except Exception as e:
        print(f"  ⚠️  Image non téléchargée : {e}")
    return None

# ─── SCRAPER PAGE LISTE ───────────────────────────────────────────────────────
def scrape_listing(driver, url):
    print(f"  📄 Chargement : {url}")
    driver.get(url)
    time.sleep(DELAY)

    # accepte les cookies si la bannière apparaît
    try:
        btn = WebDriverWait(driver, 5).until(
            EC.element_to_be_clickable((By.CSS_SELECTOR,
                "button#didomi-notice-agree-button, "
                "button[data-testid='cookie-accept'], "
                ".didomi-continue-without-agreeing"
            ))
        )
        btn.click()
        time.sleep(1)
    except:
        pass  # pas de bannière cookies

    # récupère les liens produits
    links = []
    for el in driver.find_elements(By.CSS_SELECTOR,
            "a.at-product-card__link, a[data-product-id], "
            "a[href*='/F-'][href*='-velo']"):
        href = el.get_attribute("href")
        if href and '/F-' in href and href not in links:
            links.append(href)

    # page suivante
    try:
        next_btn = driver.find_element(By.CSS_SELECTOR,
            "a[rel='next'], .pagination__next a")
        next_url = next_btn.get_attribute("href")
    except:
        next_url = None

    return links, next_url

# ─── SCRAPER PAGE PRODUIT ─────────────────────────────────────────────────────
def scrape_product(driver, url):
    print(f"    🚲 {url}")
    driver.get(url)
    time.sleep(DELAY)

    def txt(selector, attr=None):
        try:
            el = driver.find_element(By.CSS_SELECTOR, selector)
            return el.get_attribute(attr) if attr else el.text.strip()
        except:
            return ""

    name        = txt("h1.at-product-title, h1[itemprop='name']") or "Produit inconnu"
    brand       = txt("[itemprop='brand'] [itemprop='name'], .at-product-brand")
    description = txt(".at-product-description, [itemprop='description']")[:1000]

    # prix
    price_raw = txt("[itemprop='price']", "content") or txt(".at-product-price__value")
    price = parse_price(price_raw)

    # image principale
    img_url = txt("[itemprop='image']", "src") or \
              txt(".at-product-gallery__main img", "src") or \
              txt(".at-product-gallery__main img", "data-src") or ""

    # couleurs
    colors = []
    for swatch in driver.find_elements(By.CSS_SELECTOR,
            ".at-product-color-picker__item, [data-color]"):
        try:
            color_name = (swatch.get_attribute("data-color") or
                          swatch.get_attribute("title") or
                          swatch.text.strip())
            color_imgs = swatch.find_elements(By.TAG_NAME, "img")
            color_img_url = color_imgs[0].get_attribute("src") if color_imgs else img_url
            if color_name:
                colors.append({"color": color_name, "image_url": color_img_url})
        except:
            continue

    # téléchargement image principale
    local_image = None
    if img_url:
        slug = slugify(name)
        ext  = img_url.split('.')[-1].split('?')[0] or 'jpg'
        local_image = download_image(img_url, f"{slug}.{ext}")

    # téléchargement images couleurs
    local_colors = []
    for c in colors:
        c_slug  = slugify(name + '-' + c['color'])
        c_ext   = c['image_url'].split('.')[-1].split('?')[0] or 'jpg'
        c_local = download_image(c['image_url'], f"{c_slug}.{c_ext}")
        local_colors.append({"color": c['color'], "image": c_local or local_image})

    return {
        "name":        name,
        "slug":        slugify(name),
        "brand":       brand,
        "price":       price,
        "description": description,
        "image":       local_image or "",
        "colors":      local_colors,
    }

# ─── COLLECTE TOUS LES PRODUITS ───────────────────────────────────────────────
def collect_all(driver):
    all_products = []
    url  = CATEGORY_URL
    page = 0

    while url and page < MAX_PAGES:
        page += 1
        print(f"\n📦 Page {page}/{MAX_PAGES}")
        product_urls, next_url = scrape_listing(driver, url)
        print(f"  → {len(product_urls)} produit(s) trouvé(s)")

        for p_url in product_urls:
            p = scrape_product(driver, p_url)
            if p:
                all_products.append(p)

        url = next_url

    return all_products

# ─── GÉNÉRATION DU SEEDER PHP ─────────────────────────────────────────────────
def generate_seeder(products):
    lines = [
        "<?php\n",
        "namespace Database\\Seeders;\n",
        "use Illuminate\\Database\\Seeder;",
        "use Illuminate\\Support\\Facades\\DB;\n",
        "class AllTricksSeeder extends Seeder\n{",
        "    public function run(): void\n    {",
        "        DB::table('products')->truncate();\n",
        "        $products = [",
    ]

    for p in products:
        colors_php = json.dumps(p['colors'], ensure_ascii=False)
        lines.append(f"""            [
                'name'        => {json.dumps(p['name'], ensure_ascii=False)},
                'slug'        => {json.dumps(p['slug'], ensure_ascii=False)},
                'brand'       => {json.dumps(p['brand'], ensure_ascii=False)},
                'price'       => {p['price']},
                'description' => {json.dumps(p['description'], ensure_ascii=False)},
                'image'       => {json.dumps(p['image'], ensure_ascii=False)},
                'colors'      => json_encode({colors_php}),
                'created_at'  => now(),
                'updated_at'  => now(),
            ],""")

    lines += [
        "        ];\n",
        "        DB::table('products')->insert($products);",
        "    }",
        "}\n",
    ]

    with open(OUTPUT_FILE, 'w', encoding='utf-8') as f:
        f.write("\n".join(lines))
    print(f"\n✅ Seeder généré : {OUTPUT_FILE}")

# ─── MAIN ─────────────────────────────────────────────────────────────────────
if __name__ == "__main__":
    print("🚀 Démarrage du scraper Alltricks (Selenium)...\n")
    driver = create_driver()
    try:
        products = collect_all(driver)
    finally:
        driver.quit()

    print(f"\n✅ {len(products)} produit(s) récupéré(s)")

    if products:
        generate_seeder(products)
        print(f"📸 Images dans : {IMAGES_DIR}")
        print("\n👉 Lance maintenant :")
        print("   php artisan db:seed --class=AllTricksSeeder")
    else:
        print("\n⚠️  Aucun produit trouvé.")
        print("   → Alltricks utilise peut-être un rendu JS complexe.")
        print("   → Retire --headless pour voir ce qui se passe dans le navigateur.")
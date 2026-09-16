# مستندات توسعه Fandoogh Framework 1.3.0

## معماری

- `app/Core`: بوت، ثابت‌ها، Container، Migration و چرخه عمر.
- `app/Managers`: مدیریت پنل و وضعیت ماژول‌ها.
- `modules`: ماژول‌های دسته محصول با Repository، Service و Renderer.
- `app/Customers` و `app/Projects`: نوع محتوا، taxonomy، ذخیره‌سازی و خروجی مستقل.
- `app/Elementor`: ثبت شرطی Dynamic Tagها.
- `assets`: فایل‌های مدیریت و Frontend.

Namespace اصلی `Fandoogh\\` است. Autoloader کلاس‌های `app` را مستقیم و namespace ماژول‌ها را از `modules/{module}` بارگذاری می‌کند. مسیر ماژول نظرات به‌صورت صریح `modules/Reviews` است؛ حساسیت حروف مسیر باید روی هاست لینوکسی رعایت شود.

## قرارداد لایه‌ها

- Repository فقط دریافت و ذخیره داده را انجام می‌دهد.
- Service اعتبارسنجی، sanitize و قواعد کاربردی را نگه می‌دارد.
- Renderer خروجی HTML را به‌صورت `string` برمی‌گرداند.
- Manager و Widget نباید HTML دامنه را داخل خود نگه دارند.

## کلیدهای اصلی

- Option ماژول‌ها: `fa_modules`
- نسخه دیتابیس: `fa_db_version`
- قفل Migration: `fa_migration_lock`
- سیاست Uninstall: `fa_delete_data_on_uninstall`
- Post typeها: `fa_customer`، `fa_project` و `fa_review_proxy`
- Taxonomyها: `fa_customer_category` و `fa_project_category`

## Migration

Migrationها در `app/Core/Migration/Migrator.php` به ترتیب نسخه اجرا می‌شوند. Migration باید idempotent باشد، داده ورودی را sanitize کند و تنها پس از موفقیت نسخه دیتابیس را افزایش دهد. قفل پنج‌دقیقه‌ای اجرای هم‌زمان را محدود می‌کند.

## امنیت

- تمام عملیات AJAX مدیریت به `manage_options` و nonce نیاز دارند.
- ثبت عمومی نظر دارای nonce، Honeypot و Rate Limit است.
- ورودی‌ها پیش از ذخیره sanitize و خروجی‌ها متناسب با context escape می‌شوند.
- HTML ویرایشگر با سیاست HTML امن وردپرس ذخیره می‌شود.

## سازگاری

- حداقل WordPress: 6.8
- حداقل PHP: 8.1
- وابستگی الزامی: WooCommerce
- Elementor اختیاری است و Hook ثبت Dynamic Tag با نسخه نصب‌شده تطبیق داده می‌شود.
- سازگاری WooCommerce HPOS در `before_woocommerce_init` اعلام شده است.

## تست

از ریشه افزونه اجرا کنید:

```text
php tests/autoloader.php
php tests/reviews-rate-limiter.php
```

این آزمون‌ها مستقل از سایت و با داده‌های ساختگی اجرا می‌شوند. آزمون autoloader حساسیت حروف مسیر را حتی روی ویندوز بررسی می‌کند. پیش از انتشار، lint همه فایل‌های `app`، `modules`، `config` و فایل ورودی با PHP 8.1 اجرا شود؛ فقط بررسی فایل‌های `app` کافی نیست. این بررسی‌ها جای نصب واقعی وردپرس و ووکامرس را نمی‌گیرند.

## انتشار

نسخه هدر، `FA_VERSION`، `Stable tag` و شماره Build باید هم‌خوان باشند. سپس lint، smoke test و بررسی JavaScript اجرا و بسته‌ای بدون `.git`، `.agents`، `tests`، `tools` و خروجی‌های توسعه ساخته می‌شود.

## سیاست Uninstall

`uninstall.php` فقط با وجود `WP_UNINSTALL_PLUGIN` اجرا می‌شود. داده‌ها پیش‌فرض حفظ می‌شوند. در حالت حذف صریح، داده‌های اختصاصی افزونه پاک می‌شوند ولی attachmentها و `product_cat` باقی می‌مانند.

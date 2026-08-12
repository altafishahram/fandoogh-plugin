# مستندات توسعه Fandoogh Framework 1.0.0

## معماری

- `app/Core`: بوت، ثابت‌ها، Container، Migration و چرخه عمر.
- `app/Managers`: مدیریت پنل و وضعیت ماژول‌ها.
- `modules`: ماژول‌های دسته محصول با Repository، Service و Renderer.
- `app/Customers` و `app/Projects`: نوع محتوا، taxonomy، ذخیره‌سازی و خروجی مستقل.
- `app/Elementor`: ثبت شرطی Dynamic Tagها.
- `assets`: فایل‌های مدیریت و Frontend.

Namespace اصلی `Fandoogh\\` است. Autoloader کلاس‌های `app` را مستقیم و namespace ماژول‌ها را از `modules/{module}` بارگذاری می‌کند.

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
- حداقل PHP: 8.2
- وابستگی الزامی: WooCommerce
- Elementor اختیاری است و Hook ثبت Dynamic Tag با نسخه نصب‌شده تطبیق داده می‌شود.
- سازگاری WooCommerce HPOS در `before_woocommerce_init` اعلام شده است.

## تست

از ریشه افزونه اجرا کنید:

```powershell
tests\smoke.php
```

تست‌رانر تغییرات Activation و Migration را داخل transaction انجام می‌دهد و در پایان Rollback می‌کند. تست شاخه حذف کامل عمداً روی سایت واقعی اجرا نمی‌شود.

## انتشار

نسخه هدر، `FA_VERSION`، `Stable tag` و شماره Build باید هم‌خوان باشند. سپس lint، smoke test و بررسی JavaScript اجرا و بسته‌ای بدون `.git`، `.agents`، `tests`، `tools` و خروجی‌های توسعه ساخته می‌شود.

## سیاست Uninstall

`uninstall.php` فقط با وجود `WP_UNINSTALL_PLUGIN` اجرا می‌شود. داده‌ها پیش‌فرض حفظ می‌شوند. در حالت حذف صریح، داده‌های اختصاصی افزونه پاک می‌شوند ولی attachmentها و `product_cat` باقی می‌مانند.

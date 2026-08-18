# مستندات توسعه Fandoogh Framework 1.0.0

## معماری

- `app/Core`: بوت، ثابت‌ها، Container، Migration و چرخه عمر.
- `app/Managers`: مدیریت پنل و وضعیت ماژول‌ها.
- `modules`: ماژول‌های مستقل محتوایی و مرکز سفارشات با Repository، Service و Renderer.
- `app/Customers` و `app/Projects`: نوع محتوا، taxonomy، ذخیره‌سازی و خروجی مستقل.
- `modules/ordercenter`: مرکز سفارشات WooCommerce با Query/Stats/Meta Manager و Service مستقل.
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

- تمام عملیات AJAX مدیریت به nonce و capability متناسب با دامنه خود نیاز دارند؛ تنظیمات فندق `manage_options` و قیمت ماشین حساب/مرکز سفارشات `manage_woocommerce` را استفاده می‌کنند.
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

در بسته فعلی PHPUnit یا `tests\smoke.php` وجود ندارد. بررسی‌های ایستا را از ریشه افزونه اجرا کنید:

```powershell
Get-ChildItem -Recurse -Filter *.php | ForEach-Object { php -l $_.FullName }
Get-ChildItem -Recurse -Filter *.js | ForEach-Object { node --check $_.FullName }
```

تست runtime باید روی یک WordPress واقعی با WooCommerce فعال انجام شود و شامل فعال‌سازی/غیرفعال‌سازی ماژول‌ها، Migration، HPOS، ثبت سفارش، تغییر وضعیت و Uninstall باشد.

## انتشار

نسخه هدر، `FA_VERSION`، `Stable tag` و شماره Build باید هم‌خوان باشند. سپس lint، چک JavaScript و smoke checklist runtime اجرا و بسته‌ای بدون `.git`، `.agents`، `tests`، `tools` و خروجی‌های توسعه ساخته می‌شود.

## سیاست Uninstall

`uninstall.php` فقط با وجود `WP_UNINSTALL_PLUGIN` اجرا می‌شود. داده‌ها پیش‌فرض حفظ می‌شوند. در حالت حذف صریح، داده‌های اختصاصی افزونه پاک می‌شوند ولی attachmentها و `product_cat` باقی می‌مانند.

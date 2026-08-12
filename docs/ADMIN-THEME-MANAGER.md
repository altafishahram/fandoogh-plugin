# Admin Theme Manager

مدیر پوسته پنل فندق از معماری Static Dynamic CSS استفاده می‌کند. CSS فقط هنگام ذخیره، Reset، Import، تغییر نسخه Schema یا تغییر Build ساخته می‌شود و در بارگذاری عادی تنها فایل آماده Enqueue خواهد شد.

## محدوده

نسخه 1 فقط صفحات خود Fandoogh را با Scope کلاس `.fa-admin-shell` تغییر می‌دهد. هیچ Selector سراسری روی `wp-admin`، افزونه‌های دیگر یا Frontend اعمال نمی‌شود.

## مسیر فایل‌ها

فایل‌های تولیدشده در مسیر استاندارد Upload وردپرس ذخیره می‌شوند:

```text
wp-content/uploads/fandoogh/admin-theme/generated-{hash}.css
```

نام فایل از Hash محتوای CSS ساخته می‌شود. حداکثر دو فایل، نسخه جاری و نسخه قبلی سالم، نگهداری می‌شوند.

## امکانات

- Preset شیشه‌ای فندق، نیمه‌شب و ساده
- حالت روشن، تاریک و هماهنگ با سیستم
- فونت Vazirmatn، Tahoma و System UI
- تنظیم رنگ‌ها، Radius، Blur و شفافیت شیشه
- Preview زنده و ایزوله
- Import و Export تنظیمات JSON
- Reset و تولید مجدد CSS
- اصلاح خودکار کنتراست متن‌های اصلی
- Fallback به CSS پیش‌فرض یا فایل Generated قبلی
- قفل تولید هم‌زمان و ذخیره اتمیک

## امنیت

همه مقادیر از Schema و Allowlist عبور می‌کنند. مسیر فایل، نام فونت، CSS خام و URL دلخواه از کاربر پذیرفته نمی‌شود. ذخیره AJAX به nonce و capability `manage_options` نیاز دارد.

## توسعه

برای افزودن Preset، آرایه `SettingsSchema::presets()` را گسترش دهید. هر تغییر در ساختار Generated CSS باید همراه با افزایش `SettingsSchema::VERSION` یا Build افزونه باشد تا فایل به‌صورت خودکار بازتولید شود.

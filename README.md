## تعليمات تشغيل المشروع

لتشغيل المشروع على جهازك المحلي، اتبع الخطوات التالية:

```bash
# استنساخ المشروع
git clone https://github.com/stringAsmaa/aladhan.git
cd aladhan

# تثبيت الاعتمادات
composer install

# نسخ ملف البيئة وضبطه
cp .env.example .env

# توليد مفتاح التطبيق
php artisan key:generate

# تنفيذ المايغريشن لإنشاء قاعدة البيانات
php artisan migrate

# تشغيل الخادم المحلي
php artisan serve

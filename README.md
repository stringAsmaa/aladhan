## تعليمات تشغيل المشروع

لتشغيل المشروع على جهازك المحلي، اتبع الخطوات التالية:


# استنساخ المشروع
```bash
git clone https://github.com/stringAsmaa/aladhan.git
```
```bash
cd aladhan
```
# تثبيت الاعتمادات
```bash
composer install
```
# نسخ ملف البيئة وضبطه
```bash
cp .env.example .env
```
# توليد مفتاح التطبيق
```bash
php artisan key:generate
```
# توليد JWT secret
```bash
php artisan jwt:secret
```
# تنفيذ المايغريشن لإنشاء قاعدة البيانات
```bash
php artisan migrate
```
# تشغيل الخادم المحلي
```bash
php artisan serve
```

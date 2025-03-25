# ใช้ PHP 8.1 (หรือเวอร์ชันที่ต้องการ)
FROM php:8.1-apache

# ติดตั้งส่วนขยายที่ต้องใช้
RUN docker-php-ext-install mysqli

# คัดลอกไฟล์ทั้งหมดเข้า Container
COPY . /var/www/html/

# เปลี่ยน Permission
RUN chown -R www-data:www-data /var/www/html/
RUN chmod -R 755 /var/www/html/

# เปิดพอร์ต 80
EXPOSE 80

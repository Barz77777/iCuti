FROM php:8.1-apache

# Install ekstensi PHP (LDAP dan mysqli)
RUN apt-get update && apt-get install -y \
    libldap2-dev \
    && docker-php-ext-install ldap mysqli

# Aktifkan mod_rewrite
RUN a2enmod rewrite

# Set ServerName agar tidak muncul warning
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Ganti document root ke folder public/
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

# Update konfigurasi VirtualHost agar Apache tahu folder public/ sebagai root
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf

# Tambahkan konfigurasi <Directory> untuk memastikan akses folder
RUN echo '<Directory /var/www/html/public>\nOptions Indexes FollowSymLinks\nAllowOverride All\nRequire all granted\n</Directory>' >> /etc/apache2/apache2.conf

# Copy semua file project ke dalam image
COPY . /var/www/html/

# Atur permission
RUN chown -R www-data:www-data /var/www/html

# Jalankan Apache
CMD ["apache2-foreground"]


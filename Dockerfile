# Use official PHP with Apache image
FROM php:8.2-apache

# Copy source code to Apache web root
COPY . /var/www/html/

# Optional: enable mod_rewrite if needed
RUN a2enmod rewrite

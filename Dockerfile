# Usamos una imagen oficial de PHP con el servidor Apache incluido
FROM php:8.2-apache

# Copiamos todo el código de tu proyecto a la carpeta pública del servidor
COPY . /var/www/html/

# Habilitamos la reescritura de URLs (útil para el enrutamiento)
RUN a2enmod rewrite

# Exponemos el puerto 80 para que Render pueda mostrar la web
EXPOSE 80

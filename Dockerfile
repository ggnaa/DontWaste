FROM php:8.2-apache

# Se copia todo el código a la carpeta del proyecto del servidor
COPY . /var/www/html/

# Elimina comentarios <!-- --> de todos los archivos .php y .html
RUN find /var/www/html/ -type f \( -name "*.php" -o -name "*.html" \) -exec sed -i 's/<!--.*-->//g' {} \;

# Elimina comentarios /* */ de todos los archivos .css
RUN find /var/www/html/ -name "*.css" -exec sed -i ':a;N;$!ba;s/\/\*[^*]*\*\+ \([^/*][^*]*\*\+\)*\// /g' {} \;

# Se habilita la reescritura de URLs
RUN a2enmod rewrite

# Para que render pueda mostrar la web
EXPOSE 80

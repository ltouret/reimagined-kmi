# Use an official PHP image with FPM
FROM php:8.1-fpm-alpine

RUN apk add --no-cache nginx
# Copy your application code
# COPY . /var/www/html

# Expose Nginx port
EXPOSE 80

# Start Nginx (assuming it's already installed in the image)
CMD ["nginx", "-g", "daemon off;"]

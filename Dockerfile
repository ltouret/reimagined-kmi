# Use a minimal PHP image
FROM php:7.4-cli

# Set working directory
WORKDIR /var/www/html

# Copy the current directory content to the container
#COPY . .
# Mount the current directory as a volume
#VOLUME ["/var/www/html"]

# Expose port 8080
EXPOSE 8080

# Start PHP built-in server on port 8080
CMD ["php", "-S", "0.0.0.0:8080", "app.php"]

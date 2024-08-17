# Use a minimal PHP image
FROM php:7.4-cli

# Set working directory
WORKDIR /var/www/html

# Copy the current directory content to the container
COPY . .

# Expose port 8080
EXPOSE 8080

# Start PHP built-in server on port 8080
RUN chmod +x ./start.sh
ENTRYPOINT ["./start.sh"]

#!/bin/sh

# Wait for database to be ready
echo "Waiting for database to be ready..."
while ! php -r "
    \$host = getenv('DB_HOST');
    \$port = getenv('DB_PORT');
    \$user = getenv('DB_USERNAME');
    \$pass = getenv('DB_PASSWORD');
    \$db   = getenv('DB_DATABASE');
    
    echo \"Attempting to connect to PostgreSQL at \$host:\$port...\n\";
    
    try {
        \$conn = pg_connect(\"host=\$host port=\$port dbname=\$db user=\$user password=\$pass\");
        if (!\$conn) {
            echo \"Connection failed: \" . pg_last_error() . \"\n\";
            exit(1);
        }
        echo \"Successfully connected to PostgreSQL!\n\";
        pg_close(\$conn);
        exit(0);
    } catch (Exception \$e) {
        echo \"Connection error: \" . \$e->getMessage() . \"\n\";
        exit(1);
    }
" 2>&1; do
    echo "Database not ready yet... Retrying in 1 second"
    sleep 1
done

echo "Database is ready!"

# Clear configuration and cache
echo "Clearing configuration and cache..."
php artisan config:clear
php artisan cache:clear

# Run migrations
echo "Running migrations..."
php artisan migrate --seed

php artisan generate:embedding

# Start queue worker in the background
echo "Starting Laravel queue worker..."
php artisan queue:work --tries=3 --timeout=90 &
QUEUE_PID=$!

# Start the application
echo "Starting Laravel application..."
php artisan serve --host=0.0.0.0 --port=8000 &
SERVER_PID=$!

# Function to handle shutdown
cleanup() {
    echo "Shutting down..."
    kill $QUEUE_PID
    kill $SERVER_PID
    exit 0
}

# Trap SIGTERM and SIGINT
trap cleanup SIGTERM SIGINT

# Wait for both processes
wait $QUEUE_PID $SERVER_PID 
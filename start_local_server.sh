#!/bin/bash

echo "========================================"
echo " Wishluv Buildcon - Local Server Setup"
echo "========================================"
echo

# Check if PHP is installed
if ! command -v php &> /dev/null; then
    echo "ERROR: PHP is not installed or not in PATH"
    echo "Please install PHP first"
    echo
    echo "On macOS: brew install php"
    echo "On Ubuntu: sudo apt install php"
    echo "On CentOS: sudo yum install php"
    exit 1
fi

echo "PHP is installed:"
php --version
echo

# Check if we're in the right directory
if [ ! -f "index.php" ]; then
    echo "ERROR: index.php not found"
    echo "Please run this script from the project root directory"
    exit 1
fi

echo "Starting PHP development server..."
echo
echo "Website will be available at:"
echo "  http://localhost:8000"
echo
echo "Admin Panel will be available at:"
echo "  http://localhost:8000/admin"
echo
echo "Setup Test will be available at:"
echo "  http://localhost:8000/test_local_setup.php"
echo
echo "Press Ctrl+C to stop the server"
echo "========================================"
echo

# Start PHP built-in server
php -S localhost:8000

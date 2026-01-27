#!/bin/bash
# =============================================================================
# EC2 User Data - Downloads application from S3
# =============================================================================

set -e
exec > >(tee /var/log/user-data.log) 2>&1

echo "=========================================="
echo "Starting EC2 configuration..."
echo "=========================================="

# Install packages
dnf update -y
dnf install -y httpd php php-mysqlnd php-pdo

# Start Apache
systemctl start httpd
systemctl enable httpd

# Download application from S3
echo "Downloading application from S3..."
aws s3 sync s3://${s3_bucket}/ /var/www/html/ --delete

# Configure database environment variables
cat > /etc/httpd/conf.d/app-env.conf << EOF
SetEnv DB_HOST "${db_host}"
SetEnv DB_PORT "${db_port}"
SetEnv DB_NAME "${db_name}"
SetEnv DB_USER "${db_user}"
SetEnv DB_PASS "${db_pass}"
EOF

# Set permissions
chown -R apache:apache /var/www/html
chmod -R 755 /var/www/html

# Restart Apache
systemctl restart httpd

echo "=========================================="
echo "EC2 configuration completed!"
echo "=========================================="

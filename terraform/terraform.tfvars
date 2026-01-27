# =============================================================================
# Terraform Variables - Copy to terraform.tfvars and update
# =============================================================================

project_name = "webapp"
environment  = "dev"
aws_region   = "us-east-1"

# VPC
vpc_cidr             = "10.0.0.0/16"
public_subnet_cidrs  = ["10.0.1.0/24", "10.0.2.0/24"]
private_subnet_cidrs = ["10.0.10.0/24", "10.0.20.0/24"]

# EC2
ec2_instance_type = "t3.micro"
key_name          = ""                # Optional: your SSH key name
allowed_ssh_cidrs = []                # Optional: ["YOUR_IP/32"]

# RDS
db_engine            = "mysql"
db_engine_version    = "8.0"
db_instance_class    = "db.t3.micro"
db_allocated_storage = 20
db_name              = "webapp"
db_username          = "admin"
db_password          = "CHANGE_ME_123!"  # ⚠️ CHANGE THIS!
db_port              = 3306
db_multi_az          = false

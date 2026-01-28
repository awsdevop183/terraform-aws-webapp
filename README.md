# Terraform AWS Web Application

A complete AWS infrastructure project with VPC, EC2 (Auto Scaling + ALB), and RDS — all managed with Terraform.

## 📁 Project Structure

```
terraform-aws-webapp/
├── app/                    # Application code
│   ├── index.php          # Main application
│   ├── config.php         # Configuration
│   └── health.php         # Health check endpoint
│
├── terraform/             # Infrastructure code
│   ├── main.tf           # Main configuration
│   ├── variables.tf      # Input variables
│   ├── outputs.tf        # Outputs
│   ├── terraform.tfvars.example
│   │
│   └── modules/
│       ├── vpc/          # VPC, subnets, NAT Gateway
│       ├── ec2/          # ALB, ASG, Launch Template, S3
│       └── rds/          # RDS MySQL/PostgreSQL
```

## 🏗️ Architecture

```
                         Internet
                            │
                            ▼
                    ┌───────────────┐
                    │      ALB      │
                    └───────┬───────┘
                            │
            ┌───────────────┼───────────────┐
            ▼               ▼               ▼
    ┌──────────────┐ ┌──────────────┐      ...
    │     EC2      │ │     EC2      │  (Auto Scaling)
    │  (Public)    │ │  (Public)    │
    └──────┬───────┘ └──────┬───────┘
           │                │
           └────────┬───────┘
                    ▼
            ┌──────────────┐
            │     RDS      │
            │  (Private)   │
            └──────────────┘
```

## ✨ Features

- **VPC**: Custom networking with public/private subnets across 2 AZs
- **EC2**: Auto Scaling Group (2-4 instances) with Application Load Balancer
- **RDS**: MySQL or PostgreSQL in private subnets with encryption
- **S3**: Application code stored and synced to EC2 instances
- **Security**: Least-privilege security groups, no public DB access

## 🚀 Quick Start

```bash
# 1. Navigate to terraform directory
cd terraform

# 2. Create your variables file
cp terraform.tfvars.example terraform.tfvars

# 3. Edit terraform.tfvars (set your db_password!)
vi terraform.tfvars

# 4. Initialize Terraform
terraform init

# 5. Review the plan
terraform plan

# 6. Deploy
terraform apply

# 7. Access your app
# URL will be shown in outputs
```

## 🔄 Updating the Application

When you update files in the `app/` directory:

```bash
cd terraform
terraform apply
```

Terraform will:
1. Upload changed files to S3
2. You can trigger instance refresh manually or wait for scale events

To force redeploy to all instances:
```bash
aws autoscaling start-instance-refresh \
  --auto-scaling-group-name webapp-dev-asg
```

## 📤 Outputs

| Output | Description |
|--------|-------------|
| `web_url` | Application URL (ALB DNS) |
| `vpc_id` | VPC identifier |
| `rds_endpoint` | Database connection endpoint |
| `app_bucket` | S3 bucket with app code |

## 💰 Estimated Cost

~$76/month (us-east-1):
- EC2 (2x t3.micro): ~$15
- ALB: ~$16
- NAT Gateway: ~$32
- RDS (db.t3.micro): ~$13

## 🧹 Cleanup

```bash
terraform destroy
```

## 📝 Video Tutorial Sections

1. **Intro** - What we're building
2. **Project Structure** - Explain app/ and terraform/ separation
3. **VPC Module** - Networking setup
4. **EC2 Module** - ALB, ASG, S3 deployment
5. **RDS Module** - Database setup
6. **Deploy & Test** - Run terraform apply, access app
7. **Update App** - Show how changes flow through
8. **Cleanup** - Destroy resources

---

**Happy Terraforming! 🚀**

# =============================================================================
# Outputs
# =============================================================================

output "web_url" {
  description = "Application URL"
  value       = "http://${module.ec2.alb_dns_name}"
}

output "vpc_id" {
  description = "VPC ID"
  value       = module.vpc.vpc_id
}

output "alb_dns_name" {
  description = "ALB DNS name"
  value       = module.ec2.alb_dns_name
}

output "rds_endpoint" {
  description = "RDS endpoint"
  value       = module.rds.db_endpoint
}

output "app_bucket" {
  description = "S3 bucket with application code"
  value       = module.ec2.app_bucket_name
}

data "aws_elb_service_account" "main" {}

data "aws_acm_certificate" "bmltenabled_org" {
  domain      = "*.bmltenabled.org"
  statuses    = ["ISSUED"]
  most_recent = true
}

data "aws_route53_zone" "aws_bmlt_app" {
  name = "aws.bmlt.app."
}

data "aws_security_group" "ecs_clusters" {
  name   = "ecs-clusters"
  vpc_id = data.aws_vpc.main.id
}

data "aws_security_group" "rds_mysql" {
  name   = "rds-mysql"
  vpc_id = data.aws_vpc.main.id
}

data "aws_iam_instance_profile" "ecs" {
  name = "bmlt-ecs"
}

data "aws_lb" "main" {
  name = "bmlt"
}

data "aws_lb_listener" "main_443" {
  load_balancer_arn = data.aws_lb.main.arn
  port              = 443
}

data "aws_vpc" "main" {
  filter {
    name   = "tag:Name"
    values = ["bmlt"]
  }
}

data "aws_subnets" "main" {
  filter {
    name   = "vpc-id"
    values = [data.aws_vpc.main.id]
  }
}

data "aws_subnet" "main" {
  for_each = toset(data.aws_subnets.main.ids)
  id       = each.value
}

data "aws_db_subnet_group" "bmlt" {
  name = "bmlt"
}

data "aws_secretsmanager_secret" "docker" {
  name = "docker"
}

data "aws_iam_role" "ec2_assume" {
  name = "ec2-assume"
}

locals {
  cloudwatch_config = {
    logs = {
      logs_collected = {
        files = {
          collect_list = [
            {
              file_path       = "/var/log/messages"
              log_group_name  = "${local.cluster_name}/var/log/messages"
              log_stream_name = "{instance_id}"
            },
            {
              file_path       = "/var/log/ecs/ecs-init.log"
              log_group_name  = "${local.cluster_name}/var/log/ecs/ecs-init.log"
              log_stream_name = "{instance_id}"
            },
            {
              file_path       = "/var/log/ecs/ecs-agent.log"
              log_group_name  = "${local.cluster_name}/var/log/ecs/ecs-agent.log"
              log_stream_name = "{instance_id}"
            },
            {
              file_path       = "/var/log/ecs/audit.log"
              log_group_name  = "${local.cluster_name}/var/log/ecs/audit.log"
              log_stream_name = "{instance_id}"
            }
          ]
        }
      }
    }
  }
}

data "cloudinit_config" "cluster" {
  part {
    content_type = "text/x-shellscript"
    content      = <<EOF
#!/usr/bin/env bash
set -e

echo ECS_CLUSTER=${local.cluster_name} >> /etc/ecs/ecs.config

dnf install -y amazon-cloudwatch-agent

cat > /opt/aws/amazon-cloudwatch-agent/etc/cloudwatch-config.json <<'CWEOF'
${jsonencode(local.cloudwatch_config)}
CWEOF

/opt/aws/amazon-cloudwatch-agent/bin/amazon-cloudwatch-agent-ctl \
  -a fetch-config \
  -m ec2 \
  -s \
  -c file:/opt/aws/amazon-cloudwatch-agent/etc/cloudwatch-config.json

systemctl enable amazon-cloudwatch-agent
EOF
  }
}

data "aws_ami" "ecs" {
  most_recent = true

  owners = ["amazon"]

  filter {
    name   = "name"
    values = ["al2023-ami-ecs-hvm-*-x86_64"]
  }
}

data "aws_key_pair" "this" {
  key_name           = "bmlt"
  include_public_key = true
}

data "aws_iam_role" "ecs_task" {
  name = "ecs-task"
}

data "aws_iam_role" "ecs_service" {
  name = "ecs-service"
}

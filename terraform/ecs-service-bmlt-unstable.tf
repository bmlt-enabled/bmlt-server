resource "aws_ecs_task_definition" "bmlt_unstable" {
  family             = "bmlt-unstable"
  task_role_arn      = data.aws_iam_role.ecs_task.arn
  execution_role_arn = data.aws_iam_role.ecs_task.arn

  container_definitions = jsonencode(
    [
      {
        name = "bmlt-server"
        portMappings = [
          {
            hostPort      = 0
            containerPort = 8000
            protocol      = "tcp"
          }
        ]
        environment = [
          {
            name  = "MEETING_STATES_AND_PROVINCES",
            value = "CT,MA,NH,NJ,NY,PA,VT"
          },
          {
            name  = "NEW_UI_ENABLED",
            value = "true"
          },
          {
            name  = "DB_DATABASE",
            value = "rootserver"
          },
          {
            name  = "DB_USER",
            value = "rootserver"
          },
          {
            name  = "DB_PASSWORD",
            value = "rootserver"
          },
          {
            name  = "DB_HOST",
            value = "bmlt-db"
          },
          {
            name  = "DB_PREFIX",
            value = "na"
          }
        ]
        links            = ["bmlt-db"]
        workingDirectory = "/tmp"
        image            = "bmltenabled/bmlt-server:latest"
        repositoryCredentials = {
          credentialsParameter = data.aws_secretsmanager_secret.docker.arn
        }
        command = [
          "/bin/bash",
          "/tmp/start-bmlt.sh"
        ]
        logConfiguration = {
          logDriver = "awslogs"
          options = {
            awslogs-group         = aws_cloudwatch_log_group.bmlt_server.name
            awslogs-region        = "us-east-1"
            awslogs-stream-prefix = "bmlt-server"
          }
        }
        memoryReservation = 256
        linuxParameters = {
          initProcessEnabled = true
        }
      },
      {
        name = "bmlt-db",
        portMappings = [
          {
            containerPort = 3306
            protocol      = "tcp"
          }
        ]
        environment = [
          {
            name  = "MARIADB_ROOT_PASSWORD",
            value = "rootserver"
          },
          {
            name  = "MARIADB_DATABASE",
            value = "rootserver"
          },
          {
            name  = "MARIADB_USER",
            value = "rootserver"
          },
          {
            name  = "MARIADB_PASSWORD",
            value = "rootserver"
          }
        ]
        workingDirectory = "/tmp",
        image            = "bmltenabled/bmlt-server-sample-db:unstable"
        repositoryCredentials = {
          credentialsParameter = data.aws_secretsmanager_secret.docker.arn
        }
        logConfiguration = {
          logDriver = "awslogs"
          options = {
            awslogs-group         = aws_cloudwatch_log_group.bmlt_db.name
            awslogs-region        = "us-east-1"
            awslogs-stream-prefix = "bmlt-db"
          }
        },
        memoryReservation = 144
        linuxParameters = {
          initProcessEnabled = true
        }
      }
    ]
  )
}

resource "aws_ecs_service" "bmlt_unstable" {
  name                               = "bmlt-unstable"
  cluster                            = aws_ecs_cluster.main.id
  desired_count                      = 1
  iam_role                           = data.aws_iam_role.ecs_service.name
  task_definition                    = aws_ecs_task_definition.bmlt_unstable.arn
  enable_execute_command             = true
  deployment_minimum_healthy_percent = 100

  load_balancer {
    target_group_arn = aws_lb_target_group.bmlt_unstable.id
    container_name   = "bmlt-server"
    container_port   = 8000
  }

  lifecycle {
    ignore_changes = [task_definition]
  }
}

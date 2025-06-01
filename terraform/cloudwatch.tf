resource "aws_cloudwatch_log_group" "bmlt_server" {
  name              = "bmlt-server"
  retention_in_days = 7
}

resource "aws_cloudwatch_log_group" "bmlt_db" {
  name              = "bmlt-db"
  retention_in_days = 7
}

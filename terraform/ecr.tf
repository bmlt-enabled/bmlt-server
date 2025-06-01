resource "aws_ecrpublic_repository" "bmlt-server" {
  repository_name = "bmlt-server"

  catalog_data {
    description       = "BMLT Root Server"
    architectures     = ["x86-64", "ARM 64"]
    operating_systems = ["Linux"]
  }
}

resource "aws_ecrpublic_repository" "bmlt-server-base" {
  repository_name = "bmlt-server-base"

  catalog_data {
    description       = "BMLT Server BASE"
    architectures     = ["x86-64", "ARM 64"]
    operating_systems = ["Linux"]
  }
}

resource "aws_ecrpublic_repository" "bmlt-server-sample-db" {
  repository_name = "bmlt-server-sample-db"

  catalog_data {
    description       = "BMLT Server Sample DB"
    architectures     = ["x86-64", "ARM 64"]
    operating_systems = ["Linux"]
  }
}

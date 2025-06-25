# Basic Meeting List Toolbox Server

<h1 align="center">
<a href="https://github.com/bmlt-enabled/bmlt-server/releases/latest"><img src="https://img.shields.io/github/v/release/bmlt-enabled/bmlt-server"></a>
<a href="https://php.net"><img src="https://img.shields.io/badge/php-%5E8.2-8892BF.svg" alt="PHP Programming Language"></a>
<a href="https://raw.githubusercontent.com/bmlt-enabled/bmlt-server/main/LICENSE"><img src="https://img.shields.io/github/license/bmlt-enabled/bmlt-server"></a>
<a href="https://github.com/bmlt-enabled/bmlt-server/actions/workflows/test.yml"><img src="https://github.com/bmlt-enabled/bmlt-server/actions/workflows/test.yml/badge.svg" alt="Tests"></a>
<a href="https://github.com/bmlt-enabled/bmlt-server/actions/workflows/main.yml"><img src="https://img.shields.io/github/actions/workflow/status/bmlt-enabled/bmlt-server/main.yml?branch=main&logo=github&style=flat-square" alt="Build Status"></a>
<a href="https://app.codecov.io/gh/bmlt-enabled/bmlt-server/tree/main"><img src="https://codecov.io/gh/bmlt-enabled/bmlt-server/branch/main/graph/badge.svg?token=E64EDTCREH"></a>
<a href="https://github.com/bmlt-enabled/bmlt-server/releases"><img src="https://img.shields.io/github/downloads/bmlt-enabled/bmlt-server/total"></a>
</h1>

DESCRIPTION
-----------

The Basic Meeting List Toolbox (BMLT, hereafter) is a very powerful client/server system
that has been written for a very specific purpose, for a very specific clientele.

It is designed to track and locate Narcotics Anonymous meetings, which are regularly-scheduled, weekly, recurring events.

The original intended clientele is Narcotics Anonymous Service bodies (although other 12 step fellowships have started
using BMLT as well). The service body implements a BMLT server, and provides the server to other NA Service bodies.
This project is the server for the BMLT. It is the "server" part of the BMLT "client/server" architecture.

You can find out more about the BMLT on the [website](https://bmlt.app).

[Follow this link to access the BMLT Server GitHub repository](https://github.com/bmlt-enabled/bmlt-server).
There are also links to various predecessor legacy repositories [here](#older-repositories) at the end of this README.
For specific information on setting up a development environment for work on the BMLT server, please
see [CONTRIBUTING.md](CONTRIBUTING.md) in the GitHub repository.

REQUIREMENTS
------------

The server requires a MySQL 5.7 database or higher and PHP 8.2 or higher.In addition, the following PHP modules
are required: `curl gd intl mbstring mysql xml zip`. In particular, make sure you have `intl` -- this one is more
likely to be missing. There are a variety of MySQL modules for PHP, and others will probably work as a substitute
for `mysql` itself.

For more information about server requirements, see the "Things You Will Need Before You Install" section of
[Installing a New Server](https://bmlt.app/setting-up-the-bmlt/).
 
INSTALLATION
------------

For instructions on installing the server, see [installation/README.md](installation/README.md) in the GitHub repository.

# Security Monitor

Your go to for monitoring security of your apps dependencies.


## Features

* Connect to GitHub/GitLab with your personal token
* Track projects your personal access token has access to
* more coming, see todo list at the bottom

### Project overview

On this page you can see the list of current vulnerabilities along with historical chart of their amount.

![image](docs/project_report.png)

## Setting up

> Assuming you have [Docker](https://www.docker.com/community-edition#/download) and [dory](https://github.com/FreedomBen/dory) with SSL certs stored in `~/.dinghy/certs`
1. Run `make certs`
1. Run `make up`

Done, you can access the application on [www.security.dev](https://www.security.dev)

## TODO list

- [ ] Github/Gitlab webhook support
- [ ] running checks on schedule
- [ ] API for e.g. Icinga
- [ ] secure storage of VCS credentials
- [ ] authentication & authorization
- [ ] closer integration with Github APIs (Checks API maybe?)
- [x] list of packages installed per application with versions
- [x] list of applications using a given package
- [x] historical stats about vulnerable packages in project

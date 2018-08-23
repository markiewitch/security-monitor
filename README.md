# Security Monitor

Your go to for monitoring security of your apps dependencies.


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

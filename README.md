# Readme

The custom image configures the nextcloud instance. It automatically install the plugins `sociallogin`, `groupfolders` as well as a custom plugin to create groupfolders automatically.
Also removes default user files and imports the whole NextCloud configuration.

## Build development container locally

Use `docker-compose -f compose-local.yml up` to build the customized nextcloud image and build run this image and an mysql server
as container.

### Without Postgres

To run the container seperately or on a custom database instance use the following:

To build the container execute following command:

```bash
docker build --target development -t schulcloud/schulcloud-nextcloud/dev .
```

To create the container execute following command:

```bash
docker create -p 8080:80 -e .env --name schulcloud-nextcloud schulcloud/schulcloud-nextcloud/dev:latest
```

To start (or stop) the container execute following command:

```bash
docker start schulcloud-nextcloud
```

```bash
docker stop schulcloud-nextcloud
```

The NextCloud Instance will be available at [`http://localhost:8080`](http://localhost:8080). For the admin login, use [`http://localhost:8080?direct=1`](http://localhost:8080?direct=1). You may login into the instance with username `admin` and password `admin`.

## NextCloud configuration

ENVS ... tbd

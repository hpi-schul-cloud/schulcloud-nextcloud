# Schulcloud-Nextcloud

This repository contains:

- a Dockerfile for development and production image
- a values.yml for the nextcloud helm chart

The custom image configures the nextcloud instance and uses environment variables for configuration.
These env variables can be used e.g. for installing, enabling and disabling plugins.

## Build Container locally

You have the choice between helm and docker-compose to setup your schulcloud-nextcloud instance locally.

### With Helm

#### Installation

Preconditions:

- git bash or something else
- installed docker-desktop with activated kubernetes

Install helm

```
$ curl -fsSL -o get_helm.sh https://raw.githubusercontent.com/helm/helm/main/scripts/get-helm-3
$ chmod 700 get_helm.sh
$ ./get_helm.sh
```

Check installation with `helm version`.

```
helm repo add nextcloud https://nextcloud.github.io/helm/
helm repo update
```

If docker-desktop isn't the current kubernetes context `kubectl config get-contexts`, you can make it your current with
`kubectl config use-context docker-desktop`.

#### Build and run the kubernetes production image

The production build uses Docker to build the image and Helm/Kubernetes to run it.
It uses the values.yml to configure the environment. To build and run the image use:

```
./run-production.sh
```

This builds the new image and removes all old instances before starting new ones.

To stop and remove all containers use:

```
helm uninstall schulcloud-nextcloud
```

`helm upgrade schulcloud-nextcloud nextcloud/nextcloud -f values.yml`

### With docker

The following command builds and runs the development image as a container. It also runs a postgres server, an
minio server and a temporary service for the nextcloud bucket creation.
The compose uses the `.env` to configure the environment. To start and build the images use:

```
docker-compose up --build
```

The compose file allows the mounting of plugins, so changes get transferred to the container automatically while it is
running.

## NextCloud configuration

The NextCloud instance will be available at [`http://localhost:8080`](http://localhost:8080).
For the admin login, use [`http://localhost:8080/login?noredir=1`](http://localhost:8080/login?noredir=1).
You may login into the instance with username `admin` and password `admin`.

The s3 bucket (minio console) will be available
at [`http://localhost:9101/buckets/nextcloud/browse`](http://localhost:9101/buckets/nextcloud/browse)
with username `admin12345` and password `admin12345`.

| Environment variable     | E.g.                                                                                                              | comment                                                                                                                                        |
|--------------------------|-------------------------------------------------------------------------------------------------------------------|------------------------------------------------------------------------------------------------------------------------------------------------|
| POSTGRES_DB              | nextcloud                                                                                                         |                                                                                                                                                |
| POSTGRES_USER            | nextcloud                                                                                                         |                                                                                                                                                |
| POSTGRES_PASSWORD        | nextcloud                                                                                                         |                                                                                                                                                |
| POSTGRES_HOST            | db                                                                                                                |                                                                                                                                                |
| INSTALL_PLUGINS          | sociallogin groupfolders ...                                                                                      | Installs all referecend plugins from nextcloud app store. If the appstore is unreachable the container startup fails.                          |
| NEXTCLOUD_ADMIN_USER     | admin                                                                                                             |                                                                                                                                                |
| NEXTCLOUD_ADMIN_PASSWORD | admin                                                                                                             |                                                                                                                                                |
| RUN_CONFIGURATION        | true                                                                                                              | Option to disable the automatic configuration of nextcloud                                                                                     |
| ENABLE_PLUGINS           | schulcloud                                                                                                        | Enables all referenced plugins. Precondition the plugin is installed.                                                                          |
| DISABLE_PLUGINS          | accessibility activity circles comments...                                                                        | Disable all referenced plugins. Precondition the plugin is installed.                                                                          |
| CONFIG_JSON              | {"system":{"app_install_overwrite":["gluusso","groupfolder...                                                     | Contains the whole nextcloud configuration. It will be only imported after installation of nextcloud and overrides values of config.php files. |
| EXTERNAL_GIT_PLUGINS     | https://github.com/PaulLereverend/NextcloudExtract.git:[NEW_FOLDER_NAME]:[VERSION_TAG OR BRANCH_NAME] https://... | Clones git repos with a specific version or branch name and renames the cloned folder. Cloned plugin also have to be in ENABLE_PLUGINS.        |
| PHP_MEMORY_LIMIT         | 512M                                                                                                              | Default value is 512M. The configuration script needs more memory for php memory to run the nextcloud occ commands.                            |

| Environment variable for object storage as primary storage | E.g.       | comment                                                                                                                                                                                                                                                                                                                                                            |
|------------------------------------------------------------|------------|--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| MINIO_ROOT_USER*                                           | admin12345 | Username of local minio. It's also the OBJECTSTORE_S3_KEY. See compose.yml.                                                                                                                                                                                                                                                                                        |
| MINIO_ROOT_PASSWORD*                                       | admin12345 | Passowrd of local minio. It's also the OBJECTSTORE_S3_SECRET. See compose.yml.                                                                                                                                                                                                                                                                                     |
| OBJECTSTORE_S3_BUCKET                                      | nextcloud  | Name of the s3 bucket which will be used as primay storage for nextcloud.                                                                                                                                                                                                                                                                                          |
| OBJECTSTORE_S3_KEY                                         | admin12345 | The s3 key which will be used from nextcloud. It's also the MINIO_ROOT_USER. See compose.yml.                                                                                                                                                                                                                                                                      |
| OBJECTSTORE_S3_SECRET                                      | admin12345 | The s3 secret which will be used from nextcloud. It's also the MINIO_ROOT_PASSWORD. See compose.yml.                                                                                                                                                                                                                                                               |
| OBJECTSTORE_S3_HOST                                        | storage    | The host of the s3 storage. Could be something like s3.eu-central-1.aws.com.                                                                                                                                                                                                                                                                                       |
| OBJECTSTORE_S3_PORT                                        | 9000       | The port of the s3 storage.                                                                                                                                                                                                                                                                                                                                        |
| OBJECTSTORE_S3_AUTOCREATE                                  | false      | Flag which should be autocreate the bucket if it doesn't exists. Doesn't work locally.                                                                                                                                                                                                                                                                             |
| OBJECTSTORE_S3_SSL*                                        | false      | Self-explanatory.                                                                                                                                                                                                                                                                                                                                                  |
| OBJECTSTORE_S3_USEPATH_STYLE*                              | true       | use_path_style is usually not required (and is, in fact, incompatible with newer Amazon datacenters), but can be used with non-Amazon servers where the DNS infrastructure cannot be controlled. Ordinarily, requests will be made with http://bucket.hostname.domain/, but with path style enabled, requests are made with http://hostname.domain/bucket instead. |
| OBJECTSTORE_S3_LEGACYAUTH*                                 | true       | Legacy authentication is only required for S3 servers that only implement version 2 authentication, by default version 4 authentication will be used.                                                                                                                                                                                                                                                                                                                                                                   |

*Only needed for local development

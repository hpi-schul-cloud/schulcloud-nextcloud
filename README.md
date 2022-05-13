# Schulcloud-Nextcloud

This repository contains:
- a Dockerfile for development and production image
- a values.yml for the nextcloud helm chart
- a custom nextcloud plugin

The custom image configures the nextcloud instance and uses environment variables for configuration.
These env variables can be used for installing, enabling and disabling plugins.

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

If docker-desktop isnt the current kubernetes context `kubectl config get-contexts`, you can make it your current with
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

The following command builds and runs the development image as a container. It also runs a postgres server.
The compose uses the `.env` to configure the environment. To start and build the images use:

```
docker-compose up --build
```

The schulcloud folder, our custom plugin, gets mounted automatically and can be edited while the container is running.


## NextCloud configuration

The NextCloud instance will be available at [`http://localhost:8080`](http://localhost:8080).
For the admin login, use [`http://localhost:8080/login?noredir=1`](http://localhost:8080/login?noredir=1).
You may login into the instance with username `admin` and password `admin`.

| Environment variable     | E.g.                                                          | comment                                                                                                                                        |
|--------------------------|---------------------------------------------------------------|------------------------------------------------------------------------------------------------------------------------------------------------|
| POSTGRES_DB              | nextcloud                                                     |                                                                                                                                                |
| POSTGRES_USER            | nextcloud                                                     |                                                                                                                                                |
| POSTGRES_PASSWORD        | nextcloud                                                     |                                                                                                                                                |
| POSTGRES_HOST            | db                                                            |                                                                                                                                                |
| INSTALL_PLUGINS          | sociallogin groupfolders ...                                  | Installs all referecend plugins from nextcloud app store. If the appstore is unreachable the container startup fails.                          |
| NEXTCLOUD_ADMIN_USER     | admin                                                         |                                                                                                                                                |
| NEXTCLOUD_ADMIN_PASSWORD | admin                                                         |                                                                                                                                                |
| ENABLE_PLUGINS           | schulcloud                                                    | Enables all referenced plugins. Precondition the plugin is installed.                                                                          |
| DISABLE_PLUGINS          | accessibility activity circles comments...                    | Disable all referenced plugins. Precondition the plugin is installed.                                                                          |
| CONFIG_JSON              | {"system":{"app_install_overwrite":["gluusso","groupfolder... | Contains the whole nextcloud configuration. It will be only imported after installation of nextcloud and overrides values of config.php files. |


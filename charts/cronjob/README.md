# cronjob

![Version: 0.1.0](https://img.shields.io/badge/Version-0.1.0-informational?style=flat-square) ![Type: application](https://img.shields.io/badge/Type-application-informational?style=flat-square) ![AppVersion: 2.1.3](https://img.shields.io/badge/AppVersion-2.1.3-informational?style=flat-square)

A generic Helm chart for Kubernetes cronjobs

## How to install this chart

```console
helm install chart_name ./cronjob
```

To install the chart with the release name `my-release`:

```console
helm install chart_name ./cronjob
```

To install with some set values:

```console
helm install chart_name ./cronjob --set values_key1=value1 --set values_key2=value2
```

To install with custom values file:

```console
helm install chart_name ./cronjob -f values.yaml
```

## Values

| Key | Type | Default | Description |
|-----|------|---------|-------------|
| affinity | object | `{}` |  |
| clusterRole.annotations | object | `{}` |  |
| clusterRole.create | bool | `false` | Create a clusterRole and bind it to the serviceaccount with a clusterRoleBinding (see https://kubernetes.io/docs/reference/access-authn-authz/rbac/) |
| clusterRole.rules | list | `[]` |  |
| env | list | `[]` | Environment variables to be passed to all job pods (see https://kubernetes.io/docs/tasks/inject-data-application/define-environment-variable-container/ and https://kubernetes.io/docs/concepts/configuration/secret/#using-secrets-as-environment-variables) |
| envFrom | list | `[]` |  |
| extraVolumeMounts | list | `[]` | VolumeMounts to be passed to all job pods (see https://kubernetes.io/docs/concepts/storage/volumes/) |
| extraVolumes | list | `[]` | Volumes to be passed to all job pods (see https://kubernetes.io/docs/concepts/storage/volumes/) |
| fullnameOverride | string | `""` |  |
| image.pullPolicy | string | `"IfNotPresent"` |  |
| image.repository | string | `"schulcloud/infra-tools"` |  |
| image.tag | string | `"3.0.0"` | Overrides the image tag whose default is the chart appVersion. |
| imagePullSecrets | list | `[]` |  |
| jobs[0].activeDeadlineSeconds | int | `300` |  |
| jobs[0].args[0] | string | `"echo \"foo\""` |  |
| jobs[0].backoffLimit | int | `3` |  |
| jobs[0].command[0] | string | `"/bin/sh"` |  |
| jobs[0].command[1] | string | `"-c"` |  |
| jobs[0].completions | int | `5` |  |
| jobs[0].concurrencyPolicy | string | `"Forbid"` |  |
| jobs[0].env | list | `[]` | Additional job specific environment variables |
| jobs[0].envFrom | list | `[]` | Additional job specific environment variables from configMaps or secrets |
| jobs[0].extraVolumeMounts | list | `[]` | Additional job specific volumeMounts |
| jobs[0].extraVolumes | list | `[]` | Additional job specific volumes |
| jobs[0].failedJobsHistoryLimit | int | `1` |  |
| jobs[0].image.pullPolicy | string | `"IfNotPresent"` |  |
| jobs[0].image.repository | string | `"nginx"` |  |
| jobs[0].image.tag | string | `"latest"` |  |
| jobs[0].name | string | `"example"` | Not optional |
| jobs[0].parallelism | int | `1` |  |
| jobs[0].podAnnotations | object | `{}` |  |
| jobs[0].resources | object | `{}` |  |
| jobs[0].restartPolicy | string | `"Never"` |  |
| jobs[0].schedule | string | `"*/5 * * * *"` | Not optional |
| jobs[0].startingDeadlineSeconds | int | `30` |  |
| jobs[0].successfulJobsHistoryLimit | int | `1` |  |
| jobs[0].suspend | bool | `false` |  |
| jobs[0].ttlSecondsAfterFinished | int | `0` |  |
| nameOverride | string | `""` |  |
| nodeSelector | object | `{}` |  |
| podAnnotations | object | `{}` |  |
| podSecurityContext | object | `{}` |  |
| resources | object | `{}` |  |
| securityContext | object | `{}` |  |
| serviceAccount.annotations | object | `{}` |  |
| serviceAccount.create | bool | `true` | Specifies whether a service account should be created |
| serviceAccount.name | string | `""` | The name of the service account to use. If not set and create is true, a name is generated using the fullname template |
| tolerations | list | `[]` |  |


#!/bin/bash

docker build --target production -t schulcloud/schulcloud-nextcloud/production .

helm uninstall schulcloud-nextcloud || true

helm install schulcloud-nextcloud nextcloud/nextcloud -f values.yml

kubectl port-forward $(kubectl get pods --namespace default -l "app.kubernetes.io/name=nextcloud" -o jsonpath="{.items[0].metadata.name}") 8080:80

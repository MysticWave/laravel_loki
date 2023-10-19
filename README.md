# Grafana Loki

### Adding to project

Basic application deployment:
```yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  (...)
spec:
  (...)
    spec:
      (...)
      containers:
        
        (...)
        ### add this volume to make Promtail work
        ### adjust the mountPath for logs directory here, and in the Promtail container
          volumeMounts:
            - mountPath: /var/www/storage/logs
              name: shared-volume

        - name: <project>-promtail
          image: "grafana/promtail:latest"
          imagePullPolicy: Always
          volumeMounts:
            - mountPath: /var/www/storage/logs
              name: shared-volume
            - mountPath: /etc/promtail
              name: config-volume

      volumes:
        - name: shared-volume
          emptyDir: {}
        - name: config-volume
          configMap:
            name: promtail-config
            items:
              - key: promtail-config.yaml
                path: config.yml

```

promtail.configmap.yml:
```yaml
# adjust project-name and namespace
# change scrape_configs.static_configs.labels.__path__ if needed
# if your logs are formatted in a different way than default Laravel logs, adjust the scrape_configs.pipeline_stages.multine.firstline
apiVersion: v1
kind: ConfigMap
metadata:
  name: promtail-config
  namespace: <project-namespace>
data:
  promtail-config.yaml: |
    server:
      http_listen_port: 9080
      grpc_listen_port: 0

    positions:
      filename: /tmp/positions.yaml

    clients:
      - url: http://loki.grafana-loki.svc.cluster.local:3100/loki/api/v1/push

    scrape_configs:
      - job_name: laravel
        static_configs:
          - targets:
              - localhost
            labels:
              job: <project-name>
              __path__: /var/www/storage/logs/*log
        pipeline_stages:
          - multiline:
              firstline: '^\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\] .*'
              max_wait_time: 1s

```

### Connecting to Grafana UI

run:
```bash
kubectl port-forward service/loki-grafana 3000:80
```
and visit: `localhost:3000`

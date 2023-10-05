# Install Loki stack for laravel application

1. Copy `promtail-config.yaml` to `.docker/web/promtail-config.yaml`

2. Add this to your `Dockerfile`:

```Dockerfile
# Install Promtail -----------------------------------------------------------
RUN curl -O -L "https://github.com/grafana/loki/releases/download/v2.4.1/promtail-linux-amd64.zip"
RUN unzip "promtail-linux-amd64.zip" -d /usr/local/bin && mv /usr/local/bin/promtail-linux-amd64 /usr/local/bin/promtail && rm "promtail-linux-amd64.zip"
RUN chmod a+x /usr/local/bin/promtail
ADD ./.docker/web/promtail-config.yaml /etc/promtail/promtail-config.yaml
# ----------------------------------------------------------------------------
```

3. Copy `LokiLogFormatter.php` to `app/Support/LokiLogFormatter.php` (or wherever you want, just don't forget to change the file location in the next step)

4. Open `config/logging.php` and change your logging stack options:

```php
'channels' => [
    // ...
        'stack' => [
            'driver' => 'stack',
            'channels' => ['daily'],
            'ignore_exceptions' => false,
            'tap' => [App\Support\LokiLogFormatter::class]  // <-- You need only this single line
        ],
    // ...
];
```

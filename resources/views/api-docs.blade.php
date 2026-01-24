<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentación API - Departamentos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swagger-ui-dist@3/swagger-ui.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        .topbar {
            background: #667eea;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .topbar h1 {
            margin: 0;
            font-size: 1.8em;
        }

        .topbar p {
            margin: 5px 0 0 0;
            opacity: 0.9;
        }

        #swagger-ui {
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="topbar">
        <h1>📚 Documentación API - Portal Departamentos</h1>
        <p>Base URL: http://192.168.100.61:8000/api</p>
    </div>

    <div id="swagger-ui"></div>

    <script src="https://cdn.jsdelivr.net/npm/swagger-ui-dist@3/swagger-ui-bundle.js"></script>
    <script>
        SwaggerUIBundle({
            url: '/docs/openapi.yaml',
            dom_id: '#swagger-ui',
            deepLinking: true,
            presets: [
                SwaggerUIBundle.presets.apis,
                SwaggerUIBundle.SwaggerUIStandalonePreset
            ],
            plugins: [
                SwaggerUIBundle.plugins.DownloadUrl
            ],
            layout: "BaseLayout"
        });
    </script>
</body>
</html>

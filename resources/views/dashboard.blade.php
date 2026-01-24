<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Departamentos - 192.168.100.61:8000</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 60px 40px;
            max-width: 900px;
            width: 100%;
        }

        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 10px;
            font-size: 2.5em;
        }

        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 40px;
            font-size: 1.1em;
        }

        .status-box {
            background: #f0f9ff;
            border-left: 4px solid #667eea;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 40px;
            color: #333;
        }

        .status-box .port {
            font-weight: bold;
            color: #667eea;
            font-size: 1.1em;
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .service-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            text-decoration: none;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4);
        }

        .service-card h3 {
            margin-bottom: 15px;
            font-size: 1.3em;
        }

        .service-card p {
            font-size: 0.95em;
            opacity: 0.9;
            margin-bottom: 15px;
        }

        .service-card .icon {
            font-size: 2.5em;
            margin-bottom: 10px;
        }

        .service-card.api {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .service-card.api:hover {
            box-shadow: 0 15px 40px rgba(245, 87, 108, 0.4);
        }

        .service-card.mobile {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .service-card.mobile:hover {
            box-shadow: 0 15px 40px rgba(79, 172, 254, 0.4);
        }

        .service-card.docs {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }

        .service-card.docs:hover {
            box-shadow: 0 15px 40px rgba(67, 233, 123, 0.4);
        }

        .service-card button {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 2px solid white;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            font-size: 1em;
            transition: all 0.3s ease;
        }

        .service-card button:hover {
            background: white;
            color: #667eea;
        }

        .service-card.api button:hover {
            color: #f5576c;
        }

        .service-card.mobile button:hover {
            color: #00f2fe;
        }

        .service-card.docs button:hover {
            color: #38f9d7;
        }

        .info-section {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 15px;
            border: 1px solid #e9ecef;
        }

        .info-section h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 1.2em;
        }

        .info-section p {
            color: #666;
            margin-bottom: 10px;
            line-height: 1.6;
        }

        .code-block {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 8px;
            overflow-x: auto;
            font-family: 'Courier New', monospace;
            margin-top: 10px;
            font-size: 0.9em;
        }

        .footer {
            text-align: center;
            margin-top: 40px;
            color: #999;
            font-size: 0.9em;
        }

        @media (max-width: 768px) {
            h1 {
                font-size: 1.8em;
            }

            .container {
                padding: 30px 20px;
            }

            .services-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🏠 Portal Departamentos</h1>
        <p class="subtitle">Gestión centralizada en puerto 8000</p>

        <div class="status-box">
            ✅ <strong>Servidor activo:</strong> <span class="port">http://192.168.100.61:8000</span>
        </div>

        <div class="services-grid">
            <a href="/api" class="service-card api">
                <div class="icon">🔌</div>
                <h3>API REST</h3>
                <p>Endpoints para gestión de departamentos, usuarios y reservas</p>
                <button>Ver API</button>
            </a>

            <a href="/api-docs" class="service-card docs">
                <div class="icon">📚</div>
                <h3>Documentación</h3>
                <p>Especificación OpenAPI de todos los endpoints</p>
                <button>Documentación</button>
            </a>

            <div class="service-card mobile">
                <div class="icon">📱</div>
                <h3>App Móvil</h3>
                <p>React Native + Expo disponible en desarrollo</p>
                <button onclick="alert('Accede mediante tu dispositivo móvil\nURL: http://192.168.100.61:8000/api')">Conectar</button>
            </div>
        </div>

        <div class="info-section">
            <h3>📋 Información de Configuración</h3>
            <p><strong>API Base URL:</strong> Todos los clientes (web, móvil) deben usar:</p>
            <div class="code-block">http://192.168.100.61:8000/api</div>

            <p style="margin-top: 20px;"><strong>Rutas principales:</strong></p>
            <ul style="margin-left: 20px; color: #666;">
                <li><code style="background: #f0f0f0; padding: 2px 6px; border-radius: 3px;">GET /api/departments</code> - Listar departamentos</li>
                <li><code style="background: #f0f0f0; padding: 2px 6px; border-radius: 3px;">POST /api/departments</code> - Crear departamento (requiere autenticación)</li>
                <li><code style="background: #f0f0f0; padding: 2px 6px; border-radius: 3px;">PUT /api/departments/{id}</code> - Actualizar departamento</li>
                <li><code style="background: #f0f0f0; padding: 2px 6px; border-radius: 3px;">POST /api/auth/login</code> - Iniciar sesión</li>
                <li><code style="background: #f0f0f0; padding: 2px 6px; border-radius: 3px;">PUT /api/auth/profile</code> - Actualizar perfil</li>
            </ul>

            <p style="margin-top: 20px;"><strong>Token de autenticación:</strong></p>
            <p>Se envía en el header: <code style="background: #f0f0f0; padding: 2px 6px; border-radius: 3px;">Authorization: Bearer {token}</code></p>
        </div>

        <div class="footer">
            <p>🔒 Todos los servicios en un único puerto para facilitar el desarrollo</p>
            <p>Versión 1.0 - 2026</p>
        </div>
    </div>
</body>
</html>

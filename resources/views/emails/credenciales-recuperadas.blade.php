<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 14px; color: #333; }
        .credenciales { background: #f2f2f2; padding: 12px 16px; border-radius: 6px; margin: 16px 0; }
    </style>
</head>
<body>
    <p>Hola {{ $usuario->nombre }},</p>

    <p>Se generaron nuevas credenciales de acceso para tu cuenta:</p>

    <div class="credenciales">
        <p><strong>Usuario:</strong> {{ $usuario->usuario }}</p>
        <p><strong>Contraseña:</strong> {{ $password }}</p>
    </div>
</body>
</html>

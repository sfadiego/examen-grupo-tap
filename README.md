# Examen Grupo TAP


### Levantar servicios Docker

levantar mongodb y Mailpit con docker
```
docker compose up -d
docker compose down
```

## Configuración de Laravel

### Librería para Laravel + MongoDB

```bash
composer require mongodb/laravel-mongodb
pecl install mongodb
```

Verificar que la extensión se instaló correctamente:

```bash
php -m | grep mongodb
```

### Conexión (`.env`)

```env
DB_CONNECTION=mongodb
MONGODB_URI=mongodb://localhost:27017
MONGODB_DATABASE={nombre_db}
```

### Generar `APP_KEY`

```bash
php artisan key:generate
```

### Correr `seeds`

solo se corren seeds la primera vez
```bash
php artisan db:seed
```

### Storage (subida de archivos)

```bash
php artisan storage:link
```

### Dependencias de exportación (Excel y PDF)

```bash
composer require barryvdh/laravel-dompdf
composer require maatwebsite/excel
```

Documentación de `maatwebsite/excel`: [https://docs.laravel-excel.com/4.x/exports/](https://docs.laravel-excel.com/4.x/exports/)


### Documentacion Postman

Documentación generada en postman : [https://documenter.getpostman.com/view/1266324/2sBYArVsiP#752f900d-d85f-4001-9cec-c15e08f11d8c](https://documenter.getpostman.com/view/1266324/2sBYArVsiP#752f900d-d85f-4001-9cec-c15e08f11d8c)


### Configurar Mailpit

.env
```
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="recover@tapterminal.com"
MAIL_FROM_NAME="${APP_NAME}"
```


interfaz para ver los correos : [http://localhost:8025](http://localhost:8025) 

## Pruebas

borrar contenido en schemas mongo desde docker:
```
db = db.getSiblingDB('examen_grupo_tap');
db.producto.deleteMany({});
db.usuario.deleteMany({});
db.perfil.deleteMany({});
db.seccion.deleteMany({});
db.bitacora.deleteMany({});
```



## Preguntas / Aclaraciones del examen

- Se entendió que un **Perfil** es similar a un **rol**, por eso se declararon de esa manera, y una **Seccion** es el permiso/pantalla

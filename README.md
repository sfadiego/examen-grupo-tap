# Examen Grupo TAP

## MongoDB con Docker

### Descargar imagen

```bash
docker pull mongo:7.0
```

### Levantar contenedor

```bash
docker run -d \
  --name mongo-tap \
  -p 27017:27017 \
  -v mongo-tap-data:/data/db \
  mongo:7.0
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



## Preguntas / Aclaraciones del examen

- Se entendió que un **Perfil** es similar a un **rol**, por eso se declararon de esa manera, y una **Seccion** es el permiso/pantalla

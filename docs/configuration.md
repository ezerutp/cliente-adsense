# Configuración

## Variables de entorno

Las variables parten de `.env.example`.

### Aplicación

```dotenv
APP_NAME=Laravel
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost
```

En producción:

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL` debe ser la URL HTTPS real.

### Base de datos

El proyecto usa SQLite por defecto:

```dotenv
DB_CONNECTION=sqlite
```

### Sesión, cache y colas

```dotenv
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

Las tablas necesarias se crean mediante las migraciones base de Laravel.

### Correo

Por defecto el correo se escribe en logs:

```dotenv
MAIL_MAILER=log
```

Para recuperación de contraseña y verificación real debe configurarse un proveedor SMTP.

## Configuración administrable

Ruta: `/dashboard/settings`

La pantalla se divide en:

### Portada

- Texto principal del logo.
- Texto destacado del logo.
- Título del sitio.
- Subtítulo.
- URL de imagen de portada.

El nombre de marca se guarda en dos partes para respetar el diseño bicolor del
header. El texto principal usa `--site-text` y el destacado usa
`--site-primary`; el footer concatena ambas partes y genera sus iniciales
automáticamente.

### Colores

Gestiona variables CSS públicas y administrativas:

```text
--site-primary
--site-primary-hover
--site-text
--site-muted
--site-bg
--admin-ink
--admin-ink-hover
--admin-muted
--admin-danger
--admin-focus
```

`SiteSetting::inlineCssVariableBlock()` genera el bloque `:root`.

### Servidor

- País.
- Código ISO.
- Offset UTC.

`SiteSetting::SERVER_COUNTRIES` también contiene el prefijo telefónico usado como fallback al construir contactos.

### Confirmación de edad

- Activación.
- Clave de `localStorage`.
- Badge, título y descripción.
- Botones de confirmar y salir.
- URL de salida.
- Texto legal.

El componente `age-confirmation-modal` recuerda la confirmación en el navegador.

### Footer

El footer público se configura como columnas dinámicas. Cada columna contiene:

- Título.
- Uno o más elementos con texto y enlace.

La estructura se almacena en `site_settings.footer_columns` como JSON:

```json
[
  {
    "title": "Información",
    "items": [
      {"label": "Categorías", "href": "/#categorias"},
      {"label": "Publicar anuncio", "href": "/publicar-anuncio"}
    ]
  }
]
```

El editor permite hasta 8 columnas y 12 enlaces por columna. Acepta rutas internas,
anclas, URLs HTTP/HTTPS y enlaces `mailto:`, `tel:` o `sms:`. La columna titulada
`Legal` también alimenta los enlaces legales de la franja inferior.

`FooterSeeder` carga la estructura inicial al ejecutar `php artisan migrate --seed`
o `php artisan db:seed`. Solo rellena el footer cuando no existe o está vacío, de
modo que una configuración personalizada no se pierde.

### Ubicaciones

- Alta, edición y eliminación.
- Paginación de 15 elementos.
- Rechazo de duplicados ignorando espacios y mayúsculas.
- No se permite eliminar una ubicación usada por posts.
- Renombrar una ubicación actualiza los posts asociados.

## Integraciones de contacto

Proveedores:

- WhatsApp.
- Telegram.
- SMS.
- Personalizado.

Cada integración configura:

- Nombre.
- Proveedor.
- URL base.
- Color.
- Icono.
- Credenciales JSON opcionales.
- Estado activo.

Los providers no personalizados son únicos; se permiten varias integraciones `custom`.

## Seguridad

No guardar secretos sensibles en `credentials` sin cifrado adicional. El campo tiene cast JSON, no cifrado automático.

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

- Título del sitio.
- Subtítulo.
- URL de imagen de portada.

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

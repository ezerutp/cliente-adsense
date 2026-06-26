# Módulo de videos

## Administración

Rutas bajo `/dashboard/videos`.

Operaciones:

- Crear.
- Editar.
- Eliminar.
- Activar/ocultar.

Campos:

- Título.
- Slug opcional.
- Descripción.
- Iframe del video.
- Orden.
- Estado.
- Fecha de publicación opcional.

## Iframes

El formulario acepta:

- Iframe completo.
- URL directa del `src`.

El backend extrae y persiste solo `iframe_src`, validando que sea una URL
HTTP/HTTPS. La vista renderiza un iframe controlado por la aplicación.

## Galería pública

Ruta:

```text
/videos
```

Muestra solo videos activos con `published_at` vacío o pasado.

## Endpoint JSON POC

Ruta:

```text
POST /api/video-posts
```

Payload mínimo:

```json
{
  "admin_email": "admin@test.com",
  "admin_password": "password",
  "title": "Video demo",
  "description": "Descripción opcional",
  "iframe": "<iframe src=\"https://www.youtube.com/embed/demo\"></iframe>"
}
```

Campos opcionales:

- `slug`.
- `sort_order`.
- `is_active`.
- `published_at`.

La autenticación del POC recibe la contraseña en texto plano y la valida contra
el hash del usuario admin. Debe reemplazarse por un mecanismo más seguro antes
de producción.

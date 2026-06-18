# Base de datos, migraciones y seeders

## Entidades principales

### `categories`

| Campo | Descripción |
| --- | --- |
| `name` | Nombre visible |
| `slug` | Identificador público único |
| `description` | Texto opcional |
| `image_url` | Imagen opcional |
| `sort_order` | Orden administrativo/público |
| `is_active` | Visibilidad |

Eliminar una categoría elimina sus posts en cascada.

### `posts`

Campos relevantes:

```text
category_id
title
slug
subtitle
location
body
cover_image_url
gallery_image_urls
contact fields and generated URLs
tags
is_active
is_vip
published_at
ends_at
```

`location` es obligatorio desde la migración del 18 de junio de 2026.

### `locations`

```text
name unique
department
sort_order
timestamps
```

El modelo no tiene relación FK directa con posts; la asociación es por nombre.

### `post_cards`

```text
post_id nullable
title
color
fill_background
fields JSON
sort_order
is_active
```

`post_id = NULL` representa una plantilla.

### `integrations`

```text
name
provider
base_url
button_color
icon
credentials JSON
is_active
```

### `site_settings`

Registro singleton con textos, colores, parámetros regionales y `footer_columns`
como JSON para las columnas y enlaces del footer público.

### `age_gate_settings`

Registro singleton con contenido y comportamiento del modal de edad.

### Autenticación y permisos

Laravel crea usuarios, sesiones y tokens. Spatie crea roles, permisos y tablas pivote.

## Cronología de migraciones

### Base Laravel

- Usuarios, sesiones y reset de contraseña.
- Cache y locks.
- Jobs, batches y failed jobs.

### 9–10 de junio de 2026

- Tablas de permisos.
- Tabla de categorías.

### 15 de junio de 2026

- Activación de categorías.
- Posts.
- Integraciones.
- Campos de contacto.
- Estado, publicación, vencimiento y VIP de posts.
- Site settings.
- Slugs de posts con backfill.
- Confirmación de edad.
- Presentación de integraciones.
- Eliminación de unicidad global de provider.
- Cards y color.

### 16 de junio de 2026

- Fondo completo de cards.
- Backfill de presentación desde plantillas.
- Campo inicialmente nullable `posts.location`.

### 18 de junio de 2026

`create_locations_table_and_require_post_location`:

1. Crea `locations`.
2. Inserta Lima como fallback.
3. Completa posts sin ubicación.
4. Importa ubicaciones históricas de posts.
5. Convierte `posts.location` a `NOT NULL`.

## Seeders

### `LocationSeeder`

- Carga aproximadamente 110 distritos/ciudades comunes.
- Cubre 25 departamentos.
- Usa `updateOrCreate`, por lo que puede ejecutarse varias veces.
- El nombre es la clave lógica.

### `FooterSeeder`

- Inicializa las columnas Información, Legal, Ayuda y Contacto.
- Crea el registro singleton de configuración cuando no existe.
- Rellena `footer_columns` cuando está vacío.
- No sobrescribe un footer personalizado.

### `DatabaseSeeder`

1. Ejecuta `LocationSeeder`.
2. Ejecuta `FooterSeeder`.
3. Limpia cache de permisos.
4. Crea 25 permisos.
5. Crea roles `admin`, `editor` y `viewer`.
6. Sincroniza permisos por rol.
7. Crea el administrador de desarrollo.

## Permisos sembrados

Grupos:

- Posts.
- Categorías.
- Cards.
- Integraciones.
- Configuración.
- Usuarios.
- Roles.

Nota: las rutas administrativas actuales exigen `role:admin`; la matriz de permisos todavía no se aplica individualmente a cada endpoint.

## Comandos

```bash
php artisan migrate
php artisan migrate:status
php artisan db:seed
php artisan db:seed --class=Database\\Seeders\\LocationSeeder
php artisan db:seed --class=Database\\Seeders\\FooterSeeder
php artisan migrate:fresh --seed
```

## Recomendaciones

- Respaldar antes de migrar producción.
- No editar migraciones ya ejecutadas; crear nuevas.
- Si se migra a FK `location_id`, preparar backfill por nombre y resolver duplicados.
- Revisar collation para garantizar unicidad case-insensitive de ubicaciones en motores distintos.

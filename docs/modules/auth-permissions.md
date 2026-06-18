# Autenticación, roles y permisos

## Autenticación

Laravel Breeze proporciona:

- Registro.
- Login/logout.
- Recuperación de contraseña.
- Confirmación de contraseña.
- Verificación de correo.
- Perfil.

## Roles

`DatabaseSeeder` crea:

### Admin

Recibe todos los permisos.

### Editor

Recibe permisos de contenido para posts, categorías, cards e integraciones.

### Viewer

Recibe permisos de lectura.

## Autorización efectiva actual

Las rutas administrativas se agrupan con:

```php
middleware(['auth', 'verified', 'role:admin'])
```

Esto significa que, aunque editor y viewer tengan permisos, actualmente no acceden al CRUD administrativo por ruta.

## Permisos

Se crean permisos para:

- Posts.
- Categorías.
- Cards.
- Integraciones.
- Configuración.
- Usuarios.
- Roles.

## Próximo nivel recomendado

Sustituir o complementar `role:admin` con middleware por permiso:

```php
permission:posts.edit
```

También pueden usarse policies para autorización por modelo.

## Usuario de desarrollo

```text
admin@test.com
Vidarte;123
```

Nunca usar estas credenciales en producción.

# EmetianMetrics

## Estado inicial y próximos pasos

Se ha implementado la primera fase del backend PHP con soporte para:

- Configuración de MySQL en XAMPP (`backend/config.php`)
- Conexión PDO segura (`backend/db.php`)
- Helpers comunes y gestión de sesión (`backend/helpers.php`)
- Migraciones de base de datos (`backend/migrations/001_create_schema.sql`)
- Script de migración ejecutable (`backend/migrations/migrate.php`)
- Auth básico de registro, login, logout y recuperación de contraseña
- Rutas API iniciales para `users` y `items`
- Vistas principales protegidas con PHP: `views/dashboard.php` y `views/profile.php`

## Cómo ejecutar la migración

1. Asegúrate de que XAMPP esté arrancado con MySQL activado.
2. Abre una terminal en la carpeta del proyecto.
3. Ejecuta:

```powershell
php backend\migrations\migrate.php
```

## Cómo usar el login y registro

- Página de inicio: `index.html`
- Registro: `views/register.html`
- Recuperar contraseña: `views/forgot-password.html`

Después del login la aplicación redirige a `views/dashboard.php`.

## Nuevas páginas añadidas

- `views/items.php` — gestión de ítems con creación, edición, eliminación y subida de imagen.
- `views/users.php` — listado de usuarios con DataTables.
- `views/profile.php` — edición de perfil con avatar y campos guardados en MySQL.

## Endpoints añadidos

- `backend/api/profile.php` — devuelve los datos del usuario actual.
- `backend/api/profile_update.php` — actualiza el perfil y sube avatar.
- `backend/api/item_save.php` — crea o actualiza un ítem.
- `backend/api/item_delete.php` — elimina un ítem.

## Siguientes pasos recomendados

1. Añadir protección completa en las rutas `views/dashboard.php` y `views/profile.php` (ya están protegidas con sesión PHP).
2. Implementar CRUD para `items` y otra tabla adicional con formularios y DataTables.
3. Añadir subida de imágenes con `backend/uploads/`.
4. Mejorar el flujo de recuperación de contraseña para enviar correos.
5. Conectar los datos del perfil y los ítems a la base de datos desde el frontend.

# EmetianMetrics

## Resumen del proyecto

EmetianMetrics es una aplicación web PHP/MySQL diseñada para gestionar métricas y favoritos de criptomonedas en un entorno XAMPP local. Incluye:

- Autenticación de usuarios con registro, login, logout y recuperación de contraseña.
- Dashboard de criptomonedas con datos del backend.
- CRUD de favoritos cripto con subida de imagen.
- Perfil de usuario editable y protegido por sesión.
- Envío de correo con PHPMailer para notificaciones (configurable en `backend/config.php`).

## Estructura del proyecto

```
EmetianMetrics/
├─ assets/
│  ├─ js/                # JavaScript de la aplicación
│  ├─ styles/            # CSS
│  └─ public/            # Imágenes y recursos estáticos
├─ backend/
│  ├─ api/               # Endpoints JSON y operaciones CRUD
│  ├─ auth/              # Login, register, logout y recuperación
│  ├─ consumo_api/       # API de datos cripto y caché
│  ├─ migrations/        # Scripts de migración SQL
│  ├─ uploads/           # Imágenes subidas por usuarios
│  ├─ logs/              # Archivos de log (generados)
│  ├─ config.php         # Configuración de DB y email
│  ├─ db.php             # Conexión PDO con MySQL
│  └─ helpers.php        # Utilidades comunes y sesión
├─ vendor/               # Dependencias Composer
├─ views/                # Vistas de la aplicación
│  ├─ landing.html       # Página de bienvenida
│  ├─ login.html
│  ├─ register.html
│  ├─ forgot-password.html
│  ├─ reset-password.html
│  ├─ dashboard.php      # Dashboard protegido
│  ├─ items.php          # CRUD de favoritos cripto
│  ├─ users.php          # Listado de usuarios
│  ├─ favourites.php     # Favoritos por usuario
│  └─ profile.php        # Perfil protegido
├─ composer.json
├─ composer.lock
├─ composer.phar        # Composer local opcional
└─ .gitignore
```

> Nota: `views/dashboard.html` y `views/profile.html` son versiones estáticas/prototipo; las páginas reales de la aplicación son las versiones PHP protegidas.

## Preparación para presentar

### 1. Requisitos

- XAMPP con Apache y MySQL activos.
- PHP 7.4+.
- Composer instalado globalmente o `composer.phar` local.

### 2. Instalación

Desde la raíz del proyecto:

```powershell
composer install
```

Si no tienes Composer global:

```powershell
php composer.phar install
```

### 3. Configuración

Edita `backend/config.php` para ajustar:

- Credenciales de base de datos MySQL.
- SMTP de correo (PHPMailer) si quieres enviar notificaciones.

### 4. Migrar la base de datos

Ejecuta:

```powershell
php backend\migrations\migrate.php
```

Esto crea las tablas necesarias y aplica las migraciones.

### 5. Uso

- Abre `http://localhost/EmetianMetrics/`.
- El sitio redirige a `views/landing.html`.
- Usa el enlace de login para iniciar sesión.

### 6. Páginas clave

- `views/login.html`
- `views/register.html`
- `views/forgot-password.html`
- `views/reset-password.html`
- `views/dashboard.php`
- `views/items.php`
- `views/users.php`
- `views/favourites.php`
- `views/profile.php`

## Limpieza realizada

Se eliminaron los siguientes archivos generados no necesarios para la presentación:

- `composer-setup.php`
- `assets/subirCarpeta.txt`
- `backend/logs/item_save.log`

También se agregó `.gitignore` para excluir archivos temporales y de caché.

## Recomendaciones para la presentación

- Mantén `backend/logs/` limpio antes de presentar.
- Usa los archivos PHP protegidos para el demo en vivo.
- Si no necesitas los prototipos HTML estáticos, puedes conservarlos como referencia o retirarlos.
- Verifica que `backend/uploads/` tenga permisos de escritura en XAMPP.

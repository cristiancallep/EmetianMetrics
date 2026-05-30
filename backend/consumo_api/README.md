# EmetianMetrics

## Descripción

EmetianMetrics es una aplicación web desarrollada en PHP que proporciona métricas y datos relacionados con criptomonedas. Incluye funcionalidades para obtener datos de monedas principales y gráficos de precios.

## Características

- API para datos de monedas principales
- Generación de gráficos de precios
- Caché de datos para optimización
- Interfaz web simple

## Instalación

1. Clona el repositorio:
   ```
   git clone <url-del-repositorio>
   ```

2. Asegúrate de tener PHP instalado (versión 7.4 o superior).

3. Instala dependencias si es necesario (verifica vendor/ para Composer).

4. Configura tu servidor web (ej. Apache con XAMPP) para apuntar al directorio del proyecto.

## Uso

- Accede a `index.php` en tu navegador para la interfaz principal.
- Usa los endpoints de la API en `api/` para datos programáticos.

## Estructura del Proyecto

- `index.php`: Página principal
- `api/`: Endpoints de la API
- `assets/`: Recursos estáticos
- `cache/`: Archivos de caché
- `vendor/`: Dependencias de terceros

## Contribución

1. Crea una rama para tu feature: `git checkout -b feature/nueva-funcionalidad`
2. Haz commits descriptivos
3. Push a la rama y crea un Pull Request

## Licencia

[Especifica la licencia aquí]
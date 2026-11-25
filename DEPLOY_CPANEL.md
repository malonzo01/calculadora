# 🚀 Guía de Despliegue en cPanel · Calculadora Capybaras Digitales

Esta guía detalla los pasos para publicar la aplicación en tu servidor cPanel bajo el dominio **`http://calculadora.capybarasdigitales.com`**.

---

## 📋 1. Datos de Producción Configurados

| Parámetro | Valor de Producción |
| :--- | :--- |
| **URL del Sistema** | `http://calculadora.capybarasdigitales.com` |
| **Base de Datos** | `capybara_calculadora` |
| **Usuario BD** | `capybara_master` |
| **Contraseña BD** | `"#Sophia*01"` *(Nota: entre comillas dobles en el .env por el carácter `#`)* |
| **Zona Horaria** | `America/Caracas` |
| **PHP Requerido** | PHP 8.2 o PHP 8.3 (Recomendado en cPanel: PHP 8.3) |

---

## 📦 2. Archivos Clave Preparados para cPanel

1. **`.env.production`**: Archivo con todas las credenciales de producción listas. Al subir el proyecto a cPanel, solo debes renombrarlo a `.env`.
2. **`capybara_calculadora.sql`**: Dump de la base de datos listo para importar en phpMyAdmin si no tienes acceso SSH.
3. **`public/build/`**: Todos los assets de Tailwind CSS, fuentes y JavaScript ya están compilados y listos para producción.
4. **`.htaccess` (en raíz)** y **`public/.htaccess`**:
   - Redirección automática de peticiones a `/public/`.
   - Protección contra descarga de `.env`, `.git` y archivos sensibles.
   - Compresión Gzip y caché de cabeceras para máxima velocidad.

---

## 🛠️ 3. Pasos de Instalación en cPanel

### Paso A: Crear el Subdominio en cPanel
1. Ve a **cPanel > Dominios / Subdominios**.
2. Crea el subdominio: `calculadora.capybarasdigitales.com`.
3. En **Document Root** (Directorio raíz):
   - **Opción recomendada:** Apuntar directamente a la carpeta `/public` del proyecto (por ejemplo: `calculadora.capybarasdigitales.com/public` o `public_html/calculadora/public`).
   - Si cPanel no te permite agregar `/public`, puedes apuntar a la raíz del proyecto; el archivo `.htaccess` raíz ya se encargará de reenviar el tráfico a `/public/` protegiendo tus credenciales.

---

### Paso B: Base de Datos MySQL en cPanel
1. Ve a **cPanel > Bases de Datos MySQL**.
2. Crea la base de datos: `capybara_calculadora` *(o con el prefijo de tu cPanel, ej: `usuario_capybara_calculadora`)*.
3. Crea el usuario: `capybara_master` con la clave `#Sophia*01`.
4. Asocia el usuario a la base de datos y marca **TODOS LOS PRIVILEGIOS**.
5. Ve a **phpMyAdmin > Selecciona la base de datos > Pestaña Importar** y sube el archivo `capybara_calculadora.sql` que dejamos en el proyecto (o corre `php artisan migrate --force`).

---

### Paso C: Subir los Archivos del Proyecto
1. Comprime en un archivo `.zip` todo el contenido de la carpeta `C:\laragon\www\calculadora` (excluyendo la carpeta `node_modules` y `.git` para que pese poco).
2. Sube el `.zip` a través del **Administrador de Archivos de cPanel** al directorio de tu subdominio y descomprímelo.
3. En el Administrador de Archivos de cPanel (con la opción *Mostrar archivos ocultos* activada):
   - Renombra `.env.production` a `.env` (si aún no existe).

---

### Paso D: Permisos de Carpetas (Indispensable en cPanel)
Asegúrate de que las siguientes carpetas tengan permisos de escritura **`775`** o **`755`**:
- `storage/` (y todas sus subcarpetas `storage/framework/`, `storage/logs/`)
- `bootstrap/cache/`

---

### Paso E: Optimización Final de Laravel (Vía Terminal de cPanel o SSH)
Si tienes acceso a la Terminal de cPanel, ejecuta:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```
*(Si no tienes terminal, no te preocupes: la aplicación funcionará de igual manera de forma automática).*

---

## ⏱️ 4. Cron Job para Automatizar Tasa del BCV (Opcional)
Para que el programador de tareas de Laravel se ejecute periódicamente y mantenga la tasa del BCV siempre al día:
1. Ve a **cPanel > Trabajos de Cron (Cron Jobs)**.
2. Agrega una tarea cada 15 minutos o cada hora:
```bash
* * * * * cd /home/TU_USUARIO/calculadora.capybarasdigitales.com && php artisan schedule:run >> /dev/null 2>&1
```
*(Reemplaza `TU_USUARIO` y la ruta por la correspondiente en tu servidor).*

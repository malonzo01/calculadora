# 🦫 Calculadora de Costos & Retorno WhatsApp · Capybaras Digitales C.A.

Aplicación web interactiva desarrollada con **Laravel 13**, **Livewire**, **Volt**, **MySQL** y **Tailwind CSS**, diseñada para estimar con exactitud financiera:
1. Lo que cobra **Meta WhatsApp Cloud API** (primeros 1.000 mensajes de servicio gratis, tarifas oficiales en USD).
2. Lo que cuesta **atender manualmente a mano** con personal comercial.
3. El dinero que se **escapa en ventas** por consultas que quedan sin respuesta inmediata.
4. **Conversión automática a Bolívares (Bs.)** sincronizada en tiempo real con la tasa oficial del **Banco Central de Venezuela (BCV)**.

---

## 🎨 Identidad & Diseño
- **Branding Oficial:** Capybaras Digitales C.A.
- **Paleta de Colores:** Verde Agro (`#33521c`), Fondo Suave (`#e8f1dc`), Tierra Capybara (`#835425`), Dorado Ámbar (`#DAA520`) y Crema Base (`#FDFBF7`).
- **Contacto Oficial WhatsApp:** [+58 424-4582245](https://wa.me/584244582245).

---

## 🚀 Características
- **Reactividad en tiempo real:** Desarrollado sobre Livewire Volt (Single File Component).
- **Tasa BCV Automatizada:** Consumo de APIs financieras y fallback de scraping directo a `bcv.org.ve` con caché inteligente de 1 hora.
- **Presets de un clic:** Comercio Local, Servicios / B2B, E-Commerce y Salud / Consultas.
- **Descarga de Informe PDF:** Generación en servidor con DomPDF, membrete corporativo y cálculos en USD y Bolívares.
- **Persistencia en MySQL:** Guardado con UUID único para compartir simulaciones mediante URL (`?s=uuid`).
- **SEO & GEO Optimizado:** Metadatos estructurados (Schema.org JSON-LD, OpenGraph, Twitter Card, Geo-tags para Venezuela y FAQ integrada).

---

## 🛠️ Requisitos
- PHP 8.2 o superior (PHP 8.3 recomendado)
- Extensiones PHP: `pdo_mysql`, `gd`, `openssl`, `curl`
- MySQL 8.0+
- Composer 2.x

---

## ⚙️ Instalación Local

```bash
# 1. Clonar el repositorio
git clone https://github.com/malonzo01/calculadora.git
cd calculadora

# 2. Instalar dependencias de PHP
composer install

# 3. Configurar variables de entorno
cp .env.example .env
php artisan key:generate

# 4. Ejecutar migraciones en MySQL
php artisan migrate

# 5. Compilar assets (si deseas modificar el CSS)
npm install && npm run build

# 6. Iniciar servidor de desarrollo
php artisan serve
```

---

## 📄 Licencia
Desarrollado para **Capybaras Digitales C.A.** - Todos los derechos reservados.

# 🍷 Tienda de Vinos — Proyecto 2

## Aplicación en Producción

### Frontend (Aplicación Principal)

> **[tiendavinos-proyecto2-frontend-production.up.railway.app](https://tiendavinos-proyecto2-frontend-production.up.railway.app)**

Accede aquí para usar la tienda completa: catálogo de productos, carrito de compras, gestión de pedidos y panel de administración.

---

### Backend (API REST)

> [tiendavinos-proyecto2-bakend-production.up.railway.app](https://tiendavinos-proyecto2-bakend-production.up.railway.app)

API Laravel que gestiona productos, categorías, marcas, pedidos y autenticación.

---

## Instalación y Uso Local

### Requisitos previos

- PHP 8.2+
- Composer
- Node.js 18+
- pnpm

> Si no tienes pnpm instalado, ejecuta primero:
> ```bash
> npm install -g pnpm
> ```

---

### 1. Configurar el Backend

```bash
cd backend-tienda-vinos
```

Instala las dependencias de PHP:

```bash
composer install
npm install
```

Copia el archivo de entorno y genera la clave de aplicación:

```bash
cp .env.example .env
php artisan key:generate
```
Si no existe, crea el archivo de base de datos:
```bash
touch database/database.sqlite
```

Ejecuta las migraciones y pobla la base de datos con los seeders iniciales:
   ```bash
   php artisan migrate:fresh --seed
   ```

Inicia el servidor de desarrollo:

```bash
php artisan serve
```

El backend estará disponible en `http://localhost:8000`.

---

### 2. Configurar el Frontend

Abre una nueva terminal:

```bash
cd front-tienda-vinos
```

Descarga pnpm y luego instala las dependencias:

```bash
npm install -g pnpm
pnpm install
```

Copia el archivo de entorno y configura la URL del backend:

```bash
cp .env.example .env
```

Edita el `.env` con la URL local del backend:

```env
VITE_API_URL=http://localhost:8000/api
VITE_STORAGE_URL=http://localhost:8000
```

Inicia el servidor de desarrollo:

```bash
pnpm run dev
```

El frontend estará disponible en `http://localhost:5173`.

---

## 📁 Estructura del Repositorio

```
TiendaVinos-Proyecto2/
├── backend-tienda-vinos/   # API REST con Laravel + SQLite
└── front-tienda-vinos/     # SPA con Vue.js + Vite
```

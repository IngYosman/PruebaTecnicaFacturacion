# Sistema de Facturación

Aplicación web para la gestión de facturas de venta. Permite autenticación, dashboard con resumen, CRUD completo de facturas, informe con filtros y perfil de usuario.

## Requisitos

- PHP 8.5+
- Composer
- MySQL / MariaDB
- Node.js 18+

## Backend

```bash
cd backend
composer install
cp .env.example .env
mysql -u root < database/schema.sql
php -S 127.0.0.1:8000 -t public
```

## Frontend

```bash
cd frontend
npm install
npm run dev
```

## Credenciales de prueba

- **Email:** admin@facturacion.com
- **Contraseña:** 123456

# Prueba Técnica - Sistema de Gestión de Proyectos COTECMAR

Sistema web desarrollado con Laravel y Vue.js para la gestión de proyectos, bloques y piezas de construcción naval.

## 📋 Requisitos del Sistema

- **PHP** >= 8.1
- **Composer** >= 2.0
- **Node.js** >= 16.0
- **NPM** o **Yarn**
- **MySQL** >= 8.0 o **PostgreSQL** >= 13
- **Git**

## 🚀 Instalación

### 1. Clonar el repositorio

```bash
git clone https://github.com/tu-usuario/pruebaTecnica.git
cd pruebaTecnica
```

### 2. Instalar dependencias de PHP

```bash
composer install
```

### 3. Instalar dependencias de Node.js

```bash
npm install
# o si prefieres yarn
yarn install
```

### 4. Configurar variables de entorno

```bash
# Copiar el archivo de configuración
copy .env.example .env

# Generar la clave de aplicación
php artisan key:generate
```

### 5. Configurar la base de datos

Edita el archivo `.env` con tus credenciales de base de datos:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nombre_de_tu_base_de_datos
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña
```

### 6. Ejecutar migraciones

```bash
# Crear las tablas en la base de datos
php artisan migrate

```

### 7. Configurar storage

```bash
# Crear enlace simbólico para archivos públicos
php artisan storage:link
```

### 8. Compilar assets

```bash
# Para desarrollo
npm run dev

```

## 🏃‍♂️ Ejecutar el Proyecto

### Desarrollo

```bash
# Terminal 1: Servidor Laravel
php artisan serve

# Terminal 2: Compilación de assets en tiempo real
npm run dev

# Terminal 3: Optimizador
php artisan optimize
```

La aplicación estará disponible en: `http://localhost:8000`


## 📁 Estructura del Proyecto

```plaintext
pruebaTecnica/
├── app/
│   ├── Http/Controllers/     # Controladores
│   ├── Models/              # Modelos Eloquent
│   └── ...
├── database/
│   ├── migrations/          # Migraciones de BD
│   └── seeders/            # Seeders
├── resources/
│   ├── js/
│   │   ├── Components/     # Componentes Vue
│   │   ├── Pages/         # Páginas Vue
│   │   └── Layouts/       # Layouts
│   └── views/             # Vistas Blade
├── routes/
│   ├── web.php            # Rutas web
│   └── api.php            # Rutas API
└── public/               # Archivos públicos
```

## 🎨 Tecnologías Utilizadas

- **Backend**: Laravel 10
- **Frontend**: Vue.js 3 + Inertia.js
- **CSS**: Tailwind CSS
- **Base de Datos**: MySQL/PostgreSQL

## 🗄️ Esquema de Base de Datos

El sistema utiliza un esquema relacional jerárquico con las siguientes tablas:

### **Proyectos**

Tabla principal que agrupa los proyectos de construcción naval.

```text
proyectos
├── id (string, PK)                # Identificador único del proyecto
├── nombre (string)                 # Nombre descriptivo del proyecto
├── created_at (timestamp)
└── updated_at (timestamp)
```

### **Bloques**

Subdivisiones estructurales de cada proyecto.

```text
bloques
├── id (string, PK)                # Identificador único del bloque
├── nombre (string)                 # Nombre descriptivo del bloque
├── proyecto_id (string, FK)       # Relación con proyectos
├── created_at (timestamp)
└── updated_at (timestamp)

Índices:
- proyecto_id (para optimizar consultas por proyecto)

Relaciones:
- belongsTo: Proyecto (onDelete: cascade)
```

### **Piezas**

Componentes individuales que conforman cada bloque.

```text
piezas
├── id (string, PK)                # Identificador único de la pieza
├── nombre (string)                 # Nombre descriptivo de la pieza
├── peso_teorico (float)           # Peso teórico en kg
├── peso_real (float, nullable)    # Peso real medido en kg
├── estado (enum)                  # 'Pendiente' o 'Fabricado'
├── bloque_id (string, FK)         # Relación con bloques
├── fecha_registro (timestamp)     # Fecha de registro del peso real
├── registrado_por (string)        # Usuario que registró el peso
├── created_at (timestamp)
└── updated_at (timestamp)

Índices:
- bloque_id (para consultas por bloque)
- estado (para filtrado por estado)
- (bloque_id, estado) (índice compuesto para consultas combinadas)

Relaciones:
- belongsTo: Bloque (onDelete: cascade)
```

### **Justificación de Modificaciones al Esquema**

#### 1. **Uso de IDs tipo String**

Se implementaron identificadores de tipo `string` en lugar de auto-incrementales para:

- Permitir códigos alfanuméricos personalizados (ej: "PRY-2024-001", "BLQ-A1")
- Mayor flexibilidad en la nomenclatura de proyectos navales
- Facilitar la integración con sistemas externos que usen códigos específicos

#### 2. **Campos de Auditoría**

Se agregaron los campos `fecha_registro` y `registrado_por` en la tabla `piezas` para:

- Trazabilidad de cambios en el estado de fabricación
- Auditoría de quién y cuándo se registró el peso real
- Cumplimiento de estándares de calidad en construcción naval

#### 3. **Índices de Optimización**

Se implementaron índices estratégicos para:

- **`proyecto_id` en bloques**: Acelerar listados de bloques por proyecto
- **`bloque_id` en piezas**: Optimizar consultas de piezas por bloque
- **`estado` en piezas**: Facilitar filtrado rápido por estado de fabricación
- **Índice compuesto `(bloque_id, estado)`**: Optimizar consultas frecuentes que filtran por bloque y estado simultáneamente

#### 4. **Relaciones en Cascada**

Se configuró `onDelete('cascade')` en las foreign keys para:

- Mantener integridad referencial automáticamente
- Evitar registros huérfanos al eliminar proyectos o bloques
- Simplificar la gestión de datos relacionados

#### 5. **Campo `peso_real` Nullable**

El campo `peso_real` es nullable porque:

- Las piezas se crean en estado "Pendiente" sin peso real
- El peso real solo se registra cuando la pieza está "Fabricada"
- Refleja el flujo real del proceso de fabricación naval

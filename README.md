# Herramientas de Ayuda - Emergency Contact

## 📋 Descripción

Esta carpeta contiene herramientas de diagnóstico y debug para el sistema Emergency Contact. Estas herramientas están diseñadas para ayudar en el desarrollo y resolución de problemas.

## 🛠️ Herramientas Disponibles

### 1. Debug Login (`debug_login.php`)
- **Propósito**: Diagnosticar problemas de autenticación
- **Funcionalidades**:
  - Verificar conexión a la base de datos
  - Mostrar todos los usuarios registrados
  - Probar credenciales de autenticación
  - Verificar hashes de contraseñas
  - Información del sistema PHP

### 2. Debug Session (`debug_session.php`)
- **Propósito**: Diagnosticar problemas de sesiones
- **Funcionalidades**:
  - Mostrar información básica de la sesión
  - Listar variables de sesión activas
  - Verificar cookies de sesión
  - Configuración de sesión
  - Herramientas de gestión de sesión

### 3. Debug Database (`debug_database.php`)
- **Propósito**: Diagnosticar problemas de base de datos
- **Funcionalidades**:
  - Verificar conexión a la base de datos
  - Explorar estructura de tablas
  - Ver datos de tablas específicas
  - Ejecutor de consultas SQL
  - Información del sistema

### 4. Página Principal (`index.php`)
- **Propósito**: Página de entrada a las herramientas
- **Funcionalidades**:
  - Navegación a todas las herramientas
  - Información de uso
  - Advertencias de seguridad

## 🔧 Configuración

### Base de Datos
- **Servidor**: localhost,1433
- **Base de datos**: emergency_contact
- **Usuario**: sa
- **Contraseña**: 1001348211A@

### Cambios Realizados

- ✅ Actualizados todos los archivos de debug
- ✅ Mejorada la interfaz de usuario con estilos modernos
- ✅ Agregadas funcionalidades adicionales de diagnóstico

## ⚠️ Advertencias de Seguridad

1. **Solo para desarrollo**: Estas herramientas solo deben usarse en entornos de desarrollo
2. **No usar en producción**: Nunca dejes estas herramientas accesibles en producción
3. **Información sensible**: Estas herramientas exponen información sensible del sistema
4. **Autenticación adicional**: Considera agregar autenticación en entornos compartidos

## 🚀 Cómo Usar

1. Accede a la carpeta `help/` en tu navegador
2. Abre `index.php` para ver todas las herramientas disponibles
3. Selecciona la herramienta que necesites según el problema a diagnosticar
4. Sigue las instrucciones específicas de cada herramienta

## 📁 Estructura de Archivos

```
help/
├── index.php              # Página principal
├── debug_login.php        # Debug de autenticación
├── debug_session.php      # Debug de sesiones
├── debug_database.php     # Debug de base de datos
└── README.md             # Este archivo
```

## 🔄 Actualizaciones Recientes

- **Fecha**: $(date)
- **Cambios**:
  - Corregida configuración de base de datos
  - Mejorada interfaz de usuario
  - Agregadas funcionalidades de diagnóstico
  - Documentación completa

## 📞 Soporte

Para problemas o preguntas sobre estas herramientas, consulta la documentación del proyecto principal o contacta al equipo de desarrollo.

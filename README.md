# 💸 Bizum PHP + SQL Server

Este proyecto es un sistema de transferencias inspirado en Bizum, desarrollado en PHP con base de datos SQL Server. Está pensado como una simulación de una fintech minimalista, integrando lógica de negocio, sesiones, y validación de saldo de usuarios.

---

## ⚙️ Tecnologías utilizadas

- **PHP** (lógica de backend)
- **SQL Server** (procedimientos almacenados y persistencia)
- **XML** (gestión de sesiones y usuarios)
- **HTML + JavaScript** (formularios y envío)
- **Docker** (opcional, para entorno de pruebas)
- **FTP-KR** y **VS Code** (para despliegue y edición remota)

---

## 📂 Estructura del proyecto

BIZUM_SLN/
│
├── api/
│ └── ws.php # Punto de entrada de las transacciones
├── classes/
│ └── ClsBizum.php # Lógica principal del sistema Bizum
│
├── db/
│ └── sql/ # Procedimientos almacenados
│
├── xml/
│ ├── users.xml # Datos de usuarios (id, nombre, saldo)
│ ├── sessions.xml # Control de sesiones activas
│ └── transactions.xml # Historial de movimientos
│
├── .vscode/ # Configuración del entorno
├── .gitignore
└── README.md

---

## 🚀 Funcionalidades principales

-Login y register mediante SSID y sesiones.
- Envío de dinero entre usuarios
- Control de sesión vía XML
- Validación de saldo antes de permitir una operación
- Registro en XML de cada transacción
- Lógica encapsulada en clases (ClsBizum)
- Llamadas centralizadas desde `ws.php`
- Integración con procedimientos almacenados en SQL Server

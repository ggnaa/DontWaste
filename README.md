# DontWaste 💸

🟢 **[Live Demo: Probar Aplicación](https://dontwaste.onrender.com/index.php)**

Un dashboard financiero minimalista, *privacy-first*, que vive enteramente en la memoria de la sesión del navegador, sin depender de bases de datos externas. Diseñado con una interfaz Premium basada en *Glassmorphism* y transiciones fluidas.

---

## 📸 Demostración Visual

*(Nota para Genaro: Arrastrá y soltá acá 2 o 3 capturas de pantalla de tu proyecto. Te sugiero una del Dashboard en Modo Claro y otra de la sección de Suscripciones o Tarjetas en Modo Oscuro).*

---

## 🚀 Características Principales

- **Privacy-First:** Sin bases de datos (SQL/NoSQL). Toda la información del usuario (movimientos, tarjetas, suscripciones) persiste mediante la manipulación avanzada de `$_SESSION`.
- **UI/UX Premium:** Sistema de diseño basado en *Glassmorphism* (cristal esmerilado) usando CSS Vanilla moderno (`backdrop-filter`, `cubic-bezier`).
- **Dark Mode Nativo:** Alternancia instantánea entre temas respetando el `color-scheme` del sistema operativo, guardando la preferencia del usuario en el `localStorage`.
- **Suscripciones Inteligentes:** Seguimiento de pagos en USD con estimación de conversión a ARS.
- **Portafolio de Inversiones:** Lógica de seguimiento de activos, cálculo de rendimiento histórico (u$s / %) y renderizado de gráficos.

---

## 🛠️ Stack Tecnológico & Arquitectura

En lugar de recurrir al stack MERN tradicional y sobrecargar el cliente con librerías pesadas, este proyecto demuestra cómo construir una **SPA (Single Page Application)** ultrarrápida utilizando el poder del servidor y HTML extendido:

* **Backend:** PHP 8.2 (Lógica de negocio, enrutamiento modular y manejo de sesiones).
* **Reactividad (UI):** [Alpine.js](https://alpinejs.dev/) (Control de estado del frontend, modales, menús flotantes y Dark Mode).
* **Asincronía:** [HTMX](https://htmx.org/) (Peticiones AJAX declarativas y actualizaciones parciales del DOM usando `hx-swap-oob`, evitando recargas de página).
* **Estilos:** Vanilla CSS3 (Variables CSS, Flexbox, CSS Grid, animaciones `@keyframes` personalizadas).
* **Gráficos:** Chart.js (Renderizado dinámico de la distribución de gastos).

**¿Por qué este stack?**  
Permite mantener el estado en el servidor (PHP) de forma segura y enviar fragmentos de HTML directamente al cliente (HTMX), logrando la experiencia interactiva de React pero enviando un 80% menos de JavaScript al navegador.

---

## ⚙️ Instalación y Uso Local

Si querés correr este proyecto en tu entorno local para revisar el código, no necesitás bases de datos ni contenedores complejos. Solo requerís tener PHP instalado.

1. **Cloná el repositorio:**
   ```bash
   git clone [https://github.com/TU_USUARIO/DontWaste.git](https://github.com/TU_USUARIO/DontWaste.git)
   cd DontWaste

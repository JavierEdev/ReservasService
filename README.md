
---

## 🟨 `README.md` - ReservasService (PHP)

```md
# Reservas Service

Este servicio permite a los clientes realizar **reservas de asientos** en buses, consultar la **disponibilidad** y ejecutar el **proceso de pago** comunicándose con el servicio de Pagos. Forma parte de un sistema distribuido basado en SOA.

## 🚀 Tecnologías
- PHP 8+
- MySQL
- Apache / XAMPP / WAMP
- Arquitectura en capas
- Patrones: Adapter y Abstract Factory

## 📂 Estructura
ReservasService/
├── Domain/
├── Infrastructure/
├── Api/
├── Public/
├── config/
└── index.php


## 🔌 Endpoints

| Método | Ruta                         | Descripción                                   |
|--------|------------------------------|-----------------------------------------------|
| POST   | `/index.php/api/reservas`             | Crea una nueva reserva                        |
| GET    | `index.php/?idHorario={codigoHorario}` | Muestra asientos disponibles         |
| GET    | `/index.php/?codigo={codigoBoleto}`   | Devuelve datos completos de la reserva |
| POST   | `/index.php/api/reservaPago`        | Realiza el pago llamando al servicio de pagos |

## ⚙️ Configuración

1. Configura tu base de datos en `config/db.php`.
2. Asegúrate que `mod_rewrite` esté habilitado si usas Apache.
3. Usa Postman o Swagger desde PagosService para probar integración.

## ▶️ Ejecución
Coloca el proyecto en tu servidor Apache local (`htdocs`) y accede a:  
👉 ReservasService/Public/


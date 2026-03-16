# ABG Prime Builders Supplies Inc. - V2

The second iteration of the **Integrated Inventory Management & Security System** for ABG Prime Builders Supplies Inc. This version transitions from a custom MVC framework (V1) to a modern, distributed **N-Tier Architecture** utilizing Laravel 12, Vue.js 3, and dedicated hardware components.

---

### 💻 Version 2 Tech Stack
![Laravel](https://img.shields.io/badge/laravel-%23FF2D20.svg?style=for-the-badge&logo=laravel&logoColor=white)![Vue.js](https://img.shields.io/badge/vuejs-%2335495e.svg?style=for-the-badge&logo=vuedotjs&logoColor=%234FC08D)![Inertia.js](https://img.shields.io/badge/inertia.js-%239B51E0.svg?style=for-the-badge&logo=inertia&logoColor=white)![MySQL](https://img.shields.io/badge/mysql-%2300f.svg?style=for-the-badge&logo=mysql&logoColor=white)![Python](https://img.shields.io/badge/python-3670A0?style=for-the-badge&logo=python&logoColor=ffdd54)![Arduino](https://img.shields.io/badge/-Arduino-00979D?style=for-the-badge&logo=Arduino&logoColor=white)![TailwindCSS](https://img.shields.io/badge/tailwindcss-%2338B2AC.svg?style=for-the-badge&logo=tailwind-css&logoColor=white)

---

## 📂 Project Structure

This repository is organized into three main modules:

*   **[Web System](./web/README.md)**: The administrative hub and E-Commerce store. Powered by **Laravel 12** & **Vue 3**.
*   **[POS Bridge](./pos/README.md)**: Desktop hardware interface. Built with **Python (Tkinter)** to communicate with sensors.
*   **[Hardware Firmware](./firmware/README.md)**: Core sensor logic. Written in **C++ (Arduino)** for RFID and IR detection.

---

## 🔄 Evolutionary Overview (V1 vs V2)

This project represents a significant evolution from the original **[ABG Prime V1](https://github.com/RLASH18/abg-prime-v1)**.

### Version 1 (Parallel & Distributed Computing)
Version 1 was built on a **Custom PHP MVC Framework** for the **Parallel and Distributed Computing (PDC)** course. It focused on establishing the core business logic for **Ordering and Inventory Management**, providing basic CRUD operations and a modern UI using Tailwind & Flowbite.
*   **Context**: Academic project for Parallel & Distributed Computing.
*   **Core**: Custom-built PHP core files.
*   **Scope**: Web-based ordering and inventory management.

### Version 2 (Software Engineering 1)
Version 2 expands the scope into a full-stack **Distributed IoT and E-Commerce Ecosystem** as part of the **Software Engineering 1** curriculum.
*   **Architecture**: Transitioned to an **N-Tier Distributed Architecture** using **Inertia.js** for a Single Page Application (SPA) feel.
*   **Integration**: Added a **Python-based POS Bridge** to handle physical hardware.
*   **IoT**: Integrated **RFID** for item identification and **IR Sensors** for anti-theft security.
*   **AI**: Integrated **Google Gemini** for automated customer support.

---

## 🚀 Quick Start

To get the entire system running, you must configure all three layers:

1.  **Hardware**: Upload the firmware in `/firmware` to your Arduino Uno.
2.  **Web**: Follow the [Web Setup Guide](./web/README.md) to launch the Laravel server.
3.  **POS**: Follow the [POS Setup Guide](./pos/README.md) to launch the hardware bridge.

*Note: Ensure the `POS_API_SECRET` is identical in both the Web and POS `.env` files.*

---
<p align="center">
  <b>Software Engineering 1 Project | 2026</b><br>
  Developed for <b>ABG Prime Builders Supplies Inc.</b><br>
  <i>A Full-Stack Implementation of Inventory Monitoring and IoT Integration.</i>
</p>

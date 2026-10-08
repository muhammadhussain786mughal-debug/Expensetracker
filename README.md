# 🧾 AI Expense Receipt Analyzer

An intelligent expense management tool built with **Laravel** and **Groq Vision AI**. This application allows users to upload receipt images, automatically processes them using vision-capable Large Language Models (LLMs), and extracts structured transaction data into database records.

---

## ✨ Features

- **📸 Instant Client-Side Image Preview:** Dynamic image upload preview built with Vanilla JavaScript before submitting the form.
- **🤖 Vision AI Data Extraction:** Utilizes Groq's Vision API (`qwen/qwen3.8-27b`) to parse unstructured image data into structured JSON format.
- **💾 Automatic Database Persistence:** Automatically maps extracted receipt data (Store Name, Date, Total Amount) to database records.
- **⚡ Real-Time Processing UI:** Interactive loading states and spinner animations during API request processing to enhance user experience.
- **🛡️ Robust Fallback Handling:** Gracefully handles missing values and invalid receipts with user-friendly notices (`N/A` placeholders and custom alerts).

---

## 🛠️ Tech Stack

- **Framework:** Laravel (PHP)
- **AI Integration:** Groq API (Vision Models)
- **Frontend:** Blade, CSS3, Vanilla JavaScript, FontAwesome
- **Database:** MySQL
- **HTTP Client:** Laravel Http Facade

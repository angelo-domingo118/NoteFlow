# 📝 NoteFlow

<p align="center">
  <b>AI-powered note-taking application built with Laravel</b>
  <br><br>
  <a href="https://laravel.com"><img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat&logo=laravel&logoColor=white" alt="Laravel"></a>
  <a href="https://php.net"><img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat&logo=php&logoColor=white" alt="PHP"></a>
  <a href="https://tailwindcss.com"><img src="https://img.shields.io/badge/Tailwind-3.x-38B2AC?style=flat&logo=tailwind-css&logoColor=white" alt="Tailwind"></a>
  <a href="https://alpinejs.dev"><img src="https://img.shields.io/badge/Alpine.js-3.x-8BC0D0?style=flat&logo=alpine.js&logoColor=white" alt="Alpine.js"></a>
  <a href="https://opensource.org/licenses/MIT"><img src="https://img.shields.io/badge/License-MIT-yellow.svg?style=flat" alt="License"></a>
</p>

---

NoteFlow is a web application that helps you manage and organize your notes efficiently. It's designed as a NotebookLM clone with AI-powered features for enhanced note-taking and interaction. Transform your notes with intelligent AI assistance using Gemini and Mistral models.

<p align="center">
  <a href="#installation">Installation</a> •
  <a href="#features">Features</a> •
  <a href="#screenshots">Screenshots</a> •
  <a href="#development">Development</a> •
  <a href="#license">License</a>
</p>

## ✨ Features

<table>
  <tr>
    <td width="50%">
      <h3>Core Functionality</h3>
      <ul>
        <li>✅ User authentication and registration</li>
        <li>✅ Notebook management (create, rename, delete)</li>
        <li>✅ Note creation and organization</li>
        <li>✅ Dark mode UI with responsive design</li>
        <li>✅ Source management for AI context</li>
      </ul>
    </td>
    <td width="50%">
      <h3>AI Capabilities</h3>
      <ul>
        <li>🤖 AI-powered chat with Gemini and Mistral models</li>
        <li>🔄 Convert notes to reference sources</li>
        <li>📝 Rich text editing capabilities</li>
        <li>💡 Suggested questions to spark conversation</li>
        <li>🔍 OCR for document analysis</li>
      </ul>
    </td>
  </tr>
</table>

## 🏗️ Application Structure

NoteFlow is organized into three main panels:

1. **Sources Panel** - Upload or add reference materials to provide context for AI chat
2. **Chat Panel** - Interact with AI using the Gemini or Mistral models
3. **Notes Panel** - Create, edit, and manage your notes, with the ability to convert them to sources

## 🚀 Installation

### Prerequisites

- PHP 8.1 or higher
- Composer
- MySQL
- Node.js and npm
- Git
- XAMPP (recommended for local development)

### Setup Instructions

#### 1. Clone the Repository

```bash
git clone https://github.com/yourusername/NoteFlow.git
cd NoteFlow
```

#### 2. Install Dependencies

```bash
composer install
npm install
```

#### 3. XAMPP Setup

1. Download and install XAMPP from [apachefriends.org](https://www.apachefriends.org/)
2. Start Apache and MySQL services from the XAMPP Control Panel
3. Place the NoteFlow project in the `htdocs` directory of your XAMPP installation (or create a symbolic link)
   ```bash
   # Option 1: Copy project to htdocs
   cp -r /path/to/NoteFlow /path/to/xampp/htdocs/
   
   # Option 2: Create symbolic link (Windows with administrator privileges)
   mklink /D "C:\xampp\htdocs\NoteFlow" "C:\path\to\your\NoteFlow"
   
   # Option 2: Create symbolic link (Linux/Mac)
   ln -s /path/to/NoteFlow /path/to/xampp/htdocs/
   ```
4. Create a database for the project using phpMyAdmin:
   - Open http://localhost/phpmyadmin in your browser
   - Click on "New" in the left sidebar
   - Enter "noteflow" as database name and click "Create"

#### 4. Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

Edit your .env file to configure the database connection:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=noteflow
DB_USERNAME=root
DB_PASSWORD=
```

#### 5. Database Setup

```bash
php artisan migrate
php artisan db:seed # Optional: if seeders are available
```

#### 6. Build Assets

```bash
npm run build
```

#### 7. Access the Application

If using XAMPP:
- Open your browser and go to http://localhost/NoteFlow/public
- Alternatively, you can use Laravel's built-in server:
  ```bash
  php artisan serve
  ```
  The application will be available at http://localhost:8000

## 🧠 AI Configuration

The application uses Gemini API and Mistral API for AI features:

1. Get your [Gemini API key](https://ai.google.dev/) from Google AI Studio
2. Get your [Mistral API key](https://mistral.ai/) from Mistral AI platform
3. Add them to your .env file:
```
GEMINI_API_KEY=your_gemini_api_key_here
MISTRAL_API_KEY=your_mistral_api_key_here
```

## 🛠️ Development

### Database Schema

NoteFlow uses the following database structure:

- **users** - User account information
- **notebooks** - Collection of notes created by users
- **notes** - Content created by users within notebooks
- **sources** - Reference materials used for AI context

### Queue Worker (Optional)

If you need to process background jobs:

```bash
php artisan queue:work
```

### Testing

```bash
php artisan test
```

## 🔧 Technologies Used

<table>
  <tr>
    <th>Category</th>
    <th>Technologies</th>
  </tr>
  <tr>
    <td><strong>Backend</strong></td>
    <td>
      <a href="https://laravel.com/">Laravel</a> •
      <a href="https://laravel.com/docs/10.x/starter-kits#laravel-breeze">Laravel Breeze</a> •
      <a href="https://pestphp.com/">Pest PHP</a>
    </td>
  </tr>
  <tr>
    <td><strong>Frontend</strong></td>
    <td>
      <a href="https://alpinejs.dev/">Alpine.js</a> •
      <a href="https://tailwindcss.com/">Tailwind CSS</a> •
      <a href="https://www.tiny.cloud/">TinyMCE</a> •
      <a href="https://prismjs.com/">Prism.js</a>
    </td>
  </tr>
  <tr>
    <td><strong>AI & APIs</strong></td>
    <td>
      <a href="https://ai.google.dev/">Google Gemini API</a> •
      <a href="https://mistral.ai/">Mistral AI API</a> •
      <a href="https://github.com/mrmysql/youtube-transcript">YouTube Transcript</a>
    </td>
  </tr>
  <tr>
    <td><strong>Tools</strong></td>
    <td>
      <a href="https://docs.guzzlephp.org/">Guzzle</a> •
      <a href="https://vitejs.dev/">Vite</a> •
      <a href="https://www.apachefriends.org/">XAMPP</a>
    </td>
  </tr>
</table>

## 📜 License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

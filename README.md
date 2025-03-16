<p align="center"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></p>

# NoteFlow

NoteFlow is a web application built with Laravel that helps you manage and organize your notes efficiently. It's designed as a NotebookLM clone with AI-powered features for enhanced note-taking and interaction.

## Features

- User authentication and registration
- Notebook management (create, rename, delete)
- Note creation and organization
- AI-powered chat using Gemini and Mistral models
- Dark mode UI with responsive design
- Source management for AI context
- Convert notes to reference sources
- Rich text editing capabilities
- Suggested questions to spark conversation
- Multiple AI model support (Gemini and Mistral)
- Responsive design with dark mode

## Application Structure

NoteFlow is organized into three main panels:

1. **Sources Panel**: Upload or add reference materials to provide context for AI chat
2. **Chat Panel**: Interact with AI using the Gemini or Mistral models
3. **Notes Panel**: Create, edit, and manage your notes, with the ability to convert them to sources

## Credits & Technologies

### Backend
- [Laravel](https://laravel.com/) - PHP framework for web application development
- [Laravel Breeze](https://laravel.com/docs/10.x/starter-kits#laravel-breeze) - Authentication scaffolding
- [Pest PHP](https://pestphp.com/) - Testing framework

### Frontend
- [Alpine.js](https://alpinejs.dev/) - JavaScript framework for interactivity
- [Tailwind CSS](https://tailwindcss.com/) - Utility-first CSS framework
- [TinyMCE](https://www.tiny.cloud/) - Rich text editor via @tinymce/tinymce-vue
- [Prism.js](https://prismjs.com/) - Syntax highlighting for code blocks

### API Integrations
- [Google Gemini API](https://ai.google.dev/) - AI model for natural language processing and multimodal content
- [Mistral AI API](https://mistral.ai/) - AI model for OCR and text processing
- [MrMySQL/youtube-transcript](https://github.com/mrmysql/youtube-transcript) - Package for fetching YouTube video transcripts
- [Branko/transcriby](https://github.com/branko/transcriby) - Audio transcription service

### Tools & Utilities
- [Guzzle](https://docs.guzzlephp.org/) - HTTP client for API requests
- [Vite](https://vitejs.dev/) - Frontend build tool
- [XAMPP](https://www.apachefriends.org/) - Local development environment with Apache, MySQL, PHP

## Prerequisites

- PHP 8.1 or higher
- Composer
- MySQL
- Node.js and npm
- Git
- XAMPP (recommended for local development)

## Setup Instructions

Follow these steps to get the project running on your local machine:

### 1. Clone the Repository

```bash
git clone https://github.com/yourusername/NoteFlow.git
cd NoteFlow
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. XAMPP Setup

1. Download and install XAMPP from https://www.apachefriends.org/
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

### 4. Environment Setup

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

### 5. Database Setup

```bash
php artisan migrate
php artisan db:seed # Optional: if seeders are available
```

### 6. Build Assets

```bash
npm run build
```

### 7. Access the Application

If using XAMPP:
- Open your browser and go to http://localhost/NoteFlow/public
- Alternatively, you can use Laravel's built-in server:
  ```bash
  php artisan serve
  ```
  The application will be available at http://localhost:8000

## Database Schema

NoteFlow uses the following database structure:

- **users**: User account information
- **notebooks**: Collection of notes created by users
- **notes**: Content created by users within notebooks
- **sources**: Reference materials used for AI context

## Additional Configuration

### API Keys

The application uses Gemini API and Mistral API for AI features. If you need this functionality:
1. Get your Gemini API key from Google AI Studio
2. Get your Mistral API key from Mistral AI platform
3. Add them to your .env file:
```
GEMINI_API_KEY=your_gemini_api_key_here
MISTRAL_API_KEY=your_mistral_api_key_here
```

## Queue Worker (Optional)

If you need to process background jobs:

```bash
php artisan queue:work
```

## Testing

```bash
php artisan test
```

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

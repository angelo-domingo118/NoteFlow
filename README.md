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

## Prerequisites

- PHP 8.1 or higher
- Composer
- MySQL
- Node.js and npm
- Git

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

### 3. Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Database Setup

- Create a MySQL database named `noteflow`
- The default configuration uses:
  - Username: `root`
  - Password: (empty)
  - If your setup is different, update the DB_USERNAME and DB_PASSWORD in the .env file

```bash
php artisan migrate
php artisan db:seed # Optional: if seeders are available
```

### 5. Build Assets

```bash
npm run build
```

### 6. Start the Development Server

```bash
php artisan serve
```

The application will be available at http://localhost:8000

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

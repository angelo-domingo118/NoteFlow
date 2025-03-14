# NoteFlow: NotebookLM Clone Project Plan

## 1. Overview
This project is an AI chatbot integrated with the "gemini-2.0-flash" model, designed as a NotebookLM clone. The goal is to create a simple, self-contained note-taking and interaction web application using Laravel and modern web technologies. Although the development environment is local-first, the application supports multiple users with account registration for personalized note management.

## 2. Tech Stack
- **Backend:** Laravel (PHP 8.1+)
  - Uses built-in Laravel features and middleware.
  - Authentication scaffolded with Laravel Breeze.
- **Frontend:** Blade templating engine, Alpine.js for interactivity.
- **Styling:** Tailwind CSS (with dark mode support)
- **Build Tools:** Vite for asset bundling.
- **Database:** MySQL (configured via the `.env` file)
- **AI Integration:** 
  - Gemini API using the "gemini-2.0-flash" model for chatbot functionality.
- **Testing:** Pest for unit and feature tests

## 3. Project Scope & Features
- **User Interaction:** Supports multiple users with account registration, enabling personalized note creation and management.
- **Pages:**
  - **Landing Page:** A minimalistic introduction with a call-to-action for login/register.
  - **Authentication:** Login and registration views managed by Laravel Breeze.
  - **Dashboard:** Main user interface displaying notebooks/notes and providing controls to create, edit, and delete notes.
  - **Notebook Page:** A detailed view split into sections for sources, chat (powered by the Gemini API), and notes.
- **Functionality:** 
  - Notebook management (creating, renaming, and deleting notebooks/notes)
  - Option to convert notes into reference sources for enhanced chatbot context

## 4. Database Schema
The application database will include the following core tables:

- **users**
  - `id` (primary key)
  - `name`
  - `email` (unique)
  - `password`
  - Timestamps (`created_at`, `updated_at`)
  
- **notebooks**
  - `id` (primary key)
  - `user_id` (foreign key to users)
  - `title`
  - `description` (optional)
  - Timestamps
  
- **notes**
  - `id` (primary key)
  - `notebook_id` (foreign key to notebooks)
  - `title`
  - `content` (rich text)
  - Timestamps
  
- **sources** (optional, for additional AI context)
  - `id` (primary key)
  - `notebook_id` (foreign key to notebooks)
  - `name`
  - `type` (e.g., text, file, link)
  - `data` (to store source content or reference)
  - Timestamps

**Relationships:**
- A user can have multiple notebooks.
- Each notebook can contain multiple notes and sources.

## 5. Role-Based Access Considerations
- **User Accounts:** The application supports multiple users. Each user manages their own notebooks and notes.
- **Access Control:** No complex role-based access control is implemented at this stage. Future iterations may introduce additional roles or permissions as the application scales.

## 6. Implementation Tips
- **Routing & Controllers:** Use resource controllers; secure routes using Laravel’s `auth` middleware.
- **Validation & Error Handling:** Utilize Laravel’s Form Requests and built-in validation.
- **Testing:** Implement tests using Pest to cover key user flows.
- **Design & Responsiveness:** Focus on a clean UI with emphasis on dark mode, using Tailwind CSS utilities.
- **AI Integration:** Ensure proper API integration with the Gemini "gemini-2.0-flash" model for chatbot responses, and abstract this functionality into dedicated service classes.

## 7. Pages & Design

### 7.1 Landing Page
- **Goal:** Provide a simple, visually appealing introduction to the application without requiring any scrolling.
- **Hero Section Only:**
  - **Headline & Subheading:** Briefly describe what NoteFlow does (e.g., “Organize Your Notes Effortlessly”).
  - **Call-to-Action:** A button that takes users directly to the login/register page.
  - **Visual Elements:** A minimal or abstract background illustration to complement the text.

### 7.2 Authentication Page
- **Foundation:** Created by Laravel Breeze using Blade and Alpine.js.
- **Style & Layout:**
  - Consistent branding and color scheme aligned with Tailwind’s dark mode configuration.
  - Clean forms with labeled fields, concise error messages, and clear instructions.
  - Optionally, login and register can be combined under a single layout with tabs or kept as separate routes.
- **User Flow:** Ensures a straightforward process for new users to sign up and returning users to log in.

### 7.3 Dashboard Page
- **Design Inspiration:** A dark-themed, minimal interface inspired by provided screenshots.
- **Header:**
  - **Logo/Title (Top-Left):** Replace “NotebookLM” with your own brand name or logo.
  - **User Profile & Settings (Top-Right):** Displays the user avatar and a settings icon.
- **Main Content:**
  - **Welcome Heading:** Bold text welcoming the user (e.g., “Welcome to NoteFlow”).
  - **Create New Button:** Clearly visible button to create a new notebook or note.
  - **Cards/Collections:** Each notebook appears as a rectangular card showing:
    - **Title & Date:** For quick reference.
    - **Optional Icon/Emoji:** To give each card a distinct look.
    - **Sources or Contributors:** If shared or containing multiple resources.
    - **Hover Menu:** A small ⋮ icon appears on hover to rename, edit, or delete.
- **Layout & Sorting Controls:**
  - **Grid/List Toggle:** Icons to switch between card layout and condensed list layout.
  - **Sorting Dropdown:** Options like “Most Recent” or “Alphabetical.”
- **Styling & Responsiveness:**
  - **Dark Mode:** Deep gray or black background, white text, and accent buttons.
  - **Mobile Support:** Cards or lists reflow for smaller screens ensuring easy navigation.

### 7.4 Notebook Page Layout (Revised)
- **Header:**
  - **Notebook Title & User Controls (Top-Right):**
    - Title on the same horizontal line as the settings button and user avatar.
    - User controls allow for managing account preferences.
- **Left Panel (Sources):**
  - **Collapse Button (Top-Right of the Panel):** Allows users to hide or show the entire sources panel.
  - **Add Source Button:** Labeled “+ Add source”; clicking opens a dialog/pop-up for adding a new source (PDF, website link, text, etc.).
  - **Source List:** 
    - Each source is listed with a checkbox to toggle its inclusion for AI context.
    - **Three-Dot Menu (⋮):** Offers options to remove or rename a source.
    - **Select All Sources Checkbox:** Quickly toggles all items in the list.
- **Center Panel (Chat):**
  - **Chat Header:**
    - **Refresh Button:** Clears the current chat history after a confirmation prompt.
  - **Conversation Window:** Displays user queries and AI responses with possible citations.
  - **Question Input & Suggested Questions:**
    - **Question Input Box:** Located at the bottom for users to type queries or commands.
    - **Suggested Questions:** Quick prompts appear below the input box to auto-fill or spark conversation (e.g., “Discuss the primary responsibilities of Laravel controllers.”).
- **Right Panel (Notes):**
  - **Collapse Button (Top-Left of the Panel):** Lets users hide or show the notes panel.
  - **Notes Panel (Renamed from Studio):**
    - **Add Note Button:** Directly creates a new note.
    - **Predefined Formats (Optional):** Buttons like “Study Guide,” “Briefing Doc,” “FAQ,” or “Timeline” for AI-generated note types.
    - **List of Existing Notes:** Clickable notes display a title and short preview.
      - **Three-Dot Menu (⋮):** Options to rename or delete individual notes.
      - **Click to Open/Expand:** Opens the note in a rich text editor or displays it as read-only if AI-generated.
- **Note Editor:**
  - **Title & Content Fields:** 
    - Title at the top (e.g., “New Note”).
    - A rich text toolbar (Bold, Italic, Headings, Lists, etc.) for formatting.
  - **Convert to Source Button (Bottom):** Transforms the note into a source for use in AI context.
  - **Delete Note (Trash Icon):** Option to quickly remove the note.
  - **AI-Generated vs. User-Created:**
    - **AI-Generated Notes:** May include an indicator (e.g., “Saved responses are view only”) and are generally read-only.
    - **User-Created Notes:** Fully editable with options to rename, delete, or convert.
- **Dialog/Pop-Up for Adding Sources:**
  - **Trigger:** Appears when the user clicks “+ Add source.”
  - **File/Link Options:**
    - **Upload Button:** Drag-and-drop area or file chooser for PDF, text, Markdown.
    - **Website/YouTube:** URL-based source addition.
    - **Paste Text / Copied Text:** For manual text entry.
  - **Source Limit Indicator:** Displays the count of uploaded sources (e.g., “1/50”).

## 8. Project Structure & Codebase Overview
The codebase follows the standard Laravel project structure with additional directories for assets, tests, and configuration:

- **Configuration & Environment:**  
  Files such as `.env.example`, `config/app.php`, and related config files manage application settings and environment variables.
  
- **Application Logic:**  
  - **Controllers:** Located in `app/Http/Controllers` (including authentication, profile, and core route handling via Laravel Breeze).
  - **Models:** Found in `app/Models`, encapsulating business logic and database interactions (e.g., User model).
  - **Views:** Blade templates stored in `resources/views` for pages such as authentication, dashboards, notebooks, and profiles.
  
- **Routing & Middleware:**  
  Routes are defined in the `routes/` directory (e.g., `routes/web.php`, `routes/auth.php`), with middleware ensuring routes are secured.

- **Front-End Assets:**  
  Managed via `resources/js` and `resources/css`, with Vite (`vite.config.js`) used for asset bundling and management.

- **Database & Migrations:**  
  Migrations and seeders in the `database/migrations/` and `database/seeders/` directories set up the database schema as described above.

- **Testing:**  
  Unit and feature tests are located in the `tests/` directory, utilizing Pest for streamlined testing.

- **Additional Directories:**  
  - **Public:** Contains entry point (`public/index.php`), assets, and static files.
  - **Storage:** Used for caching, logs, sessions, and compiled views.

---

*This document serves as the initial guide for the project. It is expected to evolve as requirements are refined and additional features are developed.*

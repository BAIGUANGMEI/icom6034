# Recruitment Experiences Sharing Platform

A web-based platform where job seekers can share interview experiences, job offers, and career-related discussions within a community.

## Tech Stack

| Layer    | Technology                          |
| -------- | ----------------------------------- |
| Frontend | Vue 3 (Composition API) + Vite      |
| Backend  | Laravel 12 (PHP 8.2+)               |
| Database | MySQL 8.0+                          |
| Auth     | Laravel Sanctum (Token-based)       |
| API Docs | Swagger UI (l5-swagger / OpenAPI 3) |
| State    | Pinia                               |
| HTTP     | Axios                               |
| Router   | Vue Router 4                        |

## External APIs (called directly from frontend)

- **LinkedIn Jobs API** — Real-time job listings search (via RapidAPI)
- **News API** (https://newsapi.org/) — Job market news and industry trends

## Prerequisites

Make sure the following tools are installed on your machine:

| Tool     | Version Requirement | Check Command        |
| -------- | ------------------- | -------------------- |
| PHP      | >= 8.2              | `php -v`             |
| Composer | >= 2.x              | `composer --version` |
| Node.js  | >= 20.19 or >= 22.12| `node -v`            |
| npm      | >= 10.x             | `npm -v`             |
| MySQL    | >= 8.0              | `mysql --version`    |

## Project Structure

```
|
├── backend/                    # Laravel API Server
│   ├── app/
│   │   ├── Http/Controllers/Api/   # API Controllers
│   │   ├── Http/Requests/Api/      # Form Validation
│   │   ├── Http/Resources/         # API Resource Formatting
│   │   └── Models/                 # Eloquent Models
│   ├── database/migrations/        # Database Migrations
│   ├── routes/api.php              # API Route Definitions
│   └── .env.example                # Environment Config Template
│
├── frontend/                   # Vue 3 Client App
│   └── src/
│       ├── api/                    # Axios API Layer
│       ├── stores/                 # Pinia State Management
│       ├── router/                 # Vue Router Config
│       ├── views/                  # Page Components
│       └── components/             # Reusable Components
│
├── .gitignore
└── README.md
```

## Getting Started

### 1. Clone the Repository

```bash
git clone <repository-url>
cd icom6034
```

### 2. Database Setup

Create a MySQL database:

```sql
CREATE DATABASE job_sharing_platform CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 3. Backend Setup

```bash
cd backend

# Install PHP dependencies
composer install

# Copy environment config and configure it
cp .env.example .env

# Generate application key
php artisan key:generate
```

Edit `backend/.env` and update the following values:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=job_sharing_platform
DB_USERNAME=root
DB_PASSWORD=your_password

FRONTEND_URL=http://localhost:5173
```

Run database migrations:

```bash
php artisan migrate
```

Start the backend server:

```bash
php artisan serve
# Server runs at http://localhost:8000
```

### 4. Frontend Setup

Open a **new terminal**:

```bash
cd frontend

# Install Node.js dependencies
npm install
```

Configure `frontend/.env` with API keys:

```dotenv
VITE_API_BASE_URL=http://localhost:8000/api

# External API Keys (called directly from frontend)
VITE_NEWS_API_KEY=your_news_api_key
VITE_LINKEDIN_JOBS_API_KEY=your_linkedin_api_key
VITE_LINKEDIN_JOBS_API_URL=https://linkedin-jobs-api.p.rapidapi.com
```

Start the development server:

```bash
npm run dev
# App runs at http://localhost:5173
```

### 5. Verify Setup

- Backend API: Open http://localhost:8000/api/posts — should return a JSON response.
- API Documentation: Open http://localhost:8000/api/documentation — Swagger UI with all endpoints.
- Frontend App: Open http://localhost:5173 — should display the homepage.

## API Endpoints

### Public Routes (Backend API)

| Method | Endpoint                       | Description                 |
| ------ | ------------------------------ | --------------------------- |
| POST   | `/api/register`                | Register a new user         |
| POST   | `/api/login`                   | Login                       |
| GET    | `/api/posts`                   | List all posts              |
| GET    | `/api/posts/{id}`              | Get single post             |
| GET    | `/api/tags`                    | List all tags               |
| GET    | `/api/tags/{id}`               | Get tag with related posts  |
| GET    | `/api/search/posts`            | Search posts                |
| GET    | `/api/posts/{postId}/comments` | Get comments for a post     |

### External APIs (called from frontend)

| Service           | Endpoint                                  | Description                 |
| ----------------- | ----------------------------------------- | --------------------------- |
| LinkedIn Jobs API | `https://linkedin-jobs-api.p.rapidapi.com`| Search job listings          |
| News API          | `https://newsapi.org/v2/top-headlines`    | Get headline news            |
| News API          | `https://newsapi.org/v2/everything`       | Search news articles         |

### Protected Routes (require Bearer Token)

| Method | Endpoint                                | Description               |
| ------ | --------------------------------------- | ------------------------- |
| POST   | `/api/logout`                           | Logout                    |
| GET    | `/api/user`                             | Get current user info     |
| POST   | `/api/posts`                            | Create a new post         |
| PUT    | `/api/posts/{id}`                       | Update a post             |
| DELETE | `/api/posts/{id}`                       | Delete a post             |
| GET    | `/api/my-posts`                         | Get current user's posts  |
| POST   | `/api/posts/{postId}/comments`          | Create a comment          |
| DELETE | `/api/posts/{postId}/comments/{id}`     | Delete a comment          |

## Available Scripts

### Backend

```bash
php artisan serve              # Start development server
php artisan migrate            # Run database migrations
php artisan migrate:fresh      # Reset and re-run all migrations
php artisan route:list         # List all registered routes
php artisan l5-swagger:generate  # Regenerate Swagger/OpenAPI docs
php artisan tinker             # Interactive REPL
```

### Frontend

```bash
npm run dev       # Start development server with HMR
npm run build     # Build for production
npm run preview   # Preview production build locally
```

## Team

- **Mei Zhihan** (3036651104)
- **Liu Yinuo** (3036651013)

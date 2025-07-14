# Berai: A Modern Project & Task Management Tool

<p align="center">
  <img src="https://your-logo-url-here.com/logo.svg" alt="Berai Logo" width="120">
</p>

<p align="center">
  A simple, intuitive, and powerful open-source tool for managing projects and tasks, built with a modern tech stack. Designed for freelancers, small teams, and anyone who needs a clean way to organize their work.
</p>

<p align="center">
  <a href="#-features">Features</a> •
  <a href="#-tech-stack">Tech Stack</a> •
  <a href="#-getting-started">Getting Started</a> •
  <a href="#-license">License</a> •
  <a href="#-contributing">Contributing</a>
</p>

---

## ✨ Features

Berai provides a complete suite of tools to manage your work from start to finish.

- **Dashboard:** Get a high-level overview of all your projects and a personalized list of your open tasks.
- **Project Management:** Create and manage projects, each with its own dedicated workspace.
- **Task Management:**
  - Create detailed tasks with descriptions, due dates, assignees, and priorities (Low, Medium, High, Urgent).
  - Switch between a traditional **List View** and an interactive **Kanban Board**.
  - **Drag-and-drop** tasks to instantly update their status.
- **Team Collaboration:**
  - Invite team members to your projects by email.
  - Assign tasks to specific team members.
- **Activity Feed:** A real-time log of all significant actions within a project, such as task creation and status updates.
- **User Profiles:** Personalize your account with a custom name and avatar that appears throughout the application.
- **Secure & Performant:**
  - Built-in authorization policies to ensure users can only access their own data.
  - Debounced searching and filtering for a smooth user experience.
- **Themed UI:** A clean, modern, and responsive UI built with DaisyUI/shadcn-vue, available in both light and dark modes.

---

## 💻 Tech Stack

This project is built on a modern, full-stack framework that emphasizes developer productivity and user experience.

- **Backend:** Laravel 12+ (PHP)
- **Frontend:** Vue.js 3 (with Composition API)
- **Framework:** Inertia.js (for creating a seamless, single-page app experience)
- **Database:** PostgreSQL
- **UI:** Tailwind CSS with the [shadcn-vue](https://www.shadcn-vue.com/) component library.
- **Authentication:** Custom-built authentication system.
- **Activity Logging:** `spatie/laravel-activitylog`
- **Drag & Drop:** `vue-draggable-plus`

---

## 🚀 Getting Started

Follow these instructions to get a local copy of Berai up and running for development and testing purposes.

### Prerequisites

- PHP 8.2+
- Composer
- Node.js & npm
- PostgreSQL

### Installation

1.  **Clone the repository:**

    ```bash
    git clone https://github.com/putralangkat97/berai.git
    cd berai
    ```

2.  **Install backend dependencies:**

    ```bash
    composer install
    ```

3.  **Install frontend dependencies:**

    ```bash
    npm install
    ```

4.  **Set up your environment file:**
    Copy the example environment file and generate your application key.

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

5.  **Configure your `.env` file:**
    Open the `.env` file and update your database credentials for PostgreSQL.

    ```dotenv
    DB_CONNECTION=pgsql
    DB_HOST=127.0.0.1
    DB_PORT=5432
    DB_DATABASE=your_database
    DB_USERNAME=your_postgres_user
    DB_PASSWORD=your_postgres_password
    ```

6.  **Run database migrations:**
    This will create all the necessary tables, including the activity log table.

    ```bash
    php artisan migrate
    ```

7.  **Create the storage link:**
    This makes uploaded files (like avatars) publicly accessible.

    ```bash
    php artisan storage:link
    ```

8.  **Run the development servers:**
    You'll need to run both the Vite server (for frontend assets) and the Laravel server.
    - In one terminal, run:
      ```bash
      npm run dev
      ```
    - In a second terminal, run:
      ```bash
      php artisan serve
      ```

You can now access the application at `http://localhost:8000`!

---

## 🤝 Contributing

Contributions are what make the open-source community such an amazing place to learn, inspire, and create. Any contributions you make are **greatly appreciated**.

If you have a suggestion that would make this better, please fork the repo and create a pull request. You can also simply open an issue with the tag "enhancement".

1.  Fork the Project
2.  Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3.  Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4.  Push to the Branch (`git push origin feature/AmazingFeature`)
5.  Open a Pull Request

Don't forget to give the project a star! Thanks again!

---

## 📜 License

This project is licensed under the MIT License. See the `LICENSE.md` file for details.

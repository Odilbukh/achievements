## Installation

Follow these steps to install and run the project locally on your computer.

1. **Clone repository**

   ```bash
   git clone https://github.com/Odilbukh/achievements.git
2. **Go to project directory**

   ```bash
   cd path-to-project
3. **Install dependencies**
    ```bash
   composer install
4. **Set up environment file**
   DB_CONNECTION=mysql<br>
   DB_HOST=127.0.0.1<br>
   DB_PORT=3306<br>
   DB_DATABASE=<br>
   DB_USERNAME=<br>
   DB_PASSWORD=<br><br>
5. **Generate application key**
    ```bash
   php artisan key:generate

6. **Start migrations and populate the database**
    ```bash
    php artisan migrate
    php artisan db:seed

7. **Start the server**
    ```bash
   php artisan serve      
Your application will be available at: http://127.0.0.1:8000

## Endpoints

- **GET** `/users/{user}/achievements` 
    params: user_id
- **POST** `/comments/create`
   params: [ <br>
      user_id: int <br>
      text: string
  ]<br>
- **POST** `/lessons/watched` <br>
    params: [ <br>
        user_id: int <br>
        lesson_id: int
    ]<br>
  **!! Use header  "Accept: application/json" !!**



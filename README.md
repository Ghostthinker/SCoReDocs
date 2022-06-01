This is a laravel project with a vue.js frontend.

### 0. setup env
- copy .env.example to .env

### 1. install all composer dependencies
- `composer install`
### 2. install all npm dependencies
- `npm install`
### 3. migrate everything
- `php artisan migrate`

### 4. init vue-beautiful chat
- `git submodule sync --recursive`
- `git submodule update --init --recursive`

### 5. install dependencies
- `cd resources/vue-beautiful-chat`
- `npm install`

### 6. build frontend
- `cd ../../`
- `npm run dev`

### 7. setup laravel echo server
- `npm install -g laravel-echo-server`
- `laravel-echo-server init`
- Newly created laravel-echo-server.json might need some adjustments
- Start server `laravel-echo-server start`

### 7.1 set .env vars
  BROADCAST_DRIVER=redis
  QUEUE_DRIVER=redis
  CACHE_DRIVER=file
  QUEUE_CONNECTION=redis
  SESSION_DRIVER=file
  SESSION_LIFETIME=120

### 7.2 start queue
- `php artisan queue:listen`

### 8. Seed Database with basic Users
- `php artisan db:seed --class=UserSeeder`
- This will generate 3 Users:
- All of them will have the password "admin"
- 1.) User: admin@score.de | Role: Admin
- 2.) User: user@score.de | Role: Student
- 3.) User: team@score.de | Role: Team



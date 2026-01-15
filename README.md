# how to run this chat app on your machine

Run following commands after clone this repo

composer install
npm install

cp .env.example .env // copies the environment file (needed before generating keys)
php artisan key:generate
php artisan reverb:key
php artisan migrate

npm run dev
php artisan reverb:start // start websocket server
php artisan serve

# reimbursement-app

1.	Clone terlebih dahulu dari url di bawah ini:
https://github.com/yanuarrahmat22/reimbursement-app
2.	Masuk ke folder projek
Command: cd reimbursement-app
3.	Setup file env, dengan mengkopikan file .env-example ke .env
Command: cp .env.example .env
4.	Install Composer
Command: composer install
5.	Generate application key
Command: php artisan key:generate
6.	Buat simbolik link untuk storage aplikasi
Command: php artisan storage:link
7.	Buat database PostgreeSQL dengan nama database sesuai pada file .env
8.	Seusaikan config database pada file .env dan pastikan tidak ada yang salah. Untuk mempermudah installasi bisa disamakan saja config dan nama databasenya
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE= reimbursement_db
DB_USERNAME=your_database_username (sesuaikan dengan setting database anda)
DB_PASSWORD=your_database_password (sesuaikan dengan setting database anda)
9.	Jika database sudah dibuat, selanjutnya jalankan migrasi database
Command: php artisan migrate
10.	Selanjutnya jalankan Seeding untuk memasukan data dummy ke database
Command: php artisan db:seed
11.	Jika sudah selesai semua jalankan servernya
Command: php artisan serve
12.	Daftar akun user:
No.	NIP	Nama	Role	Password
1.	0000	Administrator	ADMINISTRATOR	000000
2.	1234	DONI	DIRECTOR	123456
3.	1235	DONO	FINANCE	123456
4.	1236	DONA	STAFF	123456

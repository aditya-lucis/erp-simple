**ERP SIMPLE**

sesuai namanya, ini adalah aplikasi ERP yang paling simple. fiturnya pun tidak seberapa kompleks dan mudah digunakan. menu ini terdiri dari master data COA (Chart Of Account), transaksi jurnal, dan buku besar (general ledger). sistem ini dibuat menggunakan Laravel versi 10.
untuk menjalani sistem ini silahkan clone repository, lakukan composer update, setelah itu buat file env berdasarkan env.example, lalu buat database di mysql atau postgresql dengan nama erp_simple, lalu lakukan php artisan migrate --seed untuk melakukan migration database dan seeding data COA.

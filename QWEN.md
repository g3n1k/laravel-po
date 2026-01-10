gunakan bahasa indonesia

# Purcase Order Management
system untuk mencatat Purcase Order (PO) yang masuk ke toko

---
## contoh kasus
---
judul open PO Inna Cookies
memiliki tanggal awal dan tanggal akhir, tgl_awal: 2025-12-01 tgl_akhir: 2025-12-31
kita dapat memasukan berbagai jenis produk dalam 1 judul PO, ex:
produk | harga | stok
kue nastar polos | 10000 | 10
kue nastar keju | 12000 | 10
sagu keju | 10000 | 5
putri coklat | 10000 | 5
    

---
## pelanggan PO
---
pelanggan dapat melakukan po berulang, untuk item yang sama akan di akumulasikan

Title | Pelanggan | tanggal | produk | item | terima | selesai
PO Inna Cookies | ibu saibah | 2025-01-12 12:03 | kue nastar polos | 3 | 0 | 0
PO Inna Cookies | Pak Nana | 2025-01-12 13:05 | sagu keju  | 1  | 0 | 0
PO Inna Cookies | ibu saibah | 2025-01-13 13:05 | kue nastar keju  | 1 | 0 | 0
PO Inna Cookies | Kakak Nuri | 2025-01-13 17:03 | putri coklat | 3  | 0 | 0
PO Inna Cookies | ibu saibah | 2025-01-14 17:03 | putri coklat | 3  | 0 | 0
PO Inna Cookies | ibu saibah | 2025-01-15 18:03 | kue nastar polos | 3  | 0 | 0

---
## downpayment
---
dapat menerima berkali-kali down payment, ex: 
Title | Pelanggan | tanggal | downpayment
PO Inna Cookies | ibu saibah | 2025-01-12 12:03 | 30000
PO Inna Cookies | ibu saibah | 2025-01-13 12:03 | 20000
PO Inna Cookies | Kakak Nuri | 2025-01-14 17:03 | 10000


---
## summary PO
---
pada summary PO berisi keterangan seperti ini

Title: PO Inna Cookies
pelanggan: 3
Total Item: 14

produk | item | free item
kue nastar polos | 6 | 4
sagu keju | 1 | 4
kue nastar keju | 1 | 9
putri coklat | 6 | 5

---
## detail PO
---
untuk detail PO dibagi per pelanggan, ex:

pelanggan | total items | total bayar | down payment | sisa bayar
ibu saibah | 10 | 102000 | 50000 | 52000
pak nana | 1 | 10000 | 0 | 10000
Kakak Nuri | 3 | 30000 | 10000 | 20000

## detail pesanan PO
pelanggan: ibu saibah
produk | harga | pcs | total 
kue nastar polos  | 10000 | 6 | 60000
kue nastar keju  | 12000 | 1 | 12000 
putri coklat | 10000 | 3 | 30000

total bayar: 102000
downpayment: 50000
sisa bayar: 102000

---
## selesaikan transaksi
---
transaksi bisa selesai saat sisa bayar di bayar, sehingga tersisa 0
summary pesanan
pelanggan: ibu saibah
produk | harga | pcs | total 
kue nastar polos  | 10000 | 6 | 60000
kue nastar keju  | 12000 | 1 | 12000 
putri coklat | 10000 | 3 | 30000

total bayar: 102000
downpayment: 50000
sisa bayar: 52000
bayar: 52000
sisa: 0

ini akan memberikan mengubah table pesanan po menjadi
Title | Pelanggan | tanggal | produk | item | terima_item | status | 
PO Inna Cookies | ibu saibah | 2025-01-12 12:03 | kue nastar polos | 3 | 3 | complete | 
PO Inna Cookies | Pak Nana | 2025-01-12 13:05 | sagu keju  | 1  | 0 | out of stock
PO Inna Cookies | ibu saibah | 2025-01-13 13:05 | kue nastar keju  | 1 | 1 | complete 
PO Inna Cookies | Kakak Nuri | 2025-01-13 17:03 | putri coklat | 3  | 0 | waiting
PO Inna Cookies | ibu saibah | 2025-01-14 17:03 | putri coklat | 3  | 2 | not complete
PO Inna Cookies | ibu saibah | 2025-01-15 18:03 | kue nastar polos | 3  | 3 | complete

status memiliki value :
waiting: setiap request po yg masuk default status ini
complete: jumlah barang sesuai item request, terima_item = item
out of stock: request po di tidak berhasil karena barang habis, maka terima_item = 0
not complete: barang kurang, terima_item < item
cancel: walau dilarang cancel request, tapi kita tetap perlu menyiapkan keadaan ini


## halaman complete-transaction :
berisi

table data pelanggan
table pesanan, 
table pesanan ini berisi 

produk, jumlah pesanan atau item_quantity,  receive quantity, harga barang, total (receive quantity * harga barang)

di bawah nya ada jumlah total yang harus di bayar yaitu
sum column total di kurangi pembayaran down payment


---
## misc:
---
* pelanggan dapat memesan lebih banyak dari stok
misal stock produk putri coklat ada 5 tapi permintaan ada 6
Title | Pelanggan | tanggal | produk | item
PO Inna Cookies | Kakak Nuri | 2025-01-13 17:03 | putri coklat | 3 
PO Inna Cookies | ibu saibah | 2025-01-14 17:03 | putri coklat | 3 

* jumlah produk dapat di edit
misal produk kue nastar keju, awal 10, lalu kita masukan 5, dengan tanggal edit dan alasan
ubah stok
produk | tanggal | jumlah awal | penyesuaian | jumlah akhir | alasan
putri coklat | 2025-01-14 17:03 | 5 | 1 | 6 | penyesuaian stok
kue nastar keju | 2025-01-14 17:03 | 10 | -5 | 5 | penyesuain permintaan

* misalkan ada produk yg stok nya lebih sedikit dari permintaan,
maka produk akan di prioritaskan kepada pemesan yang lebih dahulu
pada table pelanggan PO, karena produk putri coklat ada 5, dan request ada 6

kita lihat table pelanggan PO
Title | Pelanggan | tanggal | produk | item
...
PO Inna Cookies | Kakak Nuri | 2025-01-13 17:03 | putri coklat | 3 
PO Inna Cookies | ibu saibah | 2025-01-14 17:03 | putri coklat | 3 
...
pelanggan kakak nuri mendapat 3 item putri coklat
dan ibu saibah hanya mendapat 2 item putri coklat


# distribusi stock

---
# data master
---
- pelanggan
- purcase order (ex: OPEN PO Edamame untuk tanggal 21, OPEN PO Tahu untuk tanggal 23 bulan 12)
- produk
- Pegawai (yang menuliskan request PO)

---
# Menu Sidabar
---
- Dashboard => chart
- Purchase Order => berisi purcase order yg masih berlaku
- Data Master
    * pegawai
    * product
    * pelanggan
    * purcase order


# software
* laravel
* gunakan nginx sebagai proxy
* docker compose
* database postgresql atau mariadb, pilihkan yg terbaik
* 

# command
kita menggunakan docker compose sebagai base development
perhatikan docker-compose.yaml file 
dan perhatikan file Makefile

untuk menjalankan artisan
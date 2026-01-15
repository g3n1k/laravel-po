koreksi pada bagian purcase order master

http://localhost:8000/master/purchase-orders/create
saat sebuah purcase order dibuat, kita harus memilih produk apa yang akan di jual pada purcase order ini

http://localhost:8000/master/purchase-orders/1
pada view purcase order, kita bisa melihat produk-produk apa saja pada purcase order ini

http://localhost:8000/master/purchase-orders/1/edit
di edit, kita bisa menambah atau mengurangi jenis produk pada purcase order


http://localhost:8000/po/1/customers/create
produk yang ada disini, muncul dari pilihan produk yang kita buat pada master purcase order


pada halaman detail purcase order, 
table "produk dalam oder" tambah kan column jumlah produk di distribusi

pada column po barang, kita butuh column id bayar, sehingga pembeli bisa membeli lagi saat sudah membayar, open new transaction

tampilkan nama user yg melakukan pada log aktivitas 


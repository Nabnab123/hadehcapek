<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>Penjualan Barang</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
   <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>
</html>

<?php
   $kode = $_GET['kodepj'];
   if(isset($_POST['simpan'])){
     
   
       // SIMPAN DATA PENJUALAN
       $date = date("Y-m-d");
       $id_konsumen = $_POST['konsumen'];
       $bayar = $_POST['bayar'];
       $kembali = $_POST['kembali'];
   
   
        $tampil = $koneksi->query("INSERT into tb_penjualan (kode_penjualan,tgl_penjualan,id_konsumen,bayar,kembali) values ('$kode','$date','$id_konsumen','$bayar','$kembali')");

        $id = mysqli_insert_id($koneksi);
   
        if ($tampil) {
          ?>

          <script>alert('data berhasil disimpan ');
                        window.location="page/penjualan/struk.php?id=<?= $id ?>";</script>
        <?php
        }
   
       $barang =  $koneksi->query("select * from tb_penjualan_detail where kode_penjualan='$kode'");
   
         while ($data_barang = $barang->fetch_assoc()) {
   
               $koneksi->query("UPDATE tb_barang SET stok=stok -'$data_barang[jumlah]' WHERE kode_barcode='$data_barang[kode_barcode]'");
               echo "<script>alert('data tidak mencukupi ');</script>";
   
           }
          
         }

   
     if(isset($_POST['tambahkan'])){
       // TAMBAH BARANG KE PENJUALAN
       $kode = $_GET['kodepj'];
       $kode_barcode = $_POST['kode_barcode'];
       $konsumen = $_POST['konsumen'];
       $total_bayar = $_POST['total_bayar'];
       $jumlah = $_POST['jumlah'];
       $diskon = $_POST['diskon'];
       $potongan = $_POST['potongan'];
       $s_total = $_POST['s_total'];
       
      
       $koneksi->query("insert into tb_penjualan_detail(kode_penjualan,kode_barcode,jumlah,diskon,potongan, total) values ('$kode','$kode_barcode','$jumlah','$diskon','$potongan','$s_total')");
     }
    
   ?>
<div class="card">
   <div class="header">
      <h2>Data barang</h2>
   </div>
   <div class="body">
      <div class="row">
         <div class="col-md-6 cols-xs-2">
            <div class="table-responsive">
                                 <table class="table table-bordered table-striped table-hover js-basic-example dataTable">
                                       <thead>
                                          <tr>
                                             <th>Kode Barcode</th>
                                             <th>Nama Barang</th>
                                          </tr>
                                       </thead>
                                       
                                       <tbody>
                                          <?php

                                             $sql = $koneksi->query("select * from tb_barang");

                                             while ($data = $sql->fetch_assoc()) {
                                             
                                          ?>
                                          <tr>
                                             <td>
                                             <input type='text' value="<?php echo $data['kode_barcode']?>" id='copyText' readonly/>
                                             <!-- html button sederhana -->
                                             <button id="copyBtn" onclick='copy()'>Copy</button>

                                             </td>
                                           
                                             <td><?php echo $data['nama_barang']?></td>
                                          </tr>

                                          <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
                                          <script>
                                                const copyBtn = document.getElementById('copyBtn')
                                                const copyText = document.getElementById('copyText')
                                                
                                                copyBtn.onclick = () => {
                                                   copyText.select();    // Selects the text inside the input
                                                   document.execCommand('copy');    // Simply copies the selected text to clipboard 
                                                   Swal.fire({         //displays a pop up with sweetalert
                                                      icon: 'success',
                                                      title: 'Text copied to clipboard',
                                                      showConfirmButton: false,
                                                      timer: 1000
                                                   });
                                                }
                                          </script>
                                          <?php } ?>
                                       </tbody>
                                 </table>
            </div>
         </div>


         <form method="POST">
            <div class="col-md-4 cols-xs-4">
               <div class="form-group">
                  <label>No. Nota</label>
                  <input type="text" name="kode" value="<?php echo $kode; ?>" class="form-control" readonly style=" font-size: 16px; color: blue; font-weight: bold; "/>
               </div>
               <div class="form-group">
                  <label>Kode Barcode Barang</label>
                  <select name="kode_barcode" class="form-control js-example-basic-single" autofocus="" onchange="hitung()">
                  <?php 
                  $kodebrg = $koneksi->query("select * from tb_barang order by nama_barang");
                  while ($d_barang = $kodebrg->fetch_assoc()) {
                    echo "
                      <option value='$d_barang[kode_barcode]' >$d_barang[kode_barcode] | $d_barang[nama_barang]</option>
                    ";
                  }
                  ?>
               </select>
                  <!--<input type="text" name="kode_barcode"  class="form-control" autofocus="" onkeyup="hitung()" placeholder="0012920023" style="font-size: 20px;"/>-->
               </div>
              
            </div>
            <div class="col-md-6 col-xs-6 text-right">      
               <label>Harga Barang :</label>
               <input type="text" readonly="" style="text-align: right;" name="total_bayar" id="total_bayar" onkeyup="hitungSubTotal(detail_barang)" value="<?php echo $total_bayar;?>" >
            </div>
            <div class="col-md-6 col-xs-6 text-right">
               <label>Jumlah : </label>
               <input type="number" style="text-align: right;" name="jumlah" id="jumlah" min="1" value="" onkeyup="hitungSubTotal(detail_barang)" required>
            </div>
            <div class="col-md-6 col-xs-6 text-right">
               <label>Diskon (%) : </label>
               <input type="number" style="text-align: right;" name="diskon" step=".1" id="diskon" value="0" onkeyup="hitungSubTotal(detail_barang)" required>
            </div>
            <div class="col-md-6 col-xs-6 text-right">
               <label>Potongan Diskon : </label>
               <input type="number" style="text-align: right;" name="potongan" id="potongan" required>
            </div>
            <div class="col-md-6 col-xs-6 text-right">
               <label>Sub Total : </label>
               <input type="number" style="text-align: right;" name="s_total" id="s_total" required readonly>
            </div>
            <div class="col-md-6 col-xs-6 text-right">
            <div class="form-group">
                  <input type="submit" name="tambahkan" value="Tambahkan" class="btn btn-primary">
               </div>
            </div>
            <!--  <div class="col-md-6 col-xs-12 text-right">
               <input type="submit" name="simpan" value="Simpan" class="btn btn-primary">
               
               
               <input type="submit" name="simpan" value="Batal" class="btn btn-danger">
               
               <input type="submit" name="simpan_pj" value="Cetak" class="btn btn-success">
               
               </div> -->
      </div>
   </div>
   </form>
</div>
</div>
</div>
<div class="card">
   <div class="header">
      <h2>
         DAFTAR BELANJAAN
      </h2>
   </div>
   <div class="body">
      <div class="table-responsive">
         <table class="table table-striped ">
            <thead>
               <tr>
                  <th>No</th>
                  <th>Kode Barang</th>
                  <th>Nama Barang</th>
                  <th>Harga</th>
                  <th>Jumlah</th>
                  <th>Total</th>
                  <th>Aksi</th>
               </tr>
            </thead>
            <tbody>
               <?php
                  $no = 1;
                  $total_bayar = 0;
                  $sql = $koneksi->query("select a.*, b.nama_barang , a.jumlah, b.harga_jual from tb_penjualan_detail a JOIN tb_barang b ON a.kode_barcode = b.kode_barcode WHERE a.kode_penjualan = '".$_GET['kodepj']."'");
                  
                  while ($data = $sql->fetch_assoc()) {
                    
                  $total_bayar += $data['total'];
                  ?>
               <tr>
                  <td><?php echo $no++;?></td>
                  <td><?php echo $data['kode_barcode']?></td>
                  <td><?php echo $data['nama_barang']?></td>
                  <td><?php echo "Rp. ".number_format($data['harga_jual'])?></td>
                  <td><?php echo number_format($data['jumlah'])?></td>
                  <td><?php echo "Rp. ".number_format($data['total'])?></td>
                  <td>
                     <a onclick="return confirm('Apakah Anda Yakin Ingin Menghapus Data ini !')" href="?kodepj=<?=$_GET['kodepj']?>&page=penjualan&aksi=delete&id=<?php echo $data['id']?>" class="btn btn-danger" >Delete</a>
                  </td>
               </tr>
               <?php 
                  }    
                  ?>
            </tbody>
         </table>
      </div>
   </div>
</div>
</div>
<div class="card">
   <div class="header">
   </div>
   <div class="body">
      <form method="POST" action="" target="_blank" >
         <div class="row">
            <div class="col-md-6 col-xs-12 text-right">
               Nama Konsumen
            </div>
            <div class="col-md-6 col-xs-12">
               <select name="konsumen" class="form-control">
               <?php 
                  $konsumen = $koneksi->query("select * from tb_konsumen order by kode_konsumen");
                  while ($d_konsumen = $konsumen->fetch_assoc()) {
                    echo "
                      <option value='$d_konsumen[kode_konsumen]'>$d_konsumen[nama]</option>
                    ";
                  }
                  ?>
               </select>
            </div>
            <div class="col-md-6 col-xs-12 text-right">
               Total
            </div>
            <div class="col-md-6 col-xs-12 text-right">
               <?php echo $total_bayar; ?>
               <input type="hidden" name="total_bayar" value="<?php echo $total_bayar; ?>" />
            </div>
            <div class="col-md-6 col-xs-12 text-right" >
               Bayar
            </div>
            <div class="col-md-6 col-xs-12 text-right">
               <input type="number" name="bayar" id="bayar" class="form-control" />
            </div>
            <div class="col-md-6 col-xs-12 text-right">
               Kembali
            </div>
            <div class="col-md-6 col-xs-12 text-right">
               <input type="number" name="kembali" id="kembali" class="form-control" />
            </div>
         </div>
         <input type="submit" name="simpan" value="Print" class="btn btn-info">
         <!-- <input type="submit" name="batal" value="Batal" class="btn btn-danger"> -->
         <!-- <input type="submit" name="struk" value="Cetak" class="btn btn-info"> -->
   </div>
   </form>        
</div>
<script type="text/javascript">
   var detail_barang = null;
   function hitung(){
     var kode_barcode = document.getElementsByName("kode_barcode")[0].value;
     if(kode_barcode != "")
     {
       // Jika barcode sudah terisi, maka ambil data barangnya
       axios.get("page/penjualan/ambil-detail-barang.php?kode_barcode=" + kode_barcode)
         .then(function(res){
           detail_barang = res.data;

           if(detail_barang == null)
           {
             document.getElementsByName("total_bayar")[0].value = 1;
           }
           else
           {
             document.getElementsByName("total_bayar")[0].value = detail_barang.harga_jual;
           }
           hitungSubTotal(detail_barang);
         })
     }
     
   }
   function hitungSubTotal(detail_barang_sekarang)
   {
     var jumlah = parseInt(document.getElementById('jumlah').value);
     detail_barang_sekarang.stok = parseInt(detail_barang.stok);

     // cek jumlah yang diketik apakah dibawah stok atau tidak
     if(jumlah > detail_barang_sekarang.stok)
     {
        alert("Stok kurang");
        document.getElementById('jumlah').value = 0;  
     }
     else if(jumlah == 0)
     {
      alert("Jumlah barang tidak boleh kosong");
      document.getElementById('jumlah').value = 1;  
     }
     else
     {
       var total_bayar = document.getElementById('total_bayar').value * jumlah;
       var diskon = document.getElementById('diskon').value;
       var diskon_pot = parseInt(total_bayar) * parseFloat(diskon) / parseInt(100);
       if (!isNaN(diskon_pot)) {
         var potongan = document.getElementById('potongan').value = diskon_pot;
       }
       var sub_total = parseInt(total_bayar) - parseInt(potongan);
       if (!isNaN(sub_total)) {
         var s_total = document.getElementById('s_total').value = sub_total;
       }
     }
   }
   function hitungTotal(){
     var total_bayar = document.getElementsByName('total_bayar')[1].value;
     var bayar = document.getElementById('bayar').value ;
     var kembali = bayar - total_bayar;
     document.getElementById("kembali").value = kembali;
   }
   
   document.getElementById("bayar").addEventListener("keyup", hitungTotal)

</script>


<script type="text/javascript">
   $(document).ready(function() {
    $('.js-example-basic-single').select2();
});
</script>

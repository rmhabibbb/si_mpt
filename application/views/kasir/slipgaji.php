<style type="text/css">
    page {
      background: white;
      display: block;
      margin: 0 auto;
      margin-bottom: 0.5cm;
      box-shadow: 0 0 0.5cm rgba(0,0,0,0.5);
    }
    page[size="A4"] {  
      width: 21cm;
      height: 29.7cm; 
    }
    @media print {
      body, page {
        margin: 0;
        box-shadow: 0;
      }
    }
</style>
<section class="content">
    <div class="container-fluid">
            <?= $this->session->flashdata('msg') ?>
        <div class="row clearfix">
                 
            <div class="col-xs-12   col-sm-12  col-md-12   col-lg-12 ">
               
                <div class="card">
                    <page size="A4" id="printableArea"> 
                      <div class="header">
                            <center><h3>SMP 1 PGRI PALEMBANG</h3></center>                          
                        </div>
                        <div class="body"> 
                            <div class="table-responsive">
                            
                            <table class="table table-bordered table-striped table-hover" style="max-height: 300px">
                                <tr>
                                    <td colspan="2" style="padding-bottom: 0">
                                        <table class="table table-bordered " > 
                                            <tr>
                                                <th>NIP - Nama Lengkap</th>
                                                <th><?=$guru->nip?> - <?=$guru->nama?></th>
                                            </tr>
                                            <tr >
                                                <th>Bulan/Tahun</th>
                                                <th ><?=$laporan->bulan?>/<?=$laporan->tahun?></th>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 50%" style="padding-bottom: 0">
                                        <table class="table table-bordered table-striped table-hover" style="max-height: 300px">

                                            <tbody>
                                                
                                                 <tr>
                                                     <th   colspan="2"> 
                                                            <center>
                                                                 Gaji yang diterima 
                                                            </center>
                                                     </th> 
                                                 </tr>
                                                  
                                                
                                                 <tr>
                                                     <th style="width: 50%">
                                                         Jam Mengajar
                                                     </th>
                                                     <td> 
                                                       Rp <?= number_format($detail->jam_mengajar,0,'','.') ?> 
                                                     </td>
                                                 </tr>  
                                                 <tr>
                                                     <th style="width: 30%">
                                                         Transport
                                                     </th>
                                                     <td> 
                                                       Rp <?= number_format($detail->transport,0,'','.') ?> 
                                                     </td>
                                                 </tr>  
                                                  
                                                 <tr>
                                                     <th style="width: 30%">
                                                         Gaji Pokok
                                                     </th>
                                                     <td> 
                                                       Rp <?= number_format($detail->gaji_pokok,0,'','.') ?> 
                                                     </td>
                                                 </tr> 
                                                 <tr>
                                                     <th style="width: 30%">
                                                         Wali Kelas
                                                     </th>
                                                     <td> 
                                                       
                                                       Rp <?= number_format($detail->wali_kelas,0,'','.') ?> 
                                                     </td>
                                                 </tr> 
                                                 <tr>
                                                     <th style="width: 30%">
                                                         Piket
                                                     </th>
                                                     <td> 
                                                       
                                                       Rp <?= number_format($detail->piket,0,'','.') ?> 
                                                     </td>
                                                 </tr>
                                                  <tr>
                                                     <th style="width: 30%">
                                                         Jumlah
                                                     </th>
                                                     <th> 
                                                       <b> 
                                                        Rp <?= number_format($detail->jam_mengajar+$detail->gaji_pokok+$detail->transport+$detail->wali_kelas+ $detail->wali_kelas+$detail->piket,0,'','.') ?> 
                                                       </b>
                                                     </th>
                                                 </tr>  
                                            </tbody>

                                        </table> 
                                    </td>
                                    <td >
                                        <table class="table table-bordered table-striped table-hover" style="max-height: 300px">

                                            <tbody>
                                                   
                                                <tr>
                                                     <th   colspan="2">
                                                         <center>
                                                             Potongan Gaji
                                                         </center>
                                                     </th> 
                                                 </tr>
                                                 <tr>
                                                     <th style="width: 50%">
                                                         Bon
                                                     </th>
                                                     <td> 
                                                       Rp <?= number_format($detail->bon,0,'','.') ?> 
                                                     </td>
                                                 </tr> 
                                                 <tr>
                                                     <th style="width: 30%">
                                                         Kop. Sekolah
                                                     </th>
                                                     <td> 
                                                       
                                                       Rp <?= number_format($detail->koprasi_sekolah,0,'','.') ?> 
                                                     </td>
                                                 </tr>  

                                                 <tr>
                                                     <th style="width: 30%">
                                                         Kop. YPLP
                                                     </th>
                                                     <td> 
                                                       Rp <?= number_format($detail->koprasi_yplp,0,'','.') ?> 
                                                     </td>
                                                 </tr> 
                                                 <tr>
                                                     <th style="width: 30%">
                                                         Uang Amal
                                                     </th>
                                                     <td> 
                                                       Rp <?= number_format($detail->uang_amal,0,'','.') ?> 
                                                     </td>
                                                 </tr> 
                                                 <tr>
                                                     <th style="width: 30%">
                                                         Iuran PGRI
                                                     </th>
                                                     <td> 
                                                       Rp <?= number_format($detail->iuran_pgri,0,'','.') ?> 
                                                     </td>
                                                 </tr>  
                                                 <tr>
                                                     <th style="width: 30%">
                                                         Jumlah
                                                     </th>
                                                     <th> 
                                                       <b> 
                                                        Rp <?= number_format($detail->bon+$detail->uang_amal+$detail->koprasi_yplp+$detail->koprasi_sekolah+$detail->iuran_pgri,0,'','.') ?> 
                                                       </b>
                                                     </th>
                                                 </tr>   
                                            </tbody>

                                        </table> 
                                    </td>
                                </tr> 
                                <tr>
                                    <th colspan="2">
                                        <center>Rp <?= number_format($detail->total,0,'','.')?></center>
                                    </th>
                                </tr>
                            </table>
                            <div style="margin-left: 63%">
                                 Palembang,  ..............   20...<br>
                                Bendahara,<br> <br><br>
                                ......................
                            </div>
                            </div>
                        </div> 
                    </page> 
                <center>
                    <input type="button" class="btn btn-lg bg-indigo" onclick="printDiv('printableArea')" value="CETAK" />
                </center>
                </div>

            </div>
            </div>
           
          </div>
        </div>
    </section>
 

<script type="text/javascript">
    function printDiv(divName) {
     var printContents = document.getElementById(divName).innerHTML;
     var originalContents = document.body.innerHTML;

     document.body.innerHTML = printContents;

     window.print();

     document.body.innerHTML = originalContents;
}
</script>
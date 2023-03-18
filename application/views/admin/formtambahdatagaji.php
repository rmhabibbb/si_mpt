
    
<section class="content">
    <div class="container-fluid">
            <?= $this->session->flashdata('msg') ?>
        <div class="row clearfix">
                 
            <div class="col-xs-12   col-sm-12  col-md-12   col-lg-12 ">
               
                <div class="card">
                      <div class="header">
                            <center><h2>FORM LAPORAN GAJI BULANAN</h2></center>                          
                        </div>
                        <div class="body"> 

                            <?= form_open_multipart('bendahara/prosesgaji/') ?>
    
                            <fieldset> 
                                <div class="form-group">
                                    <div class="form-line">
                                         <div class="row">
                                             <div class="col-md-3">
                                                 <label class="control-label">ID Laporan</label>
                                                 <input type="number" name="id_laporan" class="form-control"  readonly value="<?=$laporan->id_laporan?>"  required  >
                                                 
                                             </div>  

                                             <div class="col-md-3">
                                                 <label class="control-label">Bulan/Tahun</label>
                                                 <input type="text"  class="form-control"  readonly value="<?=$laporan->bulan?>/<?=$laporan->tahun?>"  required   >
                                                 
                                             </div>  
                                             
                                             <div class="col-md-3">
                                                 <label class="control-label">NIP</label>
                                                 <input type="text" class="form-control" name="nip"  readonly value="<?=$guru->nip?>"  required   >
                                                 
                                             </div> 

                                             <div class="col-md-3">
                                                 <label class="control-label">Nama Lengkap</label>
                                                 <input type="text"  class="form-control" readonly value="<?=$guru->nama?>"  required  >
                                                 
                                             </div> 
                                         </div> 

                                   </div>
                                 </div> 
                                
                            </fieldset> 
                            <table class="table table-bordered table-striped table-hover" style="max-height: 300px">

                                <tbody>
                                    
                                     <tr>
                                         <th style="width: 30%" colspan="2">
                                             <center>
                                                 Gaji yang diterima
                                             </center>
                                         </th> 
                                     </tr>
                                      
                                    
                                     <tr>
                                         <th style="width: 30%">
                                             Jam Mengajar
                                         </th>
                                         <td> 
                                           Rp <?= number_format($njammengajar * 25000,0,'','.') ?>

                                           <input type="hidden" name="jam_mengajar"  id="jam_mengajar"   value="<?=$njammengajar * 25000?>">
                                         </td>
                                     </tr>  
                                     <tr>
                                         <th style="width: 30%">
                                             Transport
                                         </th>
                                         <td> 
                                           <input type="number" name="transport" id="transport" min="0"  class="form-control" placeholder="Masukkan Uang Transport (Bulan)" value="0">
                                         </td>
                                     </tr>   
                                     <tr>
                                         <th style="width: 30%">
                                             Gaji Pokok
                                         </th>
                                         <td> 
                                           Rp <?= number_format($gajipokok,0,'','.') ?>
                                           <input type="hidden" name="gaji_pokok" id="gaji_pokok"  required  value="<?=$gajipokok?>">
                                         </td>
                                     </tr> 
                                     <tr>
                                         <th style="width: 30%">
                                             Wali Kelas
                                         </th>
                                         <td> 
                                           Rp <?= number_format($walikelas,0,'','.') ?>
                                           <input type="hidden" name="wali_kelas" id="wali_kelas"  required  value="<?=$walikelas?>">
                                         </td>
                                     </tr> 
                                     <tr>
                                         <th style="width: 30%">
                                             Piket
                                         </th>
                                         <td> 
                                           Rp <?= number_format($piket,0,'','.') ?>
                                           <input type="hidden" name="piket" id="piket"  required  value="<?=$piket?>">
                                         </td>
                                     </tr>
                                      <tr>
                                         <th style="width: 30%">
                                             Jumlah
                                         </th>
                                         <th> 
                                           <div id="jumlah1" style="font-style: italic;"></div>
                                         </th>
                                     </tr>    
                                    <tr>
                                         <th style="width: 30%" colspan="2">
                                             <center>
                                                 Potongan Gaji
                                             </center>
                                         </th> 
                                     </tr>
                                     <tr>
                                         <th style="width: 30%">
                                             Bon
                                         </th>
                                         <td> 
                                           Rp <?= number_format($bon,0,'','.') ?>
                                           <input type="hidden" name="bon" id="bon"  required  value="<?=$bon?>">
                                         </td>
                                     </tr> 
                                     <tr>
                                         <th style="width: 30%">
                                             Koprasi Sekolah
                                         </th>
                                         <td> 
                                           Rp <?= number_format($ks,0,'','.') ?>
                                           <input type="hidden" name="kopresi_sekolah" id="kopresi_sekolah"  required  value="<?=$ks?>">
                                         </td>
                                     </tr>  
                                     <tr>
                                         <th style="width: 30%">
                                             Uang Amal
                                         </th>
                                         <td> 
                                           Rp <?= number_format($uangamal,0,'','.') ?>
                                           <input type="hidden" name="uang_amal"  id="uang_amal"  required  value="<?=$uangamal?>">
                                         </td>
                                     </tr> 
                                     <tr>
                                         <th style="width: 30%">
                                             Koprasi YPLP
                                         </th>
                                         <td> 
                                           Rp <?= number_format($YPLP,0,'','.') ?>
                                           <input type="hidden" name="kopresi_yplp" id="kopresi_yplp"  required  value="<?=$YPLP?>">
                                         </td>
                                     </tr> 
                                     <tr>
                                         <th style="width: 30%">
                                             Iuran PGRI
                                         </th>
                                         <td> 
                                           Rp <?= number_format($iuaran,0,'','.') ?>
                                           <input type="hidden" name="iuaran_pgri"  id="iuaran_pgri"  required  value="<?=$iuaran?>">
                                         </td>
                                     </tr> 
                                     <tr>
                                         <th style="width: 30%">
                                             Jumlah
                                         </th>
                                         <th> 
                                           <div id="jumlah2" style="font-style: italic;"></div>
                                         </th>
                                     </tr>  
                                     <tr>
                                         <th style="width: 30%"> 
                                             Total
                                         </th>
                                         <th> 
                                           <div id="total" style="font-style: italic;"></div>

                                         </th>
                                     </tr>  
                                </tbody>

                            </table> 
                              
                            <input type="hidden" id="fjumlah1" name="jumlah1">
                            <input type="hidden" id="fjumlah2" name="jumlah2">
                            <input type="hidden" id="ftotal" name="total">
                            <input  type="submit" class="btn bg-indigo btn-block btn-lg"  name="tambah" value="Submit">  <br><br>
                             <?php echo form_close() ?> 

     
                            </div>
                        </div>
                    </div>
            </div>
           
          </div>
        </div>
    </section>
 


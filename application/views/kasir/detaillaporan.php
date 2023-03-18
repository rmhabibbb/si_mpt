 
 <section class="content" >
    <div class="container-fluid"> 
        <?= $this->session->flashdata('msg') ?>
        <div class="row clearfix">
          
               
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                     
                    <div class="card">
                        
                        <div class="body"> 
                            <table class="table table-bordered table-striped table-hover" style="max-height: 600px">

                                <tbody>
                                    
                                     <tr>
                                         <th style="width: 60%">
                                             ID Laporan
                                         </th>
                                         <td> 
                                          
                                            <?=$laporan->id_laporan?>

                                         </td>
                                     </tr>
                                      
                                    
                                     <tr>
                                         <th style="width: 60%">
                                             Bulan/Tahun
                                         </th>
                                         <td> 
                                           <?=$laporan->bulan?>/<?=$laporan->tahun?>
                                         </td>
                                     </tr>  
                                     <tr>
                                         <th style="width: 60%">
                                             Tanggal Laporan
                                         </th>
                                         <td> 
                                           <?=date('d-m-Y' ,strtotime($laporan->tgl_buat)) ?>
                                         </td>
                                     </tr>  
                                     
                                     <tr>
                                         <th style="width: 60%">
                                             Status Laporan
                                         </th>
                                         <td> 
                                           <?php  
                                             if ($laporan->status == 0) {
                                               echo "Draft";
                                             }else{
                                              echo "Selesai";
                                             }
                                           ?>
                                         </td>
                                     </tr>   
                                   
                                </tbody>

                            </table> 
                            <?php if ($laporan->status == 0) { ?> 
                            <center>
                            <a data-toggle="modal" data-target="#selesai"  href=""><button class="btn bg-indigo">Proses</button></a> 
                             </center>  
                             <?php } ?> 
                         </div>
                    </div>
                    <div class="card">
                      <div class="header">
                            <center><h2>DATA GAJI PEGAWAI</h2></center>                          
                        </div>
                        <div class="body">   

                            <?php if ($laporan->status == 0) { ?> 
                           <a   data-toggle="modal" data-target="#tambah"  href=""><button class="btn bg-indigo">Tambah Data</button></a> 

                            <div class="table-responsive">
                                 <table class="table table-bordered table-striped table-hover  js-basic-example dataTable">
                            <?php } else { ?>  
                            <div class="table-responsive">
                                 <table class="table table-bordered table-striped table-hover  js-exportable dataTable">
                            <?php } ?>
                                    <thead>
                                        <tr>   
                                            <th style="width: 10px">No.</th>
                                            <th>NIP</th> 
                                            <th>Nama Guru/Pegawai</th> 
                                            <th>Pendatapat Gaji</th>   
                                            <th>Potongan Gaji</th>   
                                            <th>Total Gaji</th>  

                                            <?php if ($laporan->status == 0) { ?> 
                                              <th>Aksi</th>   
                                            <?php } ?>
                                        </tr>
                                    </thead> 
                                    <tbody>
                                      <?php 
                                      $i = 1;
                                      foreach ($list_data as $row): ?>  
                                        <?php $k = $this->GuruPegawai_m->get_row(['nip' => $row->nip]); ?>
                                          <tr>    
                                              <td><center><?=$i++?></center></td>
                                              <td><?=$k->nip?></td>  
                                              <td><?=$k->nama?></td>   
                                              <td><?= number_format($row->jam_mengajar+ $row->gaji_pokok+$row->transport+$row->wali_kelas+$row->piket,0,'','.') ?></td> 
                                              <td><?= number_format($row->bon+$row->iuran_pgri+$row->koprasi_sekolah+$row->koprasi_yplp+$row->uang_amal,0,'','.') ?></td> 
                                              <th>
                                                <a  data-toggle="modal" data-target="#detail-<?=$row->id?>">
                                                  <?= number_format($row->total,0,'','.')?></th>
                                                </a>

                                              <?php if ($laporan->status == 0) { ?> 
                                              <td>
                                                <center> 
                                                    <a  data-toggle="modal" data-target="#delete-<?=$row->id?>">
                                                        <button class="btn  bg-red" style="margin-top: 3px">Hapus</button>
                                                    </a> 
                                                </center>  
                                              </td> 
                                              <?php } ?>    
                                          </tr>
                                      <?php  endforeach; ?>
                                    </tbody>
                                </table>

     
                            </div>
                        </div>
                    </div>
 
                    
                </div>
    </div>
</section>

<div class="modal fade" id="selesai" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
      <div class="modal-content">
          <div class="modal-header"> 
              <h4 class="modal-title" id="defaultModalLabel"><center>PROSES LAPORAN ?</center></h4>
          </div>

                        <div class="modal-body">
                         <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">

                                        <form action="<?= base_url('bendahara/proseslaporan')?>" method="Post" > 
                                        <input type="hidden" value="<?=$laporan->id_laporan?>" name="id_laporan">  
                                        <input  type="submit" class="btn bg-green btn-block "  name="proses" value="PROSES">
                                         
                                    </div>
                                     <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                          <button type="button"  class="btn bg-red btn-block" data-dismiss="modal">BATAL</button>
                                    </div>
                                        <?php echo form_close() ?> 
                                </div> 
                            </div> 
          </div> 
          
      </div>
  </div>
</div> 


 <div class="modal fade" id="tambah" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header"> 
                            <h4 class="modal-title" id="defaultModalLabel"><center>Pilih Guru/Pegawai</center></h4>
                        </div>
                        <div class="modal-body">
                          <form action="<?= base_url('bendahara/formtambahdatagaji')?>" method="Post"  >  
                          <input type="hidden" name="id_laporan" value="<?=$laporan->id_laporan?>">

                                 <table class="table table-bordered"> 
                                        <tr>   
                                            <th>Guru/Pegawai</th> 
                                            <th>
                                               <select class="form-control" name="nip" required>
                                                  <option value="">Pilih Guru</option>
                                                  <?php  foreach ($list_guru as $k): ?>  
                                                      <?php if ($this->DetailLaporan_m->get_num_row(['nip' => $k->nip , 'id_laporan' => $laporan->id_laporan]) == 0) {  ?>
                                                    <option value="<?=$k->nip?>"><?=$k->nama?></option>
                                                  <?php } endforeach; ?>
                                                </select>
                                            </th>  
                                        </tr>   
                                </table>
                         
                            <input  type="submit" class="btn bg-indigo btn-block"  name="Pilih" value="Pilih">  <br><br>
                  
                            <?php echo form_close() ?> 
                        </div> 
                        <div class="modal-footer"> 
                            <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
</div>  

<?php $i = 1; foreach ($list_data as $row): ?> 
    <div class="modal fade" id="detail-<?=$row->id?>" tabindex="-1" role="dialog">
          <div class="modal-dialog" role="document">
              <div class="modal-content"> 
                  <div class="modal-body"> 

                  <div class="table-responsive"> 
                    <table class="table table-bordered table-striped table-hover" style="max-height: 600px">

                                <tbody>
                                    
                                     <tr>
                                         <th style="width: 60%" colspan="2">
                                             <center>
                                                 Gaji yang diterima
                                             </center>
                                         </th> 
                                     </tr>
                                      
                                    
                                     <tr>
                                         <th style="width: 60%">
                                             Jam Mengajar
                                         </th>
                                         <td> 
                                           Rp <?= number_format($row->jam_mengajar,0,'','.') ?> 
                                         </td>
                                     </tr>  
                                     <tr>
                                         <th style="width: 60%">
                                             Transport
                                         </th>
                                         <td> 
                                           Rp <?= number_format($row->transport,0,'','.') ?> 
                                         </td>
                                     </tr>  
                                      
                                     <tr>
                                         <th style="width: 60%">
                                             Gaji Pokok
                                         </th>
                                         <td> 
                                           Rp <?= number_format($row->gaji_pokok,0,'','.') ?>  
                                         </td>
                                     </tr> 
                                     <tr>
                                         <th style="width: 60%">
                                             Wali Kelas
                                         </th>
                                         <td>  
                                           Rp <?= number_format($row->wali_kelas,0,'','.') ?> 
                                         </td>
                                     </tr> 
                                     <tr>
                                         <th style="width: 60%">
                                             Piket
                                         </th>
                                         <td> 
                                           Rp <?= number_format($row->piket,0,'','.') ?> 
                                         </td>
                                     </tr>    
                                    <tr>
                                         <th style="width: 60%" colspan="2">
                                             <center>
                                                 Potongan Gaji (-)
                                             </center>
                                         </th> 
                                     </tr>
                                     <tr>
                                         <th style="width: 60%">
                                             Bon
                                         </th>
                                         <td> 
                                           Rp <?= number_format($row->bon,0,'','.') ?> 
                                         </td>
                                     </tr> 
                                     <tr>
                                         <th style="width: 60%">
                                             Koprasi Sekolah
                                         </th>
                                         <td> 
                                           Rp <?= number_format($row->koprasi_sekolah,0,'','.') ?> 
                                         </td>
                                     </tr>  
                                     <tr>
                                         <th style="width: 60%">
                                             Uang Amal
                                         </th>
                                         <td> 
                                           Rp <?= number_format($row->uang_amal,0,'','.') ?> 
                                         </td>
                                     </tr> 
                                     <tr>
                                         <th style="width: 60%">
                                             Koprasi YPLP
                                         </th>
                                         <td>  
                                           Rp <?= number_format($row->koprasi_yplp,0,'','.') ?> 
                                         </td>
                                     </tr> 
                                     <tr>
                                         <th style="width: 60%">
                                             Iuran PGRI
                                         </th>
                                         <td> 
                                          
                                           Rp <?= number_format($row->iuran_pgri,0,'','.') ?> 
                                         </td>
                                     </tr> 
                                      
                                     <tr>
                                         <th style="width: 60%"> 
                                             Total
                                         </th>
                                         <th> 
                                           <div  style="font-style: italic;">
                                           Rp <?= number_format($row->total,0,'','.') ?> </div>

                                         </th>
                                     </tr>  
                                </tbody>

                    </table> 
                  </div>
                  </div> 
              </div>
          </div>
    </div>
    <div class="modal fade" id="delete-<?=$row->id?>" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header"> 
                            <h4 class="modal-title" id="defaultModalLabel"><center>Hapus Data Gaji Pegawai?</center></h4> 
                            
                        </div>
                        <div class="modal-body"> 
                       
                         <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">

                                        <form action="<?= base_url('bendahara/delete_dlaporan')?>" method="Post" >  
                                        <input type="hidden" value="<?=$row->id?>" name="id">  
                                        <input type="hidden" value="<?=$row->id_laporan?>" name="id_laporan">  
                                        <input  type="submit" class="btn bg-red btn-block "  name="hapus" value="Ya">
                                         
                                    </div>
                                     <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                          <button type="button"  class="btn bg-green btn-block" data-dismiss="modal">Tidak</button>
                                    </div>
                            <?php echo form_close() ?> 
                                </div>
                        </div> 
                    </div>
                </div>
    </div>
<?php endforeach; ?>
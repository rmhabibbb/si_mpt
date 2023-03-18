  
    <section class="content">
        
        <?= $this->session->flashdata('msg') ?>
          <div class="row"> 
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                       <div class="header"> 
                             <center><b>DATA LAPORAN GAJI GURU/PEGAWAI</b></center>
                        </div>
                        <div class="body">
                            <a   data-toggle="modal" data-target="#tambah"  href=""><button class="btn bg-indigo">Buat Laporan</button></a> 
 
                            <div class="table-responsive">
                             <table class="table table-bordered table-striped table-hover dataTable js-basic-example">
                              <thead>
                                    <tr>   
                                        <th>No.</th>
                                        <th>ID Laporan</th> 
                                        <th>Bulan/Tahun</th>  
                                        <th>Tanggal Laporan</th>     
                                        <th>Status</th>   
                                        <th>Aksi</th>   
                                    </tr>
                                </thead>  
                            
                                <?php $i = 1; foreach ($list_data as $row): ?> 
  
                                  <tr> 
                                    <th> <?=$i++?> </th>
                                    <td> <?=$row->id_laporan?> </td>  
                                    <td> <?=$row->bulan?>/<?=$row->tahun?> </td>  
                                    <td> <?=date('d-m-Y', strtotime($row->tgl_buat)) ?> </td> 
                                    <td>
                                        <?php if ($row->status == 0) {
                                            echo "draft";
                                        }else{
                                            echo "Selesai";
                                        } 
                                        ?>
                                    </td>
                                    <td>  
                                        <center>
                                            <a href="<?=base_url('bendahara/laporan/'.$row->id_laporan)?>" >
                                                <button class="btn  bg-indigo">Lihat Detail</button>
                                            </a>
                                            <?php if ($row->status == 0) { ?>
                                                <a  data-toggle="modal" data-target="#delete-<?=$row->id_laporan?>">
                                                    <button class="btn  bg-red" style="margin-top: 3px">Hapus</button>
                                                </a>
                                            <?php } ?>
                                        </center> 
                                    </td>
                                  </tr>


                                  <?php endforeach; ?>
                              </table> 
                           </div>

                        </div>
                    </div>
                </div>
            </div> 
       
    </section>


 <div class="modal fade" id="tambah" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header"> 
                            <h4 class="modal-title" id="defaultModalLabel"><center>Form Buat Laporan</center></h4>
                        </div>
                        <div class="modal-body">
                          <form action="<?= base_url('bendahara/add_laporan')?>" method="Post"  >  
                         
                                 <table class="table table-bordered"> 
                                        <tr>   
                                            <th>Bulan</th> 
                                            <th>
                                               <input type="number" class="form-control" name="bulan" value="<?=date('m')?>"  required autofocus  >
                                            </th>  
                                        </tr>  
                                        <tr>   
                                            <th>Tahun</th> 
                                            <th>
                                               <input type="number" class="form-control" name="tahun" value="<?=date('Y')?>" required autofocus  max="<?=date('Y')?>">
                                            </th>  
                                        </tr>  
                                </table>
                         
                        <input  type="submit" class="btn bg-indigo btn-block"  name="tambah" value="Tambah">  <br><br>
                  
                            <?php echo form_close() ?> 
                        </div> 
                        <div class="modal-footer"> 
                            <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
</div>  

 
<?php $i = 1; foreach ($list_data as $row): ?> 
 
    <div class="modal fade" id="delete-<?=$row->id_laporan?>" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header"> 
                            <h4 class="modal-title" id="defaultModalLabel"><center>Hapus draft laporan?</center></h4> 
                            
                        </div>
                        <div class="modal-body"> 
                       
                         <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">

                                        <form action="<?= base_url('bendahara/delete_laporan')?>" method="Post" >  
                                        <input type="hidden" value="<?=$row->id_laporan?>" name="id">  
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
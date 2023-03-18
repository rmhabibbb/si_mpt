  
    <section class="content">
        
        <?= $this->session->flashdata('msg') ?>
          <div class="row"> 
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                       <div class="header"> 
                             <center><b>DATA GAJI GOLONGAN</b></center>
                        </div>
                        <div class="body"> 

                            <div class="table-responsive">
                             <table class="table table-bordered table-striped table-hover dataTable js-basic-example">
                              <thead>
                                    <tr>   
                                        <th>ID Golongan</th> 
                                        <th>Nama Golongan</th>    
                                        <th>Gaji Pokok</th>      
                                        <th>Aksi</th>   
                                    </tr>
                                </thead>  
                            
                                <?php $i = 1; foreach ($list_data as $row): ?> 
  
                                  <tr> 
                                    <th> <?=$row->id_golongan?> </th>
                                    <td> <?=$row->nama_golongan?> </td>  
                                    <td> Rp <?= number_format($row->gaji_pokok,0,'','.') ?> </td>  
                                    <td> 
                                       <?php if ($row->id_golongan > 1) { ?>
                                             <center>
                                            <a data-toggle="modal" data-target="#edit-<?=$row->id_golongan?>" >
                                                <button class="btn  bg-indigo">Edit Gaji Pokok</button>
                                            </a> 
                                        </center>
                                    <?php } ?>
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

 
 
<?php $i = 1; foreach ($list_data as $row): ?> 
    <div class="modal fade" id="edit-<?=$row->id_golongan?>" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header"> 
                    <h4 class="modal-title" id="defaultModalLabel"><center>Form Edit Data</center></h4>
                </div>
                <div class="modal-body">
                  <form action="<?= base_url('bendahara/update_gajigolongan')?>" method="Post"  >  
                 
                         <table class="table table-bordered"> 
                                <tr>   
                                    <th>Nama Golongan</th> 
                                    <th> 
                                       <input type="hidden" required value="<?=$row->id_golongan?>" name="id" class="form-control" readonly>
                                       <?=$row->nama_golongan?>
                                    </th>  
                                </tr> 
                                <tr>   
                                    <th>Gaji Pokok</th> 
                                    <th>
                                       <input type="text" required value="<?=$row->gaji_pokok?>" name="gaji_pokok" class="form-control">
                                    </th>  
                                </tr>  
                        </table>
                 
                <input  type="submit" class="btn bg-indigo btn-block"   name="update" value="Edit">  <br><br>
          
                    <?php echo form_close() ?> 
                </div> 
                <div class="modal-footer"> 
                    <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
 
<?php endforeach; ?>
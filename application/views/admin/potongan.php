  
    <section class="content">
        
        <?= $this->session->flashdata('msg') ?>
          <div class="row"> 
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                       <div class="header"> 
                             <center><b>DATA POTONGAN GAJI GURU/PEGAWAI</b></center>
                        </div>
                        <div class="body">
                            <a   data-toggle="modal" data-target="#tambah"  href=""><button class="btn bg-indigo">Tambah Data </button></a> 

                            <a   data-toggle="modal" data-target="#tambah2"  href=""><button class="btn bg-indigo">Tambah Data ke Semua </button></a> 
                            <br><br>

                            <div class="table-responsive">
                             <table class="table table-bordered table-striped table-hover dataTable js-basic-example">
                              <thead>
                                    <tr>   
                                        <th>No.</th>
                                        <th>NIP</th> 
                                        <th>Nama Guru</th>  
                                        <th>Jenis Potongan</th>    
                                        <th>Nominal</th> 
                                        <th>Tanggal</th> 
                                        <th>Aksi</th>   
                                    </tr>
                                </thead>  
                            
                                <?php $i = 1; foreach ($list_data as $row): ?> 
  
                                  <tr> 
                                    <th> <?=$i++?> </th>
                                    <td> <?=$row->nip?> </td> 
                                    <td> <?=$this->GuruPegawai_m->get_row(['nip' => $row->nip])->nama?> </td> 
                                    <td> <?=$row->jenis?> </td> 
                                    <td> Rp <?= number_format($row->nominal,0,'','.') ?> </td> 
                                    <td> <?=date('d-m-Y', strtotime($row->tgl)) ?> </td> 
                                    <td>  
                                        <center>
                                            <a data-toggle="modal" data-target="#edit-<?=$row->id_potongan?>" >
                                                <button class="btn  bg-indigo">Edit</button>
                                            </a>
                                            <a  data-toggle="modal" data-target="#delete-<?=$row->id_potongan?>">
                                                <button class="btn  bg-red" style="margin-top: 3px">Hapus</button>
                                            </a>
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
                <h4 class="modal-title" id="defaultModalLabel"><center>Form Tambah Data</center></h4>
            </div>
            <div class="modal-body">
              <form action="<?= base_url('bendahara/add_potongan')?>" method="Post"  >  
             
                     <table class="table table-bordered">  
                            <tr>   
                                <th>Guru</th> 
                                <th>
                                   <select class="form-control" name="nip" required>
                                        <option value="">Pilih Guru</option>
                                        <?php  foreach ($list_guru as $k): ?>   
                                          <option value="<?=$k->nip?>"><?=$k->nama?></option>
                                        <?php  endforeach; ?>
                                    </select>
                                </th>  
                            </tr>  
                            
                            <tr>   
                                <th>Jenis Potongan</th> 
                                <th> 
                                    <select class="form-control" name="jenis" required>
                                        <option value="">Pilih Jenis Potongan</option>
                                        <?php 
                                         $list_jenis = ['Bon', 'Koprasi Sekolah', 'Uang Amal', 'Koprasi YPLP', 'Iuaran PGRI'];
                                         foreach ($list_jenis as $j): ?>   
                                          <option value="<?=$j?>"><?=$j?></option>
                                        <?php  endforeach; ?>
                                    </select>
                                </th>  
                            </tr>  
                            <tr>   
                                <th>Tanggal</th> 
                                <th> 
                                    <input type="date" name="tgl" value="<?=date('Y-m-d')?>" max="<?=date('Y-m-d')?>" required class="form-control">
                                </th>  
                            </tr>  
                            <tr>   
                                <th>Nominal</th> 
                                <th> 
                                    <input type="number" name="nominal" min="1" placeholder="Masukkan Nominal" required class="form-control">
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

<div class="modal fade" id="tambah2" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header"> 
                <h4 class="modal-title" id="defaultModalLabel"><center>Form Tambah Data</center></h4>
            </div>
            <div class="modal-body">
              <form action="<?= base_url('bendahara/add_potongan2')?>" method="Post"  >  
             
                     <table class="table table-bordered">   
                            <tr>   
                                <th>Jenis Potongan</th> 
                                <th> 
                                    <select class="form-control" name="jenis" required>
                                        <option value="">Pilih Jenis Potongan</option>
                                        <?php 
                                         $list_jenis = ['Bon', 'Koprasi Sekolah', 'Uang Amal', 'Koprasi YPLP', 'Iuaran PGRI'];
                                         foreach ($list_jenis as $j): ?>   
                                          <option value="<?=$j?>"><?=$j?></option>
                                        <?php  endforeach; ?>
                                    </select>
                                </th>  
                            </tr>  
                            <tr>   
                                <th>Tanggal</th> 
                                <th> 
                                    <input type="date" name="tgl" value="<?=date('Y-m-d')?>" max="<?=date('Y-m-d')?>" required class="form-control">
                                </th>  
                            </tr>  
                            <tr>   
                                <th>Nominal</th> 
                                <th> 
                                    <input type="number" name="nominal" min="1" placeholder="Masukkan Nominal" required class="form-control">
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
    <div class="modal fade" id="edit-<?=$row->id_potongan?>" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header"> 
                    <h4 class="modal-title" id="defaultModalLabel"><center>Form Edit Data potongan</center></h4>
                </div>
                <div class="modal-body">
                  <form action="<?= base_url('bendahara/update_potongan')?>" method="Post"  >  
                    <input type="hidden" name="id" value="<?=$row->id_potongan?>">
                         <table class="table table-bordered"> 

                            <tr>   
                                <th>Guru</th> 
                                <th>
                                   <?=$row->nip?> - <?=$this->GuruPegawai_m->get_row(['nip' => $row->nip])->nama?>   
                                </th>  
                            </tr>  
                            
                            <tr>   
                                <th>Jenis Potongan</th> 
                                <th> 
                                    <select class="form-control" name="jenis" required>
                                        <option value="<?=$row->jenis?>"> <?=$row->jenis?></option>
                                        <?php 
                                         $list_jenis = ['Bon', 'Koprasi Sekolah', 'Uang Amal', 'Koprasi YPLP', 'Iuaran PGRI'];
                                         foreach ($list_jenis as $j): ?>  
                                         <?php if($j != $row->jenis) { ?> 
                                          <option value="<?=$j?>"><?=$j?></option>
                                        <?php } endforeach; ?>
                                    </select>
                                </th>  
                            </tr>  
                            <tr>   
                                <th>Tanggal</th> 
                                <th> 
                                    <input type="date" name="tgl" value="<?=date('Y-m-d', strtotime($row->tgl))?>" max="<?=date('Y-m-d')?>" required class="form-control" >
                                </th>  
                            </tr>  
                            <tr>   
                                <th>Nominal</th> 
                                <th> 
                                    <input type="number" name="nominal" min="1" placeholder="Masukkan Nominal" required class="form-control" value="<?=$row->nominal?>">
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

    <div class="modal fade" id="delete-<?=$row->id_potongan?>" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header"> 
                            <h4 class="modal-title" id="defaultModalLabel"><center>Hapus data potongan?</center></h4> 
                            <center><span style="color :red"><i>Semua data yang terkait dengan data ini akan dihapus.</i></span></center>
                        </div>
                        <div class="modal-body"> 
                       
                         <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">

                                        <form action="<?= base_url('bendahara/delete_potongan')?>" method="Post" >  
                                        <input type="hidden" value="<?=$row->id_potongan?>" name="id">  
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
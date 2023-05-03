<?php
defined('BASEPATH') or exit('No direct script access allowed');


require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Pemilik extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->data['username'] = $this->session->userdata('username');
        $this->data['id_role']  = $this->session->userdata('id_role');
        if (!$this->data['username'] || ($this->data['id_role'] != 3)) {
            $this->session->set_flashdata('msg', '<div class="alert alert-danger alert-dismissable">Anda harus login dulu</div>');
            redirect('login');
            exit;
        }
        date_default_timezone_set("Asia/Jakarta");

        $this->load->model('Akun_m');
        $this->load->model('Kategori_m');
        $this->load->model('Barang_m');
        $this->load->model('Supplier_m');
        $this->load->model('Transaksi_m');
        $this->load->model('DetailTransaksi_m');
        $this->load->model('Supply_m');
        $this->load->model('LaporanStok_m');


        $this->data['profil'] = $this->Akun_m->get_row(['username' => $this->data['username']]);
    }
    public function index()
    {
        redirect('pemilik/laporan_penjualan');
    }


    // LAPORAN PENJUALAN

    public function laporan_penjualan()
    {
        $this->data['title']     = 'Laporan Penjualan';
        $this->data['index']     = 4.1;
        $this->data['link']     = 'laporan_penjualan';

        $this->data['content']     = 'pemilik/laporan/penjualan';
        $this->load->view('pemilik/template/layout', $this->data);
    }

    public function filterLaporan()
    {
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');
        $list = $this->Transaksi_m->get_datatables($start_date, $end_date);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $field) {

            $row = array();


            $row[] = $field->kd_transaksi;
            $row[] = ($field->nama_customer != NULL) ? $field->nama_customer : '-';
            $row[] = number_format($field->total_bayar, 2, '.', ',');
            $row[] = date('d-m-Y H:i', strtotime($field->tgl_transaksi));

            $row[] = '<a href="' . base_url('pemilik/detail_laporan/t/') . $field->kd_transaksi . '" class="btn btn-success me-2"><i class="fas fa-eye"></i></a>';

            $data[] = $row;
        }

        $output = array(
            "draw"                 => $_POST['draw'],
            "recordsTotal"         => $this->Transaksi_m->count_all($start_date, $end_date),
            "recordsFiltered"     => $this->Transaksi_m->count_filtered($start_date, $end_date),
            "data"                 => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
    }

    public function download_laporan_penjualan()
    {

        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');

        if ($start_date == "" && $end_date == "") {
            $filename         = 'Laporan_Penjualan - SEMUA';

            $data_penjualan = $this->Transaksi_m->get(['status' => 1]);
        } else {
            if ($start_date == $end_date) {
                $filename         = 'Laporan_Penjualan - ' . $start_date;

                $data_penjualan = $this->Transaksi_m->get(['DATE(tgl_transaksi)' => $start_date, 'status' => 1]);
            } else {
                $filename         = 'Laporan_Penjualan_' . $start_date . '-' . $end_date;

                $data_penjualan = $this->Transaksi_m->get(['DATE(tgl_transaksi) >=' => $start_date, 'DATE(tgl_transaksi) <=' => $end_date, 'status' => 1]);
            }
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();


        // Buat sebuah variabel untuk menampung pengaturan style dari header tabel
        $style_col = [
            'font' => ['bold' => true], // Set font nya jadi bold
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ],
            'borders' => [
                'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
                'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
                'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
                'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
            ]
        ];

        // Buat sebuah variabel untuk menampung pengaturan style dari isi tabel
        $style_row = [
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ],
            'borders' => [
                'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
                'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
                'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
                'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
            ]
        ];

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kode Transaksi');
        $sheet->setCellValue('C1', 'Tanggal Transaksi');
        $sheet->setCellValue('D1', 'Nama Customer');
        $sheet->setCellValue('E1', 'Kontak Customer');
        $sheet->setCellValue('F1', 'Alamat Customer');
        $sheet->setCellValue('G1', 'Total Bayar');
        $sheet->setCellValue('I1', 'Total Pendapatan Penjualan');

        $sheet->getStyle('A1')->applyFromArray($style_col);
        $sheet->getStyle('B1')->applyFromArray($style_col);
        $sheet->getStyle('C1')->applyFromArray($style_col);
        $sheet->getStyle('D1')->applyFromArray($style_col);
        $sheet->getStyle('E1')->applyFromArray($style_col);
        $sheet->getStyle('F1')->applyFromArray($style_col);
        $sheet->getStyle('G1')->applyFromArray($style_col);
        $sheet->getStyle('I1')->applyFromArray($style_col);
        $no   = 1;
        $rows = 2;
        $total = 0;
        foreach ($data_penjualan as $val) {
            $sheet->setCellValue('A' . $rows, $no);
            $sheet->setCellValue('B' . $rows, $val->kd_transaksi);
            $sheet->setCellValue('C' . $rows, date('d-m-Y', strtotime($val->tgl_transaksi)));
            $sheet->setCellValue('D' . $rows, ($val->nama_customer == "") ? '-' : $val->nama_customer);
            $sheet->setCellValue('E' . $rows, ($val->kontak_customer == "") ? '-' : $val->kontak_customer);
            $sheet->setCellValue('F' . $rows, ($val->alamat_customer == "") ? '-' : $val->alamat_customer);
            $sheet->setCellValue('G' . $rows, number_format($val->total_bayar, 2, ',', '.'));

            $sheet->getStyle('A' . $rows)->applyFromArray($style_row);
            $sheet->getStyle('B' . $rows)->applyFromArray($style_row);
            $sheet->getStyle('C' . $rows)->applyFromArray($style_row);
            $sheet->getStyle('D' . $rows)->applyFromArray($style_row);
            $sheet->getStyle('E' . $rows)->applyFromArray($style_row);
            $sheet->getStyle('F' . $rows)->applyFromArray($style_row);
            $sheet->getStyle('G' . $rows)->applyFromArray($style_row);

            $no++;
            $rows++;
            $total = $total + $val->total_bayar;
        }

        $sheet->setCellValue('I2', number_format($total, 2, ',', '.'));

        $sheet->getStyle('I2')->applyFromArray($style_row);

        // Set width kolom
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(30);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(40);

        $sheet->setTitle("Laporan Penjualan");

        // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }

    public function detail_laporan()
    {
        if ($this->uri->segment(3) == 't') {
            $id =  $this->uri->segment(4);
            if ($this->Transaksi_m->get_num_row(['kd_transaksi' => $id]) == 0) {
                $this->session->set_flashdata('warning', 'Data tidak ditemukan!');
                redirect('pemilik/laporan_penjualan');
                exit;
            }

            $this->data['trans'] = $this->Transaksi_m->get_row(['kd_transaksi' => $id]);
            $this->data['dtrans'] = $this->DetailTransaksi_m->view_transaksi_detail(['kd_transaksi' => $id]);


            $this->data['title']     = 'Detail Transaksi Laporan Penjualan';
            $this->data['index']     = 4.1;
            $this->data['link']     = 'laporan_penjualan';

            $this->data['content']     = 'pemilik/laporan/detail_laporan_penjualan';
            $this->load->view('pemilik/template/layout', $this->data);
        } elseif ($this->uri->segment(3) == 's') {
            $id =  $this->uri->segment(4);
            if ($this->Supply_m->get_num_row(['kd_transaksi' => $id]) == 0) {
                $this->session->set_flashdata('warning', 'Data tidak ditemukan!');
                redirect('pemilik/laporan_penjualan');
                exit;
            }

            $this->data['trans'] = $this->Supply_m->view_detail_supply(['kd_transaksi' => $id]);
            $this->data['dtrans'] = $this->DetailTransaksi_m->view_transaksi_detail(['kd_transaksi' => $id]);


            $this->data['title']     = 'Detail Transaksi Laporan Pembelian';
            $this->data['index']     = 4.2;
            $this->data['link']     = 'laporan_pembelian';

            $this->data['content']     = 'pemilik/laporan/detail_laporan_pembelian';
            $this->load->view('pemilik/template/layout', $this->data);
        } else {
            redirect('pemilik');
        }
    }

    // LAPORAN PENJUALAN

    // LAPORAN PEMBELIAN

    public function laporan_pembelian()
    {
        $this->data['title']     = 'Laporan Pembelian';
        $this->data['index']     = 4.2;
        $this->data['link']     = 'laporan_pembelian';

        $this->data['content']     = 'pemilik/laporan/pembelian';
        $this->load->view('pemilik/template/layout', $this->data);
    }

    public function filterLaporanSupply()
    {
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');
        $list = $this->Supply_m->get_datatables($start_date, $end_date);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $field) {

            $row = array();


            $row[] = $field->kd_transaksi;
            $row[] = ($field->nama_supplier != NULL) ? $field->nama_supplier : '-';
            $row[] = number_format($field->total_bayar, 2, '.', ',');
            $row[] = date('d-m-Y H:i', strtotime($field->tgl_transaksi));

            $row[] = '<a href="' . base_url('pemilik/detail_laporan/s/') . $field->kd_transaksi . '" class="btn btn-success me-2"><i class="fas fa-eye"></i></a>';

            $data[] = $row;
        }

        $output = array(
            "draw"                 => $_POST['draw'],
            "recordsTotal"         => $this->Supply_m->count_all($start_date, $end_date),
            "recordsFiltered"     => $this->Supply_m->count_filtered($start_date, $end_date),
            "data"                 => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
    }

    public function download_laporan_pembelian()
    {

        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');

        if ($start_date == "" && $end_date == "") {
            $filename         = 'Laporan_Pembelian - SEMUA';

            $data_pembelian = $this->Supply_m->view_transaksi_supply(['status' => 1]);
        } else {
            if ($start_date == $end_date) {
                $filename         = 'Laporan_Pembelian - ' . $start_date;

                $data_pembelian = $this->Supply_m->view_transaksi_supply(['DATE(tgl_transaksi)' => $start_date, 'status' => 1]);
            } else {
                $filename         = 'Laporan_Pembelian_' . $start_date . '-' . $end_date;

                $data_pembelian = $this->Supply_m->view_transaksi_supply(['DATE(tgl_transaksi) >=' => $start_date, 'DATE(tgl_transaksi) <=' => $end_date, 'status' => 1]);
            }
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();


        // Buat sebuah variabel untuk menampung pengaturan style dari header tabel
        $style_col = [
            'font' => ['bold' => true], // Set font nya jadi bold
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ],
            'borders' => [
                'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
                'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
                'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
                'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
            ]
        ];

        // Buat sebuah variabel untuk menampung pengaturan style dari isi tabel
        $style_row = [
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ],
            'borders' => [
                'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
                'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
                'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
                'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
            ]
        ];

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kode Transaksi');
        $sheet->setCellValue('C1', 'Tanggal Transaksi');
        $sheet->setCellValue('D1', 'Nama Supplier');
        $sheet->setCellValue('E1', 'Kontak Supplier');
        $sheet->setCellValue('F1', 'Alamat Supplier');
        $sheet->setCellValue('G1', 'Total Bayar');
        $sheet->setCellValue('I1', 'Total Harga Pembelian');

        $sheet->getStyle('A1')->applyFromArray($style_col);
        $sheet->getStyle('B1')->applyFromArray($style_col);
        $sheet->getStyle('C1')->applyFromArray($style_col);
        $sheet->getStyle('D1')->applyFromArray($style_col);
        $sheet->getStyle('E1')->applyFromArray($style_col);
        $sheet->getStyle('F1')->applyFromArray($style_col);
        $sheet->getStyle('G1')->applyFromArray($style_col);
        $sheet->getStyle('I1')->applyFromArray($style_col);
        $no   = 1;
        $rows = 2;
        $total = 0;
        foreach ($data_pembelian as $val) {
            $sheet->setCellValue('A' . $rows, $no);
            $sheet->setCellValue('B' . $rows, $val->kd_transaksi);
            $sheet->setCellValue('C' . $rows, date('d-m-Y', strtotime($val->tgl_transaksi)));
            $sheet->setCellValue('D' . $rows, ($val->nama_supplier == "") ? '-' : $val->nama_supplier);
            $sheet->setCellValue('E' . $rows, ($val->kontak_supplier == "") ? '-' : $val->kontak_supplier);
            $sheet->setCellValue('F' . $rows, ($val->alamat_supplier == "") ? '-' : $val->alamat_supplier);
            $sheet->setCellValue('G' . $rows, number_format($val->total_bayar, 2, ',', '.'));

            $sheet->getStyle('A' . $rows)->applyFromArray($style_row);
            $sheet->getStyle('B' . $rows)->applyFromArray($style_row);
            $sheet->getStyle('C' . $rows)->applyFromArray($style_row);
            $sheet->getStyle('D' . $rows)->applyFromArray($style_row);
            $sheet->getStyle('E' . $rows)->applyFromArray($style_row);
            $sheet->getStyle('F' . $rows)->applyFromArray($style_row);
            $sheet->getStyle('G' . $rows)->applyFromArray($style_row);

            $no++;
            $rows++;
            $total = $total + $val->total_bayar;
        }

        $sheet->setCellValue('I2', number_format($total, 2, ',', '.'));

        $sheet->getStyle('I2')->applyFromArray($style_row);

        // Set width kolom
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(30);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(40);

        $sheet->setTitle("Laporan Pembelian");

        // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }

    // LAPORAN PEMBELIAN

    // LAPORAN PEMBELIAN

    public function laporan_stok_barang()
    {
        $this->data['title']     = 'Laporan Stok Barang';
        $this->data['index']     = 4.3;
        $this->data['link']     = 'laporan_stok_barang';

        $this->data['content']     = 'pemilik/laporan/stok';
        $this->load->view('pemilik/template/layout', $this->data);
    }

    public function filterLaporanStok()
    {
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');
        $list = $this->LaporanStok_m->get_datatables($start_date, $end_date);

        if ($start_date == "" && $end_date == "") {
            $data_pembelian = $this->Supply_m->view_transaksi_supply(['status' => 1]);
        } else {
            if ($start_date == $end_date) {
                $data_pembelian = $this->Supply_m->view_transaksi_supply(['DATE(tgl_transaksi)' => $start_date, 'status' => 1]);
            } else {
                $data_pembelian = $this->Supply_m->view_transaksi_supply(['DATE(tgl_transaksi) >=' => $start_date, 'DATE(tgl_transaksi) <=' => $end_date, 'status' => 1]);
            }
        }

        if ($start_date == "" && $end_date == "") {
            $data_penjualan = $this->Transaksi_m->get(['status' => 1]);
        } else {
            if ($start_date == $end_date) {
                $data_penjualan = $this->Transaksi_m->get(['DATE(tgl_transaksi)' => $start_date, 'status' => 1]);
            } else {
                $data_penjualan = $this->Transaksi_m->get(['DATE(tgl_transaksi) >=' => $start_date, 'DATE(tgl_transaksi) <=' => $end_date, 'status' => 1]);
            }
        }



        $data = array();
        $no = $_POST['start'];
        foreach ($list as $field) {
            $n_beli = 0;
            $n_jual = 0;

            foreach ($data_pembelian as $tb) {
                $n_beli = $n_beli + $this->DetailTransaksi_m->get_sum_beli(['kd_transaksi' => $tb->kd_transaksi, 'id_barang' => $field->id_barang]);
            }

            foreach ($data_penjualan as $tb) {
                $n_jual = $n_jual + $this->DetailTransaksi_m->get_sum_jual(['kd_transaksi' => $tb->kd_transaksi, 'id_barang' => $field->id_barang]);
            }

            $row = array();


            $row[] = $field->id_barang;
            $row[] = ($field->nama_barang != NULL) ? $field->nama_barang : '-';
            $row[] = ($field->nama_kategori != NULL) ? $field->nama_kategori : '-';
            $row[] = ($field->ukuran != NULL) ? $field->ukuran : '-';
            $row[] = ($field->jenis != NULL) ? $field->jenis : '-';
            $row[] = ($field->deskripsi != NULL) ? $field->deskripsi : '-';
            $row[] = $n_beli;
            $row[] = $n_jual;

            $data[] = $row;
        }

        $output = array(
            "draw"                 => $_POST['draw'],
            "recordsTotal"         => $this->LaporanStok_m->count_all($start_date, $end_date),
            "recordsFiltered"     => $this->LaporanStok_m->count_filtered($start_date, $end_date),
            "data"                 => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
    }

    public function download_laporan_stok()
    {

        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');

        $list_barang = $this->Barang_m->view_barang('deleted_at', 'asc', []);

        if ($start_date == "" && $end_date == "") {
            $filename         = 'Laporan_Stok_Barang - SEMUA';

            $data_pembelian = $this->Supply_m->view_transaksi_supply(['status' => 1]);
            $data_penjualan = $this->Transaksi_m->get(['status' => 1]);
        } else {
            if ($start_date == $end_date) {
                $filename         = 'Laporan_Stok_Barang - ' . $start_date;

                $data_pembelian = $this->Supply_m->view_transaksi_supply(['DATE(tgl_transaksi)' => $start_date, 'status' => 1]);
                $data_penjualan = $this->Transaksi_m->get(['DATE(tgl_transaksi)' => $start_date, 'status' => 1]);
            } else {
                $filename         = 'Laporan_Stok_Barang_' . $start_date . '-' . $end_date;

                $data_pembelian = $this->Supply_m->view_transaksi_supply(['DATE(tgl_transaksi) >=' => $start_date, 'DATE(tgl_transaksi) <=' => $end_date, 'status' => 1]);
                $data_penjualan = $this->Transaksi_m->get(['DATE(tgl_transaksi) >=' => $start_date, 'DATE(tgl_transaksi) <=' => $end_date, 'status' => 1]);
            }
        }


        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();


        // Buat sebuah variabel untuk menampung pengaturan style dari header tabel
        $style_col = [
            'font' => ['bold' => true], // Set font nya jadi bold
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ],
            'borders' => [
                'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
                'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
                'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
                'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
            ]
        ];

        // Buat sebuah variabel untuk menampung pengaturan style dari isi tabel
        $style_row = [
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ],
            'borders' => [
                'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
                'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
                'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
                'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
            ]
        ];

        $sheet->setCellValue('A1', 'ID Barang');
        $sheet->setCellValue('B1', 'Nama Barang');
        $sheet->setCellValue('C1', 'Kategori');
        $sheet->setCellValue('D1', 'Ukuran');
        $sheet->setCellValue('E1', 'Jenis');
        $sheet->setCellValue('F1', 'Deskripsi');
        $sheet->setCellValue('G1', 'Jumlah Supply');
        $sheet->setCellValue('H1', 'Jumlah Terjual');

        $sheet->getStyle('A1')->applyFromArray($style_col);
        $sheet->getStyle('B1')->applyFromArray($style_col);
        $sheet->getStyle('C1')->applyFromArray($style_col);
        $sheet->getStyle('D1')->applyFromArray($style_col);
        $sheet->getStyle('E1')->applyFromArray($style_col);
        $sheet->getStyle('F1')->applyFromArray($style_col);
        $sheet->getStyle('G1')->applyFromArray($style_col);
        $sheet->getStyle('H1')->applyFromArray($style_col);
        $no   = 1;
        $rows = 2;
        $total = 0;
        foreach ($list_barang as $val) {

            $n_beli = 0;
            $n_jual = 0;

            foreach ($data_pembelian as $tb) {
                $n_beli = $n_beli + $this->DetailTransaksi_m->get_sum_beli(['kd_transaksi' => $tb->kd_transaksi, 'id_barang' => $val->id_barang]);
            }

            foreach ($data_penjualan as $tb) {
                $n_jual = $n_jual + $this->DetailTransaksi_m->get_sum_jual(['kd_transaksi' => $tb->kd_transaksi, 'id_barang' => $val->id_barang]);
            }

            $sheet->setCellValue('A' . $rows, $val->id_barang);
            $sheet->setCellValue('B' . $rows, $val->nama_barang);
            $sheet->setCellValue('C' . $rows, $val->nama_kategori);
            $sheet->setCellValue('D' . $rows, $val->ukuran);
            $sheet->setCellValue('E' . $rows, $val->jenis);
            $sheet->setCellValue('F' . $rows, $val->deskripsi);
            $sheet->setCellValue('G' . $rows, $n_beli);
            $sheet->setCellValue('H' . $rows, $n_jual);

            $sheet->getStyle('A' . $rows)->applyFromArray($style_row);
            $sheet->getStyle('B' . $rows)->applyFromArray($style_row);
            $sheet->getStyle('C' . $rows)->applyFromArray($style_row);
            $sheet->getStyle('D' . $rows)->applyFromArray($style_row);
            $sheet->getStyle('E' . $rows)->applyFromArray($style_row);
            $sheet->getStyle('F' . $rows)->applyFromArray($style_row);
            $sheet->getStyle('G' . $rows)->applyFromArray($style_row);
            $sheet->getStyle('H' . $rows)->applyFromArray($style_row);

            $no++;
            $rows++;
        }

        // Set width kolom
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(30);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(20);

        $sheet->setTitle("Laporan Stok Barang");

        // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }

    // LAPORAN PEMBELIAN


    // MASTER USERS

    public function users()
    {
        $this->data['title']     = 'Master User';
        $this->data['index']     = 5.1;
        $this->data['link']     = 'master_user';

        $this->data['users']       = $this->Akun_m->get(['username !=' => $this->data['profil']->username, 'deleted_at' => NULL]);
        $this->data['content']     = 'pemilik/master/user_index';
        $this->load->view('pemilik/template/layout', $this->data);
    }

    public function user_form()
    {
        $this->data['title']     = 'Form Tambah User';
        $this->data['index']     = 5.1;
        $this->data['link']     = 'master_user';

        $this->data['content']     = 'pemilik/master/user_form';
        $this->load->view('pemilik/template/layout', $this->data);
    }

    public function user_store()
    {
        $this->form_validation->set_rules('username', 'Username', 'required');
        $this->form_validation->set_rules('nama_user', 'Nama', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');
        $this->form_validation->set_rules('no_hp', 'Nomor HP', 'required');
        $this->form_validation->set_rules('role', 'Role', 'required');

        if ($this->form_validation->run() == false) {
            $this->data['title']     = 'Form Tambah User';
            $this->data['index']     = 5.1;

            $this->data['content']     = 'pemilik/master/user_form';
            $this->load->view('pemilik/template/layout', $this->data);
        } else {

            $username      = preg_replace('/\s+/', '',  $this->input->post('username', true));
            if ($this->Akun_m->get_num_row(['username' => $username]) != 0) {
                $this->session->set_flashdata('warning', 'Username telah digunakan!');
                redirect('pemilik/user_form');
                exit;
            }

            $data = [
                'username'      => addslashes($username),
                'nama_user' => addslashes($this->input->post('nama_user', true)),
                'no_hp' => addslashes($this->input->post('no_hp', true)),
                'role' => addslashes($this->input->post('role', true)),
                'is_aktif' => addslashes($this->input->post('aktif', true)),
                'password' => md5($this->input->post('password')),
                'created_at' => date('Y-m-d H:i:s')
            ];

            if ($this->Akun_m->insert($data)) {
                $this->session->set_flashdata('success', 'User berhasil ditambah!');
                redirect('pemilik/users');
            } else {
                $this->session->set_flashdata('warning', 'Gagal, coba lagi!');
                redirect('pemilik/user_form');
            }
        }
    }

    public function user_edit()
    {
        $id =  $this->uri->segment(3);
        if ($this->Akun_m->get_num_row(['username' => $id]) == 0) {
            $this->session->set_flashdata('warning', 'Data tidak ditemukan!');
            redirect('pemilik/users');
            exit;
        } elseif ($this->Akun_m->get_row(['username' => $id])->deleted_at != NULL) {
            $this->session->set_flashdata('warning', 'Data tidak ditemukan!');
            redirect('pemilik/users');
            exit;
        }

        $this->data['user'] = $this->Akun_m->get_row(['username' => $id]);

        $this->data['title']     = 'Form Edit User';
        $this->data['index']     = 5.1;
        $this->data['link']     = 'master_user';

        $this->data['content']     = 'pemilik/master/user_edit';
        $this->load->view('pemilik/template/layout', $this->data);
    }

    public function user_update()
    {
        $this->form_validation->set_rules('username', 'Username', 'required');
        $this->form_validation->set_rules('nama_user', 'Nama', 'required');
        $this->form_validation->set_rules('no_hp', 'Nomor HP', 'required');
        $this->form_validation->set_rules('role', 'Role', 'required');

        if ($this->form_validation->run() == false) {
            $this->data['title']     = 'Form Edit User';
            $this->data['index']     = 5.1;
            $this->data['link']     = 'master_user';

            $this->data['content']     = 'pemilik/master/user_edit';
            $this->load->view('pemilik/template/layout', $this->data);
        } else {

            $username      = preg_replace('/\s+/', '',  $this->input->post('username', true));
            $username_old      = preg_replace('/\s+/', '',  $this->input->post('username_old', true));
            if ($this->Akun_m->get_num_row(['username' => $username]) != 0 && ($username_old != $username)) {
                $this->session->set_flashdata('warning', 'Username telah digunakan!');
                redirect('pemilik/user_edit/' . $username_old);
                exit;
            }

            $data = [
                'username'      => addslashes($username),
                'nama_user' => addslashes($this->input->post('nama_user', true)),
                'no_hp' => addslashes($this->input->post('no_hp', true)),
                'role' => addslashes($this->input->post('role', true)),
                'is_aktif' => addslashes($this->input->post('aktif', true)), 
                'updated_at' =>  date('Y-m-d H:i:s')
            ];

            if ($this->Akun_m->update($username_old, $data)) {
                $this->session->set_flashdata('success', 'User berhasil diedit!');
                redirect('pemilik/user_edit/' . $username);
            } else {
                $this->session->set_flashdata('warning', 'Gagal, coba lagi!');
                redirect('pemilik/user_edit/' . $username_old);
            }
        }
    }

    public function user_update_pass()
    {
        $this->form_validation->set_rules('password', 'Password', 'required');
        $this->form_validation->set_rules('password_confirm', 'Password Konfirmasi', 'required');

        if ($this->form_validation->run() == false) {
            $data = [
                'status'         => 'Warning',
                'icon'         => 'warning',
                'message'         => validation_errors(),
            ];
        } else {

            if ($this->input->post('password') != $this->input->post('password_confirm')) {
                $data = [
                    'status'         => 'Warning',
                    'icon'         => 'warning',
                    'message'         => 'Password dan konfirmasi password tidak sama',
                ];
            } else {
                $username      = preg_replace('/\s+/', '',  $this->input->post('id', true));
                $data = [
                    'updated_at' =>  date('Y-m-d H:i:s'),
                    'password' => md5($this->input->post('password')),
                ];

                if ($this->Akun_m->update($username, $data)) {
                    $data = [
                        'status'         => 'Success',
                        'icon'         => 'success',
                        'message'         => 'Password berhasil diganti!',
                    ];
                } else {
                    $data = [
                        'status'         => 'Warning',
                        'icon'         => 'warning',
                        'message'         => 'Gagal, coba lagi!',
                    ];
                }
            }
        }

        echo json_encode($data);
    }

    public function user_delete()
    {
        $id = $this->input->post('id');
        $this->Akun_m->update($id, ['deleted_at' => date('Y-m-d H:i:s'), 'is_aktif' => 0]);

        redirect('pemilik/users');
    }

    // MASTER USERS


    // MASTER KATEGORI

    public function kategori()
    {
        $this->data['title']     = 'Master Kategori';
        $this->data['index']     = 5.2;
        $this->data['link']     = 'master_kategori';

        $this->data['list_kategori']       = $this->Kategori_m->get(['deleted_at' => NULL]);
        $this->data['content']     = 'pemilik/master/kategori_index';
        $this->load->view('pemilik/template/layout', $this->data);
    }

    public function kategori_form()
    {
        $this->data['title']     = 'Form Tambah Kategori';
        $this->data['index']     = 5.2;
        $this->data['link']     = 'master_kategori';

        $this->data['content']     = 'pemilik/master/kategori_form';
        $this->load->view('pemilik/template/layout', $this->data);
    }

    public function kategori_store()
    {
        $this->form_validation->set_rules('nama_kategori', 'Nama Kategori', 'required');

        if ($this->form_validation->run() == false) {
            $this->data['title']     = 'Form Tambah Kategori';
            $this->data['index']     = 5.2;
            $this->data['link']     = 'master_kategori';

            $this->data['content']     = 'pemilik/master/kategori_form';
            $this->load->view('pemilik/template/layout', $this->data);
        } else {

            $data = [
                'nama_kategori' => addslashes($this->input->post('nama_kategori', true)),
                'deskripsi' => addslashes($this->input->post('deskripsi', true)),
                'created_at' => date('Y-m-d H:i:s')
            ];

            if ($this->Kategori_m->insert($data)) {
                $this->session->set_flashdata('success', 'Kategori berhasil ditambah!');
                redirect('pemilik/kategori');
            } else {
                $this->session->set_flashdata('warning', 'Gagal, coba lagi!');
                redirect('pemilik/kategori_form');
            }
        }
    }

    public function kategori_edit()
    {
        $id =  $this->uri->segment(3);
        if ($this->Kategori_m->get_num_row(['id_kategori' => $id]) == 0) {
            $this->session->set_flashdata('warning', 'Data tidak ditemukan!');
            redirect('pemilik/kategori');
            exit;
        } elseif ($this->Kategori_m->get_row(['id_kategori' => $id])->deleted_at != NULL) {
            $this->session->set_flashdata('warning', 'Data tidak ditemukan!');
            redirect('pemilik/kategori');
            exit;
        }

        $this->data['kategori'] = $this->Kategori_m->get_row(['id_kategori' => $id]);

        $this->data['title']     = 'Form Edit Kategori';
        $this->data['index']     = 5.2;
        $this->data['link']     = 'master_kategori';

        $this->data['content']     = 'pemilik/master/kategori_edit';
        $this->load->view('pemilik/template/layout', $this->data);
    }

    public function kategori_update()
    {

        $this->form_validation->set_rules('nama_kategori', 'Nama Kategori', 'required');

        if ($this->form_validation->run() == false) {
            $data = [
                'status'         => 'Warning',
                'icon'         => 'warning',
                'message'         => validation_errors(),
            ];
        } else {

            $data = [
                'nama_kategori' => addslashes($this->input->post('nama_kategori', true)),
                'deskripsi' => addslashes($this->input->post('deskripsi', true)),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            if ($this->Kategori_m->update($this->POST('id_kategori'), $data)) {
                $data = [
                    'status'         => 'Success',
                    'icon'         => 'success',
                    'message'         => 'Data berhasil diedit!',
                ];
            } else {
                $data = [
                    'status'         => 'Warning',
                    'icon'         => 'warning',
                    'message'         => 'Gagal, coba lagi!',
                ];
            }
        }
        echo json_encode($data);
    }

    public function kategori_delete()
    {
        $id = $this->input->post('id');
        $this->Kategori_m->update($id, ['deleted_at' => date('Y-m-d H:i:s')]);

        redirect('pemilik/kategori');
    }

    // MASTER KATEGORI

    // MASTER BARANG

    public function barang()
    {
        $this->data['title']     = 'Master Barang';
        $this->data['index']     = 5.3;
        $this->data['link']     = 'master_barang';

        $this->data['list_barang']       = $this->Barang_m->get(['deleted_at' => NULL]);
        $this->data['content']     = 'pemilik/master/barang_index';
        $this->load->view('pemilik/template/layout', $this->data);
    }

    public function barang_form()
    {
        $this->data['title']     = 'Form Tambah Barang';
        $this->data['index']     = 5.3;
        $this->data['link']     = 'master_barang';

        $this->data['list_kategori'] = $this->Kategori_m->get(['deleted_at' => NULL]);
        $this->data['content']     = 'pemilik/master/barang_form';
        $this->load->view('pemilik/template/layout', $this->data);
    }

    public function barang_store()
    {
        $this->form_validation->set_rules('nama_barang', 'Nama Barang', 'required');
        $this->form_validation->set_rules('id_kategori', 'Kategori', 'required');
        $this->form_validation->set_rules('harga', 'Harga Harga', 'required');
        $this->form_validation->set_rules('stok', 'Stok Barang', 'required');

        if ($this->form_validation->run() == false) {
            $this->data['title']     = 'Form Tambah Barang';
            $this->data['index']     = 5.3;
            $this->data['link']     = 'master_barang';

            $this->data['list_kategori'] = $this->Kategori_m->get(['deleted_at' => NULL]);
            $this->data['content']     = 'pemilik/master/barang_form';
            $this->load->view('pemilik/template/layout', $this->data);
        } else {

            if ($_FILES['foto']['name'] !== '') {
                $id_foto = rand(1, 9);
                for ($j = 1; $j <= 4; $j++) {
                    $id_foto .= rand(0, 9);
                }

                if ($this->upload($id_foto, 'barang', 'foto')) {
                    $foto = $id_foto . '.jpg';
                } else {
                    $this->session->set_flashdata('warning', 'Gagal upload foto, coba lagi!');
                    redirect('pemilik/barang_form');
                    exit;
                }
            } else {
                $foto = NULL;
            }

            $data = [
                'nama_barang' => addslashes($this->input->post('nama_barang', true)),
                'id_kategori' => addslashes($this->input->post('id_kategori', true)),
                'deskripsi' => addslashes($this->input->post('deskripsi', true)),
                'harga' => addslashes($this->input->post('harga', true)),
                'foto' => $foto,
                'stok' => addslashes($this->input->post('stok', true)),
                'created_at' => date('Y-m-d H:i:s')
            ];

            if ($this->Barang_m->insert($data)) {
                $this->session->set_flashdata('success', 'Barang berhasil ditambah!');
                redirect('pemilik/barang');
                exit;
            } else {
                $this->session->set_flashdata('warning', 'Gagal, coba lagi!');
                redirect('pemilik/barang_form');
                exit;
            }
        }
    }

    public function barang_edit()
    {
        $id =  $this->uri->segment(3);
        if ($this->Barang_m->get_num_row(['id_barang' => $id]) == 0) {
            $this->session->set_flashdata('warning', 'Data tidak ditemukan!');
            redirect('pemilik/barang');
            exit;
        } elseif ($this->Barang_m->get_row(['id_barang' => $id])->deleted_at != NULL) {
            $this->session->set_flashdata('warning', 'Data tidak ditemukan!');
            redirect('pemilik/barang');
            exit;
        }

        $this->data['list_kategori'] = $this->Kategori_m->get(['deleted_at' => NULL]);
        $this->data['barang'] = $this->Barang_m->get_row(['id_barang' => $id]);

        $this->data['title']     = 'Form Edit Barang';
        $this->data['index']     = 5.3;
        $this->data['link']     = 'master_barang';

        $this->data['content']     = 'pemilik/master/barang_edit';
        $this->load->view('pemilik/template/layout', $this->data);
    }

    public function barang_update()
    {
        $id = $this->POST('id_barang');
        $this->form_validation->set_rules('nama_barang', 'Nama Barang', 'required');
        $this->form_validation->set_rules('id_kategori', 'Kategori', 'required');
        $this->form_validation->set_rules('harga', 'Harga Harga', 'required');
        $this->form_validation->set_rules('stok', 'Stok Barang', 'required');

        if ($this->form_validation->run() == false) {
            $this->data['list_kategori'] = $this->Kategori_m->get(['deleted_at' => NULL]);
            $this->data['barang'] = $this->Barang_m->get_row(['id_barang' => $id]);

            $this->data['title']     = 'Form Edit Barang';
            $this->data['index']     = 5.3;
            $this->data['link']     = 'master_barang';

            $this->data['content']     = 'pemilik/master/barang_edit';
            $this->load->view('pemilik/template/layout', $this->data);
        } else {

            $path = $this->POST('path');

            if ($_FILES['foto']['name'] !== '') {
                $id_foto = rand(1, 9);
                for ($j = 1; $j <= 4; $j++) {
                    $id_foto .= rand(0, 9);
                }
                if ($this->upload($id_foto, 'barang', 'foto')) {
                    @unlink(realpath(APPPATH . '../assets/barang/' . $path));
                    $foto = $id_foto . '.jpg';
                } else {
                    $this->session->set_flashdata('warning', 'Gagal upload foto, coba lagi!');
                    redirect('pemilik/barang_edit/' . $id);
                    exit;
                }
            } else {
                $foto = $path;
            }

            $data = [
                'nama_barang' => addslashes($this->input->post('nama_barang', true)),
                'id_kategori' => addslashes($this->input->post('id_kategori', true)),
                'deskripsi' => addslashes($this->input->post('deskripsi', true)),
                'harga' => addslashes($this->input->post('harga', true)),
                'foto' => $foto,
                'stok' => addslashes($this->input->post('stok', true)),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            if ($this->Barang_m->update($id, $data)) {
                $this->session->set_flashdata('success', 'Data barang berhasil diedit!');
                redirect('pemilik/barang_edit/' . $id);
                exit;
            } else {
                $this->session->set_flashdata('warning', 'Gagal, coba lagi!');
                redirect('pemilik/barang_edit/' . $id);
                exit;
            }
        }
    }

    public function barang_delete()
    {
        $id = $this->input->post('id');
        $this->Barang_m->update($id, ['deleted_at' => date('Y-m-d H:i:s')]);

        redirect('pemilik/barang');
    }

    // MASTER BARANG

    // MASTER SUPPLIER

    public function supplier()
    {
        $this->data['title']     = 'Master Supplier';
        $this->data['index']     = 5.4;
        $this->data['link']     = 'master_supplier';
        $this->data['list_supplier']       = $this->Supplier_m->get(['deleted_at' => NULL]);
        $this->data['content']     = 'pemilik/master/supplier_index';
        $this->load->view('pemilik/template/layout', $this->data);
    }

    public function supplier_form()
    {
        $this->data['title']     = 'Form Tambah Supplier';
        $this->data['index']     = 5.4;
        $this->data['link']     = 'master_supplier';

        $this->data['list_kategori'] = $this->Kategori_m->get(['deleted_at' => NULL]);
        $this->data['content']     = 'pemilik/master/supplier_form';
        $this->load->view('pemilik/template/layout', $this->data);
    }

    public function supplier_store()
    {
        $this->form_validation->set_rules('nama_supplier', 'Nama Supplier', 'required');
        $this->form_validation->set_rules('kontak', 'Kontak', 'required');
        $this->form_validation->set_rules('alamat', 'Alamat', 'required');

        if ($this->form_validation->run() == false) {
            $data = [
                'status'         => 'warning',
                'icon'         => 'warning',
                'message'         => validation_errors(),
            ];
        } else {
            $data_supplier = [
                'nama_supplier' => addslashes($this->input->post('nama_supplier', true)),
                'kontak' => addslashes($this->input->post('kontak', true)),
                'alamat' => addslashes($this->input->post('alamat', true)),
                'created_at' => date('Y-m-d H:i:s')
            ];

            if ($this->Supplier_m->insert($data_supplier)) {
                $data = [
                    'status'         => 'success',
                    'icon'         => 'success',
                    'message'         => 'Supplier berhasil ditambah!',
                ];
            } else {
                $data = [
                    'status'         => 'warning',
                    'icon'         => 'warning',
                    'message'         => 'Gagal, coba lagi!',
                ];
            }
        }
        echo json_encode($data);
    }

    public function supplier_edit()
    {
        $id =  $this->uri->segment(3);
        if ($this->Supplier_m->get_num_row(['id_supplier' => $id]) == 0) {
            $this->session->set_flashdata('warning', 'Data tidak ditemukan!');
            redirect('pemilik/supplier');
            exit;
        } elseif ($this->Supplier_m->get_row(['id_supplier' => $id])->deleted_at != NULL) {
            $this->session->set_flashdata('warning', 'Data tidak ditemukan!');
            redirect('pemilik/supplier');
            exit;
        }

        $this->data['supplier'] = $this->Supplier_m->get_row(['id_supplier' => $id]);

        $this->data['title']     = 'Form Edit Supplier';
        $this->data['index']     = 5.4;
        $this->data['link']     = 'master_supplier';

        $this->data['content']     = 'pemilik/master/supplier_edit';
        $this->load->view('pemilik/template/layout', $this->data);
    }

    public function supplier_update()
    {
        $this->form_validation->set_rules('nama_supplier', 'Nama Supplier', 'required');
        $this->form_validation->set_rules('kontak', 'Kontak', 'required');
        $this->form_validation->set_rules('alamat', 'Alamat', 'required');

        if ($this->form_validation->run() == false) {
            $data = [
                'status'         => 'warning',
                'icon'         => 'warning',
                'message'         => validation_errors(),
            ];
        } else {
            $id = $this->POST('id_supplier');
            $data_supplier = [
                'nama_supplier' => addslashes($this->input->post('nama_supplier', true)),
                'kontak' => addslashes($this->input->post('kontak', true)),
                'alamat' => addslashes($this->input->post('alamat', true)),
                'created_at' => date('Y-m-d H:i:s')
            ];

            if ($this->Supplier_m->update($id, $data_supplier)) {
                $data = [
                    'status'         => 'success',
                    'icon'         => 'success',
                    'message'         => 'Supplier berhasil diedit!',
                ];
            } else {
                $data = [
                    'status'         => 'warning',
                    'icon'         => 'warning',
                    'message'         => 'Gagal, coba lagi!',
                ];
            }
        }
        echo json_encode($data);
    }

    public function supplier_delete()
    {
        $id = $this->input->post('id');
        $this->Supplier_m->update($id, ['deleted_at' => date('Y-m-d H:i:s')]);

        redirect('pemilik/supplier');
    }

    // MASTER BARANG



















    // PROFILE

    public function profil()
    {

        $this->data['title']     = 'Profile';
        $this->data['link']     = 'profile';
        $this->data['index'] = 6;
        $this->data['content'] = 'pemilik/profile';
        $this->load->view('pemilik/template/layout', $this->data);
    }

    public function profile_update()
    {
        $this->form_validation->set_rules('username', 'Username', 'required');
        $this->form_validation->set_rules('nama_user', 'Nama', 'required');
        $this->form_validation->set_rules('no_hp', 'Nomor HP', 'required');

        if ($this->form_validation->run() == false) {
            $data = [
                'status'         => 'warning',
                'icon'         => 'warning',
                'message'         => validation_errors(),
            ];
        } else {

            $username      = preg_replace('/\s+/', '',  $this->input->post('username', true));
            $username_old      = preg_replace('/\s+/', '',  $this->input->post('username_old', true));
            if ($this->Akun_m->get_num_row(['username' => $username]) != 0 && ($username_old != $username)) {
                $data = [
                    'status'         => 'warning',
                    'icon'         => 'warning',
                    'message'         => 'Username telah digunakan!',
                ];
            } else {
                $data = [
                    'username'      => addslashes($username),
                    'nama_user' => addslashes($this->input->post('nama_user', true)),
                    'no_hp' => addslashes($this->input->post('no_hp', true)),
                    'updated_at' =>  date('Y-m-d H:i:s')
                ];

                if ($this->Akun_m->update($username_old, $data)) {
                    $user_session = [
                        'username'    => $username,
                    ];
                    $this->session->set_userdata($user_session);
                    $data = [
                        'status'         => 'success',
                        'icon'         => 'success',
                        'message'         => 'Data berhasil disimpan!',
                    ];
                } else {
                    $data = [
                        'status'         => 'warning',
                        'icon'         => 'warning',
                        'message'         => 'Gagal, coba lagi!',
                    ];
                }
            }
        }
        echo json_encode($data);
    }

    public function profile_update_pass()
    {
        $this->form_validation->set_rules('password_old', 'Password Lama', 'required');
        $this->form_validation->set_rules('password', 'Password Baru', 'required');
        $this->form_validation->set_rules('password_confirm', 'Password Konfirmasi', 'required');

        if ($this->form_validation->run() == false) {
            $data = [
                'status'         => 'warning',
                'icon'         => 'warning',
                'message'         => validation_errors(),
            ];
        } else {

            if ($this->data['profil']->password != md5($this->POST('password_old'))) {
                $data = [
                    'status'         => 'warning',
                    'icon'         => 'warning',
                    'message'         => 'Password lama salah!',
                ];
            } elseif ($this->input->post('password') != $this->input->post('password_confirm')) {
                $data = [
                    'status'         => 'warning',
                    'icon'         => 'warning',
                    'message'         => 'Password dan konfirmasi password tidak sama',
                ];
            } else {
                $username      = preg_replace('/\s+/', '',  $this->input->post('id', true));
                $data = [
                    'updated_at' =>  date('Y-m-d H:i:s'),
                    'password' => md5($this->input->post('password')),
                ];

                if ($this->Akun_m->update($username, $data)) {
                    $data = [
                        'status'         => 'success',
                        'icon'         => 'success',
                        'message'         => 'Password berhasil diganti!',
                    ];
                } else {
                    $data = [
                        'status'         => 'warning',
                        'icon'         => 'warning',
                        'message'         => 'Gagal, coba lagi!',
                    ];
                }
            }
        }

        echo json_encode($data);
    }

    // PROFIL

}

/* End of file Nilai.php */
/* Location: ./application/controllers/Nilai.php */
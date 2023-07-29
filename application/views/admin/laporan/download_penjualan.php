<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan</title>
    <style>
        table,
        td,
        th {
            border: 1px solid;
            padding: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }
    </style>
</head>

<body>

    <div id="container">
        <h1 style="text-align: center;">Laporan Penjualan Lisan Collection</h1>
        <h3 style="text-align: center;">Periode : <?= $periode ?></h3>

        <div id="body" style="width: 100%;">
            <table style="width:100%; border-collapse: collapse;" border="1">
                <thead style="background-color:#051E34;color:#fff;">
                    <tr>
                        <th>Kode Transaksi</th>
                        <th>Tanggal</th>
                        <th>Nama Customer</th>
                        <th>Kontak Customer</th>
                        <th>Total Bayar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1;
                    $total = 0;
                    foreach ($data_penjualan as $row) { ?>
                        <tr>
                            <td><?= $row->kd_transaksi ?></td>
                            <td><?= date('d-m-Y', strtotime($row->tgl_transaksi)) ?></td>
                            <td><?= ($row->nama_customer) ? $row->nama_customer : "-" ?></td>
                            <td><?= ($row->kontak_customer) ? $row->kontak_customer : "-" ?></td>
                            <td style="text-align: right;"><?= number_format($row->total_bayar, 2, ',', '.') ?></td>
                        </tr>
                    <?php $total += $row->total_bayar;
                    } ?>
                    <tr>
                        <th style="text-align: center;" colspan="4">Total</th>
                        <th><?= number_format($total, 2, ',', '.') ?></th>
                    </tr>
                </tbody>
            </table>

            <div style="margin-top: 100px; width:30%;  float: right;">
                <span style="text-align: left;">
                    .................., <?= date('d - m - Y') ?>
                    <br>
                    Pemilik,

                    <div style="margin-top: 150px;">
                        ............................................
                    </div>
                </span>
            </div>
        </div>

    </div>

</body>

</html>
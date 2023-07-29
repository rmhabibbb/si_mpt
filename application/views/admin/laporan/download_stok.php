<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Laporan Stok Barang</title>
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
        <h1 style="text-align: center;">Laporan Stok Barang Lisan Collection</h1>
        <h3 style="text-align: center;">Periode : <?= $periode ?></h3>

        <div id="body" style="width: 100%;">
            <table style="width:100%; border-collapse: collapse;" border="1">
                <thead style="background-color:#051E34;color:#fff;">
                    <tr>
                        <th>ID Barang</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Ukuran</th>
                        <th>Jenis</th>
                        <th>Jumlah Supply</th>
                        <th>Jumlah Terjual</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1;
                    $total1 = 0;
                    $total2 = 0;
                    foreach ($data as $row) { ?>
                        <tr>
                            <td><?= $row['id_barang'] ?></td>
                            <td><?= $row['nama_barang']  ?></td>
                            <td><?= $row['nama_kategori']  ?></td>
                            <td><?= $row['ukuran']  ?></td>
                            <td><?= $row['jenis']  ?></td>
                            <td><?= $row['n_beli']  ?></td>
                            <td><?= $row['n_jual']  ?></td>
                        </tr>
                    <?php $total1 += $row['n_beli'];
                        $total2 += $row['n_jual'];
                    } ?>
                    <tr>
                        <th style="text-align: center;" colspan="5">Total</th>
                        <th><?= $total1 ?></th>
                        <th><?= $total2 ?></th>
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
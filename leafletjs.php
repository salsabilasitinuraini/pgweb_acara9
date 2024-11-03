<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web GIS Kabupaten Sleman</title>
    <!-- Memasukkan stylesheet untuk Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <style>
        /* Gaya untuk keseluruhan body */
        body {
            background-color: #FFF6E3;
            font-family: Arial, sans-serif;
        }
        /* Gaya untuk judul utama */
        h1 {
            text-align: center;
            color: #4A4A4A;
            margin-bottom: 5px;
        }
        /* Gaya untuk judul sekunder */
        h2 {
            text-align: center;
            color: #6D6D6D;
            font-weight: normal;
            margin-top: 0;
        }
        /* Gaya untuk tabel data */
        table {
            width: 80%;
            margin: auto;
            border-collapse: collapse;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #FD8A8A;
            color: #ffffff;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #FFE5F1;
            color: #fff;
        }
        /* Gaya untuk peta */
        #map {
            width: 80%;
            height: 400px;
            margin: 20px auto;
            border: 1px solid #ddd;
        }
        /* Gaya untuk modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }
        /* Gaya untuk input di modal */
        .modal-content input[type="number"],
        .modal-content input[type="text"] {
            width: 90%;
            padding: 8px;
            margin: 8px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        /* Gaya untuk tombol di modal */
        .modal-content input[type="submit"], .modal-content button {
            padding: 8px 12px;
            background-color: #FD8A8A;
            color: #ffffff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .modal-content button {
            background-color: #aaa;
        }
    </style>
</head>
<body>
    <h1>Web GIS</h1>
    <h2>Kabupaten Sleman</h2>

    <!-- Div untuk menampilkan peta -->
    <div id="map"></div>

    <?php
    // Koneksi ke database
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "latihan";
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Mengecek koneksi
    if ($conn->connect_error) {
        die("Koneksi gagal: " . $conn->connect_error);
    }

    // Menangani penambahan data kecamatan baru
    if (isset($_POST['new_kecamatan'])) {
        $new_kecamatan = $_POST['new_kecamatan'];
        $new_luas = $_POST['new_luas'];
        $new_longitude = $_POST['new_longitude'];
        $new_latitude = $_POST['new_latitude'];
        $new_jumlah_penduduk = $_POST['new_jumlah_penduduk'];

        // SQL untuk menambahkan data
        $insert_sql = "INSERT INTO penduduk (kecamatan, Luas, Longitude, Latitude, Jumlah_Penduduk) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("sdddi", $new_kecamatan, $new_luas, $new_longitude, $new_latitude, $new_jumlah_penduduk);

        // Mengeksekusi query dan mengecek hasilnya
        if ($stmt->execute()) {
            echo "<p style='text-align: center; color: green;'>Data berhasil ditambahkan.</p>";
        } else {
            echo "<p style='text-align: center; color: red;'>Error menambahkan data: " . $conn->error . "</p>";
        }

        $stmt->close();
    }

    // Menangani pengeditan data kecamatan
    if (isset($_POST['edit_kecamatan'])) {
        $edit_kecamatan = $_POST['edit_kecamatan'];
        $edit_luas = $_POST['new_luas'];
        $edit_longitude = $_POST['new_longitude'];
        $edit_latitude = $_POST['new_latitude'];
        $edit_jumlah_penduduk = $_POST['new_jumlah_penduduk'];

        // SQL untuk mengupdate data
        $update_sql = "UPDATE penduduk SET Luas=?, Longitude=?, Latitude=?, Jumlah_Penduduk=? WHERE kecamatan=?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("dddis", $edit_luas, $edit_longitude, $edit_latitude, $edit_jumlah_penduduk, $edit_kecamatan);

        // Mengeksekusi query dan mengecek hasilnya
        if ($stmt->execute()) {
            echo "<p style='text-align: center; color: green;'>Data berhasil diupdate.</p>";
        } else {
            echo "<p style='text-align: center; color: red;'>Error mengupdate data: " . $conn->error . "</p>";
        }

        $stmt->close();
    }

    // Menangani penghapusan data kecamatan
    if (isset($_POST['delete_kecamatan'])) {
        $delete_kecamatan = $_POST['delete_kecamatan'];

        // SQL untuk menghapus data
        $delete_sql = "DELETE FROM penduduk WHERE kecamatan=?";
        $stmt = $conn->prepare($delete_sql);
        $stmt->bind_param("s", $delete_kecamatan);

        // Mengeksekusi query dan mengecek hasilnya
        if ($stmt->execute()) {
            echo "<p style='text-align: center; color: green;'>Data berhasil dihapus.</p>";
        } else {
            echo "<p style='text-align: center; color: red;'>Error menghapus data: " . $conn->error . "</p>";
        }

        $stmt->close();
    }

    // Mengambil data dari tabel penduduk
    $sql = "SELECT kecamatan, Luas, Longitude, Latitude, Jumlah_Penduduk FROM penduduk"; 
    $result = $conn->query($sql);

    // Mengecek hasil query
    if ($result === false) {
        die("Query gagal: " . $conn->error);
    }

    // Jika ada hasil, tampilkan dalam tabel
    if ($result->num_rows > 0) { 
        echo "<table><tr> 
                <th>Kecamatan</th> 
                <th>Luas</th> 
                <th>Longitude</th>
                <th>Latitude</th>
                <th>Jumlah Penduduk</th>
                <th>Aksi</th>
              </tr>"; 

        // Menyimpan lokasi untuk peta
        echo "<script>var locations = [];</script>";
        
        // Menampilkan data dalam tabel
        while($row = $result->fetch_assoc()) { 
            echo "<tr>
                    <td>".$row["kecamatan"]."</td>
                    <td>".$row["Luas"]."</td>
                    <td>".$row["Longitude"]."</td>
                    <td>".$row["Latitude"]."</td>
                    <td align='right'>".$row["Jumlah_Penduduk"]."</td>
                    <td>
                        <!-- Tombol Edit untuk membuka modal edit -->
                        <button onclick='openEditModal(\"".$row["kecamatan"]."\", ".$row["Luas"].", ".$row["Longitude"].", ".$row["Latitude"].", ".$row["Jumlah_Penduduk"].")'>Edit</button>
                        <!-- Tombol Hapus untuk menghapus data -->
                        <form method='post' style='display:inline;'>
                            <input type='hidden' name='delete_kecamatan' value='".$row["kecamatan"]."'>
                            <input type='submit' value='Hapus' onclick='return confirm(\"Apakah Anda yakin ingin menghapus data ini?\")'>
                        </form>
                    </td>
                  </tr>";
            
            // Menyimpan lokasi
            echo "<script>locations.push({kecamatan: '".$row["kecamatan"]."', lat: ".$row["Latitude"].", lon: ".$row["Longitude"]."});</script>";
        } 
        echo "</table>"; 
    } else { 
        echo "0 hasil"; 
    }

    $conn->close();
    ?>

    <!-- Modal untuk menambahkan data -->
    <div class="modal" id="addModal">
        <div class="modal-content">
            <h2>Tambah Kecamatan</h2>
            <form method="post" id="addForm">
                <input type="text" name="new_kecamatan" placeholder="Nama Kecamatan" required>
                <input type="number" name="new_luas" placeholder="Luas (ha)" step="any" required>
                <input type="number" name="new_longitude" placeholder="Longitude" step="any" required>
                <input type="number" name="new_latitude" placeholder="Latitude" step="any" required>
                <input type="number" name="new_jumlah_penduduk" placeholder="Jumlah Penduduk" required>
                <input type="submit" value="Tambah">
                <button type="button" onclick="closeModal('addModal')">Batal</button>
            </form>
        </div>
    </div>

    <!-- Modal untuk mengedit data -->
    <div class="modal" id="editModal">
        <div class="modal-content">
            <h2>Edit Kecamatan</h2>
            <form method="post" id="editForm">
                <input type="hidden" name="edit_kecamatan" id="editKecamatan">
                <input type="number" name="new_luas" placeholder="Luas (ha)" step="any" id="editLuas" required>
                <input type="number" name="new_longitude" placeholder="Longitude" step="any" id="editLongitude" required>
                <input type="number" name="new_latitude" placeholder="Latitude" step="any" id="editLatitude" required>
                <input type="number" name="new_jumlah_penduduk" placeholder="Jumlah Penduduk" id="editJumlahPenduduk" required>
                <input type="submit" value="Simpan">
                <button type="button" onclick="closeModal('editModal')">Batal</button>
            </form>
        </div>
    </div>

    <!-- Menampilkan tombol untuk menambah kecamatan -->
    <div style="text-align: center; margin: 20px;">
        <button onclick="openAddModal()">Tambah Kecamatan</button>
    </div>

    <!-- Memasukkan script Leaflet -->
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        // Menginisialisasi peta
        var map = L.map('map').setView([-7.754049, 110.407236], 12);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
        }).addTo(map);

        // Menambahkan marker untuk setiap lokasi
        locations.forEach(function(location) {
            L.marker([location.lat, location.lon])
                .addTo(map)
                .bindPopup(location.kecamatan);
        });

        // Fungsi untuk membuka modal tambah
        function openAddModal() {
            document.getElementById('addModal').style.display = 'flex';
        }

        // Fungsi untuk menutup modal
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Fungsi untuk membuka modal edit
        function openEditModal(kecamatan, luas, longitude, latitude, jumlahPenduduk) {
            document.getElementById('editKecamatan').value = kecamatan;
            document.getElementById('editLuas').value = luas;
            document.getElementById('editLongitude').value = longitude;
            document.getElementById('editLatitude').value = latitude;
            document.getElementById('editJumlahPenduduk').value = jumlahPenduduk;
            document.getElementById('editModal').style.display = 'flex';
        }
    </script>
</body>
</html>

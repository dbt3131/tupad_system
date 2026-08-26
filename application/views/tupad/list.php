<?php
// Database Configuration
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'dole_tupad_db'; // Change to your database name

$mysqli = new mysqli($host, $user, $pass, $db);
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// 1. Create PSGC Table
$mysqli->query("
    CREATE TABLE IF NOT EXISTS `psgc_locations` (
        `psgc_code` VARCHAR(10) PRIMARY KEY,
        `name` VARCHAR(150) NOT NULL,
        `level` ENUM('Reg', 'Prov', 'CityMun', 'Bgy') NOT NULL,
        `parent_code` VARCHAR(10) NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

echo "Table created successfully.<br>";

// Helper to fetch JSON from API
function getApiData($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

// Region 3 Code
$regionCode = '0300000000';

// Insert Region 3
$stmt = $mysqli->prepare("INSERT IGNORE INTO psgc_locations (psgc_code, name, level, parent_code) VALUES (?, ?, ?, ?)");
$regName = 'REGION III (CENTRAL LUZON)';
$regLevel = 'Reg';
$nullVal = null;
$stmt->bind_param("ssss", $regionCode, $regName, $regLevel, $nullVal);
$stmt->execute();

// 2. Fetch Provinces
echo "Importing Provinces...<br>";
$provinces = getApiData("https://psgc.gitlab.io/api/regions/{$regionCode}/provinces/");

foreach ($provinces as $prov) {
    $provCode = $prov['code'];
    $provName = mb_strtoupper($prov['name']);
    $provLevel = 'Prov';
    $stmt->bind_param("ssss", $provCode, $provName, $provLevel, $regionCode);
    $stmt->execute();

    // 3. Fetch Cities & Municipalities per Province
    $citiesMun = getApiData("https://psgc.gitlab.io/api/provinces/{$provCode}/cities-municipalities/");
    foreach ($citiesMun as $cm) {
        $cmCode = $cm['code'];
        $cmName = mb_strtoupper($cm['name']);
        $cmLevel = 'CityMun';
        $stmt->bind_param("ssss", $cmCode, $cmName, $cmLevel, $provCode);
        $stmt->execute();

        // 4. Fetch Barangays per City/Municipality
        $barangays = getApiData("https://psgc.gitlab.io/api/cities-municipalities/{$cmCode}/barangays/");
        foreach ($barangays as $bgy) {
            $bgyCode = $bgy['code'];
            $bgyName = mb_strtoupper($bgy['name']);
            $bgyLevel = 'Bgy';
            $stmt->bind_param("ssss", $bgyCode, $bgyName, $bgyLevel, $cmCode);
            $stmt->execute();
        }
    }
}

echo "<strong>Region 3 Locations Imported Successfully!</strong>";
?>
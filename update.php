<?php
session_start();

if (!isset($_SESSION['token'])) {
    echo "<script>alert('You need to login first'); window.location.href='login.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    // Mengambil data JSON yang dikirimkan dalam permintaan PUT
    $data = json_decode(file_get_contents("php://input"), true);

    // Periksa apakah semua data yang diperlukan telah disertakan
    if (isset($data['id']) && isset($data['name']) && isset($data['value'])) {
        $id = $data['id'];
        $name = $data['name'];
        $value = $data['value'];

        $url = 'https://crud4-y2gaxkmn7q-uc.a.run.app/data/' . $id;
        $data = json_encode(array("name" => $name, "value" => $value));

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $_SESSION['token']
        ));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        $result = json_decode($response, true);
        echo json_encode(['message' => $result['message']]);
    } else {
        http_response_code(400);
        echo json_encode(['message' => 'Incomplete data provided']);
    }
} else {
    http_response_code(405);
    echo json_encode(['message' => 'Method Not Allowed']);
}
?>

<?php
session_start();

if (!isset($_SESSION['token'])) {
    http_response_code(401);
   
    echo json_encode(['message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    // Mengambil data JSON yang dikirimkan dalam permintaan DELETE
    $data = json_decode(file_get_contents("php://input"), true);

    // Periksa apakah id telah disertakan dalam data
    if (isset($data['id'])) {
        $id = $data['id'];

        $url = 'https://crud4-y2gaxkmn7q-uc.a.run.app/data/' . $id;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $_SESSION['token']
        ));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            http_response_code(500);
            echo json_encode(['message' => 'Error: ' . curl_error($ch)]);
        } else {
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($httpCode == 200) {
                echo json_encode(['message' => 'Record deleted successfully']);
            } else {
                http_response_code($httpCode);
                echo json_encode(['message' => 'Error deleting record']);
            }
        }
        curl_close($ch);
    } else {
        http_response_code(400);
        echo json_encode(['message' => 'No ID provided']);
    }
} else {
    http_response_code(405);
    echo json_encode(['message' => 'Method Not Allowed']);
}

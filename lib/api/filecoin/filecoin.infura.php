<?php

function get_filecoin_message($id){
   

    $curl = curl_init();
    /* 
        $url = 'https://filecoin.infura.io';
        $apiKey = "MjEyTFIySmQ5bGhQbVQwelE3NUhpSWFGcXNzOmI4ZWNmYTE2OGY3YzRmMzM4MWUyY2QxOGNkNmFjMDU3";
        $fields = '{
            "id": 0,
            "jsonrpc": "2.0",
            "method": "Filecoin.ChainGetMessage",
            "params": [{"/": "bafy2bzacedadg6yufns42ibfuxm6qcqzsb4u2ykqbv5br2y7d2zbbkcwtyipq"}]
        }';

        $headers = array("Authorization: Basic " . $apiKey, "Content-Type: application/json");

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);


        $response = curl_exec($curl);
        curl_close($curl);

        print_R($response);
    */

    curl_setopt_array($curl, array( 
    CURLOPT_URL => 'https://filecoin.infura.io',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_SSL_VERIFYHOST => 0,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS =>'{
    "id": 0,
    "jsonrpc": "2.0",
    "method": "Filecoin.ChainGetMessage",
    "params": [{"/": "'.$id.'"}]
    }',
    CURLOPT_HTTPHEADER => array(
    'Authorization: Basic MjEyTFIySmQ5bGhQbVQwelE3NUhpSWFGcXNzOmI4ZWNmYTE2OGY3YzRmMzM4MWUyY2QxOGNkNmFjMDU3',
    'Content-Type: application/json'
    ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);

    // echo $response;

    $body_json = json_decode($response, true);
    return $body_json;
}
?>
<?php

declare(strict_types=1);

include_once 'DataBases.php';
include_once 'Validator.php';

class Controller
{
    /**
     * @var DataBase
     */
    private DataBase $db;

    public function __construct()
    {
        $this->db = new DataBase();
    }

    public function createContactToBitrix($post): array
    {
        $validator = new Validator();
        $mess = $validator->validateData($post);

        if (count($mess) !== 0) {
            http_response_code(400);
            return ['errors' => $mess];
        }

        $data = ['fields' => [
            'TITLE' => $post['name'] . ' ' . $post['second_name'],
            'OPENED' => 'Y',
            'NAME' => $post['name'],
            'SECOND_NAME' => $post['second_name'],
            'LAST_NAME' => $post['last_name'],
            "BIRTHDATE" => $post['birthday'],
            'STATUS_ID' => 'NEW',
            'PHONE' => [['VALUE' => $post['phone'], 'VALUE_TYPE' => 'WORK']]
        ]];

        $curlBitrix = curl_init();
        curl_setopt_array($curlBitrix, array(
            CURLOPT_URL => 'https://b24-jij6by.bitrix24.by/rest/1/q4hbokaisced2avh/crm.lead.add/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($data)
        ));

        curl_exec($curlBitrix);
        curl_close($curlBitrix);
        $this->saveToDB($post);

        return ['success' => 'Данные успешно записаны! Ожидайте звонка!!!'];
    }

    public function getCountContacts(): array
    {
        $query = 'SELECT COUNT(*) as count FROM Contacts;';

        return $this->db->executeQuery($query);
    }

    private function saveToDB(array $data): void
    {
        $data = [$data['name'], $data['second_name'], $data['last_name'], $data['phone'], $data['birthday']];
        $query = 'INSERT INTO Contacts (name, second_name, last_name, phone, birthday) VALUES (?, ?, ?, ?, ?);';

        $this->db->executeQueryStmt($query, 'sssss', $data);
    }
}
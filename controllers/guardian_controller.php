<?php
include_once '../models/guardian_model.php';
include_once '../helpers/db_helpers.php'; 
include_once '../db.php';

class GuardianController {
    private $model;
    
    public function __construct($conn) {
        $this->model = new GuardianModel($conn);
    }
    
    public function handleDelete() {
        $data = get_data();
        if (!$data || empty($data['id'])) {
            throw new InvalidArgumentException('Guardian ID is required', 400);
        }

        if (!$this->model->deleteGuardian($data['id'])) {
            throw new RuntimeException('Guardian not found or could not be deleted', 404);
        }

        return ['success' => true, 'message' => 'Guardian deleted successfully'];
    }

    public function handleCreate() {
        $data = get_data();
        if (!$data) {
            throw new InvalidArgumentException('Invalid or missing data format', 400);
        }

        $guardianData = $this->validateAndPrepareData($data);
        $this->model->addGuardian($guardianData);

        return ['success' => true, 'message' => 'Guardian added successfully'];
    }

    public function handleGetById() {
        $data = $this->model->getGuardianInfo();
        if (!$data) {
            throw new RuntimeException('Guardian not found', 404);
        }
        return ['success' => true, 'data' => $data];
    }

    public function handleGetAccounts() {
        $data = $this->model->getGuardianAccounts();
        return ['success' => true, 'data' => $data];
    }

    public function handleGetInfo() {
        $data = $this->model->getGuardianInfo();
        return ['success' => true, 'data' => $data];
    }

    private function validateAndPrepareData($data) {
        $info = $data['info'] ?? [];
        $account = $data['account_info'] ?? [];
        $contact = $data['contact_info'] ?? [];

        // Validate required fields
        $required = [
            'info.first_name' => $info['first_name'] ?? null,
            'info.last_name' => $info['last_name'] ?? null,
            'info.relationship' => $info['relationship'] ?? null,
            'account.username' => $account['username'] ?? null,
            'account.passcode' => $account['passcode'] ?? null,
            'contact.email' => $contact['email'] ?? null,
            'contact.phone' => $contact['phone_number'] ?? null
        ];

        foreach ($required as $field => $value) {
            if (empty($value)) {
                throw new InvalidArgumentException("Missing required field: $field", 400);
            }
        }

        // Basic format validation
        if (!filter_var(trim($contact['email']), FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email format", 400);
        }

        return [
            'first_name' => $info['first_name'],
            'last_name' => $info['last_name'],
            'relationship' => $info['relationship'],
            'username' => $account['username'],
            'passcode' => $account['passcode'],
            'email' => trim($contact['email']),
            'phone_number' => $contact['phone_number'],
            'profile_image' => $account['profile_image'] ?? null,
            'date_of_birth' => $info['date_of_birth'] ?? null,
            'home_address' => $info['home_address'] ?? null,
            'job' => $info['job'] ?? null
        ];
    }
}
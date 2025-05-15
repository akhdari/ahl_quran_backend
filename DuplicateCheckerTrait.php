<?php
trait DuplicateCheckerTrait
{
    protected function checkForDuplicates(string $username, string $email): void
    {
        // Check username in account_info
        $stmt = $this->conn->prepare("SELECT account_id FROM account_info WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $stmt->close();
            throw new RuntimeException("Username already exists", 409);
        }
        $stmt->close();

        // Check email in contact_info
        $stmt = $this->conn->prepare("SELECT contact_id FROM contact_info WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $stmt->close();
            throw new RuntimeException("Email already exists", 409);
        }
        $stmt->close();
    }
}
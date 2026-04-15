<?php
    class DatabaseConnection {

        private $conn;

        public function __construct(string $dbHostname, string $dbUsername, string $dbPassword, string $dbDatabase) {
            $this->conn = new mysqli($dbHostname, $dbUsername, $dbPassword, $dbDatabase);
            if ($this->conn->connect_error) {
                throw new Exception("Connection Failed: " . $this->conn->connect_error);
            }
            $this->conn->set_charset("utf8mb4");
        }

        public function query(string $query, array $values=[]) {
            // query with support for prepared statement, but can be used for a "normal" query
            // example prepared statement: $query="SELECT * FROM names WHERE name = ?"; $values=["Hello World"]

            $stmt = $this->conn->prepare($query);

            if (!$stmt) {
                throw new Exception("Prepare failed " . $this->conn->error);
            }
            // make sure youre running a minimum of PHP8.1
            if (!$stmt->execute($values)) {
                throw new Exception("Execute failed: " . $stmt->error);
            }

            $result = $stmt->get_result();

            $data = $result->fetch_all(MYSQLI_ASSOC);

            $stmt->close();

            return $data;
        }

        public function write(string $query, array $values=[]) {
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Prepare failed " . $this->conn->error); 
            }

            if (!$stmt->execute($values)) {
                throw new Exception("Execute failed: " . $stmt->error);
            }

            $result = $stmt->affected_rows;

            $stmt->close();

            return $result;
        }

        public function insertAndGetID(string $query, array $values=[]) {
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception("Prepare failed " . $this->conn->error); 
            }

            if (!$stmt->execute($values)) {
                throw new Exception("Execute failed: " . $stmt->error);
            }

            // Get the ID of the newly inserted row
            $insertId = $stmt->insert_id;

            $stmt->close();

            return $insertId;
        }
    }

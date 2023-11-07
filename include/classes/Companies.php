<?php


namespace Dash;

use PDO;
use PDOException;

class Companies
{
    private object $pdo;
    private string $companyTable = 'dash_companies';
    private array $statuses = ['active', 'inactive'];
    private array $default = [
        [
            'company_name'          => 'SecondSite',
            'address'             => '',
            'city'                  => '',
            'country'               => '',
            'vat_number'            => '',
            'primary_email'         => '',
            'primary_phone'         => '',
            'company_status'                => 'active',
            'user_id'               => 1,
            'created_by_user_id'    => 1
        ]
    ];

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        if (!$this->pdo->query("SHOW TABLES LIKE '{$this->companyTable}'")->fetchAll()) {
            $this->createTable();

            $records = $this->default;
            foreach ($records as $entry) {
                $this->save($entry);
            }
        }
        date_default_timezone_set('Africa/Johannesburg');
    }

    /**
     * Get All records from the database
     * @return mixed
     */
    public function getAll(): mixed
    {
        $table = $this->companyTable;
        $stmt = $this->pdo->prepare("SELECT * FROM {$table}");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Fetch All records by Status
     * @param $status
     * @return mixed
     */
    public function getByStatus($status): mixed
    {
        $table = $this->companyTable;
        $stmt = $this->pdo->prepare("SELECT * FROM {$table} WHERE company_status=:company_status");
        $stmt->execute(['company_status' => $status]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Gets a record based on Id
     * @param int $companyId
     * @return mixed
     */
    public function getById(int $companyId): mixed
    {
        $table = $this->companyTable;
        $stmt = $this->pdo->prepare("SELECT * FROM {$table} WHERE company_id=:company_id");
        $stmt->execute(['company_id' => $companyId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    /**
     * Returns an unfiltered count of records from the database
     * @return mixed
     */
    public function count(): mixed
    {
        $table = $this->companyTable;
        $sql = "SELECT count(*) FROM {$table}";
        $result = $this->pdo->prepare($sql);
        $result->execute();
        return $result->fetchColumn();
    }

    /**
     * Delete a record from the database
     * @param int $companyId
     * @param int $userId
     * @return bool
     */
    public function delete(int $companyId, int $userId): bool
    {
        $status = 'inactive';
        $table = $this->companyTable;
        $sql = "UPDATE {$table} SET
                 company_status=?,
                 updated_by_user_id=?
                WHERE company_id=?";
        $stmt= $this->pdo->prepare($sql);
        return $stmt->execute([
            $status,
            $userId,
            $companyId
        ]);
    }

    /**
     * Update a database record with timestamp
     * @param array $values
     * @return bool
     */
    public function update(array $values): bool
    {
        $table = $this->companyTable;
        $sql = "UPDATE {$table} SET 
                 company_name=?,
                 address=?,
                 city=?,
                 country=?,
                 vat_number=?,
                 primary_email=?,
                 primary_phone=?,
                 company_status=?,
                 updated_by_user_id=?
                WHERE company_id=?";
        $stmt= $this->pdo->prepare($sql);
        return $stmt->execute([
            $values['company_name'],
            $values['address'],
            $values['city'],
            $values['country'],
            $values['vat_number'],
            $values['primary_email'],
            $values['primary_phone'],
            $values['company_status'],
            $values['updated_by_user_id'],
            $values['company_id']
        ]);
    }


    /**
     * @param array $fields
     * @return string|int
     */
    public function save(array $fields): string|int
    {
        $table = $this->companyTable;

        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO {$table} 
                (
                 company_name, 
                 address, 
                 city, 
                 country, 
                 vat_number, 
                 primary_email, 
                 primary_phone, 
                 company_status,
                 user_id,
                 created_by_user_id
                 )
                VALUES (
                        :company_name, 
                        :address, 
                        :city, 
                        :country, 
                        :vat_number, 
                        :primary_email, 
                        :primary_phone, 
                        :company_status,
                        :user_id,
                        :created_by_user_id
                        )"
            );
            $stmt->bindParam(':company_name', $fields['company_name']);
            $stmt->bindParam(':address', $fields['address']);
            $stmt->bindParam(':city', $fields['city']);
            $stmt->bindParam(':country', $fields['country']);
            $stmt->bindParam(':vat_number', $fields['vat_number']);
            $stmt->bindParam(':primary_email', $fields['primary_email']);
            $stmt->bindParam(':primary_phone', $fields['primary_phone']);
            $stmt->bindParam(':company_status', $fields['company_status']);
            $stmt->bindParam(':user_id', $fields['user_id']);
            $stmt->bindParam(':created_by_user_id', $fields['created_by_user_id']);
            $stmt->execute();
            return (int) $this->pdo->lastInsertId('company_id');
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    public function createTable(): string
    {
        $table = $this->companyTable;
        try {
            $sql ="CREATE TABLE IF NOT EXISTS $table (
                `company_id` int(20) NOT NULL AUTO_INCREMENT,
                `company_name` varchar(255) NULL,
                `address` varchar(255) NULL,
                `city` varchar(255) NULL,
                `country` varchar(255) NULL,
                `vat_number` varchar(255) NULL,
                `primary_email` varchar(255) NULL,
                `primary_phone` varchar(255) NULL,
                `company_status` varchar(255) NULL,   
                `user_id` int(10) NOT NULL,             
                `created_by_user_id` int(10) NOT NULL,                
                `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,           
                `updated_by_user_id` int(10) NULL,
                `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`company_id`)
              ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;" ;
            $this->pdo->exec($sql);
            return $table;
        } catch (PDOException $e) {
            echo $e->getMessage();//Remove or change message in production code
        }
    }
}

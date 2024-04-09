<?php
/**
 * Site product main spec class
 *
 * @author      Stanley Sie <swookon@gmail.com>
 * @access      public
 * @version     Release: 1.0
 */

namespace Stanleysie\HkProduct;

use \PDO as PDO;
use \PDOException as PDOException;

/**
 * Class SiteProductMainSpec
 * Perform CRUD operations for the site_product_main_spec table.
 */
class SiteProductMainSpec
{
    /** @var PDO Database connection */
    private $conn;

    /**
     * SiteProductMainSpec constructor.
     * @param PDO $conn Database connection
     */
    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    /**
     * Create a new product main specification.
     *
     * @param array $data Product main specification data
     * @return bool True on success, False on failure
     */
    public function createProductMainSpec($data)
    {
        try {
            $sql = <<<EOF
                INSERT INTO site_product_main_spec
                (product_id, name, img_url, price, member_price, supply_status, inventory, created_at, updated_at)
                VALUES
                (:product_id, :name, :img_url, :price, :member_price, :supply_status, :inventory, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP())
EOF;

            $stmt = $this->conn->prepare($sql);
            return $stmt->execute($data);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Retrieve a product main specification by its ID.
     *
     * @param int $mainSpecId Main specification ID
     * @return mixed|null Main specification data if found, null otherwise
     */
    public function getProductMainSpecById($mainSpecId)
    {
        try {
            $sql = <<<EOF
                SELECT * FROM site_product_main_spec WHERE main_spec_id = :main_spec_id
EOF;

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':main_spec_id', $mainSpecId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return null;
        }
    }

    /**
     * Update an existing product main specification.
     *
     * @param array $data Updated product main specification data
     * @return bool True on success, False on failure
     */
    public function updateProductMainSpec($data)
    {
        try {
            $sql = <<<EOF
                UPDATE site_product_main_spec
                SET product_id = :product_id, name = :name, img_url = :img_url, price = :price, member_price = :member_price,
                supply_status = :supply_status, inventory = :inventory, updated_at = CURRENT_TIMESTAMP()
                WHERE main_spec_id = :main_spec_id
EOF;

            $stmt = $this->conn->prepare($sql);
            return $stmt->execute($data);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Delete a product main specification by its ID.
     *
     * @param int $mainSpecId Main specification ID
     * @return bool True on success, False on failure
     */
    public function deleteProductMainSpec($mainSpecId)
    {
        try {
            $sql = <<<EOF
                DELETE FROM site_product_main_spec WHERE main_spec_id = :main_spec_id
EOF;

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':main_spec_id', $mainSpecId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
}

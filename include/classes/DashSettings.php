<?php
/**
 * File:          DashSetup.php
 * File Created:  2021/04/02 23:23
 * Modified By:   Gregory Schoeman <gregory@secondsite.xyz>
 * PHP version 8.0
 * -----
 *
 * @category  WebApp
 * @package   NPM
 * @author    Gregory Schoeman <gregory@secondsite.xyz>
 * @copyright 2019-2021 SecondSite
 * @license   https://opensource.org/licenses/MIT  MIT
 * @version   GIT: <1.0.0>
 * @link      https://github.com/SecondSite-web/dash.git
 * @project   dash
 */

namespace Dash;

use PDO;

class DashSettings
{
    private object $pdo;
    protected array $dashSetup;
    protected string $setupTable = 'dash_config';


    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Setup::__get()
     *
     * @param mixed $setting
     * @return string|null
     */
    public function __get(mixed $setting): ?string
    {
        return $this->dashSetup[$setting] ?? null;
    }


    /**
     * Get all settings in an array
     * @return mixed
     */
    public function getAll(): mixed
    {
        return $this->dashSetup = $this->pdo->query(
            "SELECT `setting`, `value` FROM {$this->setupTable}"
        )->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    /**
     * Setup::__set()
     *
     * @param mixed $setting
     * @param mixed $value
     */
    public function __set(mixed $setting, mixed $value)
    {
        $query = $this->pdo->prepare(
            "REPLACE INTO {$this->setupTable} (setting, value) VALUES(?,?)",
            array($setting, $value)
        );
        $query->bindParam(1, $setting);
        $query->bindParam(2, $value);
        $query->execute();
    }


    /**
     * Checks if a field is set in the array
     * @param $setting
     * @return bool
     */
    public function __isset($setting): bool
    {
        if (array_key_exists($setting, $this->dashSetup)) {
            return true;
        }
        return false;
    }

}

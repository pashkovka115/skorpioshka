<?php


namespace Core\Db;

use Core\System\Traits\Singleton;
use Illuminate\Database\Capsule\Manager as Capsule;

class Db
{
    use Singleton;

    /**
     * @var $db \PDO
     */
    protected $db;
    protected $dump_sql = [];

    /**
     * @return Capsule
     */
    public function __construct()
    {
        $capsule = new Capsule;

        $capsule->addConnection([
            'driver'    => DRIVER,
            'host'      => HOST,
            'database'  => DB_NAME,
            'username'  => USER,
            'password'  => PASS,
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ]);

        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }

    public function connect()
    {
        $option = [
            \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        ];
        try {
            $this->db = new \PDO('mysql:dbname=' . \DB_NAME . ';host=' . \HOST, \USER, \PASS, $option);
            return $this->db;
        } catch (\PDOException $ex) {
            echo $ex->getMessage();
            die(' Не удалось подключиться к базе данных, попробуйте ещё.');
        }
    }

    // Не возвращает данные
    // todo: переделать
    public function execute($sql, array $params = [])
    {
        $this->dump_sql[] = $sql;
        $this->connect();
        $sth = $this->db->prepare($sql);
        $res = $sth->execute($params);
        return $res;
    }

    // todo: переделать
    public function query($sql)
    {
        $this->dump_sql[] = $sql;

        $this->connect();
        $sth = $this->db->prepare($sql);
        $res = $sth->execute();

        if (false !== $res) {
            return $sth->fetchAll();
        }
        return [];
    }


    public function dump()
    {
        echo '<p style="background: #b0bec5; padding: 20px; font-size: 16px">';
        foreach ($this->dump_sql as $value) {
            echo '<b style="color: green">', $value, '</b><br><br>';
        }
        echo '</p>';
    }

}
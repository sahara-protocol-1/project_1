<?php


namespace app;

use Aura\SqlQuery\QueryFactory;
use PDO;


class QueryBuilder {
    private $pdo, $queryFactory;

    public function test(){
        echo "test 1234";
    }

    public function __construct(PDO $pdo) { //PDO $pdo
        $this->pdo = $pdo; // phpdi из index у нас возвращается подключение с настройками
        $this->queryFactory = new QueryFactory('mysql'); // инициализировали
    }

    public function getOne_by_id($data, $id, $table) {

        $select = $this->queryFactory->newSelect();
        $select
            ->cols([$data])->from($table)
            ->where('id = :id')
            ->bindValue('id', $id);

//        var_dump($select->getBindValues()); exit;

        $sth = $this->pdo->prepare($select->getStatement());
        $sth->execute($select->getBindValues());
        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAll($table) {

        $select = $this->queryFactory->newSelect();

// https://github.com/auraphp/Aura.SqlQuery/blob/265abd3e84d2715b1bcaa23e08b725213b77ba9b/docs/select.md

        $select->cols(['*'])->from($table); // * - select all, из таблицы users. Ниже ещё инструкция, как выбирать
        //  'id',                       // column name
        //  'name AS namecol',          // one way of aliasing
        //  'col_name' => 'col_alias',  // another way of aliasing
        //  'COUNT(foo) AS foo_count'   // embed calculations directly
// ])

// prepare the statement
        $sth = $this->pdo->prepare($select->getStatement());

// bind the values and execute
        $sth->execute($select->getBindValues());

// get the results back as an associative array
        return $sth->fetchAll(PDO::FETCH_ASSOC);


    }

    public function insert($data, $table) {
        $insert = $this->queryFactory->newInsert();

        $insert
            ->into($table)                   // INTO this table
            ->cols($data);

//        echo "<pre>";
//        var_dump($insert->getStatement());
//        exit;

        $sth = $this->pdo->prepare($insert->getStatement());
        $sth->execute($insert->getBindValues());
    }

    public function update($data, $id, $table) {
        $update = $this->queryFactory->newUpdate();

        $update
            ->table($table)                  // update this table
            ->cols($data)                       // bind values as "SET bar = :bar"
            ->where('id = :id')
            ->bindValue('id', $id);

//        var_dump($update->getBindValues());exit;

        $sth = $this->pdo->prepare($update->getStatement());

        $sth->execute($update->getBindValues());
    }

    public function deleteLine($id, $table) {
        $delete = $this->queryFactory->newDelete();

        $delete
            ->from($table)
            ->where("id = :id")
            ->bindValue('id', $id);

//        var_dump($delete->getBindValues());exit;

        $sth = $this->pdo->prepare($delete->getStatement());

        $sth->execute($delete->getBindValues());
    }





}




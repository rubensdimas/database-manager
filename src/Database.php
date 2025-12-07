<?php

namespace Crefito11\DatabaseManager;

class Database
{
  /**
   * Host de conexão com o banco de dados.
   *
   * @var string
   */
  private static $host;

  /**
   * Nome do banco de dados.
   *
   * @var string
   */
  private static $name;

  /**
   * Usuário do banco.
   *
   * @var string
   */
  private static $user;

  /**
   * Senha de acesso ao banco de dados.
   *
   * @var string
   */
  private static $pass;

  /**
   * Porta de acesso ao banco.
   *
   * @var int
   */
  private static $port;

  /**
   * Nome da tabela a ser manipulada.
   *
   * @var string
   */
  private $table;

  /**
   * Instancia de conexão com o banco de dados.
   *
   * @var \PDO
   */
  private $connection;

  /**
   * Método responsável por configurar a classe.
   *
   * @param string $host
   * @param string $name
   * @param string $user
   * @param string $pass
   * @param int    $port
   */
  public static function config($host, $name, $user, $pass, $port = 3306)
  {
    self::$host = $host;
    self::$name = $name;
    self::$user = $user;
    self::$pass = $pass;
    self::$port = $port;
  }

  /**
   * Define a tabela e instancia e conexão.
   *
   * @param string $table
   */
  public function __construct($table = null)
  {
    $this->table = $table;
    $this->setConnection();
  }

  public function __destruct()
  {
    try {
      $this->connection = null;
    } catch (\PDOException $e) {
      echo $e->getMessage();
    } catch (\Exception $e) {
      echo $e->getMessage();
    }
  }

  /**
   * Método responsável por criar uma conexão com o banco de dados.
   */
  private function setConnection()
  {
    if (!$this->connection) {
      try {
        $this->connection = new \PDO('mysql:host=' . self::$host . ';dbname=' . self::$name . ';port=' . self::$port, self::$user, self::$pass, [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4 COLLATE utf8mb4_swedish_ci']);
        $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
      } catch (\PDOException $e) {
        echo $e->getMessage();
      }
    }

    return $this->connection;
  }

  /**
   * Método responsável por executar queries dentro do banco de dados.
   *
   * @param string $query
   * @param array  $params
   *
   * @return \PDOStatement|bool|null
   */
  public function execute($query, $params = [])
  {
    try {
      $statement = $this->connection->prepare($query);
      $statement->execute($params);

      return $statement;
    } catch (\PDOException $e) {
      echo $e->getMessage();
    }
  }

  /**
   * Método responsável por inserir dados no banco.
   *
   * @param array $values [ field => value ]
   *
   * @return int ID inserido
   */
  public function insert($values)
  {
    // DADOS DA QUERY
    $fields = array_keys($values);
    $binds = array_pad([], count($fields), '?');

    // MONTA A QUERY
    $query = 'INSERT INTO ' . $this->table . ' (' . implode(',', $fields) . ') VALUES (' . implode(',', $binds) . ')';

    // EXECUTA O INSERT
    $this->execute($query, array_values($values));

    // RETORNA O ID INSERIDO
    return $this->connection->lastInsertId();
  }

  /**
   * Método responsável por executar uma consulta no banco.
   *
   * @param string $where
   * @param string $order
   * @param string $limit
   * @param string $fields
   *
   * @return \PDOStatement
   */
  public function select($where = null, $order = null, $limit = null, $fields = '*')
  {
    // DADOS DA QUERY
    $where = !is_null($where) && strlen($where) ? 'WHERE ' . $where : '';
    $order = !is_null($order) && strlen($order) ? 'ORDER BY ' . $order : '';
    $limit = !is_null($limit) && strlen($limit) ? 'LIMIT ' . $limit : '';

    // MONTA A QUERY
    $query = 'SELECT ' . $fields . ' FROM ' . $this->table . ' ' . $where . ' ' . $order . ' ' . $limit;

    // EXECUTA A QUERY
    return $this->execute($query);
  }

  /**
   * Método responsável por executar uma consulta no banco.
   *
   * @param string $where
   * @param string $order
   * @param string $limit
   * @param string $fields
   *
   * @return \PDOStatement
   */
  public function selectRespostaWithJoin($inner = null, $where = null, $order = null, $limit = null, $fields = '*')
  {
    // DADOS DA QUERY
    $where = !is_null($where) && strlen($where) ? 'WHERE ' . $where : '';
    $order = !is_null($order) && strlen($order) ? 'ORDER BY ' . $order : '';
    $limit = !is_null($limit) && strlen($limit) ? 'LIMIT ' . $limit : '';

    // MONTA A QUERY
    $query = 'SELECT ' . $fields . ' FROM ' . $this->table . ' ' . $inner . ' ' . $where . ' ' . $order . ' ' . $limit;

    // EXECUTA A QUERY
    return $this->execute($query);
  }

  /**
   * Método responsável por executar uma consulta no banco com inner join.
   *
   * @param string $innerJoin
   * @param string $innerJoin2
   * @param string $on
   * @param string $on2
   * @param string $fields
   *
   * @return \PDOStatement
   */
  public function selectWithJoinEventos($where = null, $order = null, $limit = null, $fields = '*', $innerJoin = null)
  {
    // DADOS DA QUERY
    $where = !is_null($where) && strlen($where) ? 'WHERE ' . $where : '';
    $order = !is_null($order) && strlen($order) ? 'ORDER BY ' . $order : '';
    $limit = !is_null($limit) && strlen($limit) ? 'LIMIT ' . $limit : '';

    // MONTA A QUERY
    $query = 'SELECT ' . $fields . ' FROM ' . $this->table . ' ' . $innerJoin . ' ' . $where . ' ' . $order . ' ' . $limit;

    // EXECUTA A QUERY
    return $this->execute($query);
  }

  /**
   * Método responsável por executar uma consulta no banco com inner join.
   *
   * @param string $where
   * @param string $limit
   * @param string $fields
   *
   * @return \PDOStatement
   */
  public function selectWithJoin($where = null, $limit = null, $fields = '*')
  {
    // DADOS DA QUERY
    $where = !is_null($where) && strlen($where) ? 'WHERE ' . $where : '';
    $limit = !is_null($limit) && strlen($limit) ? 'LIMIT ' . $limit : '';

    // MONTA A QUERY
    $query = 'SELECT ' . $fields . ' FROM ' . $this->table . ' INNER JOIN manifestante ON manifestante.id_manifestante = solicitacao.manifestante
    INNER JOIN endereco_manifestante ON endereco_manifestante.id_endereco = solicitacao.endereco ' . $where . ' ' . $limit;

    // EXECUTA A QUERY
    return $this->execute($query);
  }

  /**
   * Método responsável por executar uma consulta no banco com inner join.
   *
   * @param string $where
   * @param string $limit
   * @param string $fields
   *
   * @return \PDOStatement
   */
  public function selectWithJoin1($where = null, $limit = null, $fields = '*')
  {
    // DADOS DA QUERY
    $where = !is_null($where) && strlen($where) ? 'WHERE ' . $where : '';
    $limit = !is_null($limit) && strlen($limit) ? 'LIMIT ' . $limit : '';

    // MONTA A QUERY
    $query = 'SELECT ' . $fields . ' FROM ' . $this->table . ' INNER JOIN registro_pf_info_pessoal ON registro_pf_info_pessoal.id_pf_dados = registro_pf.info_pessoal INNER JOIN registro_pf_contato ON registro_pf_contato.id_pf_contato = registro_pf.contato INNER JOIN registro_pf_identificacao ON registro_pf_identificacao.id_pf_identificacao = registro_pf.identificacao INNER JOIN registro_pf_cam ON registro_pf_cam.id_pf_cam = registro_pf.cam INNER JOIN registro_pf_eleitor ON registro_pf_eleitor.id_pf_eleitor = registro_pf.titulo_eleitor INNER JOIN registro_pf_formacao ON registro_pf_formacao.id_pf_formacao = registro_pf.formacao ' . $where . ' ' . $limit;

    // EXECUTA A QUERY
    return $this->execute($query);
  }

  /**
   * Método responsável por executar uma consulta no banco com inner join.
   *
   * @param string $where
   * @param string $limit
   * @param string $fields
   *
   * @return \PDOStatement
   */
  public function selectWithJoin2($where = null, $limit = null, $fields = '*')
  {
    // DADOS DA QUERY
    $where = !is_null($where) && strlen($where) ? 'WHERE ' . $where : '';
    $limit = !is_null($limit) && strlen($limit) ? 'LIMIT ' . $limit : '';

    // MONTA A QUERY
    $query = 'SELECT ' . $fields . ' FROM ' . $this->table . ' INNER JOIN registro_pf_info_pessoal ON registro_pf_info_pessoal.id_pf_dados = registro_pf.info_pessoal INNER JOIN registro_pf_contato ON registro_pf_contato.id_pf_contato = registro_pf.contato INNER JOIN registro_pf_identificacao ON registro_pf_identificacao.id_pf_identificacao = registro_pf.identificacao INNER JOIN segunda_via_cedula ON segunda_via_cedula.id_cedula = registro_pf.segunda_via INNER JOIN registro_pf_eleitor ON registro_pf_eleitor.id_pf_eleitor = registro_pf.titulo_eleitor ' . $where . ' ' . $limit;

    // /EXECUTA A QUERY
    return $this->execute($query);
  }

  /**
   * Método responsável por executar uma consulta no banco com inner join.
   *
   * @param string $where
   * @param string $limit
   * @param string $fields
   *
   * @return \PDOStatement
   */
  public function selectWithJoin3($where = null, $limit = null, $fields = '*')
  {
    // DADOS DA QUERY
    $where = !is_null($where) && strlen($where) ? 'WHERE ' . $where : '';
    $limit = !is_null($limit) && strlen($limit) ? 'LIMIT ' . $limit : '';

    // MONTA A QUERY
    $query = 'SELECT ' . $fields . ' FROM ' . $this->table . ' INNER JOIN registro_pf_info_pessoal ON registro_pf_info_pessoal.id_pf_dados = registro_pf.info_pessoal INNER JOIN registro_pf_contato ON registro_pf_contato.id_pf_contato = registro_pf.contato INNER JOIN registro_pf_identificacao ON registro_pf_identificacao.id_pf_identificacao = registro_pf.identificacao INNER JOIN transferencia_registro ON transferencia_registro.id_transferencia = registro_pf.transferencia INNER JOIN registro_pf_eleitor ON registro_pf_eleitor.id_pf_eleitor = registro_pf.titulo_eleitor INNER JOIN registro_pf_formacao ON registro_pf_formacao.id_pf_formacao = registro_pf.formacao ' . $where . ' ' . $limit;

    // EXECUTA A QUERY
    return $this->execute($query);
  }

  /**
   * Método responsável por executar uma consulta no banco com inner join.
   *
   * @param string $where
   * @param string $limit
   * @param string $fields
   *
   * @return \PDOStatement
   */
  public function selectWithJoin4($where = null, $limit = null, $fields = '*')
  {
    // DADOS DA QUERY
    $where = !is_null($where) && strlen($where) ? 'WHERE ' . $where : '';
    $limit = !is_null($limit) && strlen($limit) ? 'LIMIT ' . $limit : '';

    // MONTA A QUERY
    $query = 'SELECT ' . $fields . ' FROM ' . $this->table . ' INNER JOIN registro_pf_info_pessoal ON registro_pf_info_pessoal.id_pf_dados = registro_pf.info_pessoal INNER JOIN registro_pf_contato ON registro_pf_contato.id_pf_contato = registro_pf.contato INNER JOIN registro_pf_identificacao ON registro_pf_identificacao.id_pf_identificacao = registro_pf.identificacao INNER JOIN baixa_registro_pf ON baixa_registro_pf.id_baixa_registro = registro_pf.baixa ' . $where . ' ' . $limit;

    // EXECUTA A QUERY
    return $this->execute($query);
  }

  /**
   * Método responsável por executar uma consulta no banco com inner join.
   *
   * @param string $where
   * @param string $limit
   * @param string $fields
   *
   * @return \PDOStatement
   */
  public function selectWithJoinPj($where = null, $limit = null, $fields = '*')
  {
    // DADOS DA QUERY
    $where = !is_null($where) && strlen($where) ? 'WHERE ' . $where : '';
    $limit = !is_null($limit) && strlen($limit) ? 'LIMIT ' . $limit : '';

    // MONTA A QUERY
    $query = 'SELECT ' . $fields . ' FROM ' . $this->table . ' INNER JOIN registro_pf_contato ON registro_pf_contato.id_pf_contato = registro_pf.contato INNER JOIN registro_pj ON registro_pj.id_registro_pj = registro_pf.id_pj ' . $where . ' ' . $limit;

    // EXECUTA A QUERY
    return $this->execute($query);
  }

  /**
   * Método responsável por executar uma consulta no banco com inner join.
   *
   * @param string $where
   * @param string $limit
   * @param string $fields
   *
   * @return \PDOStatement
   */
  public function selectWithJoinEstagiario($where = null, $limit = null, $fields = '*')
  {
    // DADOS DA QUERY
    $where = !is_null($where) && strlen($where) ? 'WHERE ' . $where : '';
    $limit = !is_null($limit) && strlen($limit) ? 'LIMIT ' . $limit : '';

    // MONTA A QUERY
    $query = 'SELECT ' . $fields . ' FROM ' . $this->table . ' INNER JOIN registro_pf_info_pessoal ON registro_pf_info_pessoal.id_pf_dados = registro_pf.info_pessoal INNER JOIN registro_pf_contato ON registro_pf_contato.id_pf_contato = registro_pf.contato INNER JOIN registro_pf_identificacao ON registro_pf_identificacao.id_pf_identificacao = registro_pf.identificacao ' . $where . ' ' . $limit;

    // EXECUTA A QUERY
    return $this->execute($query);
  }

  /**
   * Método responsável por executar uma consulta no banco com inner join.
   *
   * @param string $where
   * @param string $limit
   * @param string $fields
   *
   * @return \PDOStatement
   */
  public function selectWithJoinConsultorio($where = null, $limit = null, $fields = '*')
  {
    // DADOS DA QUERY
    $where = !is_null($where) && strlen($where) ? 'WHERE ' . $where : '';
    $limit = !is_null($limit) && strlen($limit) ? 'LIMIT ' . $limit : '';

    // MONTA A QUERY
    $query = 'SELECT ' . $fields . ' FROM ' . $this->table . ' INNER JOIN registro_pf_contato ON registro_pf_contato.id_pf_contato = registro_pf.contato INNER JOIN registro_consultorio ON registro_consultorio.id_consultorio = registro_pf.consultorio ' . $where . ' ' . $limit;

    // EXECUTA A QUERY
    return $this->execute($query);
  }

  /**
   * Método responsável por executar uma consulta no banco com inner join.
   *
   * @param string $where
   * @param string $limit
   * @param string $fields
   *
   * @return \PDOStatement
   */
  public function selectWithJoinConsultorioDrf($where = null, $limit = null, $fields = '*')
  {
    // DADOS DA QUERY
    $where = !is_null($where) && strlen($where) ? 'WHERE ' . $where : '';
    $limit = !is_null($limit) && strlen($limit) ? 'LIMIT ' . $limit : '';

    // MONTA A QUERY
    $query = 'SELECT ' . $fields . ' FROM ' . $this->table . ' INNER JOIN registro_pf_contato ON registro_pf_contato.id_pf_contato = registro_pf.contato INNER JOIN registro_consultorio ON registro_consultorio.id_consultorio = registro_pf.consultorio INNER JOIN end_consultorio ON end_consultorio.id_end_consultorio = registro_pf.end_consultorio ' . $where . ' ' . $limit;

    // EXECUTA A QUERY
    return $this->execute($query);
  }

  /**
   * Método responsável por executar uma consulta no banco com inner join.
   * @param string $join
   * @param string $where
   * @param string $limit
   * @param string $fields
   *
   * @return \PDOStatement
   */
  public function selectWithJoinDefault($join = null, $where = null, $order = null, $limit = null, $fields = '*')
  {
    // DADOS DA QUERY
    $where = !is_null($where) && strlen($where) ? 'WHERE ' . $where : '';
    $order = !is_null($order) && strlen($order) ? 'ORDER BY ' . $order : '';
    $limit = !is_null($limit) && strlen($limit) ? 'LIMIT ' . $limit : '';

    // MONTA A QUERY
    $query = 'SELECT ' . $fields . ' FROM ' . $this->table . ' ' . $join . ' ' . $where . ' ' . $order . ' ' . $limit;

    // EXECUTA A QUERY
    return $this->execute($query);
  }

  /**
   * Método responsável por executar atualizações no banco de dados.
   *
   * @param string $where
   * @param array  $values [ field => value ]
   *
   * @return bool
   */
  public function update($where, $values)
  {
    // DADOS DA QUERY
    $fields = array_keys($values);

    // MONTA A QUERY
    $query = 'UPDATE ' . $this->table . ' SET ' . implode('=?,', $fields) . '=? WHERE ' . $where;

    // EXECUTAR A QUERY
    $this->execute($query, array_values($values));

    // RETORNA SUCESSO
    return true;
  }

  /**
   * Método responsável por excluir dados do banco.
   *
   * @param string $where
   *
   * @return bool
   */
  public function delete($where)
  {
    // MONTA A QUERY
    $query = 'DELETE FROM ' . $this->table . ' WHERE ' . $where;

    // EXECUTA A QUERY
    $this->execute($query);

    // RETORNA SUCESSO
    return true;
  }
}

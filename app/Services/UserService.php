<?php

namespace App\Services;
use Exception;
use App\Models\User;
use App\Services\Service;

class UserService extends Service{

    private $tableBD = 'usuario';
    private $fields = 'a.id,a.token,a.login,a.nome,a.telefone,a.email,a.senha,a.date_insert,a.status,
    b.id as id_type,b.title as title_type,b.ref as ref_type';
    private $forpage = 20;

    function __construct() {
        parent::__construct();
        $this->table = "usuario";
    }

    /**
     * Verifica se o usuário por login e e-mail
     *
     * @param string $login
     * @param boolean $isAPI
     * @return User
     */
    public function getByAuth($login,$isAPI = false)
    {
        $stmt = $this->PDO->prepare(
            " SELECT {$this->fields} FROM {$this->table} AS a WHERE a.login LIKE '%:login%' OR a.email LIKE '%:login%' AND a.status = :status_ativo "
        );
        $stmt->bindValue(":login",$login);
        $stmt->bindValue(":status_ativo",User::STATUS_ATIVO);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_OBJ);
        return $this->transformResult($result,$isAPI);
    }

    /**
     * Faz Paginação dos usuários
     *
     * @param array $body
     * @param boolean $isAPI
     * @return array
     */
    public function paginate($body,$isAPI = false)
    {

        $order = ' a.id DESC ';
        $completeWhere = $this->mountWhere($body);
        $page = isset($body['page']) ? $body['page'] : 1;
        $forpageApi = isset($body['forpage']) && $body['forpage'] != '' ? \intval($body['forpage']) : $this->forpage;

        $innerTables = [
            " LEFT JOIN type_user AS b ON a.id_type = b.id "
        ];
        $paginateResults = $this->genericPaginate($this->fields,$innerTables,$completeWhere,$page,$forpageApi,$order);
        $paginateResults['results'] = $this->transformResults($paginateResults['results'],$isAPI);
        
        return $paginateResults;

    }

    /**
     * Salva um novo usuário
     *
     * @param array $body
     * @param boolean $returnId
     * @return object
     */
    public function save($body,$returnId = false)
    {
        $body['senha'] = \md5($body['senha']);
        return $this->genericeSave($body,['id'],$returnId);
    }

    /**
     * Atualiza os dados do usuário
     *
     * @param array $body
     * @return object
     */
    public function update($body)
    {
        $updateQuery = "";
        $excludeKeys = ['id','rsenha','senha','id_empresa'];
        return $this->genericUpdate($body,$excludeKeys);
    }

    /**
     * Atualiza o status do usuário
     *
     * @param int $status
     * @param int $id_user
     * @return object
     */
    public function updateStatus($status,$id_user)
    {
        if (!in_array($status,[0,1])){
            return \throwJsonException('Status inválido');
        }
        return $this->genericUpdateStatus($status,$id_user);
    }

    /**
     * Atualiza o tipo do usuário
     *
     * @param int $type
     * @param string $token
     * @return object
     */
    public function changeType($type,$token)
    {
        try{
            $stmt = $this->PDO->prepare(
                ' UPDATE ' . $this->tableBD . ' SET '.
                ' id_type = :type '.
                ' WHERE token = :token '
            );
            $stmt->bindValue(':token',$token);
            $stmt->bindValue(':type',$type);
            $stmt->execute();
            return true;
        }catch (\Exception $ex) {
            return \throwJsonException($ex->getMessage());
        }
    }

    /**
     * Remove o usuário do banco
     *
     * @param int $id_user
     * @return object
     */
    public function delete($id_user)
    {
        try {
            $stmt = $this->PDO->prepare(
                ' DELETE FROM ' . $this->tableBD.
                ' WHERE id = :id '
            );
            $stmt->bindValue(':id',$id_user);
            $stmt->execute();
            return true;
        } catch (\Exception $ex) {
            return \throwJsonException($ex->getMessage());
        }
    }

    /**
     * Retorna a senha do usuário
     *
     * @param int $id_user
     * @return string
     */
    public function returnPass($id_user){
        $pass = null;
        $stmt = $this->PDO->prepare(
            ' SELECT senha FROM '. $this->tableBD.
            ' WHERE id = :id_user '
        );
        $stmt->bindValue(':id_user',$id_user);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_OBJ);
        if ($result != false && $result != null){
            $pass = $result->senha;
        }
        return $pass;
    }

    /**
     * Retorna os usuários
     *
     * @param string $paran
     * @param object $value
     * @return User
     */
    public function returnByParan($paran,$value)
    {

        if (!in_array($paran,['token','login','id','email'])){
            return null;
        }

        $stmt = $this->PDO->prepare(
            ' SELECT ' . $this->fields . ' FROM ' . $this->tableBD . ' AS a '.
            ' LEFT JOIN type_user AS b ON a.id_type = b.id '.
            ' WHERE a.'.$paran.' = :'.$paran
        );
        $stmt->bindValue(':'.$paran,$value);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_OBJ);
        return $this->transformResult($result);
    }

    /**
     * Retorna o último 
     *
     * @return void
     */
    public function getLast()
    {
        $stmt = $this->PDO->prepare(
            ' SELECT ' . $this->fields . ' FROM ' . $this->tableBD . ' AS a '.
            ' LEFT JOIN type_user AS b ON a.id_type = b.id '.
            ' ORDER BY a.id DESC LIMIT 1 '
        );
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_OBJ);
        return $this->transformResult($result);
    }

    /**
     * Verifica se já existe um usuário com o login
     *
     * @param string $login
     * @return boolean
     */
    public function checkUser($login,$token = "")
    {
        $stmt = $this->PDO->prepare('SELECT id,token FROM ' . $this->tableBD . ' WHERE login = :login and status = 1');
        $stmt->bindValue(':login',$login);
        $stmt->execute();
        $results = $stmt->fetchAll(\PDO::FETCH_OBJ);
        if ($token != ""){
            if (count($results) > 0){
                $exist = false;
                foreach($results as $key => $value){
                    if ($value->token != $token){
                        $exist = true;
                        break;
                    }
                }
                return $exist;
            }else{
                return false;
            }
        }else{
            if (count($results) > 0){
                return true;
            }else{
                return false;
            }
        }
    }

    /**
     * Verifica se já existe um usuário com o email
     *
     * @param string $email
     * @return boolean
     */
    public function checkEmail($email,$token = "")
    {
        $stmt = $this->PDO->prepare('SELECT id,token FROM ' . $this->tableBD . ' WHERE email = :email and status = 1');
        $stmt->bindValue(':email',$email);
        $stmt->execute();
        $results = $stmt->fetchAll(\PDO::FETCH_OBJ);
        if ($token != ""){
            if (count($results) > 0){
                $exist = false;
                foreach($results as $key => $value){
                    if ($value->token != $token){
                        $exist = true;
                        break;
                    }
                }
                return $exist;
            }else{
                return false;
            }
        }else{
            if (count($results) > 0){
                return true;
            }else{
                return false;
            }
        }
    }

    /**
     * Retorna o objeto de recuperação de senha
     *
     * @param string $token
     * @return object
     */
    public function getRecoverToken($token)
    {
        $stmt = $this->PDO->prepare('SELECT token,email,id_user FROM recover_password '.
        ' WHERE date_expire > CURRENT_TIMESTAMP AND token = :token '
        );
        $stmt->bindValue(':token',$token);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_OBJ);
        return $result != false && $result != null ? $result : null;
    }

    /**
     * Remove o token de recuperação da senha
     *
     * @param string $token
     * @return boolean
     */
    public function removeRecoverToken($token)
    {
        try{
            $stmt = $this->PDO->prepare('DELETE FROM recover_password ' .
            ' WHERE token = :token ');
            $stmt->bindValue(':token', $token);
            $stmt->execute();
            return true;
        } catch (\Exception $ex){
            return false;
        }
    }

    /**
     * Cria o token do usuário para recuperação
     *
     * @param string $email
     * @param int $id_user
     * @return string
     */
    public function createRecoverToken($email,$id_user){
        
        $stmt = $this->PDO->prepare('SELECT token,email FROM recover_password '.
        ' WHERE date_expire > CURRENT_TIMESTAMP AND email = :email '
        );
        $stmt->bindValue(':email',$email);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_OBJ);

        if ($result != null && $result != false){
            return $result->token;
        }

        $token = bin2hex(random_bytes(50));
        try{
            $stmt = $this->PDO->prepare('INSERT INTO recover_password ' .
            ' (token,email,id_user,date_expire) '.
            ' VALUES ( :token,:email,:id_user,DATE_ADD(now() , INTERVAL 1 HOUR) )');
            $stmt->bindValue(':token', $token);
            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':id_user', $id_user);
            $stmt->execute();
            return $token;
        } catch (Exception $ex){
            return \throwJsonException($ex->getMessage());
        }

    }

    /**
     * Atualiza a senha do usuário
     *
     * @param string $token
     * @param string $pass
     * @return object
     */
    public function updatePass($token,$pass)
    {
        try{
            $stmt = $this->PDO->prepare('UPDATE ' . $this->tableBD . ' SET ' .
            'senha = :pass '.
            ' WHERE token = :token ');
            $stmt->bindValue(':pass', md5($pass));
            $stmt->bindValue(':token', $token);
            $stmt->execute();
            return true;
        } catch (Exception $ex){
            return \throwJsonException($ex->getMessage());
        }
    }

    /**
     * Transforma o array para array de Users
     *
     * @param array $results
     * @param boolean $isApi
     * @return Users[]
     */
    private function transformResults($results,$isApi = false)
    {
        $users = [];
        if (!\is_array($results) || count($results) == 0){
            return $users;
        }
        for ($i = 0; $i < count($results); $i++) {
            $result = (object) $results[$i];
            $user = $this->transformResult($result,$isApi);
            if ($user != null){
                $users[] = $user;
            }
        }
        return $users;
    }

    /**
     * Transforma para OBJETO User
     *
     * @param object $result
     * @param boolean $isApi
     * @return User
     */
    private function transformResult($result,$isApi = false)
    {
        
        if ($result == null || $result == false) {
            return null;
        }

        $user = new User();
        if (isset($result->status)){
            $user->setStatus($result->status);
        }
        $user->setId($result->id);
        $user->setToken($result->token);
        $user->setLogin($result->login);
        $user->setType($result->id_type);
        $user->setNome($result->nome);
        $user->setEmail($result->email);
        $user->setDataInsert($result->date_insert);
        $user->setTelefone($result->telefone);

        if (isset($result->id_type) && $result->id_type != null){
            $type = [
                'id' => $result->id_type,
                'ref' => $result->ref_type,
                'title' => $result->title_type
            ];
            $user->setTypeObject($type);
        }

        return $user;

    }

    /**
     * Monta o SQL STRING QUERY
     *
     * @param array $body
     * @return string
     */
    private function mountWhere($body)
    {
        $completeWhere = '';
        if (!\is_array($body) || count($body) == 0){
            return $completeWhere;
        }
        $keys = array_keys($body);
        foreach($keys as $k => $v){
            switch($v){
                case 'status':
                    if ($body[$v] != ''){
                        $completeWhere .= " AND a.status = ". $body[$v];
                    }
                    break;
                case 'exclude_status':
                    if ($body[$v] != ''){
                        $completeWhere .= " AND a.status != {$body[$v]}";
                    }
                    break;
                case 'id_type':
                    if ($body[$v] != ''){
                        $completeWhere .= " AND a.id_type = " . $body[$v];
                    }
                    break;
                case 'search':
                    if ($body[$v] != ''){
                        $search = $body[$v];
                        if (!preg_match('#[^0-9]#',$search)){
                            $searchHelper = \mask_cpf($body[$v]);
                        }else{
                            $searchHelper = $body[$v];
                        }
                        $completeWhere .= " AND ( a.nome LIKE '%".$searchHelper."%' OR a.email LIKE '%".$searchHelper."%' )";
                    }
                    break;
                case 'date_insert':
                    if ($body[$v] != ''){
                        $completeWhere .= " AND DATE(a.date_insert) >= " .  "'".strval($body[$v]) ."'";
                    }
                    break;
                case 'date_final':
                    if ($body[$v] != ''){
                        $completeWhere .= " AND DATE(a.date_insert) <= " . "'".strval($body[$v])."'";
                    }
                    break;
                default :
                    break;
            }
        }
        return $completeWhere;   
    }

}
<?php

namespace App\Services\Users;
use Exception;
use App\Models\User;
use App\Services\Service;

class UserService extends Service{

    private $fields = 'a.id,a.token,a.login,a.nome,a.telefone,a.email,a.senha,a.date_insert,a.status,
    b.id as id_type,b.title as title_type,b.ref as ref_type';
    private $forpage = 20;
    private $tableRecover = "user_recover_password";

    function __construct() {
        parent::__construct();
        $this->setTable("user");
        $this->setPrefix("a");
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
        if (isset($body['senha'])){
            $body['senha'] = \password_return($body['senha']);
        }
        if (isset($body['pass'])){
            $body['senha'] = \password_return($body['pass']);
            unset($body['pass']);
        }
        return $this->genericeSave($body,['id','rsenha'],$returnId);
    }

    /**
     * Atualiza os dados do usuário
     *
     * @param array $body
     * @return object
     */
    public function update($body)
    {
        
        $excludeKeys = ['id','token'];
        
        if (!isset($body['senha']) || empty($body['senha'])){
            $excludeKeys[] = 'senha';
        }else{
            $body['senha'] = \password_return($body['senha']);
        }

        if (!isset($body['rsenha']) || empty($body['rsenha'])){
            $excludeKeys[] = 'rsenha';
        }else{
            unset($body['rsenha']);
        }
        
        return $this->genericUpdate($body,$excludeKeys,true,false);
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
        $listStatus = [
            User::STATUS_BLOQUEADO,
            User::STATUS_ATIVO
        ];
        if (!in_array($status,$listStatus)){
            throw new Exception("Status inválido");
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
                " UPDATE {$this->table} SET id_type = :type WHERE token = :token "
            );
            $stmt->bindValue(':token',$token);
            $stmt->bindValue(':type',$type);
            $stmt->execute();
            return true;
        }catch (\Exception $ex) {
            throw new Exception($ex->getMessage());
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
        return $this->genericDelete($id_user);
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
            " SELECT senha FROM {$this->table} WHERE id = :id_user "
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
        $params = ['token','login','id','email'];
        $innerTables = [
            " LEFT JOIN type_user AS b ON a.id_type = b.id "
        ];
        return $this->transformResult($this->genericReturnParan($paran,$value,$this->fields,$params,$innerTables));
    }

    /**
     * Verifica se já existe um usuário com o login
     *
     * @param string $login
     * @return boolean
     */
    public function checkUser($login,$token = "")
    {
        $stmt = $this->PDO->prepare(
            " SELECT id,token FROM {$this->table} WHERE login = :login AND status = :status_ativo "
        );
        $stmt->bindValue(':login',$login);
        $stmt->bindValue(':status_ativo',User::STATUS_ATIVO);
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
        $stmt = $this->PDO->prepare(
            " SELECT id,token FROM {$this->table} WHERE email = :email and status = :status_ativo "
        );
        $stmt->bindValue(':email',$email);
        $stmt->bindValue(':status_ativo',User::STATUS_ATIVO);
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
        $stmt = $this->PDO->prepare(
            " SELECT token,email,id_user FROM {$this->tableRecover} ".
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
            $stmt = $this->PDO->prepare(
                " DELETE FROM {$this->tableRecover} WHERE token = :token "
            );
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
        
        $stmt = $this->PDO->prepare(
            " SELECT token,email FROM {$this->tableRecover} ".
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
            $stmt = $this->PDO->prepare(
                " INSERT INTO {$this->tableRecover} " .
                ' (token,email,id_user,date_expire) '.
                ' VALUES ( :token,:email,:id_user,DATE_ADD(now() , INTERVAL 1 HOUR) )'
            );
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
            $stmt = $this->PDO->prepare(
                " UPDATE {$this->table} SET senha = :pass WHERE token = : token "
            );
            $stmt->bindValue(':pass', password_return($pass));
            $stmt->bindValue(':token', $token);
            $stmt->execute();
            return true;
        } catch (Exception $ex){
            throw new Exception($ex->getMessage());
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

        foreach($body as $k => $v){
            switch($k){
                case 'status':
                    $completeWhere .= $v != '' ? " AND a.status = {$v} " : "";
                    break;
                case 'exclude_status':
                    $completeWhere .= $v != '' ? " AND a.status != {$v} " : "";
                    break;
                case 'id_type':
                    $completeWhere .= $v != '' ? " AND a.id_type = {$v} " : "";
                    break;
                case 'search':
                    if (!empty($v)){
                        $search = $v;
                        if (!preg_match('#[^0-9]#',$search)){
                            $searchHelper = \mask_cpf($v);
                        }else{
                            $searchHelper = $v;
                        }
                        $completeWhere .= " AND ( a.nome LIKE '%{$searchHelper}%' OR a.email LIKE '%{$searchHelper}%' )";
                    }
                    break;
                case 'date_insert':
                    if (!empty($v)){
                        $completeWhere .= " AND DATE(a.date_insert) >= '{$v}' ";
                    }
                    break;
                case 'date_final':
                    if (!empty($v)){
                        $completeWhere .= " AND DATE(a.date_insert) <= '{$v}' ";
                    }
                    break;
                default :
                    break;
            }
        }
        return $completeWhere;
    }

}
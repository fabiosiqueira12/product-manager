<?php

namespace App\Core;

use Exception;
use App\Models\User;
use App\Models\Company;
use App\Services\Users\UserService;
use App\Services\EmpresaService;

class AuthControl
{
    private $nameSession = 'ulx';
    private $nameSessionCompany = 'clx';

    function __construct()
	{

    }

    /**
     * Faz login dentro do sistem
     *
     * @param object $login Pode ser CPF|Login ou E-mail
     * @param string $senha
     * @param Company $company
     * @return void
     */
    public function makeLogin($login, $senha,$company = null)
    {
        
        if ($login == '' || $senha == "") {
            return \throwJsonException('As credenciais não foram fornecidas');
        }

        $userService = new UserService();
        $user = null;
        $user = $userService->returnByParan('login',$login);
            
        if ($user == null){
            return \throwJsonException('Usuário não encontrado');
        }

        if ($user->getStatus() == User::STATUS_BLOQUEADO){
            return \throwJsonException("O seu usuário está bloqueado");
        }
            
        $passSave = $userService->returnPass($user->getId());
        if (empty($passSave)){
            throw new Exception("Erro inesperado, recarregue a página e tente novamente");
        }

        if (!password_verify($senha, $passSave)) {
            throw new Exception('Senha Incorreta!');
        }
                
        $this->saveJustUser($user);

        return \json([
            'message' => 'Logado com sucesso',
            'result' => 1,
            'action' => 'reload'
        ]);

    }

    private function verifyPassword($senhaUser, $senhaDigitada)
    {
        if (md5($senhaDigitada) == $senhaUser) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Verifica se o token de acesso e o usuário estão logados
     */
    public function userLogged()
    {
        if ($this->getUserLogged() == null || $this->getUserLogged() == false) {
            return false;
        }        
        return true;
    }

    public function makeLogout()
    {
        $this->removeSession();
    }

    /**
     * Salva nos cookies as informações necessárias
     *
     * @param object $userLogged
     * @param object $companyLogged
     * @return void
     */
    public function saveSession($userLogged,$companyLogged = null)
    {
        $time = \mktime(24,0,0);
        $encodeUser = bin2hex(json_encode($userLogged));
        $encodeCompany = bin2hex(json_encode($companyLogged));
        if (\is_localhost()){
            \setcookie($this->nameSession, $encodeUser, $time,'/product-manager','localhost');
            \setcookie($this->nameSessionCompany, $encodeCompany, $time,'/product-manager','localhost');
        }else{
            \setcookie($this->nameSession, $encodeUser, $time,'/portal','fazagilizar.com.br');
            \setcookie($this->nameSessionCompany, $encodeCompany, $time,'/portal','fazagilizar.com.br');
        }
    }

    /**
     * Salva na sessão as informações do usuário
     */
    public function saveJustUser($userLogged)
    {
        $time = \mktime(24,0,0);
        $encodeUser = bin2hex(json_encode($userLogged));
        if (\is_localhost()){
            \setcookie($this->nameSession, $encodeUser, $time,'/product-manager','localhost');
        }else{
            \setcookie($this->nameSession, $encodeUser, $time,'/portal','fazagilizar.com.br');
        }
    }

    /**
     * Salva na sessão só a empresa
     * @param object $companyLogged
     */
    public function saveJustCompany($companyLogged)
    {
        $time = \mktime(24,0,0);
        $encodeCompany = bin2hex(json_encode($companyLogged));
        if (\is_localhost()){
            \setcookie($this->nameSessionCompany, $encodeCompany, $time,'/product-manager','localhost');
        }else{
            \setcookie($this->nameSessionCompany, $encodeCompany, $time,'/portal','fazagilizar.com.br');
        }
    }

    private function removeSession()
    {
        $time = time() - 3600;
        if (\is_localhost()){
            setcookie($this->nameSession, "", $time,'/product-manager','localhost');
            setcookie($this->nameSessionCompany, "", $time,'/product-manager','localhost');
        }else{
            setcookie($this->nameSession, "", $time,'/portal','fazagilizar.com.br');
            setcookie($this->nameSessionCompany, "", $time,'/portal','fazagilizar.com.br');
        }
    }

    /**
     * Verifica e retorna o objeto de usuário formatado da sessão
     *
     * @return object
     */
    private function getSession()
    {
        if (isset($_COOKIE[$this->nameSession]) && $_COOKIE[$this->nameSession] != ''){
            return \json_decode(\hex2bin($_COOKIE[$this->nameSession]));
        }else{
            return null;
        }
    }


    /**
     * Retorna o usuário logado
     *
     * @return User
     */
    public function getUserLogged()
    {
        $jsonObject = $this->getSession();
        if ($jsonObject == null || $jsonObject == false){
            return null;
        }
        $user = new User(\json_encode($jsonObject));
        return $user;
    }

    /**
     * Verifica e retorna o objeto de empresa formatado da sessão
     *
     * @return object
     */
    private function getSessionCompany()
    {
        if (isset($_COOKIE[$this->nameSessionCompany]) && $_COOKIE[$this->nameSessionCompany] != ''){
            return \json_decode(\hex2bin($_COOKIE[$this->nameSessionCompany]));
        }else{
            return null;
        }
    }

    /**
     * Retorna a empresa logado
     *
     * @return Company
     */
    public function getCompanyLogged()
    {
        $jsonObject = $this->getSessionCompany();
        if ($jsonObject == null || $jsonObject == false){
            return null;
        }
        $company = new Company(\json_encode($jsonObject));
        return $company;
    }

}

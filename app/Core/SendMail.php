<?php

namespace App\Core;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class SendMail
{

    public const NAME_SEND = "Faz Agilizar";
    public const EMAIL_REPLY = 'contato@fazagilizar.com.br';

    private $host;
    private $username;
    private $port;
    private $pass;
    private $protocol;
    private $autenticar;
    private $email;

    public function __construct($host,$username,$port,$pass,$protocol,$autenticar,$email)
    {
        $this->host = $host;
        $this->username = $username;
        $this->pass= $pass;
        $this->protocol = $protocol;
        $this->port = $port;
        $this->autenticar = $autenticar;
        $this->email = $email;
    }

    /**
     * Envia para muitos dispositivos
     *
     * @param string $html
     * @param string $assunto
     * @param array $emails
     * @param string $emailReply
     * @param string $nameReply
     * @param string $actionResult
     * @return object
     */
    public function sendToMany($html,$assunto,$emails,$emailReply,$nameReply,$actionResult = 'success')
    {
        try {
            $count = 0;
            $stmp = new PHPMailer(true);
            $stmp->isSMTP();
            $stmp->isHTML();
            $stmp->CharSet = 'UTF-8';
            $stmp->Host = $this->host;
            $stmp->Username = $this->username;
            $stmp->From = $this->email;    
            $stmp->FromName = self::NAME_SEND;
            $stmp->Password = $this->pass;
            $stmp->Port = $this->port;
            $stmp->SMTPAuth = $this->autenticar == 1 ? true : false;
            if ($this->protocol != ""){
                $stmp->SMTPSecure = $this->protocol;
            }
            $stmp->Subject = $assunto;
            $stmp->clearReplyTos();
            $stmp->addReplyTo(self::EMAIL_REPLY,self::NAME_SEND);
            $stmp->Body = $html;
            $stmp->msgHTML($html);

            foreach($emails as $k => $email){
                $stmp->addAddress($email);
                if ($stmp->send()) {
                    $count++;
                }else{
                    $stmp->smtp->reset();
                }
                $stmp->clearAddresses();
            }
            
            return json([
                'message' => 'E-mail enviado para ' . $count,
                'result' => 1,
                'action' => $actionResult
            ]);
        } catch (\Exception $ex) {
            return throwJsonException($ex->getMessage());
        }
    }
    
    /**
     * Envia para vários com HTMl de casa
     *
     * @param string $assunto
     * @param array $list
     * @param string $emailReply
     * @param string $nameReply
     * @param string $actionResult
     * @return object
     */
    public function sendToManyWithHtml($assunto,$list,$emailReply,$nameReply,$actionResult = 'success')
    {
        try {

            $count = 0;
            $stmp = new PHPMailer(true);
            $stmp->isSMTP();
            $stmp->isHTML();
            $stmp->CharSet = 'UTF-8';
            $stmp->Host = $this->host;
            $stmp->Username = $this->username;
            $stmp->From = $this->email;    
            $stmp->FromName = self::NAME_SEND;
            $stmp->Password = $this->pass;
            $stmp->Port = $this->port;
            $stmp->SMTPAuth = $this->autenticar == 1 ? true : false;
            if ($this->protocol != ""){
                $stmp->SMTPSecure = $this->protocol;
            }
            $stmp->Subject = $assunto;
            $stmp->clearReplyTos();
            $stmp->addReplyTo(self::EMAIL_REPLY,self::NAME_SEND);

            foreach($list as $k => $item){
                $stmp->Body = $item->html;
                $stmp->msgHTML($item->html);
                $stmp->addAddress($item->email);
                if ($stmp->send()) {
                    $count++;
                }else{
                    $stmp->smtp->reset();
                }
                $stmp->clearAddresses();
            }
            
            return json([
                'message' => 'E-mail enviado para ' . $count,
                'result' => 1,
                'action' => $actionResult
            ]);
        } catch (\Exception $ex) {
            return throwJsonException($ex->getMessage());
        }
    }

    /**
     * Envia e-mail com arquivo
     *
     * @param string $html
     * @param string $assunto
     * @param string $emailSend
     * @param string $nameSend
     * @param string $filePath
     * @param string $fileName
     * @param string $emailReply
     * @param string $nameReply
     * @return object
     */
    public function sendWithFile($html,$assunto,$emailSend,$nameSend,$filePath = "",$fileName = "",$emailReply = "",$nameReply = "")
    {
        try {
            
            $stmp = new PHPMailer(true);
            $stmp->isSMTP();
            $stmp->isHTML();
            $stmp->CharSet = 'UTF-8';
            $stmp->Host = $this->host;
            $stmp->Username = $this->username;
            $stmp->From = $this->email;    
            $stmp->FromName = self::NAME_SEND;
            $stmp->Password = $this->pass;
            $stmp->Port = $this->port;
            $stmp->SMTPAuth = $this->autenticar == 1 ? true : false;
            if ($this->protocol != ""){ 
                $stmp->SMTPSecure = $this->protocol;
            }
            $stmp->Subject = $assunto;
            $stmp->clearReplyTos();
            $stmp->addReplyTo(
                !empty($emailReply) ? $emailReply : self::EMAIL_REPLY,
                !empty($nameReply) ? $nameReply : self::NAME_SEND
            );
            $stmp->addAddress($emailSend,$nameSend);
            $stmp->addAttachment($filePath,$fileName);
            $stmp->Body = $html;
            $stmp->msgHTML($html);
            if (!$stmp->send()) {
                return \throwJsonException($stmp->ErrorInfo);
            }
            return true;
        } catch (\Exception $ex) {
            return throwJsonException($ex->getMessage());
        }
    }

    /**
     * Envia o email com PHPMailer
     *
     * @param string $html
     * @param string $assunto
     * @param string $emailSend
     * @param string $nameSend
     * @param string $emailReply
     * @param string $nameReply
     * @return object
     */
    public function send($html,$assunto,$emailSend,$nameSend,$emailReply = "",$nameReply = ""){
        try {
            
            $stmp = new PHPMailer(true);
            $stmp->isSMTP();
            $stmp->isHTML();
            $stmp->CharSet = 'UTF-8';
            $stmp->Host = $this->host;
            $stmp->Username = $this->username;
            $stmp->From = $this->email;    
            $stmp->FromName = self::NAME_SEND;
            $stmp->Password = $this->pass;
            $stmp->Port = $this->port;
            $stmp->SMTPAuth = $this->autenticar == 1 ? true : false;
            if ($this->protocol != ""){ 
                $stmp->SMTPSecure = $this->protocol;
            }
            $stmp->Subject = $assunto;
            $stmp->clearReplyTos();
            $stmp->addReplyTo(self::EMAIL_REPLY,self::NAME_SEND);
            $stmp->addAddress($emailSend,$nameSend);
            $stmp->Body = $html;
            $stmp->msgHTML($html);
            if (!$stmp->send()) {
                return \throwJsonException($stmp->ErrorInfo);
            }
            return json([
                'message' => 'E-mail enviado com sucesso',
                'result' => 1,
                'action' => 'success'
            ]);
        } catch (\Exception $ex) {
            return throwJsonException($ex->getMessage());
        }
    }

    /**
     * Envia o email com PHPMailer
     *
     * @param string $html
     * @param string $assunto
     * @param string $emailSend
     * @param string $nameSend
     * @param string $emailReply
     * @param string $nameReply
     * @return boolean
     */
    public function sendWithBoolean($html,$assunto,$emailSend,$nameSend,$emailReply = "",$nameReply = "")
    {
        try {
            
            $stmp = new PHPMailer(true);
            $stmp->isSMTP();
            $stmp->isHTML();
            $stmp->CharSet = 'UTF-8';
            $stmp->Host = $this->host;
            $stmp->Username = $this->username;
            $stmp->From = $this->email;    
            $stmp->FromName = self::NAME_SEND;
            $stmp->Password = $this->pass;
            $stmp->Port = $this->port;
            $stmp->SMTPAuth = $this->autenticar == 1 ? true : false;
            if ($this->protocol != ""){ 
                $stmp->SMTPSecure = $this->protocol;
            }
            $stmp->Subject = $assunto;
            $stmp->clearReplyTos();
            $stmp->addReplyTo(self::EMAIL_REPLY,self::NAME_SEND);
            $stmp->addAddress($emailSend,$nameSend);
            $stmp->Body = $html;
            $stmp->msgHTML($html);
            if (!$stmp->send()) {
                return \throwJsonException($stmp->ErrorInfo);
            }
            return true;
        } catch (\Exception $ex) {
            return false;
        }
    }


}

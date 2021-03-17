<?php

namespace App\Core;

class FileControl
{
    private $tamanhoMaximo = 10000000; //Tamanho máximo do arquivo em bytes
    private $pasta = "assets/files/";
    function __construct($tamanhoMaximo = 10000000,$pasta = "assets/files/")
    {
        $this->tamanhoMaximo = $tamanhoMaximo;
        $this->pasta = $pasta;
        if (!is_dir($this->pasta)) {
            mkdir($this->pasta);     
        }
    }
    
    public function verificaSeEhMenor($sizeFoto)
    {
        if ($sizeFoto < $this->tamanhoMaximo) {
            return true;
        } else {
            return false;
        }
    }
    public function salvaImagem($imagem)
    {
    
        $extensao = $this->retornaExtensao($imagem);
        $nomeImagem = $this->geraNomeImagem($extensao);
        $caminhoImagem = $this->pasta . $nomeImagem;
        // Faz o upload da imagem para seu respectivo caminho
        $teste = move_uploaded_file($imagem["tmp_name"], $caminhoImagem);
        if ($teste){
            return $nomeImagem;
        }else{
            return '';
        }
    }

    //Funções privadas
    private function retornaExtensao($imagem)
    {
        preg_match("/\.(gif|bmp|png|jpg|jpeg|pdf|docx|ogg|doc|mp4|avi|mov|wmv|mp3|txt|xlsx|csv|cdr){1}$/i", $imagem['name'], $ext);
        if (isset($ext[1]) && $ext[1] != ''){
            return $ext[1];
        }else{
            preg_match("/^image\/(gif|bmp|png|jpg|jpeg|pdf|docx|ogg|doc|mp4|avi|mov|wmv|mp3|txt|xlsx|csv|cdr){1}$/i", $imagem['type'],$ext2);
            return $ext2[1];
        }
    }
    
    private function geraNomeImagem($extensao)
    {
        return md5(uniqid(time())) . "." . $extensao;
    }
}
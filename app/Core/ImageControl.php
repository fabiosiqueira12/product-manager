<?php

namespace App\Core;

use Exception;

class ImageControl
{
    private $tamanhoMaximo = 3000000; //Tamanho máximo do arquivo em bytes
    private $pasta = "assets/uploads/";
    function __construct($tamanhoMaximo = 3000000,$dir = null)
    {
        $this->tamanhoMaximo = $tamanhoMaximo;
        if (!is_dir("assets/uploads/")) {
            mkdir("assets/uploads/");
        }
        if (!empty($dir)){
            $this->pasta = $dir . "/assets/uploads/";
        }
    }
    
    public function verificaSeEhImagem($typeFoto)
    {
        if ($typeFoto == "image/svg+xml"){
            return true;
        }
        if ($typeFoto == "image/x-icon"){
            return true;
        }
        if (preg_match("/^image\/(pjpeg|jpeg|png|gif|bmp|svg+xml|x-icon)$/", $typeFoto)) {
            return true;
        } else {
            return false;
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
        move_uploaded_file($imagem["tmp_name"], $caminhoImagem);
        return $nomeImagem;
    }

    /**
     * Salva imagem base64 no projeto
     *
     * @param string $base64
     * @return object
     */
    public function salvaImagemBase64($base64)
    {
        try {
            $image_parts = explode(";base64,", $base64);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            $nomeImagem = $this->geraNomeImagem($image_type);
            $caminhoImagem = $this->pasta . $nomeImagem;
            $result = file_put_contents($caminhoImagem,$image_base64);
            if (!is_bool($result)){
                return $nomeImagem;
            }else{
                return \throwJsonException('Não foi possível salvar a imagem');
            }
        } catch (Exception $ex) {
            return \throwJsonException($ex->getMessage());
        }
    }

    /**
     * Salva imagem por url
     *
     * @param string $url
     * @return object
     */
    public function saveImageFromUrl($url)
    {
        $img = [
            'name' => $url,
            'type' => $url
        ];

        $ch = curl_init(); 
  
        curl_setopt($ch, CURLOPT_HEADER, 0); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($ch, CURLOPT_URL, $url); 
    
        $data = curl_exec($ch); 
        curl_close($ch); 

        if ($data == null){
            return \throwJsonExceptionReturn("A imagem não foi baixada");
        }

        try {
            $nomeImagem = $this->geraNomeImagem($this->retornaExtensao($img));
            $caminhoImagem = $this->pasta . $nomeImagem;
            $result = file_put_contents($caminhoImagem,$data);
            if (!is_int($result)){
                return \throwJsonExceptionReturn('Não foi possível salvar a imagem');
            }
            return $nomeImagem;
        } catch (Exception $ex) {
            return \throwJsonExceptionReturn($ex->getMessage());
        }
        
    }

    /**
     * Copia a imagem no servidor
     *
     * @param string $url
     * @return object
     */
    public function copy($url)
    {
        $explode = explode("/uploads/",$url);
        $file = $explode[1];
        $img = [
            'name' => $url,
            'type' => $url
        ];
        $nomeImagem = $this->geraNomeImagem($this->retornaExtensao($img));
        $result = copy(
            $this->pasta . $file,
            $this->pasta . $nomeImagem
        );
        if ($result){
            return $nomeImagem;
        }
        return $result;
    }

    public function saveTest($url)
    {
        $img = [
            'name' => $url,
            'type' => $url
        ];

        $ch = curl_init(); 
  
        curl_setopt($ch, CURLOPT_HEADER, 0); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($ch, CURLOPT_URL, $url); 
    
        $data = curl_exec($ch); 
        curl_close($ch); 

        if ($data == null){
            return \throwJsonExceptionReturn("A imagem não foi baixada");
        }

        $nomeImagem = $this->geraNomeImagem($this->retornaExtensao($img));
        $caminhoImagem = $this->pasta . $nomeImagem;
        $result = file_put_contents($caminhoImagem,$data);
        if (!is_int($result)){
            return $result;
        }
        return $nomeImagem;
    }

    //Funções privadas
    private function retornaExtensao($imagem)
    {
        preg_match("/\.(gif|bmp|png|jpg|jpeg|svg|ico){1}$/i", $imagem['name'], $ext);
        if (isset($ext[1]) && $ext[1] != ''){
            return $ext[1];
        }else{
            preg_match("/^image\/(pjpeg|jpeg|png|gif|bmp|svg|ico)$/", $imagem['type'],$ext2);
            return $ext2[1];
        }
    }

    private function geraNomeImagem($extensao)
    {
        return md5(uniqid(time())) . "." . $extensao;
    }
    
}
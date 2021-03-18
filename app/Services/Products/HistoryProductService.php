<?php

namespace App\Services\Products;

use App\Services\Service;

class HistoryProductService extends Service{
    
    private $fields = 'a.*,b.title AS title_product,c.nome AS nome_user,c.login AS login_user';
    private $forpage = 20;
    private $innerTables = [];

    public function __construct()
    {
        parent::__construct();
        $this->setTable("product_history");
        $this->setPrefix("a");
        $this->innerTables = [
            " INNER JOIN product AS b ON b.id = a.id_product ",
            " INNER JOIN user AS c ON c.id = a.id_user "
        ];
    }

    /**
     * Faz paginação de histórico do produto
     *
     * @param array $body
     * @return array
     */
    public function paginate($body)
    {
        $completeWhere = $this->mountWhere($body);
        $order = ' a.id DESC ';
        $page = isset($body['page']) ? $body['page'] : 1;
        $forpageApi = isset($body['forpage']) && $body['forpage'] != '' ? \intval($body['forpage']) : $this->forpage;

        $paginateResults = $this->genericPaginate($this->fields,$this->innerTables,$completeWhere,$page,$forpageApi,$order);

        return $paginateResults;

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
                case 'id_product':
                    $completeWhere .= !empty($v) ? " AND a.id_product = {$v} " : "";
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

?>
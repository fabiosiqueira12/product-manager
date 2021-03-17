<?php

namespace App\Core;

class CalcJuros
{

    /**
     * @var float
     */
    private $capital = 2029.99;

    /**
     * @var float
     */
    private $jurosAoMes = 2.29;

    /**
     * @var int
     */
    private $parcelasSemJuros = 10;

    /**
     * Gerar a lista de parcelas
     * @param float $valorMinimoParcela
     * @param int $maxParcelas
     * @return array
     */
    public function gerarParcelas(float $valorMinimoParcela = 10, int $maxParcelas = 12)
    {

        $result = [];

        foreach (range(1, $maxParcelas) as $parcelas) {

            $valorParcela = $this->calcValorParcela($parcelas);

            if ($valorParcela >= $valorMinimoParcela or $parcelas == 1) {

                $juros = round($parcelas * $valorParcela - $this->capital, 2);
                $juros = $parcelas > $this->getParcelasSemJuros() ? $juros : 0;

                $result[$parcelas] = [
                    'parcelas' => $parcelas,
                    'descricao' => "{$parcelas}x " . ($juros ? 'com juros' : 'sem juros') . " de R$ " . Number::real($valorParcela),
                    'valorParcela' => $valorParcela,
                    'montante' => $parcelas * $valorParcela,
                    'juros' => $juros,
                ];
            }
        }
        return $result;
    }

    /**
     * @param int $parcelas
     * @return float|int
     */
    public function calcValorParcela(int $parcelas)
    {
        if ($parcelas > 0) {
            if ($this->getParcelasSemJuros() >= $parcelas or $this->getJurosAoMes() <= 0) {
                return round($this->capital / $parcelas, 2);
            } else {
                $taxaJuros = pow(1 + $this->jurosAoMes / 100, $parcelas);
                return round($this->capital * (($taxaJuros * ($this->jurosAoMes / 100)) / ($taxaJuros - 1)), 2);
            }
        } else {
            return 0;
        }
    }

    /**
     * @return int
     */
    public function getParcelasSemJuros()
    {
        return $this->parcelasSemJuros;
    }

    /**
     * @param int $parcelas
     * @return $this
     */
    public function setParcelasSemJuros(int $parcelas)
    {
        $this->parcelasSemJuros = max(1, $parcelas);
        return $this;
    }

    /**
     * @return float
     */
    public function getJurosAoMes()
    {
        return $this->jurosAoMes;
    }

    /**
     * @param float $jurosAoMes
     * @return $this
     */
    public function setJurosAoMes($jurosAoMes)
    {
        $this->jurosAoMes = $jurosAoMes;
        return $this;
    }

    /**
     * @param int $parcelas
     * @return float|int
     */
    public function calcJuros(int $parcelas)
    {
        if ($this->getParcelasSemJuros() >= $parcelas or $this->getJurosAoMes() <= 0) {
            return 0;
        } else {
            return $this->calcValorParcela($parcelas) * $parcelas - $this->getCapital();
        }
    }

    /**
     * @return float
     */
    public function getCapital()
    {
        return $this->capital;
    }

    /**
     * @param float $capital
     * @return $this
     */
    public function setCapital(float $capital)
    {
        $this->capital = $capital;
        return $this;
    }

}
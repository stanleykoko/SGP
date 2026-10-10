<?php

        Class Nacionalidade{
            private $codigoNacionalidade;
            private $nomeNacionalidade;
            private $dataRegisto;
            private $nomePessoa;
            private $nomePais;
            private $nomeContinente;

            public function __construct($codigoNacionalidade,$nomeNacionalidade,$dataRegisto,$nomePessoa,$nomePais,$nomeContinente){
                $this->codigoNacionalidade=$codigoNacionalidade;
                $this->nomeNacionalidade=$nomeNacionalidade;
                $this->dataRegisto=$dataRegisto;
                $this->nomePessoa=$nomePessoa;
                $this->nomePais=$nomePais;
                $this->nomeContinente=$nomeContinente;
            }

            public function getCodigoNacionalidade() {return $this->codigoNacionalidade;}
            public function setCodigoNacionalidade($codigoNacionalidade){$this->codigoNacionalidade=$codigoNacionalidade;}
            public function getNomeNacionalidade() {return $this->nomeNacionalidade;}
            public function setNomeNacionalidade($nomeNacionalidade){$this->nomeNacionalidade=$nomeNacionalidade;}
            public function getDataRegisto() {return $this->dataRegisto;}
            public function setDataRegisto($dataRegisto){$this->dataRegisto=$dataRegisto;}
            public function getNomePessoa() {return $this->nomePessoa;}
            public function setNomePessoa($nomePessoa){$this->nomePessoa=$nomePessoa;}
            public function getNomePais() {return $this->nomePais;}
            public function setNomePais($nomePais){$this->nomePais=$nomePais;}
            public function getNomeContinente() {return $this->nomeContinente;}
            public function setNomeContinente($nomeContinente){$this->nomeContinente=$nomeContinente;}
        }
?>
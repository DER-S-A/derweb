<?php
    /**
     * clase para crear input para CUIT
     * autor: Ezequiel Algañaraz
     * fecha: 5-03-2020 
     */ 
    class HtmlCUIT
    {
        var $mId = "";
        var $mValor = "";
        var $mTitulo = "";
        var $mClass = "";
        var $mPlaceholder = "ingrese cuit";
        var $mAwesomefont = "";
        var $type = "text";
        var $mMaxlength = "13";
        var $mOnKeyUp = "";
        var $aContent = array();

        function __construct($xId, $xvalor)
        {           
			$this->mValor = $xvalor;
			$this->mId = $xId;
        }

        function getMaxLenght()
        {
            return $this->mMaxlength;
        }

        function setOnKeyUp($xOnKeyUp) 
        {
            $this->mOnKeyUp = $xOnKeyUp;
        }

        function getValor()
        {
          return $this->mValor;
        }


        function getId()
        {
            return $this->mId;
        }

        function getClass()
        {
            return $this->mClass;
        }
        
        function getType()
        {
            return $this->type;
        }
        
        function setPlaceholder($xplaceholder)
        {
            $this->mPlaceholder = $xplaceholder;
        }
        
        function getPlaceholder()
        {
            return $this->mPlaceholder;
        }
                
        function toHtml()
        {
            $result = "\n<input type=\"" . $this->getType() . "\" "; 
            $result .= " name=\"" . $this->getId() . "\"";
            $result .= " id=\"" . $this->getId() . "\" ";
            $result .= " placeholder=\"" . $this->getPlaceholder() . "\"";
			$result .= " value=\"" . $this->getValor() . "\" maxlength=\"" . $this->getMaxLenght() . "\"";
			$result .= " onkeyup=\"this.value=cuitRestringirLetra(this.value);cuitVerificar('". $this->getId() ."');" . $this->mOnKeyUp . "\"/>";
            if ($this->getValor() != "")
            {
                $result .= "<script>";
                $result .= " cuitValidarCargado('" . $this->getId() . "')";
                $result .= " </script>"; 
            }
            return $result;           
        }
    }
     

?>
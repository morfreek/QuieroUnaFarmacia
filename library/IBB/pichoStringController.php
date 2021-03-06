<?php
class IBB_pichoStringController{

    public function generar_slug($cadena, $separador = '-'){
            //sin espacios al inicio o al final
            $cadena = trim($cadena);
            //limpiamos caracteres los latinos
            $cadena =  $this->caracteres_latinos($cadena); 
            //pasamos a minÃºscula la cadena
            $cadena = strtolower($cadena); 
            //limpiamos saltos de lÃ­neas y tab
            $cadena = ereg_replace("[ \t\n\r]+", " ", $cadena);
            //limpiamos todos los caracteres invÃ¡lidos
            $cadena = ereg_replace("[^ A-Za-z0-9_]", "", $cadena);
            //limpieza de espacios vacÃ­os de los valores invÃ¡lidos al final de la cadena
            $cadena = trim($cadena);
            //reemplazamos los espacios vacios por el separador
            $cadena = str_replace(" ", $separador, $cadena);
            return $cadena;
    }
    private function caracteres_latinos($cadena){
            //acentos
            $cadena = ereg_replace("(À|Á|Â|Ã|Ä|Å|à|á|â|ã|ä|å)","a",$cadena);
            $cadena = ereg_replace("(È|É|Ê|Ë|è|é|ê|ë)","e",$cadena);
            $cadena = ereg_replace("(Ì|Í|Î|Ï|ì|í|î|ï)","i",$cadena);
            $cadena = ereg_replace("(Ò|Ó|Ô|Õ|Ö|Ø|ò|ó|ô|õ|ö|ø)","o",$cadena);
            $cadena = ereg_replace("(Ù|Ú|Û|Ü|ù|ú|û|ü)","u",$cadena);
            //la ñ
            $cadena = ereg_replace("(Ñ|ñ)","n",$cadena);
            //caracteres extraños
            $cadena = ereg_replace("(Ç|ç)","c",$cadena);
            $cadena = ereg_replace("ÿ","y",$cadena);
            return $cadena;
    }
    
    public function controlar_largo($string, $largo){
        return (strlen($string)>$largo) ? substr($string, 0, $largo):$string;
    }
}
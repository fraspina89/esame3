
<?php

ini_set("auto_detect_line_endings", true);

use MieClassi\Utility as UT;

function head($title,$arrCSS=null)
{
    $str= '<!DOCTYPE html>'. 
        '<html lang="it">'. 
        '<head>'. 
        '<meta charset="UTF-8">'. 
        '<meta name="viewport" content="width=device-width, initial-scale=1.0">'. 
        '<title>' . $title . '</title>'. 
        '<link href="css/comune.min.css" rel="stylesheet">';
    if($arrCSS!==null){
        foreach($arrCSS as $cssSingolo){
            $str.='<link href="css/'.$cssSingolo.'" rel="stylesheet">';
        }
    }   
    $str.='</head>';
    return $str;
}

function menu($menu)
{
    $arr = json_decode(UT::leggiTesto($menu));
    $selezionato = UT::richiestaHTTP("selezionato");
    $selezionato = ($selezionato == null) ? 1 : $selezionato;
    $str='<nav class="menu">
                <input id="controllo" type="checkbox">
                <label class="label-controllo" for="controllo">
                    <span></span>
                </label>
                <a href="#" class="logo">MENU</a>
                <ul id="menu">';

    foreach ($arr as $link) {
        $n = $link->id;
        $classeSelezionato = ($n == $selezionato) ? ' class="selezionato"' : '';
        $str.=sprintf(
            '<li %s><a href="%s?selezionato=%u" title="%s" class="vociMenu">%s</a></li>',
            $classeSelezionato,
            $link->url,
            $link->id,
            $link->title,
            $link->nome
        );
    }

    $str.='</ul></nav>';
    return $str;
}

function lavori($file)
{
    $str='';
    $lav = json_decode(UT::leggiTesto($file));
    foreach ($lav as $lavoro) {
        $tmp = '<div>
            <h2>%s</h2>
              <a href="work.php?idWork=%u" title="%s">
                <img src="./img/%s" alt="%s" >
                 </a>
                    </div>';
        $str.=sprintf($tmp, $lavoro->titolo, $lavoro->id, $lavoro->title, $lavoro->img, $lavoro->alt);
    }
    return $str;
}

function footer()
{
    $str= '<footer>
            <div class="privacy">
                <ul>
                    <li>
                        <a href="#" title="leggi la cookies policy">cookies policy</a>
                    </li>
                    <li>
                        <a href="#" title="leggi la privacy policy">privacy policy</a>
                    </li>
                    <li>
                        <a href="#" title="leggi la privacy copyright">copyright</a>
                    </li>
                </ul>
            </div>
          </footer>';
          return $str;
}
?>



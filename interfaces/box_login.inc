<?php
$logado = json_decode(utf8_decode(file_get_contents($host . "/services/api/rest/json/?method=logado")));
//echo file_get_contents($host . "/services/api/rest/json/?method=logado");
echo '<form action="" method="post">' . "\n";
if ($logado->status == 0 && $logado->result != true){
echo '<fieldset id="login" class="some">' . "\n";
} else {
echo '<fieldset id="login">' . "\n";
}

echo '<label>login</label> <input type="text" size="15" name="username"><br>'."\n";
echo '<label>senha</label> <input type="password" size="15" name="password"><br>'."\n";
echo '<button onclick="logar(this); return false;">OK</button>'."\n";
echo '</fieldset>'."\n";

if ($logado->status == 0 && $logado->result != true){
echo '<fieldset id="menu">' . "\n";
} else {
echo '<fieldset id="menu" class="some">' . "\n";
}
?>
<button onclick="sair();return false;">Sair</button>
</fieldset>
</form>
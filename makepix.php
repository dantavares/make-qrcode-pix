<!DOCTYPE html>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1" />
<head> 
<link rel="stylesheet" href="style.css" />
<link rel="stylesheet" href="alertify.css" />
<link rel="stylesheet" href="default.css" />
<script src="alertify.js"></script>
<title>Gerador de QRCode PIX</title>
<link rel="icon" href="logo-pix.png">
</head>
<html><body>

<?php
require 'vendor/autoload.php';

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\Label\Font\OpenSans;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

//--------------------------------------------------------------------
/*
function genRndStr($length = 4) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }
    return $randomString;
}
*/

//Função para calucular o CRC16-CCITT-FFFF, 
//conforme seção 4.7.3 CRC (ID “63”) da referência #1 do EMV-MPM
function computeCRC($str) {
    $bytes = unpack('C*', $str);
    
	//Tabela em conformidade com polinômio ‘1021’, não alterar!
	$crcTable = [
        0x0000, 0x1021, 0x2042, 0x3063, 0x4084, 0x50a5, 0x60c6, 0x70e7,
        0x8108, 0x9129, 0xa14a, 0xb16b, 0xc18c, 0xd1ad, 0xe1ce, 0xf1ef,
        0x1231, 0x0210, 0x3273, 0x2252, 0x52b5, 0x4294, 0x72f7, 0x62d6,
        0x9339, 0x8318, 0xb37b, 0xa35a, 0xd3bd, 0xc39c, 0xf3ff, 0xe3de,
        0x2462, 0x3443, 0x0420, 0x1401, 0x64e6, 0x74c7, 0x44a4, 0x5485,
        0xa56a, 0xb54b, 0x8528, 0x9509, 0xe5ee, 0xf5cf, 0xc5ac, 0xd58d,
        0x3653, 0x2672, 0x1611, 0x0630, 0x76d7, 0x66f6, 0x5695, 0x46b4,
        0xb75b, 0xa77a, 0x9719, 0x8738, 0xf7df, 0xe7fe, 0xd79d, 0xc7bc,
        0x48c4, 0x58e5, 0x6886, 0x78a7, 0x0840, 0x1861, 0x2802, 0x3823,
        0xc9cc, 0xd9ed, 0xe98e, 0xf9af, 0x8948, 0x9969, 0xa90a, 0xb92b,
        0x5af5, 0x4ad4, 0x7ab7, 0x6a96, 0x1a71, 0x0a50, 0x3a33, 0x2a12,
        0xdbfd, 0xcbdc, 0xfbbf, 0xeb9e, 0x9b79, 0x8b58, 0xbb3b, 0xab1a,
        0x6ca6, 0x7c87, 0x4ce4, 0x5cc5, 0x2c22, 0x3c03, 0x0c60, 0x1c41,
        0xedae, 0xfd8f, 0xcdec, 0xddcd, 0xad2a, 0xbd0b, 0x8d68, 0x9d49,
        0x7e97, 0x6eb6, 0x5ed5, 0x4ef4, 0x3e13, 0x2e32, 0x1e51, 0x0e70,
        0xff9f, 0xefbe, 0xdfdd, 0xcffc, 0xbf1b, 0xaf3a, 0x9f59, 0x8f78,
        0x9188, 0x81a9, 0xb1ca, 0xa1eb, 0xd10c, 0xc12d, 0xf14e, 0xe16f,
        0x1080, 0x00a1, 0x30c2, 0x20e3, 0x5004, 0x4025, 0x7046, 0x6067,
        0x83b9, 0x9398, 0xa3fb, 0xb3da, 0xc33d, 0xd31c, 0xe37f, 0xf35e,
        0x02b1, 0x1290, 0x22f3, 0x32d2, 0x4235, 0x5214, 0x6277, 0x7256,
        0xb5ea, 0xa5cb, 0x95a8, 0x8589, 0xf56e, 0xe54f, 0xd52c, 0xc50d,
        0x34e2, 0x24c3, 0x14a0, 0x0481, 0x7466, 0x6447, 0x5424, 0x4405,
        0xa7db, 0xb7fa, 0x8799, 0x97b8, 0xe75f, 0xf77e, 0xc71d, 0xd73c,
        0x26d3, 0x36f2, 0x0691, 0x16b0, 0x6657, 0x7676, 0x4615, 0x5634,
        0xd94c, 0xc96d, 0xf90e, 0xe92f, 0x99c8, 0x89e9, 0xb98a, 0xa9ab,
        0x5844, 0x4865, 0x7806, 0x6827, 0x18c0, 0x08e1, 0x3882, 0x28a3,
        0xcb7d, 0xdb5c, 0xeb3f, 0xfb1e, 0x8bf9, 0x9bd8, 0xabbb, 0xbb9a,
        0x4a75, 0x5a54, 0x6a37, 0x7a16, 0x0af1, 0x1ad0, 0x2ab3, 0x3a92,
        0xfd2e, 0xed0f, 0xdd6c, 0xcd4d, 0xbdaa, 0xad8b, 0x9de8, 0x8dc9,
        0x7c26, 0x6c07, 0x5c64, 0x4c45, 0x3ca2, 0x2c83, 0x1ce0, 0x0cc1,
        0xef1f, 0xff3e, 0xcf5d, 0xdf7c, 0xaf9b, 0xbfba, 0x8fd9, 0x9ff8,
        0x6e17, 0x7e36, 0x4e55, 0x5e74, 0x2e93, 0x3eb2, 0x0ed1, 0x1ef0
    ];

    $crc = 0xFFFF;
    foreach ($bytes as $byte) {
        $j = ($byte ^ $crc >> 8) & 0xFF;
        $crc = $crcTable[$j] ^ ($crc << 8);
    }

    $answer = ($crc ^ 0) & 0xFFFF;
        
    return strtoupper(dechex($answer));
}

//----------------------------------------------------------------------

//Classe base para montagem do codigo str do qrcode pix
class emv {
	public $id;
    public $val;
        
    public function __construct($id, $val) {
    	$this->id = $id;
    	$this->val = $val;
        $this->tam = sprintf("%02d", strlen($val));
    }
    
    public function makestr() {
		return $this->id.$this->tam.$this->val;
    }
}

//-----------------------------------------------------------------------

if (($_SERVER['REQUEST_METHOD'] === 'POST') || ($_SERVER['REQUEST_METHOD'] === 'GET')) {
	$pixkey	= trim($_POST['pixkey']);
    $valor  = sprintf("%01.2f", str_replace(",", ".", $_POST['valor']));
    $nome   = strtoupper(trim($_POST['nome']));
    $city   = strtoupper(trim($_POST['city']));
    $id		= trim($_POST['id']);
	$gqrcode = $_GET['pix'];
	$glink = $_GET['glink'];
	
//Inicio da criação dos objetos, para montagem str do qrcode pix

//Payload Format Indicator
$pfi = new emv("00","01");

//Point of Initiation Method
$pim = new emv("01","11");

//Merchant Account Information
$mai_gui = new emv("00","BR.GOV.BCB.PIX"); //Globally Unique Identifier
$mai_key = new emv("01",$pixkey); //Chave PIX
$mai_id = new emv("02",$id); //ID

if ($id == '') 
	$mai = new emv("26", $mai_gui->makestr().$mai_key->makestr());
else
	$mai = new emv("26", $mai_gui->makestr().$mai_key->makestr().$mai_id->makestr());

//Merchant Category Code
$mcc = new emv("52","0000");

//Transaction Currency
$tc = new emv("53","986");

//Transaction Amount
$ta = new emv("54",$valor);

//Country Code
$cc = new emv("58","BR");

//Merchant Name
$mn = new emv("59",$nome);

//Merchant City
$mc = new emv("60",$city);

//Additional Data Field
$adf_rl = new emv("05","***"); //Reference Label
$adf = new emv("62", $adf_rl->makestr());

//Montagem str do qrcode pix
$qrcode = trim($qrcode);

if ($gqrcode == "") { 
	$qrcodestr = $pfi->makestr().$pim->makestr().$mai->makestr().$mcc->makestr().$tc->makestr(). 
				$ta->makestr().$cc->makestr().$mn->makestr(). $mc->makestr().$adf->makestr()."6304"; 
				//Final "6304" = CRC16-CCITT-FFFF

	//Calculo do CRC
	$qrcodestr = $qrcodestr.computeCRC($qrcodestr);
}
else 
	$qrcodestr = $gqrcode;

//----------------------------------------------------------------------------

//Codigo str do pix completo, inicio da criação a imagem png do qrcode

$builder = new Builder(
    writer: new PngWriter(),
    writerOptions: [],
    validateResult: false,
    data: $qrcodestr,
    encoding: new Encoding('UTF-8'),
    errorCorrectionLevel: ErrorCorrectionLevel::Low,
    size: 300,
    margin: 5,
    roundBlockSizeMode: RoundBlockSizeMode::Margin,
    logoPath: __DIR__.'/logo-pix.png',
    logoResizeToWidth: 50,
    logoPunchoutBackground: true,
    labelText: 'Pague com PIX!',
    labelFont: new OpenSans(20),
    labelAlignment: LabelAlignment::Center
);

$qrcode = $builder->build();
$b64qrcode = 'data:image/png;base64,'.base64_encode($qrcode->getString());

$fullurl = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?pix='.$qrcodestr;

$cpicon = '<svg width="15" height="15" viewBox="0 0 0.45 0.45" xmlns="http://www.w3.org/2000/svg">'.
'<path d="M.394.167.392.163V.161L.389.155.277.043.271.039H.268L.262.038H.187a.056.056 0 0 0-.056.056'.
'v.018H.112a.056.056 0 0 0-.056.056v.188a.056.056 0 0 0 .056.057h.15A.056.056 0 0 0 .32.356V.338h.019'.
'A.056.056 0 0 0 .395.281V.167M.281.101.33.15H.3A.02.02 0 0 1 .281.131Zm0 .255a.02.02 0 0 1-.019.019h-'.
'.15A.02.02 0 0 1 .095.356V.168A.02.02 0 0 1 .112.15h.019v.131a.056.056 0 0 0 .057.057h.094ZM.356.281A.'.
'02.02 0 0 1 .338.3h-.15A.02.02 0 0 1 .17.281V.093A.02.02 0 0 1 .188.075h.056v.056A.056.056 0 0 0 .3.188h.056Z"/></svg>';

echo "<ul class='form-style-1'>";

echo "<li><center><Label class='required'>Confirme a identidade e o valor antes de pagar!</label></center></li>";
echo "<center><a download='qrcode_pix.png' href='$b64qrcode'> <img src='$b64qrcode' title='Clique no QRCode para baixar' /> </a></center>";
echo "<li><label onclick='btnclick()' title='Clique para copiar'>Código PIX $cpicon</label></li>";
echo "<li><textarea id='txtpix' style='padding:.5rem;' rows='3' cols='50' readonly>$qrcodestr</textarea></li>";
echo "<li><a href='https://wa.me/?text=$qrcodestr' >Compartilhar código PIX no WhatsApp</a></li>";

echo "<li>";
if (!($glink == '1'))
	echo "<a href='$fullurl&glink=1'>Gerar link de pagamento</a>";
else {
	$api_url = 'https://tinyurl.com/api-create.php?url=';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $api_url.urlencode($fullurl).'&private=1');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    	curl_setopt($ch, CURLOPT_HEADER, 0);
	
	$data = curl_exec($ch);
	curl_close($ch);
	echo '<li> <label onclick="pastetxt('."'".$data."', 'Link Copiado!')".'"'." title='Clique para copiar o link'>Link de pagamento: $data $cpicon</label></li>";	
} 

echo "</li><BR>";

}
?>
<li><center><a href="https://tinyurl.com/2a4du5vf"><img src="donate_icon.jpg" title="Este sistema foi útil pra você? Considere me enviar uma doação!" width="50" height="60"></a><BR>Doação</center></li>
<li><center><a href="https://dantavares.tec.br/home/index.php/pagina-de-ajuda/">Preciso de Ajuda!</a></center></li>
</ul>
</body>
<script>

function pastetxt(txt, msg) {
	var textArea = document.createElement("textarea");
	textArea.value = txt;
  
	// Avoid scrolling to bottom
	textArea.style.position = "fixed";
	document.body.appendChild(textArea);
	textArea.focus();
	textArea.select();
	textArea.setSelectionRange(0, 99999); // For mobile devices
	document.execCommand('copy');
	document.body.removeChild(textArea);
	alertify.notify(msg , 'success', 3);
}

function btnclick() {
	var copyText = document.getElementById("txtpix");
	pastetxt(copyText.value, 'Código PIX Copiado!');
}
</script>
</html>

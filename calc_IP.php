<?php
require "calc_IP_functions.php";

if($_SERVER["REQUEST_METHOD"] == "GET"){
  $DefaultMask = "255.255.255.0";
  $wildCardMask = "0.0.0.255";
  
  if($_GET["adress"] != ""){
    $adressInput = $_GET["adress"];
  }else{
    $adressInput = "192.168.0.1";
    echo "<font color= '#00ff00'>Adresse IP non renseignée, adresse par défaut : 192.168.0.1</font><br>";
  }

  if($_GET["mask"] != ""){
    $maskInput = $_GET["mask"];
  }else{
    $maskInput = $DefaultMask;
    echo "<font color= '#00ff00'>Masque non renseigné, masque par défaut : /24</font><br><br>";
  }

  if($_GET["subnetMask"] != ""){
    $subnetMaskInput = $_GET["subnetMask"];
  }else{
    $subnetMaskInput = 25;
    echo "<font color= '#00ff00'>Masque de sous-réseau non renseigné, masque par défaut : /25</font><br><br>";
  }

  if(count(explode(".", $adressInput)) == 4){
    if(count(explode(".",$maskInput)) != 4){
      //Mask en représentation CIDR converti en binaire
      $maskInput = cidrToMaskBin($maskInput);
    }else{
      //Mask en représentation décimale converti en binaire
      if(intval($maskInput) < 32 || intval($maskInput) > 0){
        $maskInput = ipOctetToBin($maskInput);
      }else{
        die("<font color= '#00ff00'>Masque réseau invalide</font><br><br>");
      }
    }
    
    $wildCardMask = wildCardByMask($maskInput);
    $adressInput = ipOctetToBin($adressInput);
    $netAdress = netAdress($adressInput, $maskInput);
    
    echo(
      "<table>
        <tbody>
          <tr>
            <td>Adresse :</td>
            <td><font color='#0000ff'>".ipBinToOctet($adressInput)."</font></td>
            <td><font color='#909090'>".$adressInput."</font></td>
          </tr>
          <tr>
            <td>Netmask :</td>
            <td><font color='#0000ff'>".ipBinToOctet($maskInput)."</font></td>
            <td><font color='#ff0000'>".$maskInput."</font></td>
          </tr>
          <tr>
            <td>Wildcard :</td>
            <td><font color='#0000ff'>".ipBinToOctet($wildCardMask)."</font></td>
            <td><font color='#909090'>".$wildCardMask."</font></td>
          </tr>
          <tr>
            <td>Network :</td>
            <td><font color='#0000ff'>".ipBinToOctet($netAdress)."</font></td>
            <td><font color='#909090'>".$netAdress."</font></td>
          </tr>
          <tr>
            <td>Broadcast :</td>
            <td><font color='#0000ff'>".ipBinToOctet(broadcast($adressInput, $wildCardMask))."</font></td>
            <td><font color='#909090'>".broadcast($adressInput, $wildCardMask)."</font></td>
          </tr>
          <tr>
            <td>HostMin :</td>
            <td><font color='#0000ff'>".ipBinToOctet(hostMin($netAdress))."</font></td>
            <td><font color='#909090'>".hostMin($netAdress)."</font></td>
          </tr>
          <tr>
            <td>HostMax :</td>
            <td><font color='#0000ff'>".ipBinToOctet(hostMax(broadcast($adressInput, $wildCardMask)))."</font></td>
            <td><font color='#909090'>".hostMax(broadcast($adressInput, $wildCardMask))."</font></td>
          </tr>
        </tbody>
      </table>"
    );

    echo "<br><font color= '#00ff00'>Subnets:</font><br><br>";
    
    if(count(explode(".",$subnetMaskInput)) != 4){
      //Mask en représentation CIDR converti en binaire
      $subnetMaskInput = cidrToMaskBin($subnetMaskInput);
    }else{
      //Mask en représentation décimale converti en binaire
      $subnetMaskInput = ipOctetToBin($subnetMaskInput);
    }

    $wildCardMask2 = wildCardByMask($subnetMaskInput);
    
    echo("
      <table>
        <tbody>
        <tr>
          <td>Netmask :</td>
          <td><font color='#0000ff'>".ipBinToOctet($subnetMaskInput)." = ".maskBinToCidr($subnetMaskInput)."</font></td>
          <td><font color='#ff0000'>".$subnetMaskInput."</font></td>
        </tr>
        <tr>
          <td>Wildcard :</td>
          <td><font color='#0000ff'>".ipBinToOctet($wildCardMask2)."</font></td>
          <td><font color='#909090'>".$wildCardMask2."</font></td>
        </tr>
        </tbody>
      </table>
    ");

    $tabSubNet = subnet($netAdress, $maskInput, $subnetMaskInput);

    foreach($tabSubNet as $subNet){
      echo("<br>
      <table>
        <tbody>
          <tr>
            <td>Network :</td>
            <td><font color='#0000ff'>".ipBinToOctet($subNet)."</font></td>
            <td><font color='#909090'>".$subNet."</font></td>
          </tr>
          <tr>
            <td>Broadcast :</td>
            <td><font color='#0000ff'>".ipBinToOctet(broadcast($subNet, $wildCardMask2))."</font></td>
            <td><font color='#909090'>".broadcast($subNet, $wildCardMask2)."</font></td>
          </tr>
          <tr>
            <td>HostMin :</td>
            <td><font color='#0000ff'>".ipBinToOctet(hostMin($subNet))."</font></td>
            <td><font color='#909090'>".hostMin($subNet)."</font></td>
          </tr>
          <tr>
            <td>HostMax :</td>
            <td><font color='#0000ff'>".ipBinToOctet(hostMax(broadcast($subNet, $wildCardMask2)))."</font></td>
            <td><font color='#909090'>".hostMax(broadcast($subNet, $wildCardMask2))."</font></td>
          </tr>
        </tbody>
      </table>
      <br>
      ");
    }
    
  }else{
    die("Adresse non valide");
  }

  
}

?>

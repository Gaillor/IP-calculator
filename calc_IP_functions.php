<?php 
  function ipOctetToBin($octet){
    $bin = "";
    $tabOctet = explode(".", $octet);
    if($octet != ""){
      for ($i=0; $i<4; $i++){
        $bin .= str_pad(decbin($tabOctet[$i]),8,'0',STR_PAD_LEFT);
        if($i < 3){
        $bin.=".";
        }
      }
    }
    return $bin;
  }


  function ipBinToOctet($binaire){
    $octet = "";
    $tabBin = explode(".", $binaire);
  
    if($binaire != ""){
      for ($i=0; $i<4; $i++){
        $octet .= bindec($tabBin[$i]);
        if($i < 3){
        $octet.=".";
        }
      }
    }
    return $octet;
  }


  function cidrToMaskBin($cidr){
    $tabOctet = [32];
    $mask = "";
    $complete = 0;
    for($i=0; $i<32; $i++){
      if($i < $cidr){
        $tabOctet[$i] = 1;
      }else{
        $tabOctet[$i] = 0;
      }

      //formation des 32 bits
      if($complete == 8){
        $mask .= ".";
        $complete = 0;
      }
      
      $mask .= $tabOctet[$i];
      $complete++;
      
    }

    return $mask;
  }

  function wildCardByMask($mask){
    $wildcard = "";
    
    foreach(str_split($mask) as $bit){
      switch($bit):
        case "0":
          $wildcard .= "1";
          break;
        case "1":
          $wildcard .= "0";
          break;
        case ".":
          $wildcard .= ".";
          break;
      endswitch;
    }

    return $wildcard;
  }

  
  function EtLogique($ip, $mask){
    $netAdress = "";
    $length = strlen($mask);
    
    if($ip != "" && $mask != ""){
      for($i=0; $i<$length; $i++){
        if($ip[$i] == $mask[$i]){
          $netAdress .= $ip[$i];
        }else{
          $netAdress .= "0";
        }
      }
    }else{
      echo "adresse(s) non renseignée(s)<br>";
    } 

    return $netAdress;
  }

  function OuLogique($ip, $mask){
    $netAdress = "";
    $length = strlen($mask);

    if($ip != "" && $mask != ""){
      for($i=0; $i<$length; $i++){
        if($ip[$i] == $mask[$i]){
          $netAdress .= $ip[$i];
        }else{
          $netAdress .= "1";
        }
      }
    }else{
      echo "adresse(s) non renseignée(s)<br>";
    }

    return $netAdress;
  }


  function netAdress($ip, $mask){
    return EtLogique($ip, $mask);
  }


  function broadcast($netAdr, $wildCard){
    return OuLogique($netAdr, $wildCard);
  }

  function hostMin($netAdr){
    $hostMin = "";
    if($netAdr != ""){
      $hostMin = $netAdr;
      $hostMin[-1] = "1";
    }

    return $hostMin;
  }

  function hostMax($broadCast){
    $hostMax = "";
    if($broadCast != ""){
      $hostMax = $broadCast;
      $hostMax[-1] = "0";
    }

    return $hostMax;
  }


  function genererCombinations($n) {
      $combinaisons = [];

      // Calculer le nombre total de combinaisons possibles (2^n)
      $totalCombinations = pow(2, $n);

      // Générer toutes les combinaisons
    if($totalCombinations < 250){
      for ($i = 0; $i < $totalCombinations; $i++) {
          // Convertir la valeur en binaire avec n bits
          $binaire = str_pad(decbin($i), $n, '0', STR_PAD_LEFT);

          // Ajouter la combinaison au tableau
          $combinaisons[] = $binaire;
      }
    }else{
      die("<br><font color= '#800080'>Nombre de combinaisons trop grand, pensez à ajuster vos données</font><br><br>");
    }

      return $combinaisons;
  }

  function maskBinToCidr($mask){
    $count = 0;
    foreach(str_split($mask) as $bit){
      if($bit == "0"){
        break;
      }
      else{
        if($bit == "1"){
          $count++;
        }
      }
    }

    return $count;
  }


  function separateWithDot($adress){
    if (strlen($adress) == 32){
      $tabAdress = str_split($adress, 8);
      $adress = "";
      foreach($tabAdress as $octet){
        $adress .= $octet . ".";
      }
      //pour éliminer le dernier point
      $adress = substr($adress, 0, -1);
    }

    return $adress;
  }

  function subnet($netAdress, $mask, $newMask){
    $mask = maskBinToCidr($mask);
    $newMask = maskBinToCidr($newMask);

    if($newMask > $mask){
      $nBit = $newMask - $mask;
    }else{
      die("<br><font color='#800080'> Erreur : le masque de sous-réseau est trop petit pour le masque de réseau </font><br>");
    }
    
    //Toutes les combinaisons possibles
    $combinaisons = genererCombinations($nBit);
    $tabOctetNetAdress = explode(".", $netAdress);

    //Enlève les points de séparation entre les bits
    $netAdressWithoutDot = "";
    foreach($tabOctetNetAdress as $octet){
      $netAdressWithoutDot .= $octet;
    }

    //Nombre de combinaisons
    $nCombi = count($combinaisons);

    $indiceFirst = $mask;
    $indiceLast = $newMask - 1;
    $adressSubnetWithoutDot = "";
    $adressSubnetWithDot = "";
    $tabSubNet = null;
    //Maximum 30 subnets
    if($nCombi < 30){
      $tabSubNet = [$nCombi];
      for($i=0; $i<$nCombi; $i++){
      
        $adressSubnetWithoutDot = substr_replace($netAdressWithoutDot, $combinaisons[$i], $indiceFirst, $indiceLast-$indiceFirst+1);

        $adressSubnetWithDot = separateWithDot($adressSubnetWithoutDot);
    
        //Ajout dans la liste des sous-réseaux
        $tabSubNet[$i] = $adressSubnetWithDot;
      }
    }else{
      echo "Nombre de sous-réseaux trop élevé";
    }
  if($tabSubNet != null){
    return $tabSubNet;
  }else{
    die("<br><font color='#800080'>Erreur : aucun sous-réseau généré</font><br>");
  }
  }


?> 
<?php
if(preg_match('#' . basename(__FILE__) . '#i', $_SERVER['PHP_SELF'])) { exit('You are not allowed to call this page directly.'); }


class hwAPIv5Calc{
    
    // OLD ALGO START HERE  created by rahulzz rahulmayappa@gmail.com 2009
    
    function calculateOld($imei, $mode){
			$arrayofbytes = array();
			$digesthash = md5($imei.$this->mode($mode));
			$arrayofbytes = $this->bytearray($digesthash);
			return $this->xorbytes($arrayofbytes);
	}
    
   function mode($arg){
			$this->unlock = "5e8dd316726b0335";
			$this->flash = "97b7bc6be525ab44";

			if($arg == 'unlock'){
				return $this->unlock;
			}
			else{
				return $this->flash;
			}
	}
    
   	private function bytearray($hash){
		$splitdigest = substr(chunk_split($hash,2,":"),0,-1);
		$arrdigest = explode(":",$splitdigest);
		return $arrdigest;
	}
	
	private function xorbytes($arr){
	   $codes = "";
		foreach (range(0,3) as $i) {
			$code = dechex(hexdec($arr[$i]) ^ hexdec($arr[4+$i]) ^ hexdec($arr[8+$i])  ^ hexdec($arr[12+$i]));
			if(strlen($code)< 2) {
				$code = "0" . $code;
			}
			$codes = $codes . $code;
		}

		$tmpcdec = hexdec($codes); 
		$tmp1dec = hexdec("1ffffff");
		$tmp2dec = hexdec("2000000");
		$c = $tmpcdec & $tmp1dec;
		$c = $c | $tmp2dec;
		
		return $c;
	}
    
    // NEW ALGO START HERE compailed by rahul g nair rahulmayappa@gmail.com 2013-14
       
    
    function calculateNew($aImei) {
    $i=$this->HWE_MDM_NCK_V2_ALGO_SELCTOR($aImei); 

	switch ($i) {
		case 0:
			$Code= $this->HWE_MDM_NCK_V2_VAR0 ($aImei);
			break;
		case 1:
			$Code= $this->HWE_MDM_NCK_V2_VAR1 ($aImei);
			break;
		case 2:
			$Code= $this->HWE_MDM_NCK_V2_VAR2 ($aImei);
			break;
		case 3:
			$Code= $this->HWE_MDM_NCK_V2_VAR3 ($aImei);
			break;
		case 4:
			$Code= $this->HWE_MDM_NCK_V2_VAR4 ($aImei);
			break;
		case 5:
			$Code= $this->HWE_MDM_NCK_V2_VAR5 ($aImei);
			break;
		case 6:
			$Code= $this->HWE_MDM_NCK_V2_VAR6 ($aImei);
			break;			
        }
    //if($Code[0] == 0)$Code[0] = 9;
    return $Code;
    }
    
    function HWE_MDM_NCK_V2_ALGO_SELCTOR ($aImei){
        $Id = "";
        for ($i = 0; $i<15; $i++) {
		  $Id = $Id + (ord($aImei[$i]) +($i+1))*($i+1);
        }  
	return ($Id % 7);
    }
    
    function crcKw($num){
        $crc = crc32($num);
        if($crc & 0x80000000){
        $crc ^= 0xffffffff;
        $crc += 1;
        $crc = -$crc;
        }
        return $crc;
    }  
    
    function get_unit($hex){
	   $V0=sprintf("%08X",(ord($hex[0]) & 0x000000FF));
	   $V1=sprintf("%08X",(ord($hex[1]) << 0x08) & 0x0000FF00);
	   $V2=sprintf("%08X",(ord($hex[2]) << 0x10) & 0x00FF0000);
	   $V3=sprintf("%08X",(ord($hex[3]) << 0x18) & 0xFF000000);
	   return  $this->bchexdec((substr($V3,0,2).substr($V2,2,2).substr($V1,4,2).substr($V0,6,2)));
    }

    function bchexdec($hex) {
        if(strlen($hex) == 1) {
		return hexdec($hex); 
        } else {
		$remain = substr($hex, 0, -1);
		$last = substr($hex, -1);
		return bcadd(bcmul(16, $this->bchexdec($remain)), hexdec($last));
	}
}

    function bcdechex($dec){
	   $last = bcmod($dec, 16);
	   $remain = bcdiv(bcsub($dec, $last), 16);
       if($remain == 0) {
		  return dechex($last);
	   } else {
		  return $this->bcdechex($remain).dechex($last);
       }
    }
    
    function hex2str($src){
	    $length = count( $src );
        $dst = "";
	    for( $i = 0; $i < $length; $i++ ){
	        $dst .= sprintf( "%c", ( $src[ $i ] ));
	    }
        return $dst;
    }

    function str2hex($src){
	   $length = strlen( $src );
        $dst = "";
		for( $i = 0; $i < $length; $i++ ){
	       $dst .= sprintf( "%02X", ord( $src[ $i ] ));
        }
        return $dst;
    }

    function hextostr($hex){
	    $str='';
        for ($i=0; $i < strlen($hex)-1; $i+=2){
        $str .= chr(hexdec($hex[$i].$hex[$i+1]));
        }
        return $str;
    }
    
    function HWE_MDM_NCK_V2_VAR0 ($aImei){
	$Tbl = array(0x001966A9,0x0021058F,0x002AEDA9,0x0037CE91,
		0x00488C9F,0x005E507D,0x007A9BE5,0x009F644B,
		0x00CF35A1,0x010D5F55,0x015E2F25,0x01C73D6B,
		0x024FCFDD,0x03015B47,0x03E829E9);
	
	$Pass=array();
	$S=0;
	$aNck="";
	for ($i = 0; $i<15; $i++) {
		$S=$S+(ord($aImei[$i])* $Tbl[$i]);
	}
	
	for ($i = 0; $i<8; $i++) {
		$Pass[$i]=($S & 0x0F) % 0x0A;
		$S=$S >> 4;
	}
	
	
	if( $Pass[0] == 0 )
		$Pass[0]=1;
	
	for ($i = 0; $i<8; $i++) {
		$aNck[$i]=$Pass[$i]+0x30;
	}
	
	
	return $this->hex2str($aNck);
    }
    
    function HWE_MDM_NCK_V2_VAR1 ($aImei){
        $crc = $this->crcKw($aImei);
        if ( strlen($crc) > 8 ){
            $crc = substr($crc,strlen($crc)-8,8);
        }
        while(strlen($crc)<8){
            $crc = "9" . $crc;
        }
        if ($crc[0]=='0' || $crc[0]=='-' || $crc[0] == ' '){
	       $crc[0]='9';
        }
        $crc = substr($crc,-8);
        return $crc;
    }
    
    function HWE_MDM_NCK_V2_VAR2 ($aImei) {
	   $Buf=md5($aImei);
       $A=hexdec($Buf[0].$Buf[1]) % 10;
        if ($A==0)  
		  $Buf ='05'.substr($Buf,2,strlen($Buf)-2);
        else 
		  $Buf =sprintf( "%02X",$A & 0xFF).substr($Buf,2,strlen($Buf)-2);
	   $aNck="";
        for( $i = 0; $i<16;  $i=$i+2 ) {
            if (hexdec($Buf[$i].$Buf[$i+1]) > 0x30 && hexdec($Buf[$i].$Buf[$i+1])< 0x39){
                $aNck.=chr(hexdec($Buf[$i].$Buf[$i+1]));
            } else {
            $aNck.=chr((hexdec($Buf[$i].$Buf[$i+1]) % 0xA) + 0x30);
            }
        }
	   return $aNck;
    }
    
    function HWE_MDM_NCK_V2_VAR3 ($aImei){
	   $Buf=md5($aImei. $this->hextostr('7f2270465154e80d3afe22dbe80f3dbf'));
	   $Dgst=pack('H*',$Buf);
	   $aNck="";
	   $Pass_Byte=array();
       for( $i = 0; $i < 4; $i++ ) $Pass_Byte[$i] = ord($Dgst[$i+0x00]) ^ ord($Dgst[$i+0x04]) ^ ord($Dgst[$i+0x08]) ^ ord($Dgst[$i+0xC]);
       $aNck= $Pass_Byte[0] << 0x18 | $Pass_Byte[1] << 0x10 | $Pass_Byte[2] << 0x8 | $Pass_Byte[3];
       $aNck= ($aNck & 0x1FFFFFF) | 0x2000000;
	   return $aNck;
    }
    
    function HWE_MDM_NCK_V2_VAR4 ($aImei){
       $aNck = "";
	   $Data_Buff=pack('H*',$this->str2hex($aImei).'5A');
       for($i = 0; $i < 8; $i++)$Res_Buff[$i]= ord($Data_Buff[$i]) ^ ord($Data_Buff[$i+8]);
       $Magic_Buff=pack('H*',$this->str2hex('5739146280098765432112345678905'));
       for( $i = 0; $i < 8; $i++ ) $Res_Buff[$i]= ($Magic_Buff[(($Res_Buff[$i]) & 0x0F)+(($Res_Buff[$i]) >> 4)]);
       if ($Res_Buff[0] ==0) {
		  for( $i = 0; $i < 8; $i++ )
		  if ($Res_Buff[$i]<> 0) break;
		  $Res_Buff[0]= $i;
	       }
	   for( $i = 0; $i < 8; $i++ ) $aNck.=$Res_Buff[$i];
       return ($aNck);
    }
    
    function HWE_MDM_NCK_V2_VAR5 ($aImei) {
        $Dgst=pack('H*',sha1($aImei));
        $A= $this->bchexdec(sprintf("%08X",ord($Dgst[0]) << 0x18 | ord($Dgst[1]) << 0x10 | ord($Dgst[2]) << 0x8 | ord($Dgst[3])));
	    $B= $this->bchexdec(sprintf("%08X",ord($Dgst[4]) << 0x18 | ord($Dgst[5]) << 0x10 | ord($Dgst[6]) << 0x8 | ord($Dgst[7])));
        return substr($A.$B,0,8);
    }
    
    function HWE_MDM_NCK_V2_VAR6 ($aImei) {

	$Magic_Table = array(0x01,0x01,0x02,0x03,0x05,0x08,0x0D,0x15, 0x22,0x37,0x59,0x90);
	$Buff = array_fill(0, 0x180, 0x00);
	$dest_buff = array_fill(0, 8, 0x00);



	for( $i = 0; $i < 0x0f; $i++ )
	switch ($i % 3) {
		case 0:
			$Buff[$i] =(ord($aImei[$i]) >> 2) & 0xFF |  (ord($aImei[$i]) << 6) & 0xFF;
			break;
		case 1:
			$Buff[$i] = (ord($aImei[$i]) >> 3) & 0xFF|  (ord($aImei[$i]) << 5) & 0xFF;
			break;
		case 2:
			$Buff[$i] = (ord($aImei[$i]) << 4) & 0xFF|  (ord($aImei[$i]) >> 4) & 0xFF;
			break;
	}

	$sum_1 = 0;
	for( $i = 0; $i < 7; $i++ ) 
		$sum_1 = $sum_1 + (($Buff[$i] << 8)   + ($Buff[0x0E - $i]));
	$sum_1 = $sum_1 +$Buff[8];

	$j=0;
	for( $i = 0x0F; $i < 0x80; $i++ ) {
		
		$var_34 = floor($i / 0x0C);
		
		$R1= $i % 0x0C;
		
		$var_38 = $R1 + $var_34;

		if ($var_38 >= 0x0C)   $var_38 = $var_38 -0x0C;
		
		$R1 = $j % 0x0C;
		
		if ($var_34<2) 
			$var_34 = $R1 + $var_34;
		else 
			$var_34 = $R1 + ($var_34 * 0x0D) - 0x18;

		if ($j==0) 
			$R0= sprintf("%08X",($Buff[$sum_1 % $i]) | (0xFFFFFFFF - $Buff[$sum_1 % $i+1])) ;
		else   
			$R0 = sprintf("%08X",(0xFFFFFFFF- $Buff[$sum_1 % $j]) | ($Buff[$sum_1 % $i])) ;
		
		$Buff[$i]=	intval(substr($R0,-2,2),16)|(($Buff[$var_34] & $Magic_Table[$var_38]));
		$j+=1;
		
	}
	
	$Sum_2 = 0;
	for( $i = 0; $i < 7; $i++ )   
		$Sum_2 = $Sum_2 + (((ord($aImei[$i])) << 8) | ord($aImei[$i+1]));
	$Sum_2 = $Sum_2 + ord($aImei[0x0E]);
	
	$Temp=$this->hex2str($Buff);
	$Dgst=md5(substr($Temp,0,0x80));
	$idx = $Sum_2 & 3;
	$hash_unit =($this->hextostr(substr($Dgst,$idx*8,8)));
	$hash_unit=($this->get_unit($hash_unit));

	
	$DgstA=pack("H*",$Dgst);
	$Nck_idx=0;
	
	for( $i = 0; $i < 0x10; $i++ ) 
	{
		
		if ((ord($DgstA[$i]) >= 0x30) & (ord($DgstA[$i]) <= 0x39) )
		{
			$dest_buf[$Nck_idx] = ord($DgstA[$i]);
			$Nck_idx =$Nck_idx+1;
		}
		if ($Nck_idx == 8) {
			if ($dest_buf[0] ==0x30)
				if ($Sum_2 == 0)   
					$dest_buf[0] = (ord($Dgst[0]) & 7) + 0x31;
				else 
					$dest_buf[0] = (ord($Dgst[1]) & 7) + 0x31;
			return $this->hex2str($dest_buf);
		}
		
	}
	$j = 0;


	While ($hash_unit != 0):
		$R1 = bcmod($hash_unit , 0xA);
		$hash_unit = bcdiv( $hash_unit , 0xA);

		$dest_buf[$Nck_idx] = $R1 + 0x30;
		$Nck_idx +=1;
		
		if (( $hash_unit == 0) && ($j == 0)) 
		{
			$j = 1;
			$hash_unit =( $this-> hextostr(substr($Dgst,3-$idx,8)));
			$hash_unit=$this->get_unit($hash_unit);
		}
		
		if ($Nck_idx == 8){
			if ($dest_buf[0] ==0x30)
				if ($Sum_2 == 0)   
					$dest_buf[0] = (ord($DgstA[0]) & 7) + 0x31;
				else 
					$dest_buf[0] = (ord($DgstA[1]) & 7) + 0x31;
				
				return $this->hex2str($dest_buf);	
		}
	endwhile;
	
	for( $i = 0; $i < 0x10; $i++ ) 
	{
		if ($Nck_idx== 8)  
			$dest_buf[0] = (ord($DgstA[$i]) % 10) + 0x30;
		else
		{
			$dest_buf[$Nck_idx] = (ord($DgstA[$i]) % 10) + 0x30;
			$Nck_idx+=1;
		}
		if ($Nck_idx>=8) 
			if ($dest_buf[0] != 0x30) return $this->hex2str($dest_buf);
	}
	
	if ($Nck_idx == 8){
		if ($dest_buf[0] ==0x30)
			if ($Sum_2 == 0)   
				$dest_buf[0] = (ord($DgstA[0]) & 7) + 0x31;
			else 
				$dest_buf[0] = (ord($DgstA[1]) & 7) + 0x31;
		return $this->hex2str($dest_buf);
    }
    
    }
}
?>
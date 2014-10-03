<?php

class NumberToText {

	private $number = 0;
	private $text 	= '';

	public function __construct($number)
	{
		$number = trim($number);

		if ( preg_match('/^[0-9\.]+$/', $number) )
		{
			$this->number = $number;
		}

		$this->parse( $this->number );	
	} 

	private function parse($number)
	{
		$number_part = explode('.', $number);

		if ( isset($number_part[0]) )
		{
			$entero = $number_part[0];
			$length = strlen($entero);
			$resto	= substr($entero, 1, strlen($entero));
 
			// Para valores dede 1 millon hasta 999 millones
			if ( preg_match('/^[0-9]{7,9}$/', $entero) )
			{ 
				$title_length 	= ( $length == 9 ? 3 : ( $length == 8 ? 2 : 1 ) );
				$title_value 	= substr($entero, 0, $title_length);
				 
				$this->text .= $this->getInteger( $title_value, $entero );

				$this->text .= ' Millon ';

				if ( preg_match('/[1-9]/', $resto) )
				{
					$resto = preg_replace('/^0+/', '', $resto); 

					$this->text .= $this->parse( $resto );
				} 
			}

			// Para valroes desde 1 mil hasta 999 mil
			if ( preg_match('/^[0-9]{4,6}$/', $entero) )
			{ 
				$title_length 	= ( $length == 6 ? 3 : ( $length == 5 ? 2 : 1 ) );
				$title_value 	= substr($entero, 0, $title_length);
				     

				$this->text    	.= $this->getInteger( $title_value, $entero ) ;  
				$this->text 	.= ($title_value <= 10 
										|| ( $title_value >= 99 && $title_value <= 100 
											|| in_array($title_value, array('11', '12', '13', '14', '15'))  ) ? ' Mil ': ' y ' ); 
 
				if ( preg_match('/[1-9]/', $resto) )
				{
					$resto 		 = preg_match('/^11/', $entero) && $title_length == 2 ? preg_replace('/^1/', '', $resto): $resto;
	 				$resto 		 = preg_match('/^12/', $entero) && $title_length == 2 ? preg_replace('/^2/', '', $resto): $resto;
	 				$resto 		 = preg_match('/^13/', $entero) && $title_length == 2 ? preg_replace('/^3/', '', $resto): $resto;
	 				$resto 		 = preg_match('/^14/', $entero) && $title_length == 2 ? preg_replace('/^4/', '', $resto): $resto;
	 				$resto 		 = preg_match('/^15/', $entero) && $title_length == 2 ? preg_replace('/^5/', '', $resto): $resto; 	
				}  

 				if ( ! in_array($entero, array('11', '12', '13','14', '15')) )
				{  
					if ( preg_match('/[1-9]/', $resto) )
					{  
						$resto = preg_replace('/^0+/', '', $resto);  

						$this->text .= $this->parse( $resto );
					}
				} 
			}

			if ( preg_match('/^[0-9]{1,3}$/', $entero) )
			{   
				$this->text .= (strlen($entero) == 1 && $this->text ? ' y ': '');

				$this->text .= $this->getInteger( $entero, $entero );

				if ( ! in_array($entero, array('11', '12', '13','14', '15')) )
				{
					if ( preg_match('/[1-9]/', $resto) )
					{
						$resto = preg_replace('/^0+/', '', $resto); 

						$this->text .= $this->parse( $resto );
					} 
				} 	
			}
		}

		if ( isset($number_part[1]) )
		{
			$decimal = $number_part[1];

			$this->text .= ' con ' . $this->getDecimal($decimal);
		}
	}

	private function getInteger($number, $original)
	{
		$integer = array(
							1 => ' Un ',
							2 => ' Dos ',
							3 => ' Tres ',
							4 => ' Cuatro ',
							5 => ' Cinco ',
							6 => ' Seis ',
							7 => ' Siete ',
							8 => ' Ocho ',
							9 => ' Nueve '
						);

		$resto = substr($original, 1, strlen($original)) ;


		if ( strlen($original) == 3 || strlen($number) == 3 )
		{  
			$integer[ 1 ] = ' Cien' . (! preg_match('/[1-9]$/', $resto) ? ' ': 'to ' ); 
			$integer[ 1 ] = preg_match('/^(0+)[1-9]$/', $resto) ? str_replace('to', '', $integer[ 1 ]): $integer[ 1 ];

			$integer[ 2 ] = ' Doscientos ';
			$integer[ 3 ] = ' Trescientos ';
			$integer[ 4 ] = ' CuatroCientos ';
			$integer[ 5 ] = ' Quinientos ';
			$integer[ 6 ] = ' Seiscientos ';
			$integer[ 7 ] = ' Setecientos ';
			$integer[ 8 ] = ' Ochocientos ';
			$integer[ 9 ] = ' Novecientos ';
		}

		if ( strlen($original) == 2 || strlen($number) == 2 )
		{
			$integer[ 1 ] = ' Diez ';
			$integer[ 2 ] = ' Veinte ';
			$integer[ 3 ] = ' Treinta ';
			$integer[ 4 ] = ' Cuarenta ';
			$integer[ 5 ] = ' Cincuenta ';
			$integer[ 6 ] = ' Sesenta ';
			$integer[ 7 ] = ' Setenta ';
			$integer[ 8 ] = ' Ochenta ';
			$integer[ 9 ] = ' Noventa ';

			$integer[ 1 ] = ( $original == 11 || $number == 11 ? ' Once ': $integer[ 1 ] ); 
			$integer[ 1 ] = ( $original == 12 || $number == 12 ? ' Doce ': $integer[ 1 ] ); 
			$integer[ 1 ] = ( $original == 13 || $number == 13 ? ' Trece ': $integer[ 1 ] ); 
			$integer[ 1 ] = ( $original == 14 || $number == 14 ? ' Catorce ': $integer[ 1 ] ); 
			$integer[ 1 ] = ( $original == 15 || $number == 15 ? ' Quince ': $integer[ 1 ] );  

		}  

		return $integer[ ( substr($number, 0, 1) ) ];
	}

	private function getDecimal($number)
	{
		return $number;
	}	

	public function getText()
	{
		return $this->text;
	}
}
 
$numero 		= @$_POST['txt_numero']; 
$numero_val		= null;

if ( $numero && @preg_match('/^[0-9\.]+$/', $numero) )
{
	$resultado 	= new NumberToText( $numero );

	$numero_val	= $numero;
	$texto 		= $resultado->getText();
} 
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Numero a Letra</title>
	<style type="text/css">form{max-width: 20em;border: 1px solid;padding: 2em;margin: 0 auto;}</style>
</head>
<body>
	<form action="" method="POST">
		<label>
			Numero
			<input type="text" name="txt_numero" value="<?php echo $numero_val; ?>" required pattern="^[0-9\.]+$">
		</label>
		<label>
			<input type="submit" value="Traducir">
		</label>
		<?php 
		if ( isset($texto) && $texto )
		{
			echo '<br><p>'.$texto.'</p>';
		}
		?>	
	</form>
</body>
</html>
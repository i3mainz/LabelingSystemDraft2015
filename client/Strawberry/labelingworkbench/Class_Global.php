<?php
    class GlobalVariables {
		
		public static $menuLanguage = null;
		public static $prefLanguage = null;
		
		public function getSelectOption($id) {
			if(GlobalVariables::$prefLanguage == "de") { $de = " selected"; } else { $de = ""; }
			if(GlobalVariables::$prefLanguage == "en") { $en = " selected"; } else { $en = ""; }
			if(GlobalVariables::$prefLanguage == "fr") { $fr = " selected"; } else { $fr = ""; }
			if(GlobalVariables::$prefLanguage == "es") { $es = " selected"; } else { $es = ""; }
			if(GlobalVariables::$prefLanguage == "it") { $it = " selected"; } else { $it = ""; }
			if(GlobalVariables::$prefLanguage == "pl") { $pl = " selected"; } else { $pl = ""; }
			if(GlobalVariables::$prefLanguage == "de-x-orig") { $dexorig = " selected"; } else { $dexorig = ""; }
			$out = 	"<select id='".$id."'>
						<option value='de'".$de.">deutsch</option>
						<option value='en'".$en.">english</option>
						<option value='fr'".$fr.">français</option>
						<option value='es'".$es.">español</option>
						<option value='it'".$it.">italiano</option>
						<option value='pl'".$pl.">polski</option>
						<option value='de-x-orig'".$dexorig.">deutsch original</option>
					</select>";
			return $out;
		}
		
	}
?>
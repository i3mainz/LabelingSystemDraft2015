package de.i3mainz.ls.utils;

public class Utils {

	/**
	 * not used
	 * @param string
	 * @return 
	 */
	public static String maskURI(String string) {
		string = string.replace(" ", "%20");
        string = string.replace(":", "%3A");
        string = string.replace("?", "%3F");
        string = string.replace("^", "%5E");
        string = string.replace("{", "%7B");
        string = string.replace("}", "%7D");
        string = string.replace("/", "%2F");
        string = string.replace("\"", "%22");
        string = string.replace("<", "%3C");
        string = string.replace(">", "%3E");
        string = string.replace("#", "%23");
        string = string.replace("&", "%26");
        string = string.replace("=", "%3D");
		return string;
	}
	
	/**
	 * not used
	 * @param string
	 * @return 
	 */
	public static String UTF8toASCII(String string) {
		// erweitern nach http://www.backbone.se/urlencodingUTF8.htm
		string = string.replace("%C3%80", "%c0"); //À
		string = string.replace("%C3%81", "%c1"); //Á
		string = string.replace("%C3%82", "%c2"); //Â
		string = string.replace("%C3%83", "%c3"); //Ã
		string = string.replace("%C3%84", "%c4"); //Ä
		string = string.replace("%C3%85", "%c5"); //Å
		string = string.replace("%C3%86", "%c6"); //Æ
		string = string.replace("%C3%87", "%c7"); //Ç
		string = string.replace("%C3%88", "%c8"); //È
		string = string.replace("%C3%89", "%c9"); //É
		string = string.replace("%C3%8A", "%ca"); //Ê
		string = string.replace("%C3%8B", "%cb"); //Ë
		string = string.replace("%C3%8C", "%cc"); //Ì
		string = string.replace("%C3%8D", "%cd"); //Í
		string = string.replace("%C3%8E", "%ce"); //Î
		string = string.replace("%C3%8F", "%cf"); //Ï
		string = string.replace("%C3%90", "%d0"); //Ð
		string = string.replace("%C3%91", "%d1"); //Ñ
		string = string.replace("%C3%92", "%d2"); //Ò
		string = string.replace("%C3%93", "%d3"); //Ó
		string = string.replace("%C3%94", "%d4"); //Ô
		string = string.replace("%C3%95", "%d5"); //Õ
		string = string.replace("%C3%96", "%d6"); //Ö
		string = string.replace("%C3%97", "%d7"); //×
		string = string.replace("%C3%98", "%d8"); //Ø
		string = string.replace("%C3%99", "%d9"); //Ù
		string = string.replace("%C3%9A", "%da"); //Ú
		string = string.replace("%C3%9B", "%db"); //Û
		string = string.replace("%C3%9C", "%dc"); //Ü
		string = string.replace("%C3%9D", "%dd"); //Ý
		string = string.replace("%C3%9E", "%de"); //Þ
		string = string.replace("%C3%9F", "%df"); //ß
		string = string.replace("%C3%A0", "%e0"); //à
		string = string.replace("%C3%A1", "%e1"); //á
		string = string.replace("%C3%A2", "%e2"); //â
		string = string.replace("%C3%A3", "%e3"); //ã
		string = string.replace("%C3%A4", "%e4"); //ä
		string = string.replace("%C3%A5", "%e5"); //å
		string = string.replace("%C3%A6", "%e6"); //æ
		string = string.replace("%C3%A7", "%e7"); //ç
		string = string.replace("%C3%A8", "%e8"); //è
		string = string.replace("%C3%A9", "%e9"); //é
		string = string.replace("%C3%AA", "%ea"); //ê
		string = string.replace("%C3%AB", "%eb"); //ë
		string = string.replace("%C3%AC", "%ec"); //ì
		string = string.replace("%C3%AD", "%ed"); //í
		string = string.replace("%C3%AE", "%ee"); //î
		string = string.replace("%C3%AF", "%ef"); //ï
		string = string.replace("%C3%B0", "%f0"); //ð
		string = string.replace("%C3%B1", "%f1"); //ñ
		string = string.replace("%C3%B2", "%f2"); //ò
		string = string.replace("%C3%B3", "%f3"); //ó
		string = string.replace("%C3%B4", "%f4"); //ô
		string = string.replace("%C3%B5", "%f5"); //õ
		string = string.replace("%C3%B6", "%f6"); //ö
		string = string.replace("%C3%B7", "%f7"); //÷
		string = string.replace("%C3%B8", "%f8"); //ø
		string = string.replace("%C3%B9", "%f9"); //ù
		string = string.replace("%C3%BA", "%fa"); //ú
		string = string.replace("%C3%BB", "%fb"); //û
		string = string.replace("%C3%BC", "%fc"); //ü
		string = string.replace("%C3%BD", "%fd"); //ý
		string = string.replace("%C3%BE", "%fe"); //þ
		string = string.replace("%C3%BF", "%ff"); //ÿ
		return string;
	}

}

#Labeling System Widgets

##Dropdown Widget

###How to?

(1) insert jQuery in head section

```html
<script src="http://code.jquery.com/jquery-latest.js"></script>
```

(2) include style

```html
<style>
</style>
```

(3) create select element in body section

(4) set attribute lswidgettype = "dropdown"

(5) set attribute dataid = "labeling system vocabulary identifier"

```html
<select id="" lswidgettype="dropdown" dataid=""></select>
```

(6) include magic code

```html
<script>
!function(e){for(var a=0;a<e.length;a++){var l="http://labeling.i3mainz.hs-mainz.de/labelingserver/SPARQL?query=PREFIX%20ls%3A%20%3Chttp%3A%2F%2Flabeling.i3mainz.hs-mainz.de%2Fvocab%23%3E%0APREFIX%20skos%3A%20%3Chttp%3A%2F%2Fwww.w3.org%2F2004%2F02%2Fskos%2Fcore%23%3E%0APREFIX%20ls-voc%3A%20%3Chttp%3A%2F%2Flabeling.i3mainz.hs-mainz.de%2Fvocabulary%23%3E%0ASELECT%20%3Flabel%20WHERE%20%7B%0A%09%3Fs%20ls%3Aidentifier%20%3Fidentifier%20.%0A%09ls-voc%3A#dataid#%20ls%3Acontains%20%3Fs%20.%0A%09%3Fs%20skos%3AprefLabel%20%3Flabel%20.%0A%09%3Fs%20ls%3AprefLang%20%3FprefLang%20.%0A%09FILTER(LANGMATCHES(LANG(%3Flabel)%2C%20%3FprefLang))%0A%7D%20ORDER%20BY%20ASC(%3Flabel)&format=json&file=true";l=l.replace("#dataid#",$(e[a]).attr("dataid")),$.ajax({url:l,async:!1,error:function(e,a,l){console.error(l)},success:function(l){try{l=JSON.parse(l)}catch(i){}var n=l.results.bindings;for(item in n){var r=n[item].label.value;$(e[a]).append('<option value="'+r+'">'+r+"</option>")}}})}}($("select[lswidgettype='dropdown']"));
</script>
```

##Detail Widget

###How to?

(1) insert jQuery in head section

```html
<script src="http://code.jquery.com/jquery-latest.js"></script>
```

(2) include style

```html
<style>
	* { font-family: sans-serif;}
	.ls-div { border: 1px solid; background: lightgrey; width:250px; padding:10px; font-size: 16px;}
	.ls-div-language { font-style: italic; font-size: 12px; }
  a { color: black; }
</style>
```

(3) create div element in body section

(4) set attribute lswidgettype = "detail-label"

(5) set attribute dataid = "labeling system label identifier"

```html
<div id="" class="ls-div" lswidgettype="detail-label" dataid=""></div>
```

(6) include magic code

```html
<script>
!function(a){for(var e=0;e<a.length;e++){var l="http://labeling.i3mainz.hs-mainz.de/labelingserver/SPARQL?query=PREFIX%20ls%3A%20%3Chttp%3A%2F%2Flabeling.i3mainz.hs-mainz.de%2Fvocab%23%3E%0A%09%09PREFIX%20skos%3A%20%3Chttp%3A%2F%2Fwww.w3.org%2F2004%2F02%2Fskos%2Fcore%23%3E%0A%09%09PREFIX%20ls-lab%3A%20%3Chttp%3A%2F%2Flabeling.i3mainz.hs-mainz.de%2Flabel%23%3E%0A%09%09SELECT%20*%20WHERE%20%7B%0A%09%09%09%3Fs%20ls%3Aidentifier%20%3Fidentifier%20.%0A%09%09%09%3Fs%20skos%3AprefLabel%20%3Flabel%20.%0A%09%09%09%3Fs%20ls%3AprefLang%20%3FprefLang%20.%0A%09%09%09FILTER(LANGMATCHES(LANG(%3Flabel)%2C%20%3FprefLang))%0A%20%20%20%20%20%20%20%20%20%20%20%20FILTER%20(%3Fidentifier%20%3D%20%22#dataid#%22)%0A%09%09%7D%20ORDER%20BY%20ASC(%3Flabel)&format=json&file=true";l=l.replace("#dataid#",$(a[e]).attr("dataid")),$.ajax({url:l,async:!1,error:function(a,e,l){console.error(l)},success:function(l){try{l=JSON.parse(l)}catch(i){}var n=l.results.bindings;for(item in n){var r=n[item].label.value,s=n[item].prefLang.value,t=n[item].s.value;$(a[e]).append('<p><a href="'+t+'" target="_blank">'+r+'</a> <span class="ls-div-language">'+s+"</span></p>")}}})}}($("div[lswidgettype='detail-label']"));
</script>
```

##Autocomplete Widget

###How to?

(1) insert jQuery and jquery.autocomplete in head section

```html
<script src="http://code.jquery.com/jquery-latest.js"></script>
<script src="http://labeling.i3mainz.hs-mainz.de/client/jquery.autocomplete.js"></script>
```

(2) include style

```html
<style>
	* { font-family: sans-serif;}
	.autocomplete-suggestions { border: 1px solid; background: #FFF; overflow: auto; }
	.autocomplete-suggestions strong { font-weight: bold; font-style: italic; }
	.autocomplete-selected { background: lightgrey; }
	.autocomplete-search { font-size: 16px; border: 1px solid; width: 500px; height:30px; }
</style>
```

(3) create div element in body section

(4) set attribute lswidgettype = "autocomplete-ls" OR "autocomplete-creator" OR "autocomplete-vocabulary"

(5) set attribute dataid = "labeling system label identifier" OR labeling system user identifier

```html
<input type='text' class='autocomplete-search' id='' lswidgettype="autocomplete-ls" />
<input type='text' class='autocomplete-search' id='' lswidgettype="autocomplete-creator" dataid="" />
<input type='text' class='autocomplete-search' id='' lswidgettype="autocomplete-vocabulary" dataid="" />
```

(6) include magic code

```html
<script>
!function(e){$(e).autocomplete({minChars:2,showNoSuggestionNotice:!0,noSuggestionNotice:"Sorry, no matching results",serviceUrl:"http://labeling.i3mainz.hs-mainz.de/labelingserver/autocomplete",lookupFilter:function(e,t,n){var o=new RegExp("\\b"+$.Autocomplete.utils.escapeRegExChars(n),"gi");return o.test(e.value)},onSelect:function(e){window.open(e.label)},onHint:function(e){},onInvalidateSelection:function(){console.info("You selected: none")}})}($("input[lswidgettype='autocomplete-ls']")),!function(e){$(e).autocomplete({minChars:2,showNoSuggestionNotice:!0,noSuggestionNotice:"Sorry, no matching results",serviceUrl:function(){var t="http://labeling.i3mainz.hs-mainz.de/labelingserver/autocomplete";return t+="?creator="+$(e).attr("dataid")},lookupFilter:function(e,t,n){var o=new RegExp("\\b"+$.Autocomplete.utils.escapeRegExChars(n),"gi");return o.test(e.value)},onSelect:function(e){window.open(e.label)},onHint:function(e){},onInvalidateSelection:function(){console.info("You selected: none")}})}($("input[lswidgettype='autocomplete-creator']")),!function(e){$(e).autocomplete({minChars:2,showNoSuggestionNotice:!0,noSuggestionNotice:"Sorry, no matching results",serviceUrl:function(){var t="http://labeling.i3mainz.hs-mainz.de/labelingserver/autocomplete";return t+="?vocabulary="+$(e).attr("dataid")},lookupFilter:function(e,t,n){var o=new RegExp("\\b"+$.Autocomplete.utils.escapeRegExChars(n),"gi");return o.test(e.value)},onSelect:function(e){window.open(e.label)},onHint:function(e){},onInvalidateSelection:function(){console.info("You selected: none")}})}($("input[lswidgettype='autocomplete-vocabulary']"));
</script>
```

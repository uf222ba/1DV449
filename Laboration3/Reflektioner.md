# Laboration 3
Kurs: Webbteknik II, 1DV449  
Ulrika Falk, uf222ba  
Körbar version finns på: [http://www.falkebo.com/1DV449/Lab3/map.html](http://www.falkebo.com/1DV449/Lab3/map.html)

##### 1. Vilka krav måste man anpassa sig efter i de olika API:erna?
Man kan bara använda den funktionalitet som API:erna erbjuder. När det gäller Google maps behöver man en API-nyckel för att kunna få tillgång till gränssnittet.
Sveriges radios API var väldigt rättframt. Jag fick dock inte sorteringen att fungera, vilket gjordet att jag fick lösa sortering och begränsning på ett annat sätt än vad jag ursprungligen tänkte.
##### 2. Hur cachas datat för att slippa göra onödiga anrop till API:erna?
Datat sparas ner till en lokal fil och det är hela tiden den lokala filen som används. När datat hämtas, sker detta från ett AJAX-anrop till en php-fil. I php-filen sker först en kontroll om det gått mer än fem minuter från senaste hämtningen. Tiden för senaste hämtningen sparas i en textfil. Har det gått mindre än fem minuter hämtas innehållet i den lokala filen direkt, om inte, så hämtas ny data, sparas ner till den lokala filen, tiden för hämtningen sparas i tidsfilen och innehållet i den lokala filen returneras.
##### 3. Vilka risker finns med applikationen?
Den största risken är väl att man sänker Sveriges radio genom en överbelastningsattack om applikationen hela tiden frågar efter nytt data från deras server.
##### 4. Hur har du tänkt kring säkerheten i din applikation?
Eftersom applikationen inte hanterar några användarinmatningar anser jag inte att det finns några överhängande säkerhetsproblem. När det gäller Google maps API-nyckel är det vettigt att begränsa användningen till sin egen domän, så att ingen annan "snor" nyckeln och använder den på sin sida, för då kommer quotat att ta slut om det är en välbesökt sida. Den största risken är väl att man sänker Sveriges radio genom en överbelastningsattack genom att hela tiden fråga efter nytt data från deras server. 
##### 5. Hur har du tänkt kring optimeringen i din applikation?
Applikationen använder CDN för bootstrap och jQuery. All data hämtas från den lokala datafilen.
##### 6. Referenser
Hur man sorterar arrayer i Javascript: [http://www.javascriptkit.com/javatutors/arraysort2.shtml](http://www.javascriptkit.com/javatutors/arraysort2.shtml)  
Förklaringar till hur man använder olika metoder i jQuery (i detta fall grep): [http://code.tutsplus.com/tutorials/20-helpful-jquery-methods-you-should-be-using--net-10521](http://code.tutsplus.com/tutorials/20-helpful-jquery-methods-you-should-be-using--net-10521)  
Kod och förklaringar till hur man använder infowindows och olikfärgade markörer till en Google map: [http://chrisltd.com/blog/2013/08/google-map-random-color-pins/](http://chrisltd.com/blog/2013/08/google-map-random-color-pins/)  
Javascript tutorial om arrayer: [http://javascript.info/tutorial/array](http://javascript.info/tutorial/array)  
Exempel på scrollbars: [http://www.way2tutorial.com/html/example/div_tag_scrollbar_example.php](http://www.way2tutorial.com/html/example/div_tag_scrollbar_example.php)  
Förklaring till hur man öppnar infowindows från "externa" länkar på en Google maps-karta: [http://www.interactivetools.com/forum/forum-posts.php?Gecoder---Open-map-infowindow-from-external-link-79317](http://www.interactivetools.com/forum/forum-posts.php?Gecoder---Open-map-infowindow-from-external-link-79317)  
Hur man gör Javascript closures: [http://www.mennovanslooten.nl/blog/post/62](http://www.mennovanslooten.nl/blog/post/62)
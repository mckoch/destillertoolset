  <div id="teaser">
    <div class="subcolumns">
      <div class="c25l">
      </div>
      <div class="c25l">
      </div>
      <div class="c25l">
      </div>
      <div class="c25r">
      </div>
    </div>
  </div>
  
  <div id="main">
    <div id="col1">
      <div id="col1_content" class="clearfix">
 
			<?php 
			
			/**
* @package DestillerV0.5
* @author mckoch@mckoch.de
* @copyright M.C. Koch 2009
* @license GNU General Public License  http://www.fsf.org 
* @link http://content-analyzer.de Destiller home}
* @filesource
*/
			//this is a pure text/html file!
			echo   ("
			<ul class='tabs' id='allabouttab'><li><a href='#'>?</a></li><li><a href='#'>README</a></li><li><a href='#'>Version</a></li><li><a href='#'>FAQ</a></li>
			<li><a href='#'>Disclaimer</a></li><li><a href='#'>More about</a></li>
			</ul><div class='panes'>
			<div> </div>
			<div>
			<h2>In K&uuml;rze</h2>
			<p>Der Text & Link Destiller analysiert und vergleicht MicroContentObjekte (Websites) hinsichtlich 
			ihrer technischen und semantischen Struktur. Neben einer Vielzahl &uuml;blicher textstatistischer Untersuchungen verschafft der Destiller einen &uuml;berblick sowohl hinsichtlich Textstrukturen, Sprachgebrauch als auch technisch-formalem Aufbau eines ganzen Feldes von frei definierbaren URLs. Haupt-Einsatzfeld des Destillers ist im Bereich der Search Engine Optimization (SEO). In der 'public version' ist die Untersuchung auf jeweils einen URL eingeschr&auml;nkt, Umleitungen und enthaltene Verweise werden nicht automatisch verfolgt.
			</p>
			<h2>About this Version</h2>
			<p>Die aktuelle Version 0.4a public des Text & Link Destillers bietet eine einfache lineare Analyse (fast) beliebiger URLs. Neben den g&auml;ngigen Kennzahlen wird ein 'Semantic Footprint' berechnet und einmalig als Grafik ausgegeben. F&uuml;r das &ouml;ffenliche Front-End sind keine weiteren Parametrisierungen m&ouml;glich. Die Benutzung von Firefox ab Version 2, aktiviertes Javascript und Cookies sind Vorraussetzung zur effektiven Benutzung. Viel Spass!
			</p>
			
			<h2>Im Detail</h2>
			<p>Der Text und Link Destiller ist ein einzigartiges Online-Tool des 'semantischen Web' zur Untersuchung und Vergleich von Micro Content Objekten. Die Analyse gew&auml;hrt eine Suchmaschinen &auml;hnliche Sicht auf ein Feld thematisch oder technisch verwandter Websites nebst enthaltenem Begriffsspektrum. 
			</p>
			<p>Neben der quantitativen Untersuchung von Webtexten versucht der Destiller, Text-Objekte qualitativ unabh&auml;ngig von tats&auml;chlichen Keywords abzubilden und zu unterscheiden, eine einfache visuelle Repr&auml;sentation ist der angezeigte 'Semantic Footprint', der 'semantische Fingerabdruck' (besser: Fu&szlig;abdruck) einer Website.
			</p>
			<p>Der berechnete 'Koeffizient relativer Informationsdichte' dient als eigene Kennzahl f&uuml;r die 'Inhaltsschwere' des einzelnen Objektes in der aktuellen Gesamtmenge der untersuchten Objekte; d.h. ein (relativ) hoher KiR spiegelt die system-interne 'Vermutung' einer eben so hohen wie wohl geformten Menge relevanter Informationen in einem bestimmten Objekt (hier: Website) innerhalb der Gesamtmenge aller untersuchten MicroContentObjekte.
			</p> 
			
			</div><div>
			<h2>FREE VERSION</h2>
			<p>Berechnete Kennzahlen: Flesch-Kincaid Reading Ease, Flesch-Kincaid Grade Level, Gunning Fog Score, Colman-Liau Index, SMOG Index, Automated Readability Index (Anmerkung: hierbei handelt es sich durchg&auml;ngig um Ma&szlig;zahlen aus dem angloamerikanischen Sprachraum; diese sind also nur begrenzt auf Texte in anderen Sprachen als Englisch anwendbar). Hinzu kommen Kennzahlen sowie absolute und Durchschnittswerte allgemeiner Textstatistik wie Gesamtzeichenzahl, Wortanzahl, Satzl&auml;ngen, verwendeter Sprachschatz, Silbenanzahl (u.a.). 
			</p>
			<h2>PRO VERSION:</h2>
			<p>Alle Funtionen der FREE VERSION, m. E. zus&auml;tzlich:
			- Erl&auml;uterungen zu Kennzahlen und ihren jeweilig aktuellen Wertebereich
			- DOM-Analyse der Dokumente
			- Zugriff und Analyse einzelner DOM-Elemente &uuml;ber CSS-Selektoren
			- heuristische Spam-Wahrscheinlichkeits-Analyse
			- Erkennung und Ber&uuml;cksichtigung von Umleitungen und HTTP-Headern
			- Erkennung der im Dokument verwendeten Sprache, automatische Anwendung von 
			- sprachspezifische Ausnahmeregelungen (Stoppw&ouml;rter, Silbentrennung)
			- interner Pagerank nach Google-Whitepapers, Vergleich mit tats&auml;chlichem Pagerank ( Implementation nach http://ilpubs.stanford.edu:8090/422/ )
			- Abspeichern eigener Analysen
			- Anzeige der URIs im Footprint
			- Aufbau Benutzer-spezifischer Datenbank, 'einblendbare' Gesamtdatenbank
			- Klassifizierung von Texten, lernf&auml;hig an Hand von Beispielen
			- Eingabe von zu analysierenden Texten &uuml;ber Formular (!!)
			- Erstellung eigener Wordclouds aus Analysedatenbank, Visualisierung von Begriffszusammenh&auml;ngen
			- Batch-Modus: automatisierte Analysel&auml;ufe aus URL-Listen und/oder Suchergebnissen
			- einstellbare Tiefe des Batchmodus
			- regelbare Trennsch&auml;rfe der integrierten Ranking-Algorhythmen (Worth&auml;ufigkeit, eigene Stoppw&ouml;rter, eigene Keywords, Wortl&auml;nge, Silbenzahl, Min/Max der Kennzahlen)
			- Einzel- und Cloudanalysen als indiviualisiertes PDF speicherbar mit Erl&auml;uterungen 
			- schrittweise Bedienungsanleitung zur Erstellung und Interpretation eigener Analysen
			- Zugriff auf gespeicherte Objektdatenbank
			- Erstellung von speicherbaren URL-Listen, Text&uuml;bersichten, Grafiken, konfigurierbare Highscorelisten, modifizierbare Ranking-Formeln
			</p>
			<h2>API VERSION:</h2>
			<p>Grunds&auml;tzlich bietet der Destiller eine f&uuml;r Drittprogramme oder Webdienste per XML-RPC nutzbare API an. Sollte Interesse an einer Nutzung bestehen, so benutzen Sie bitte <a href='modules.php?name=Feedback'>das Kontaktformular f&uuml;r eine formlose Anfrage</a>.
			</p>
			</div><div>
			<h2>Wozu?</h2>
			<p>Beispiel: meine Positionierung in den Google-Ergebnissen ist besch****. Mit dem Destiller lassen sich das semantische Umfeld und die Dokumenten- sowie Textstruktur der erfolgreich(er)en Mitbewerber untersuchen und in Beziehung zur eigenen Website setzen. Die Gesamtanalyse und Darstellung erfolgt sowohl auf technischer als auch auf sprachlicher Ebene f&uuml;r jedes einzelne wie auch die Gesamtmenge der untersuchten Dokumente. Im Gegensatz zu g&auml;ngigen SEO-Tools wird &uuml;ber Keywords hinaus (prim&auml;r) die Gesamtstruktur von Text und Darstellung unabh&auml;nig von den verwendeten Begriffen untersucht und abgebildet. Hieraus lassen sich (ggfls. unter Hinzuziehung von Fachleuten?!) folgende Fragen beantworten: Benutzt meine Website die richtige Sprache? Ist meine Verlinkung im richtigen Umfeld? Ist die technische und semantische Struktur meiner Seiten in Ordnung? Kurz: wie sieht eine Suchmaschine meine Seiten im Vergleich zu meinen Mitbewerbern und wo ist Optimierungsbedarf?
			</p>
			<h2>F&uuml;r wen?</h2>
			<p>Nun, der Destiller eignet sich f&uuml;r Textarbeiter jeder Art, vozugsweise im Internet ;-) Dies kann zur Optimierung der eigenen Website dienen, als auch f&uuml;r Kundenwebsites oder Konkurrenzanalysen im Vorfeld eines (Re-)Launchs: sowohl der einzelne Websitebetreiber als auch Reseller und Consultants profitieren von der umfassenden Analyse und Darstellung des Text & Link Destillers. Und nat&uuml;rlich alle, die sich f&uuml;r state-of-the-art-Webtechnologie und fortgeschrittenes Content Management interessieren. 
			</p>
			<h2>Szenarien</h2>
			1.) Ich will eine Website launchen. Wie kann mir der Destiller helfen?
			Ablauf: 
			- Abfragen der Target-Words und / oder
			- Erstellen einer Liste (Top  10 -Top 50) der Mitbewerber
			- Abfrage der einzelnen Links (einfaches copy & paste der Listen)
			- Resultat: eine &uuml;bersicht &uuml;ber verwendete Sprache und verlinktes Umfeld der untersuchten Websites; entspricht einer Vokabular-Empfehlung f&uuml;r die Erstellung von Webtexten. &uuml;bersicht thematisch relevanter Links und Verlinkungsm&ouml;glichkeiten.
			
			2.) Ich habe eine Website, die 'unter Ferner liefen' bei mir wichtigen Keywords performt?
			Ablauf:
			- Abfrage der Top 10 bis Top 50
			- Abfrage der einzelnen Links (copy & paste der erstellte Listen)
			- Abfrage der eigenen Website
			- Resultat: siehe 1.), zus&auml;tzlich eine &uuml;bersicht &uuml;ber 'unterst&uuml;tzende Themen' (abh&auml;ngig von Gesamtsuchtiefe) und eine &uuml;bersicht &uuml;ber anzunehmende 'Begriffsl&uuml;cken' bzw. 'Linkl&uuml;cken'.
			
			3.) Ich will das Ranking meiner Website verbessern und neue Keywords erschlie&szlig;en?
			- Siehe 1.) und 2.). Bei entsprechender Suchtiefe und Feinjustage kann der Destiller hier interessante Vorschl&auml;ge hinsichtlich der Erschlie&szlig;ung neuer Content-Schwerpunkte nebst zu verwendendem Vokabular machen.
			
			4.) Ich will meine Website einfach Mal checken?
			- You're welcome!
			
			5.) Ich muss Texte schreiben und/oder f&uuml;r Verlinkung sorgen?
			- Siehe 1.) und 2.) und 3.) ;-)
			</div><div>
			<h2>Disclaimer</h2>
			<p>No warranties.... Auch wenn der Destiller versucht, intern Methoden der Suchmaschinen nach zu bilden, so besteht keine Gew&auml;hr hinsichtlich der tats&auml;chlichen Richtigkeit der Ergebnisse als auch daraus gezogener R&uuml;ckschl&uuml;sse oder gar getroffener Massnahmen. Der Dienst speichert keine fremden oder Copyright-gesch&uuml;tzen Inhalte ab, sondern lediglich eine Fraktalisierung der gefundenen Elemente. Der &ouml;ffentliche Dienst 'Text & Link Destiller' incl. Highscoreliste wird 'as is' , d.h. ohne jegliche Gew&auml;hrleistungen angeboten. Eine Nutzung der &ouml;ffentlich zug&auml;nglichen Ergebnisse zu kommerziellen Zwecken ist nicht zul&auml;ssig, eben so wenig die Nutzung als Zugangsvermittler (Proxy). Zeitpunkt, IP-Adresse und abgfragte URLs werden automatisch gespeichert. Die kontinuierlich ver&ouml;ffentlichten Links (letzte 25, Highscores) werden t&auml;glich gepr&uuml;ft und ggfls. manuell gel&ouml;scht. F&uuml;r die Inhalte der (tempor&auml;r) verlinkten Seiten sind ausschlie&szlig;lich deren Betreiber verantwortlich. Spamseiten, Linkfarmen und XXX-Seite werden ohne Vorank&uuml;ndigung, Benachrichtigung oder h&auml;here Begr&uuml;ndung gel&ouml;scht, ggf. aber auch exemplarisch im System belassen. Falls Sie unseri&ouml;se oder sonst wie anst&ouml;&szlig;ige Seiten hinter den Links in der Toplist finden, so benachrichtigen Sie uns bitte formlos <a href='modules.php?name=Feedback'>hier &uuml;ber das Kontaktformular</a> und lassen Sie uns 24 Stunden Zeit. Eine wie auch immer geartete Verpflichtung zur Speicherung der Ergebnisse oder Ver&ouml;ffentlichung eines URLs besteht nicht. Sollten Sie ihren URL hier gel&ouml;scht haben wollen, so benutzen Sie bitte ebenfalls das <a href='modules.php?name=Feedback'>Kontaktformular</a>. Mit den Ergebnissen ist keine wie auch immer geartete Bewertung der Inhalte, Aussagen, technischen Konstruktion oder sonstiger Ausstattung der untersuchten Websites verbunden.
			</p>
			</div><div>
			<h2>Complex View</h2>
			<img src='/images/xi-all-box.png' align='right' width='100'><p>Der Text & Link Destiller ist Komponente des Technik-Paketes unter dem (working title) Xi, repr&auml;sentiert durch &Xi;. Xi &Xi; ist eine Toolsuite zur dezentralisierten Bildung von Suchnetzwerken und wird derzeit als 'aside' Plugin unter PHP f&uuml;r g&auml;ngige CMS entwickelt. Xi &Xi; beinhaltet sowohl ein Objektmodell zur dezentralen Abstraktion und Fraktalisierung von Webcontent als auch ein HTTP/XML-RPC geeignetes Protokoll (bgHTTP) zur Bildung semantisch orientierter P2P-Netzwerke. Auch wenn die vollst&auml;ndigen Quellcodes der Zeit nicht offen liegen, so ist das Projekt Xi &Xi; langfristig in der OpenSource angesiedelt.
			</p>
			</div></div>");?>
      </div>
    </div>
    <div id="col2">
      <div id="col2_content" class="clearfix">
        
      </div>
    </div>
    <div id="col3">
      <div id="col3_content" class="clearfix">
        
      </div>
      <!-- IE Column Clearing -->
      <div id="ie_clearing"> &#160; </div>
    </div>
  </div>
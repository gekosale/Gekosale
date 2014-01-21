{% extends "layout.tpl" %}
{% block content %}
<h2>{% trans %}TXT_SHIPMENT_PACKAGING{% endtrans %}</h2>
<div class="block">
<div class="container">
                	

    <h2 class="static_page_head page_head_prices">
        Dopuszczalne wymiary i waga przesyłek:<br>
    </h2> 

    <div class="packing">
        <div class="fl">
            <h3>Przesyłki o wadze do 31,5 kg</h3>
            <div>
                <ol style="margin-bottom: 21px;">
                    <li>waga przesyłki (z opakowaniem): max. <b>31,5 kg</b>;</li>
                    <li>suma długości i obwodu przesyłki: max. <b>300 cm</b>;</li>
                    <li>najdłuższy z wymiarów nie może przekroczyć: max. <b>200 cm</b>;</li>
                    <li>maksymalna objętość paczki: 0,35 m3.</li>
                </ol> 
            </div>
        </div>
        <div class="fl">
            <h3>Przesyłki o wadze powyżej 31,5 kg</h3>
            <div>
                <ol>
                    <li>podstawa max. 80 cm x120 cm (paleta z towarem);</li>
                    <li>wysokość max. 180 cm (paleta z towarem);</li>
                    <li>waga przesyłki (z opakowaniem): max. 500 kg;</li>
                    <li>w przypadku gdy waga gabarytowa* przekracza wagę rzeczywistą, cena ustalana jest za wagę gabarytową (dotyczy paczek o objętości powyżej 0,25 m3/250 000 cm3).</li>
                    <li>Jeżeli waga nie przekracza 41,6 kg na liście wpisywana jest waga rzeczywista paczki.</li>                    
                </ol>
                
                <span>
                    * waga gabarytowa = [długość (cm) x szerokość (cm) x wysokość (cm) / 6000]
                </span>
            </div>
        </div> 
        <div class="clear"></div>
    </div>
    
    <h2 class="static_page_head">Zasady pakowania przesyłek</h2>
    <div class="packing" style="border:0;margin-bottom: 0;">
        <div class="fl">
            <h3>Zasady pakowania przesyłek o wadze do 31,5 kg</h3>
            <div>
                <ol>
                    <li>Wytrzymałość i twardość opakowania zewnętrznego powinna być dostosowana do wagi i charakteru przewożonego towaru.</li>
                    <li>Z opakowania zewnętrznego należy usunąć wszystkie stare oznaczenia, napisy, naklejki, a w szczególności stare listy przewozowe.</li>
                    <li>Do zaklejania kartonów powinno się taśmy firmowe</li>
                    <li>Na opakowaniu zewnętrznym należy umieścić dane adresowe nadawcy i odbiorcy przesyłki</li>
                    <li>Wielkość opakowania musi być dostosowana do zawartości przesyłki lub powinno zastosować się dodatkowe wypełniacze, tak aby zminimalizować puste przestrzenie wewnątrz opakowania.</li>
                    <li>Towar w paczce powinien być poprzegradzany tekturowymi przegródkami lub zapakowany w mniejsze opakowania jednostkowe tak, aby przewożony w liczbie kilku sztuk nie stykał się bezpośrednio ze sobą.</li>
                    <li>Dno i górna część opakowania powinny być dodatkowo usztywnione i wzmocnione.</li>
                    <li>Jeżeli wymaga tego specyfika transportowanego towaru oraz zalecenia producenta na kartonie powinny znaleźć się oznakowania ostrzegawcze: “nie rzucać”, “góra/dół” itp..</li>
                    <li>W przypadku towarów, których specyfika może przyczynić się do oddzielenia listu przewozowego od paczki, należy dodatkowo zastosować wzmocnienie jako podkład pod foliową kieszeń na list przewozowy (np. folia stretch lub zaklejenie taśmą).</li>
                    <li>Przesyłki składające się z kilku sklejonych ze sobą paczek powinny być połączone w sposób uniemożliwiający rozdzielenie się ich podczas transportu. Zabrania się połączenia kilku paczek szarą taśmą. Do połączenia należy stosować taśmę firmową nadawcy lub folię z indywidualnymi cechami Klienta. Każda z takich paczek winna posiadać informację pozwalającą na ustalenie nr przesyłki. Na liście przewozowym w uwagach należy wpisać ilość połączonych ze sobą paczek.</li>
                </ol>
            </div>
        </div>
        <div class="fl">
            <h3>Zasady pakowania przesyłek o wadze powyżej 31,5 kg</h3>
            <div>
                <ol>
                    <li>Przesyłki o wadze powyżej 31,5 kg powinny być ułożone na palecie. Maksymalny wymiar palety nie powinien przekraczać wymiarów palety EUR.</li>
                    <li>Wymiary palety to: podstawa 80x120 cm, wysokość 180 cm (paleta z towarem), maksymalna waga to 500 kg.</li>
                    <li>Towar powinien być zapakowany w mniejsze opakowania jednostkowe tak, aby przewożony w liczbie kilku sztuk nie stykał się bezpośrednio ze sobą. Lżejsze elementy powinny być ustawione na górze palety.</li>
                    <li>Elementy składowe palety powinny być ułożone równomiernie, w sposób uniemożliwiający przechylanie się całości towaru na boki podczas transportu.</li>
                    <li>W przypadku przesyłania większych elementów, które są szczególnie narażone na uszkodzenia (np. szyby samochodowe), paleta powinna posiadać konstrukcję chroniącą towar przed uszkodzeniami.</li>
                    <li>Rogi oraz górna część palety powinny być usztywnione.</li>
                    <li>Transportowany towar powinien być przytwierdzony do palety taśmami, pasami lub folią stretch w sposób uniemożliwiający przesuwanie i przemieszczanie się towaru podczas transportu.</li>
                    <li>Poszczególne warstwy transportowanego asortymentu powinny być oddzielone od siebie tekturowymi przekładkami lub tekturą falistą itp.</li>
                    <li>Z opakowania zewnętrznego należy usunąć wszystkie stare oznaczenia, napisy, naklejki, a w szczególności stare listy przewozowe. Na opakowaniu zewnętrznym należy umieścić dane adresowe nadawcy oraz odbiorcy przesyłki .</li>
                    <li>Opakowanie zewnętrzne powinno być zabezpieczone taśmami plombującymi oraz oklejone naklejkami.</li>
                    <li>Elementy przesyłki nie powinny wystawać poza obrys palety</li>
                </ol>
            </div>
        </div> 
        <div class="clear"></div>
    </div>    

    <div class="packing" style="border-bottom: 1px dotted #DDDDDD">
        <div>
            <h3>Poradniki pakowania DPD (pdf)</h3>
            <div>
                <ul class="pdf_download">
                    <li><a href="http://superpaczka.pl/file/packing/dpd/1.pdf" title="Sposób pakowania - przesyłki o wadze do 31kg">Sposób pakowania - przesyłki o wadze do 31kg</a> [ 348,7 kB ]</li>
                    <li><a href="http://superpaczka.pl/file/packing/dpd/2.pdf" title="Sposób pakowania - przesyłki o wadze powyżej 31kg">Sposób pakowania - przesyłki o wadze powyżej 31kg</a> [ 579,1 kB ]</li>
                    <li><a href="http://superpaczka.pl/file/packing/dpd/3.pdf" title="Sposób pakowania - art. motoryzacyjne">Sposób pakowania - art. motoryzacyjne</a>  [316,3 kB ]</li>
                    <li><a href="http://superpaczka.pl/file/packing/dpd/4.pdf" title="Sposób pakowania - art. szklane">Sposób pakowania - art. szklane</a> [ 833,8 kB ]</li>
                    <li><a href="http://superpaczka.pl/file/packing/dpd/5.pdf" title="Sposób pakowania - odzież">Sposób pakowania - odzież</a> [ 245,8 kB ]</li>
                    <li><a href="http://superpaczka.pl/file/packing/dpd/6.pdf" title="Sposób pakowania - RTV/AGD">Sposób pakowania - RTV/AGD</a> [ 484,8 kB ]</li>
                    <li><a href="http://superpaczka.pl/file/packing/dpd/7.pdf" title="Sposób pakowania - urządzenia sanitarne">Sposób pakowania - urządzenia sanitarne</a> [ 170,1 kB ]</li>
                </ul>
            </div>
        </div>
    </div>    
    
    
                </div>
</div>

{% endblock %}


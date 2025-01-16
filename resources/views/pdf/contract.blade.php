<!DOCTYPE html>
<html lang="sw">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mkataba wa Kupangisha</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 1px;
            font-size: 17px;
            color: black;
            line-height: 1.5;
            text-align: justify;
        }

        .header {
            text-align: center;
            margin-bottom: 1px;
        }

        .contract-terms,
        .signature {
            margin: 1px 0;
        }

        .signature p {
            margin: 1px 0;
        }

        .line {
            border-top: 1px solid black;
            width: 30%;
            margin: 0;
        }

        .footer {
            margin-top: 1px;
            text-align: center;
            color: gray;
            font-size: 0.8em;
        }

        .main-content {
            min-height: calc(90vh - 60px);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .page-break {
            page-break-before: always;
        }


        .main-content {
            margin-bottom: 10px;
            font-family: Arial, sans-serif;
        }

        .info-box {
            border: 1px solid #ccc;
            padding: 13px;
            border-radius: 10px;
            background-color: #f4f4f4;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .info-box p {
            font-size: 16px;
            line-height: 1.4;
            margin: 0;
            color: #333;
        }

        .info-box strong {
            color: #000;
            font-weight: bold;
        }

        .signature-line {
            color: #555;
            font-style: italic;
            margin-right: 10px;
        }

        @media screen and (max-width: 768px) {
            .info-box {
                padding: 10px;
            }

            .info-box p {
                font-size: 14px;
            }
        }
    </style>
</head>

<body>

    <div class="header">
        <h2>Mkataba wa Upangishaji wa Chumba cha Biashara</h2>
    </div>

    <div class="contract-terms">

        <p>Mkataba huu unahusisha pande kuu mbili kama ifuatavyo:</p>

        <p>Ndugu, <strong>{{ $house_owner }}</strong> mwenye namba ya simu <strong>{{ $phone_number }}</strong> akiwa kama mmiliki/msimamizi halali wa nyumba iliyopo eneo
            la <strong>{{ $house_location }}</strong> katika mtaa wa <strong>{{ $street_name }}</strong>.
            Pamoja na Ndugu <strong>{{ $tenant_name }}</strong> mwenye namba za simu <strong>{{ $tenant_phone }}</strong> na jina la biashara <strong>{{ $business_name }}</strong> akiwa kama mpangaji wa chumba kwa
            shughuli za kibiashara katika nyumba tajwa hapo juu.</p>

        <p>Kwa kuwa pande zote mbili, yaani mmiliki/msimamizi ameonesha nia ya kumpangishia mpangaji chumba namba <strong>{{ $room_name }}</strong>
            na wakati huo huo mpangaji pia ameonesha nia ya kupangishiwa chumba hicho kutoka kwa mmiliki/msimamizi, basi Ndugu <strong>{{ $tenant_name }}</strong> atatambulika kama mpangaji
            katika chumba namba <strong>{{ $room_name }}</strong> kuanzia tarehe <strong>{{ $start_date }}</strong> ambapo mkataba wake ndipo umeanza rasmi hadi tarehe <strong>{{ $end_date }}</strong> ambapo
            muda wa mkataba wake utafikia kikomo.</p>

        <p><strong>Mpangaji</strong> anawajibika kulipa kodi ya <strong>Tsh {{ number_format($rent_per_month) }}</strong> kwa kila mwezi,
            ambayo ni sawa na <strong>Tsh {{ number_format($total) }}</strong> kwa kipindi chote cha miezi <strong>{{ $contract_interval }}</strong>
            ambayo inatambulika katika mkataba huu.</p>

        <p>Kwa sasa mkataba unapoendelea, mpangaji Ndugu <strong>{{ $tenant_name }}</strong>
            @if ($amount_remaining == 0)
            amekamilisha malipo ya kodi yake kiasi cha <strong>Tsh {{ number_format($amount_paid) }}</strong>.
            @else
            ameshalipa kiasi cha <strong>Tsh {{ number_format($amount_paid) }}</strong> na kubakiza deni la kiasi cha
            <strong>Tsh {{ number_format($amount_remaining) }}</strong> ambalo anatakiwa kulipa ndani ya siku 30 za mwanzo wa mkataba wake,
            yaani kabla ya tarehe <strong>{{ \Carbon\Carbon::parse($start_date)->addDays(30)->format('d-m-Y') }}</strong>.
            @endif
        </p>

        <p><strong>Mmiliki/Msimamizi</strong> anawajibika kuhakikisha kuwa chumba kinakuwa katika hali ya usalama na utayari kwa matumizi.</p>

        <p><strong>Mpangaji</strong> anawajibika kulipa kiasi chote cha kodi kama ambavyo imeainishwa katika vipengele vya juu katika mkataba huu.</p>

        <p><strong>Mpangaji</strong> pia anawajibika kuhakikisha malipo yanafanywa kwa wakati. Iwapo mpangaji atashindwa kulipa kodi ndani ya muda
            ulioainishwa katika mkataba huu, mmiliki/msimamizi anayo haki ya kuchukua hatua za kisheria ili kudai deni hilo. Katika hali kama hii mwenye
            nyumba anaweza kumwondoa katika chumba hicho bila taarifa ya ziada au amri ya mahakama au mamlaka nyingine yeyote.</p>

        <p><strong>Mpangaji</strong> anawajibika kulipia gharama zote za maji safi, ulinzi, umeme, taka na huduma zingine za kijamii kama ambavyo
            zitakuwa zikiwasilishwa kwake na mmiliki/msimamizi wa nyumba au mamlako inayohusika.</p>

        <p><strong>Mpangaji</strong> hana mamlaka ya kupangisha au kutoa chumba hiki namba {{ $room_name }} kwa mtu mwingine yeyote bila ya kuwa na idhini ya
            maandishi kutoka kwa mmiliki/msimamizi wa nyumba hii. Kufanya hivyo mpangaji atakuwa amekiuka makubaliano ya mkataba huu na atapaswa kurudisha chumba
            kwa mmiliki/msimamizi na pasipo kurudishiwa kodi yeyote.</p>

        <p><strong>Mpangaji</strong> anawajibika kutumia chumba hiku kwa ajili ya matumizi ya kibishara halali tu na inayotambulika na sheria za nchi ya Tanzania. </p>

        <p><strong>Mpangaji</strong> hatofanya shughuli au mambo ambayo yatakuwa kero kwa majirani na pia hatouza madawa ya kulevya au biashara yeyote haramu katika chumba alichopanga.</p>

        <p><strong>Mpangaji</strong> hataweka vitu vinavyoweza kulipuka au vitu vinavyoweza kuhatarisha maisha/afya ya watu pamoja na vitu ambavyo vinahitaji kibali kisheria ndani ya chumba kabla ya kupata kibali halali cha kufanya hivyo.</p>

        <p><strong>Mpangaji</strong> anawajibika kutii sheria na taratibu zote zilizowekwa na Halmashauri ya Jiji la Arusha juu ya matumizi ya chumba hiki na eneo la nje ya chumba alichopanga.</p>

        <p><strong>Mpangaji</strong> anawajibika kutunza chumba hicho na kuhakikisha hafanyi uharibifu wa aina yoyote ile.</p>

        <p><strong>Mpangaji</strong> anawajibika kumfidia mmiliki/msimamizi wa nyumba gharama zote za uharibifu atakaousababisha kwenye chumba kutokana na uzembe wake au uzembe wa wafanyakazi wake.</p>

        <p><strong>Mpangaji</strong> hatofanya matengenezo au ukarabati wa aina yoyote ile kwenye chumba pasipo idhini ya maandishi kutoka kwa msimamizi/mmiliki kwa nyumba.</p>

        <p><strong>Mpangaji</strong> analazimika kutoa taarifa ya kusitisha ama kuendelea na mkataba mwezi mmoja kabla ya tarehe ya mwisho ya mkataba wa sasa,
            yaani mwezi mmoja kabla ya tarehe <strong>{{ $end_date }}</strong>.</p>

        <p><strong>Mmiliki/Msimamizi</strong> wa nyumba naye anawajibika kutoa taarifa ya kusitisha mkataba mwezi mmoja kabla
            pindi atakapotaka kuvunja mkataba huu kwa sababu yoyote ile. ikiwa ni mwenye nyumba ndiye ameamua kuvunja mkataba huu, basi atawajibika kumrudishia mpangaji sehemu ya kodi aliyopokea kwa mwezi/miezi ya mbele.</p>

        <p>Ikiwa ni mpangaji mwenyewe ameamua kusitisha mkataba wake kabla ya tarehe ya mwisho basi hatakuwa na haki ya kumdai mwenye nyumba ili aweze kumfidia kodi yake kwa kipindi ambacho atakuwa amekwisha kukilipia na bado hakijaanza.</p>

        <p><strong>Mpangaji</strong> anawajibika kuzingatia kuwa chumba kinakuwa katika hali safi na salama kwa siku zote mpaka pale ambapo utafika ukomo wa mkataba wake.</p>

        <<p><strong>Mpangaji</strong> ananawajibika kuweka mbinu za ulinzi kwenye chumba na mali zake kwa gharama zake mwenyewe.</p>

            <p><strong>Mpangaji</strong> atawajibika kuchangia na kujitoa kwa maendeleo ya mtaa kama vile vikao, mikutano na fedha pale ambapo atahitajika katika kipindi cha upangaji wake.</p>

            <p><strong>Mmiliki/Msimamizi</strong> wa nyumba atakuwa na nafasi ya kukagua na kuangalia hali ya chumba baada ya kutoa taarifa ya mdomo au simu kwa mpangaji ndani ya siku
                tatu kabla ya siku ya ukaguzi.</p>

            <p>Mimi <strong>{{ $house_owner}}</strong> kama mmiliki/msimamizi wa nyumba tajwa hapo juu, nimempa Ndugu <strong>{{ $supervisor_name }}</strong> mwenye namba ya simu <strong>{{ $supervisor_phone_number }}</strong> idhini ya kuwa mkusanyaji wa kodi na mpokeaji wa taarifa au kero zote zinazohusu wapangaji.</p>

            <p>Tukiwa katika hali ya uelewa na utimamu wa akili, tumeyasoma, kuyaelewa na kuyakubali makubaliano yaliyopo katika mkataba huu kwa hiari na pasipo kushurutishwa au kulazimishwa
                na mtu yeyote yule mbele ya mashahidi wafuatao leo siku ya tarehe ___________________. </p>
    </div>

    <div class="main-content">
        <div class="info-box">
            <p>
                <strong>Jina la mpangaji:</strong> {{ $tenant_name }}<br>
                <span class="signature-line">Saini:</span> ____________________________
            </p>
        </div>
    </div>

    <div class="main-content">
        <div class="info-box">
            <p>
                <strong>Shahidi wa mpangaji:</strong> ____________________________________________________________________<br>
                <span class="signature-line">Saini:</span> ____________________________
            </p>
        </div>
    </div>

    <div class="main-content">
        <div class="info-box">
            <p>
                <strong>Wakili wa mpangaji:</strong> ____________________________________________________________________<br>
                <span class="signature-line">Saini:</span> ____________________________
            </p>
        </div>
    </div>

    <div class="main-content">
        <div class="info-box">
            <p>
                <strong>Jina la mmiliki/msimamizi:</strong> {{ $house_owner }}<br>
                <span class="signature-line">Saini:</span> ____________________________
            </p>
        </div>
    </div>

    <div class="main-content">
        <div class="info-box">
            <p>
                <strong>Jina la shahidi wa mmiliki/msimamizi:</strong> ___________________________________________<br>
                <span class="signature-line">Saini:</span> ____________________________
            </p>
        </div>
    </div>

    <div class="main-content">
        <div class="info-box">
            <p>
                <strong>Jina la wakili wa mmiliki/msimamizi:</strong> ___________________________________________<br>
                <span class="signature-line">Saini:</span> ____________________________
            </p>
        </div>
    </div>

    <div class="footer">
        <p>Imetayarishwa na BOBTechWaves na kutolewa siku ya tarehe: {{ \Carbon\Carbon::now()->format('d-m-Y') }}</p>
    </div>

</html>
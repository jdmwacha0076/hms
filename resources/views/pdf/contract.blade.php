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
            font-size: 15px;
            color: black;
            line-height: 1.4;
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
            margin-top: 2px;
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
    </style>
</head>

<body>

    <div class="header">
        <h2>Mkataba wa Upangishaji wa Nyumba</h2>
    </div>

    <div class="contract-terms">

        <p>Mkataba huu unaambatana na barua ya utambulisho itakayotambuliwa na uongozi wa serikali ya mtaa wa <strong>{{ $street_name }}</strong>. Mkataba huu unahusisha pande kuu mbili kama ifuatavyo:</p>

        <p>Ndugu, <strong>{{ $house_owner }}</strong> mwenye namba ya simu <strong>{{ $phone_number }}</strong> akiwa kama mmiliki/msimamizi halali wa nyumba iliyopo eneo la <strong>{{ $house_location }}</strong> katika mtaa wa <strong>{{ $street_name }}</strong> kiwanja namba <strong>{{ $plot_number }}</strong>. Pamoja na Ndugu <strong>{{ $tenant_name }}</strong> mwenye namba za simu <strong>{{ $tenant_phone }}</strong> akiwa kama mpangaji katika nyumba tajwa hapo juu.</p>

        <p>Ndugu <strong>{{ $tenant_name }}</strong> atatambulika kama mpangaji kwenye nyumba/chumba hii/hiki kuanzia tarehe <strong>{{ $start_date }}</strong> ambapo mkataba wake umeanza rasmi hadi tarehe <strong>{{ $end_date }}</strong> ambapo muda wa mkataba wake utafikia kikomo. Mpangaji atawajibika kulipa kodi ya <strong>Tsh {{ number_format($rent_per_month) }}</strong> kwa kila mwezi, ambayo ni sawa na <strong>Tsh {{ number_format($total) }}</strong> kwa kipindi chote cha miezi <strong>{{ $contract_interval }}</strong>.</p>

        <p>Kwa sasa mkataba unapoendelea, mpangaji Ndugu <strong>{{ $tenant_name }}</strong>
            @if ($amount_remaining == 0)
            amekamilisha malipo ya kodi yake kiasi cha <strong>Tsh {{ number_format($amount_paid) }}</strong>.
            @else
            ameshalipa kiasi cha <strong>Tsh {{ number_format($amount_paid) }}</strong> na kubakiza deni la kiasi cha <strong>Tsh {{ number_format($amount_remaining) }}</strong> ambalo anatakiwa kulipa ndani ya siku 30 za mwanzo wa mkataba wake, yaani kabla ya tarehe <strong>{{ \Carbon\Carbon::parse($start_date)->addDays(30)->format('d-m-Y') }}</strong>.
            @endif
        </p>


        <p>Mmiliki wa nyumba anawajibika kuhakikisha kuwa nyumba iko katika hali nzuri na inafaa kwa matumizi.</p>

        <p><strong>Mpangaji</strong> anawajibika kutunza nyumba/chumba hiyo/hicho na kuhakikisha hafanyi uharibifu wa aina yoyote. Mpangaji pia anawajibika kulipa gharama zote za huduma za kijamii zikiwemo uondoshwaji wa taka ngumu, ulinzi pamoja na usafi wa mazingira ya eneo husika.</p>

        <p><strong>Mpangaji</strong> pia anawajibika kuhakikisha malipo yanafanywa kwa wakati. Iwapo mpangaji atashindwa kulipa kodi ndani ya muda wa mkataba, mmiliki ana haki ya kuchukua hatua za kisheria ili kudai deni hilo. Ikiwa mpangaji atasababisha uharibifu wa aina yoyote ile, mmiliki anayo haki ya kuweza kudai gharama za marekebisho yatakayokuwa yamesababishwa na uharibifu huo.</p>

        <p><strong>Mpangaji</strong> atawajibika kuiacha nyumba/chumba hiyo/hicho safi na salama pale ambapo utafika ukomo wa mkataba wake. Pia atahakikisha kuwa anatumia nyumba/chumba hiyo/hicho kwa matumizi halali na pasipo kukiuka sheria za nchi.</p>

        <p><strong>Mpangaji</strong> analazimika kutoa taarifa ya kusitisha ama kuendelea na mkataba mwezi mmoja kabla. Mwenye nyumba anawajibika kutoa taarifa ya kusitisha mkataba mwezi mmoja kabla, pindi atakapotaka kuvunja mkataba huu kwa sababu yoyote.</p>

        <p>Mimi <strong>{{ $house_owner}}</strong> kama mmiliki/msimamizi wa nyumba tajwa hapo juu, nimempa Ndugu <strong>{{ $supervisor_name }}</strong> mwenye namba ya simu <strong>{{ $supervisor_phone_number }}</strong> idhini kuwa mkusanyaji wa kodi na mpokeaji wa taarifa au kero zote zinazohusu wapangaji.</p>

    </div>

    <div class="main-content">
        <div class="info-box">
            <p><strong>Saini ya mpangaji:</strong> ____________________________
                <strong>Jina la mpangaji:</strong> {{ $tenant_name }}
            </p>
        </div>
    </div>

    <div class="main-content">
        <div class="info-box">
            <p><strong>Saini ya mmiliki/msimamizi:</strong> _________________
                <strong>Jina la mmiliki/msimamizi:</strong> {{ $house_owner }}
            </p>
        </div>
    </div>

    <div class="footer">
        <p>Imetayarishwa na BOBTechWaves na kutolewa siku ya tarehe: {{ \Carbon\Carbon::now()->format('d-m-Y') }}</p>
    </div>

    <br><br>

    <div class="header">
        <h2>Barua ya Utambulisho wa Mpangaji</h2>
    </div>

    <div class="addresses">
        <div class="sender-address" style="text-align: right;">
            <p>{{ $house_owner }}<br>{{ $phone_number }}<br>{{ \Carbon\Carbon::now()->format('d-m-Y') }}</p>
        </div>
        <div class="receiver-address">
            <p>Ofisi ya Afisa Mtendaji<br>Mtaa wa {{ $street_name }}<br>S.L.P <strong>__________</strong><br></p>
        </div>
    </div>

    Ndugu,<br><br>

    <div style="text-align: center;">
        <u><strong>YAH: BARUA YA UTAMBUZI WA MPANGAJI</strong></u>
    </div>


    <div class="contract-terms">
        <p>Ninakuandikia kukutambulisha ya kuwa Ndugu, <strong>{{ $tenant_name }}</strong> mwenye kitambulisho cha <strong>{{ $id_type }}</strong> chenye namba <strong>{{ $id_number }}</strong>, kuwa ni mpangaji katika nyumba yangu iliyopo eneo la <strong>{{ $house_location }}</strong>, mtaa wa <strong>{{ $street_name }}</strong>, kiwanja namba <strong>{{ $plot_number }}</strong>. Mkataba wake umeanza rasmi tarehe <strong>{{ $start_date }}</strong> na unatarajiwa kumalizika tarehe <strong>{{ $end_date }}</strong>.</p>

        <p>Kwa taratibu na sheria za usimamizi wa wakazi wa eneo hili, naomba ofisi yako impe nafasi mpangaji huyu kujitambulisha rasmi kwa uongozi wa serikali ya mtaa wa {{ $street_name }}.</p>

        <p>Kutokana na sheria na taratibu za nchi, kwa mamlaka ya ofisi yako unayo haki na wajibu wa kumhoji Ndugu, <strong>{{ $tenant_name}}</strong> ili kuthibitisha taarifa zake na kuhakikisha kuwa anakidhi na kufuata taratibu zote za kiusalama wa nchi.</p>

        <p>Aidha, naomba mnifahamishe endapo kutakuwa na masuala yoyote ya kutiliwa shaka, kulingana na taarifa au mwenendo wake kwa hatua zaidi.</p>

        <p>Naamini ushirikiano huu utasaidia kudumisha amani na utulivu katika eneo letu. Tafadhali wasiliana nami kwa namba ya simu <strong>{{ $phone_number }}</strong> iwapo kuna maelezo ya ziada yanayohitajika.</p>

        <p>Nakushukuru kwa msaada wako na uongozi bora.</p>

        <p>Wako katika utumishi,</p>

        <p><strong>{{ $house_owner }}</strong><br>Mmiliki/Msimamizi.</p>
    </div>

    <div class="signature-section">
        <div class="signature-box">
            <p><strong>Saini ya mpangaji:</strong> ___________________________
                <strong>Jina la mpangaji:</strong> {{ $tenant_name }}
            </p>
        </div>

        <div class="signature-box">
            <p><strong>Ofisi ya Afisa Mtendaji:</strong> ___________________________________
                <strong>Tarehe:</strong> ____________________
            </p>
        </div>
    </div>

    <div class="footer">
        <p>Imetayarishwa na BOBTechWaves na kutolewa siku ya tarehe: {{ \Carbon\Carbon::now()->format('d-m-Y') }}</p>
    </div>

</html>
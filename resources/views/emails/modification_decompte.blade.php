<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Notification </title>
</head>
<body>
    <h5>{{$detail['title']}}</h5>
    <p>{{$detail['type']}}</p>
    <ul>
        <li><u>Numéro du décompte </u> : {{$detail['decompte']}}</li>
        <li><u>Date de démarrage des travaux </u> : {{$detail['demarage']}}</li>
        <li><u>Montant du décompte </u> : {{$detail['montant']}} FCFA</li>
        <li><u>Décompte modifié par </u> : {{$detail['modifiePar']}}</li>
        <li><u>Observation </u> : {{$detail['observation']}}</li>
        <li><u>Lien </u> : <a href="{{$detail['lienDecompte']}}">Access au decompte</a></li>
        <li><u>{{$detail['titreDate']}} </u> : {{$detail['dateCreation']}}</li>
    </ul>
    <p>Merci</p>
</body>
</html>

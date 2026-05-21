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
    <li><u>Numéro du décompte</u> :  {{$detail['decompte']}}</li>
    <li><u>Statut du décompte</u> : {{$detail['status']}}</li>
    <li><u>Date de rejet</u> : {{$detail['validation']}}</li>
    <li><u>Décompte rejeté par</u> : {{$detail['validePar']}}</li>
    <li><u>Lien</u> :  <a href="{{$detail['lienDecompte']}}">Access au decompte</a></li>
</ul>
<p>Merci</p>
</body>
</html>
||
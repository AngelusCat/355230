<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Покупка билетов</title>
</head>
<body>
    <p>Название события: {{ $event->getName() }}</p>
    <p>Описание: {{ $event->getDescription() }}</p>
    <p>Начало: {{ $event->getStart() }}</p>
    <p>Конец: {{ $event->getEnd() }}</p>
    <p>Купить билеты на события (выберите ползунком количество билетов каждого типа, которые Вы хотите купить)</p>
    <form action="/{{ $event->getId() }}/buyTickets" method="post">
        @csrf
        @foreach($tickets as $ticketType => $number)
            <p>
                {{ $ticketType . ": " }}<input type="range" max="{{ $number }}" name="{{ $ticketType }}">(всего доступно {{ $number }})
            </p>
        @endforeach
        <button type="submit">Купить</button>
    </form>
</body>
</html>

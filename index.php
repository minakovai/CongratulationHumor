<?php

declare(strict_types=1);

mb_internal_encoding('UTF-8');
date_default_timezone_set('Europe/Moscow');

function normalizeName(string $name): string
{
    $name = trim($name);
    $name = preg_replace('/\s+/u', ' ', $name ?? '');

    if ($name === '' || $name === null) {
        return '';
    }

    $first = mb_strtoupper(mb_substr($name, 0, 1));
    $rest = mb_strtolower(mb_substr($name, 1));

    return $first . $rest;
}

function nthWeekdayOfMonth(int $year, int $month, int $weekdayIso, int $nth): DateTimeImmutable
{
    $date = new DateTimeImmutable(sprintf('%04d-%02d-01', $year, $month));
    while ((int) $date->format('N') !== $weekdayIso) {
        $date = $date->modify('+1 day');
    }

    return $date->modify('+' . ($nth - 1) . ' weeks');
}

function getTodayHoliday(DateTimeImmutable $today): string
{
    $fixedHolidays = [
        '01-01' => 'Новый год',
        '01-07' => 'Рождество Христово',
        '02-14' => 'День всех влюблённых',
        '02-23' => 'День защитника Отечества',
        '03-08' => 'Международный женский день',
        '04-12' => 'День космонавтики',
        '05-01' => 'Праздник Весны и Труда',
        '05-09' => 'День Победы',
        '06-12' => 'День России',
        '07-08' => 'День семьи, любви и верности',
        '09-01' => 'День знаний',
        '10-05' => 'День учителя',
        '11-04' => 'День народного единства',
        '12-31' => 'Канун Нового года',
    ];

    $key = $today->format('m-d');
    if (isset($fixedHolidays[$key])) {
        return $fixedHolidays[$key];
    }

    $year = (int) $today->format('Y');

    $dynamicHolidays = [
        nthWeekdayOfMonth($year, 8, 7, 2)->format('Y-m-d') => 'День строителя', // 2-е воскресенье августа
        nthWeekdayOfMonth($year, 10, 7, 1)->format('Y-m-d') => 'День учителя (праздничное воскресенье)',
        nthWeekdayOfMonth($year, 11, 7, 4)->format('Y-m-d') => 'День матери',
    ];

    $fullDate = $today->format('Y-m-d');
    if (isset($dynamicHolidays[$fullDate])) {
        return $dynamicHolidays[$fullDate];
    }

    return 'День тёплых слов';
}

function generateGreeting(string $name, string $holiday): string
{
    $openings = [
        "%s, с праздником тебя сердечно поздравляю!",
        "%s, сегодня %s — от души тебя обнимаю!",
        "%s, в этот %s тебе я радость посылаю!",
    ];

    $wishes = [
        "Пусть всё, за что берёшься, получается легко,\nа рядом будут близкие, надёжно и тепло.",
        "Пусть вдохновение не тает, как звезда на высоте,\nи каждый новый день сияет удачей в доброте.",
        "Пусть дом наполнит смех и свет, в делах всегда успех,\nа сердце бережёт мечту и самый звонкий смех.",
    ];

    $rhymes = [
        "Пусть будет жизнь, как яркий май:\nмечтай, твори и побеждай!",
        "Пускай ведёт тебя звезда,\nи будет радость навсегда!",
        "Пусть счастье льётся через край,\nживи красиво, расцветай!",
    ];

    $opening = sprintf(
        $openings[array_rand($openings)],
        $name,
        mb_strtolower($holiday)
    );

    $wish = $wishes[array_rand($wishes)];
    $rhyme = $rhymes[array_rand($rhymes)];

    return $opening . "\n" . $wish . "\n" . $rhyme;
}

$today = new DateTimeImmutable('now');
$holiday = getTodayHoliday($today);
$inputNames = '';
$greetings = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputNames = (string) ($_POST['names'] ?? '');
    $parts = preg_split('/[\n,;]+/u', $inputNames) ?: [];

    foreach ($parts as $rawName) {
        $name = normalizeName($rawName);
        if ($name === '') {
            continue;
        }

        $greetings[$name] = generateGreeting($name, $holiday);
    }
}
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Генератор поздравлений</title>
    <style>
        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, sans-serif;
            background: linear-gradient(135deg, #fee2e2, #dbeafe 60%, #dcfce7);
            min-height: 100vh;
            color: #1f2937;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 36px 20px 56px;
        }
        .card {
            background: rgba(255,255,255,0.9);
            border-radius: 18px;
            padding: 24px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            backdrop-filter: blur(4px);
        }
        h1 { margin-top: 0; font-size: 2rem; }
        .holiday {
            font-size: 1.25rem;
            background: #eef2ff;
            border-left: 5px solid #6366f1;
            border-radius: 8px;
            padding: 12px 14px;
            margin-bottom: 20px;
        }
        textarea {
            width: 100%;
            min-height: 120px;
            border-radius: 12px;
            border: 1px solid #cbd5e1;
            padding: 12px;
            font-size: 1rem;
            resize: vertical;
            box-sizing: border-box;
        }
        button {
            margin-top: 12px;
            background: #4f46e5;
            color: white;
            border: none;
            padding: 12px 20px;
            font-size: 1rem;
            border-radius: 10px;
            cursor: pointer;
        }
        button:hover { background: #4338ca; }
        .results { margin-top: 28px; display: grid; gap: 14px; }
        .greeting {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            padding: 14px;
            white-space: pre-line;
            line-height: 1.45;
        }
        .name {
            font-weight: 700;
            color: #111827;
            margin-bottom: 8px;
        }
        .hint { color: #475569; margin-bottom: 8px; }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <h1>🎉 Генератор душевных поздравлений</h1>
        <div class="holiday">
            Сегодня: <strong><?= htmlspecialchars($holiday, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></strong>
            (<?= htmlspecialchars($today->format('d.m.Y'), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>)
        </div>

        <form method="post">
            <p class="hint">Введите имена друзей (каждое с новой строки или через запятую):</p>
            <textarea name="names" placeholder="Например:&#10;Алексей&#10;Марина&#10;Игорь"><?= htmlspecialchars($inputNames, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></textarea>
            <br>
            <button type="submit">Сгенерировать поздравления</button>
        </form>

        <?php if (!empty($greetings)): ?>
            <div class="results">
                <?php foreach ($greetings as $name => $text): ?>
                    <div class="greeting">
                        <div class="name">Для <?= htmlspecialchars($name, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>:</div>
                        <?= nl2br(htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')) ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <p>Не удалось найти имена. Попробуйте ввести хотя бы одно имя.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>

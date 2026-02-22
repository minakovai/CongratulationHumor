# CongratulationHumor

Веб-приложение для генерации душевных персональных поздравлений на русском языке с небольшой рифмой.

## Что умеет

- Показывает, **какой сегодня праздник**.
- Принимает список имён друзей (по строкам, через запятую или `;`).
- Генерирует **индивидуальное поздравление для каждого имени**.
- Использует тёплые шаблоны с лёгкой рифмой.

## Локальный запуск (PHP)

Требования: PHP 8+

```bash
php -S 0.0.0.0:8000
```

После запуска откройте:

- `http://localhost:8000/index.php` (или просто `http://localhost:8000`)

## Публикация на GitHub Pages

> Важно: GitHub Pages **не исполняет PHP**.
>
> Поэтому для Pages добавлена статическая версия в `docs/index.html` с эквивалентной логикой на JavaScript.

Что уже сделано в репозитории:

- Добавлен файл `docs/index.html` (версия для GitHub Pages).
- Добавлен workflow `.github/workflows/deploy-pages.yml` для автодеплоя Pages через GitHub Actions.

Чтобы включить публикацию:

1. Создайте GitHub-репозиторий и добавьте remote:
   ```bash
   git remote add origin https://github.com/<your-username>/CongratulationHumor.git
   ```
2. Запушьте ветку:
   ```bash
   git push -u origin <your-branch>
   ```
3. В GitHub: **Settings → Pages → Source = GitHub Actions**.
4. После успешного workflow сайт появится по адресу:
   `https://<your-username>.github.io/CongratulationHumor/`

## Структура

- `index.php` — основное PHP-приложение (серверный рендер).
- `docs/index.html` — версия для GitHub Pages (клиентский рендер).
- `.github/workflows/deploy-pages.yml` — автоматический деплой на Pages.

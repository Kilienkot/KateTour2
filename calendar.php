<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/css/calendar-style.css" />
    <title>Календарь</title>
</head>
<body>
    <?php include("blocks/header.php") ?>

    <main>
        <section class="startlife">
            <img src="sources/img/startlife.png" alt="Начни жить по полной!">
            <div class="startlife__text">
                <h2>Самое время начать <span>жить</span> по полной</h2>
            </div>
        </section>
        <section class="calendar">
            <div class="btns">  
                <div id="savelong">
                    <h3>Оставить <span>длинные выезды</span></h3>
                </div>
                <div id="savesmall">
                    <h3>Оставить <span>короткие поездки</span></h3>
                </div>
            </div>
            <div class="trips">
                <div class="trip__item long-trip">
                    <p class="trip__item-date">12 сен - 21 сен</p>
                    <p class="trip__item-description">
                    Незабываемое путишествие по просторам Камчатки
                    </p>
                </div>
                <div class="trip__item small-trip">
                    <p class="trip__item-date">12 сен - 21 сен</p>
                    <p class="trip__item-description">
                    Незабываемое путишествие по просторам Камчатки
                    </p>
                </div>
            </div>
        </section>
        <hr>
        <?php include("blocks/form.php") ?>
    </main>

    <?php include("blocks/footer.php") ?>
</body>
</html>
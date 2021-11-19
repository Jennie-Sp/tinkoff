<?php
include 'setting.php';
if(!is_dir('files')){
    mkdir('files');
}

$result = $db->query("SELECT * FROM Goods", MYSQLI_USE_RESULT);

if (!$result) {
    $db->query("CREATE TABLE Goods (id int(100), name VARCHAR(100),description VARCHAR(255),price int(10),image varchar(255),quantity int(10))");
}
?>

<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>
<body>
<header>
    <nav class="navbar navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="/">Тестовое задание</a>
        </div>
    </nav>
</header>

<div class="container my-5">
    <div class="row">
        <div class="col-6">
            <main>
                <form name="good" onsubmit="addGood(event, this)">
                    <div class="mb-3">
                        <label for="name" class="form-label">Название товара</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="desc" class="form-label">Описание</label>
                        <textarea class="form-control" name="description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Цена</label>
                        <input type="text" name="price" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Изображение товара</label>
                        <input class="form-control" type="file" name="image" accept="image/jpeg,image/png" required>
                    </div>
                    <div class="mb-3">
                        <div class="row">
                            <label for="quantity" class="form-label">Количество</label>
                            <div class="col-3">
                                <input type="number" min="1" class="form-control" name="quantity" value="1" required>
                            </div>
                            <div class="col-9">
                                <button type="submit" class="btn btn-primary">Добавить</button>
                            </div>
                        </div>
                    </div>
                </form>
            </main>
        </div>
        <div class="col-6">
            <aside>
                <h4>Список товаров</h4>
                <ul class="list-group">
                    <?php if ($result):
                        while ($row = $result->fetch_assoc()):?>
                            <li data-item="<?= $row['id'] ?>" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1"><?= $row['name'] ?></h5>
                                    <img src="/<?= $row['image'] ?>" alt="<?= $row['name'] ?>">
                                </div>
                                <p class="mb-1"><?= $row['description'] ?></p>
                                <p>
                                    <small>Цена <?= $row['price'] ?> руб.</small>
                                </p>
                                <label>
                                    Кол-во <br>
                                    <input type="number" min="1" value="<?= $row['quantity']; ?>"/>
                                </label>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <button class="btn btn-sm btn-primary"
                                            onclick="updateGood(this, <?= $row['id'] ?>)">Обновить элемент
                                    </button>
                                    <button class="btn btn-sm btn-secondary" onclick="removeGood(<?= $row['id'] ?>)">
                                        Удалить элемент
                                    </button>
                                </div>
                            </li>
                        <?php endwhile;
                    endif; ?>
                </ul>
            </aside>
        </div>
    </div>
</div>
<script>
    function addGood(e, form) {
        e.preventDefault();
        // формирование данных из формы
        let formData = new FormData(form);

        // добавление id к товару
        let list = document.querySelector('.list-group').lastElementChild;
        let number = list ? parseFloat(list.getAttribute("data-item")) + 1 : 1;
        formData.append("id", String(number));


        // запрос на добавление товара в бд
        fetch('/add.php', {
            method: 'POST',
            body: formData
        })
            .then(res => res.json())
            .then(json => {
                if (json.status) {
                    document.querySelector('.list-group').innerHTML += `
                            <li data-item="${json.query.id}" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1">${json.query.name}</h5>
                                    <img src="${json.query.path ? json.query.path : ''}" alt="${json.query.name}">
                                </div>
                                <p class="mb-1">${json.query.description}</p>
                                <p>
                                    <small>Цена ${json.query.price} руб.</small>
                                </p>
                                <label>
                                    Кол-во <br>
                                    <input type="number" min="1" value="${json.query.quantity}"/>
                                </label>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <button class="btn btn-sm btn-primary" onclick="updateGood(this, ${json.query.id})">Обновить элемент</button>
                                    <button onclick="removeGood(${json.query.id})">Удалить элемент</button>
                                </div>
                            </li>
                        `
                } else alert('Форма заполнена некорректно')
                form.reset();
            })
    }

    function removeGood(id) {
        fetch('/delete.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                id: id
            })
        })
            .then(res => res.json())
            .then(json => {
                    if (json.status) {
                        document.querySelector('[data-item="' + json.query.id + '"]').remove()
                    }
                }
            )
    }

    function updateGood(el, id) {
        fetch('/update.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                id: id,
                quantity: el.parentElement.parentElement.querySelector('input').value
            })
        })
            .then(res => res.json())
            .then(json => {
                    if (json.status) {
                        alert("Количество было изменено")
                    }
                }
            )
    }
</script>
</body>
</html>

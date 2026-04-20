<?php if (!empty($flash_success)): ?>
<div class="alert alert-success">
    <?php echo HTML::chars($flash_success); ?>
</div>
<?php endif; ?>
<?php if (!empty($flash_error)): ?>
<div class="alert alert-danger">
    <?php echo HTML::chars($flash_error); ?>
</div>
<?php endif; ?>

<h1>Типы событий</h1>

<!-- Навигационные табы -->
<ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active">
        <a href="#tab-list" aria-controls="tab-list" role="tab" data-toggle="tab">Текущий список</a>
    </li>
    <li role="presentation">
        <a href="#tab-add" aria-controls="tab-add" role="tab" data-toggle="tab">Добавить событие</a>
    </li>
    <li role="presentation">
        <a href="#tab-history" aria-controls="tab-history" role="tab" data-toggle="tab">История</a>
    </li>
</ul>

<!-- Содержимое табов -->
<div class="tab-content" style="padding-top: 20px;">
    <!-- Закладка 1: Текущий список -->
    <div role="tabpanel" class="tab-pane active" id="tab-list">
        <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Флаг</th>
                    <th>Цвет</th>
                    <th>Записывать в базу данных</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($eventtypes as $event): 
                $color = str_pad(dechex($event['COLOR']), 6, '0', STR_PAD_LEFT);
                ?>
                <tr>
                    <td><?php echo $event['ID_EVENTTYPE']; ?></td>
                    <td><?php echo HTML::chars($event['NAME']); ?></td>
                    <td><?php echo $event['FLAG']; ?></td>
                    <td>
                        <div class="color-box" style="display: inline-block; width: 20px; height: 20px; border: 1px solid #000; background-color: #<?php echo $color; ?>;" title="COLOR=<?php echo $event['COLOR']; ?> hex=#<?php echo $color; ?>"></div>
                        #<?php echo $color; ?>
                        <small class="text-muted">(<?php echo $event['COLOR']; ?>)</small>
                    </td>
                    <td class="<?php echo $event['ACTIVE'] ? 'text-success' : 'text-danger'; ?>">
                        <?php echo $event['ACTIVE'] ? 'Да' : 'Нет'; ?>
                    </td>
                    <td>
                        <a href="<?php echo URL::site('eventConfig/edit/'.$event['ID_EVENTTYPE']); ?>" class="btn btn-primary btn-xs">Редактировать</a>
                        <!-- Удаление запрещено по ТЗ -->
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p><small>Всего записей: <?php echo count($eventtypes); ?></small></p>
    </div>

    <!-- Закладка 2: Добавить событие -->
    <div role="tabpanel" class="tab-pane" id="tab-add">
        <?php if (!empty($validation_errors)): ?>
        <div class="alert alert-danger">
            <strong>Ошибки валидации:</strong>
            <ul>
                <?php foreach ($validation_errors as $field => $error): ?>
                <li><?php echo HTML::chars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <h2>Добавление нового типа события</h2>
        <form action="<?php echo URL::site('eventConfig/create'); ?>" method="post" class="form-horizontal">
            <div class="form-group">
                <label for="name" class="col-sm-2 control-label">Название:</label>
                <div class="col-sm-10">
                    <input type="text" id="name" name="NAME" class="form-control" required
                           value="<?php echo isset($old_input['NAME']) ? HTML::chars($old_input['NAME']) : ''; ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label for="flag" class="col-sm-2 control-label">Флаг:</label>
                <div class="col-sm-10">
                    <input type="number" id="flag" name="FLAG" value="<?php echo isset($old_input['FLAG']) ? HTML::chars($old_input['FLAG']) : '0'; ?>" min="0" class="form-control">
                </div>
            </div>
            
            <div class="form-group">
                <label for="color" class="col-sm-2 control-label">Цвет (десятичный):</label>
                <div class="col-sm-10">
                    <div class="input-group">
                        <input type="number" id="color" name="COLOR" value="<?php echo isset($old_input['COLOR']) ? HTML::chars($old_input['COLOR']) : '16777215'; ?>" min="0" max="16777215" class="form-control" oninput="updateColorPreview()">
                        <span class="input-group-addon">
                            <div id="colorPreview" class="color-preview" style="display: inline-block; width: 30px; height: 30px; border: 1px solid #000;"></div>
                        </span>
                    </div>
                    <small class="help-block">Диапазон 0-16777215 (0xFFFFFF), по умолчанию белый</small>
                </div>
            </div>
            
            <div class="form-group">
                <label for="sound" class="col-sm-2 control-label">Звук (путь к файлу):</label>
                <div class="col-sm-10">
                    <input type="text" id="sound" name="SOUND" placeholder="например, sound/alert.mp3" class="form-control"
                           value="<?php echo isset($old_input['SOUND']) ? HTML::chars($old_input['SOUND']) : ''; ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label for="active" class="col-sm-2 control-label">Активен:</label>
                <div class="col-sm-10">
                    <select id="active" name="ACTIVE" class="form-control">
                        <option value="1" <?php echo (isset($old_input['ACTIVE']) && $old_input['ACTIVE'] == '1') ? 'selected' : 'selected'; ?>>Да</option>
                        <option value="0" <?php echo (isset($old_input['ACTIVE']) && $old_input['ACTIVE'] == '0') ? 'selected' : ''; ?>>Нет</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="id_parent" class="col-sm-2 control-label">Родительский тип:</label>
                <div class="col-sm-10">
                    <select id="id_parent" name="ID_PARENT" class="form-control">
                        <option value="">-- Без родителя --</option>
                        <?php foreach ($parents as $parent): ?>
                        <option value="<?php echo $parent['ID_EVENTTYPE']; ?>"
                            <?php echo (isset($old_input['ID_PARENT']) && $old_input['ID_PARENT'] == $parent['ID_EVENTTYPE']) ? 'selected' : ''; ?>>
                            <?php echo HTML::chars($parent['NAME']); ?> (ID: <?php echo $parent['ID_EVENTTYPE']; ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-primary">Создать</button>
                    <button type="reset" class="btn btn-default">Очистить</button>
                </div>
            </div>
        </form>
        
        <script>
        function updateColorPreview() {
            var colorVal = document.getElementById('color').value;
            var hex = '#' + ('000000' + parseInt(colorVal || 16777215).toString(16)).slice(-6);
            document.getElementById('colorPreview').style.backgroundColor = hex;
        }
        window.onload = updateColorPreview;
        // Если есть ошибки валидации, автоматически переключиться на вкладку добавления
        <?php if (!empty($validation_errors)): ?>
        $(document).ready(function() {
            $('a[href="#tab-add"]').tab('show');
        });
        <?php endif; ?>
        </script>
    </div>

    <!-- Закладка 3: История -->
    <div role="tabpanel" class="tab-pane" id="tab-history">
        <h2>История разработки модуля</h2>
        <p>Модуль "Конфигурация событий" был разработан для системы Artonit City с целью управления типами событий СКУД.</p>
        <p>Основные этапы разработки:</p>
        <ul>
            <li>Версия 1.0 (2023) — базовый функционал: CRUD для типов событий.</li>
            <li>Версия 1.1 (2024) — добавлена валидация, улучшен интерфейс.</li>
            <li>Версия 2.0 (2025) — интеграция с Bootstrap 3, добавлены цветовые предпросмотры.</li>
        </ul>
        <p>Текущая версия: 2.0 (Kohana 3.3, PHP 5.6, Bootstrap 3).</p>
    </div>
</div>
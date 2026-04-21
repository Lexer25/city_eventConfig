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
                        <button type="button" class="btn btn-primary btn-xs edit-event-btn" 
                                data-id="<?php echo $event['ID_EVENTTYPE']; ?>"
                                data-name="<?php echo htmlspecialchars($event['NAME'], ENT_QUOTES, 'UTF-8'); ?>"
                                data-flag="<?php echo $event['FLAG']; ?>"
                                data-color="<?php echo $event['COLOR']; ?>"
                                data-sound="<?php echo htmlspecialchars($event['SOUND'], ENT_QUOTES, 'UTF-8'); ?>"
                                data-active="<?php echo $event['ACTIVE']; ?>"
                                data-id_parent="<?php echo $event['ID_PARENT']; ?>">
                            Редактировать
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p><small>Всего записей: <?php echo count($eventtypes); ?></small></p>
    </div>

    <!-- Закладка 2: Добавить событие -->
    <div role="tabpanel" class="tab-pane" id="tab-add">
        <?php if (!empty($validation_errors) && empty($old_input['ID_EVENTTYPE'])): ?>
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
        <?php if (!empty($validation_errors) && empty($old_input['ID_EVENTTYPE'])): ?>
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

<!-- Простое модальное окно (своими руками, без Bootstrap JS) -->
<style>
/* Стили для модального окна */
#simpleModalOverlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 9998;
}

#simpleModal {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 90%;
    max-width: 600px;
    background: #fff;
    border-radius: 4px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.3);
    z-index: 9999;
    max-height: 90vh;
    overflow-y: auto;
}

.simple-modal-header {
    padding: 15px;
    border-bottom: 1px solid #e5e5e5;
    background: #f5f5f5;
    border-radius: 4px 4px 0 0;
    overflow: hidden;
}

.simple-modal-header h4 {
    margin: 0;
    float: left;
}

.simple-modal-header .close {
    float: right;
    font-size: 21px;
    font-weight: bold;
    line-height: 1;
    color: #000;
    text-shadow: 0 1px 0 #fff;
    opacity: 0.5;
    background: none;
    border: none;
    cursor: pointer;
    padding: 0;
}

.simple-modal-header .close:hover {
    opacity: 0.8;
}

.simple-modal-body {
    padding: 15px;
}

.simple-modal-footer {
    padding: 15px;
    border-top: 1px solid #e5e5e5;
    text-align: right;
    background: #f5f5f5;
    border-radius: 0 0 4px 4px;
}

.simple-modal-footer .btn {
    display: inline-block;
    padding: 6px 12px;
    margin-bottom: 0;
    font-size: 14px;
    font-weight: normal;
    line-height: 1.42857143;
    text-align: center;
    cursor: pointer;
    border: 1px solid transparent;
    border-radius: 4px;
}

.simple-modal-footer .btn-default {
    color: #333;
    background: #fff;
    border-color: #ccc;
}

.simple-modal-footer .btn-primary {
    color: #fff;
    background: #337ab7;
    border-color: #2e6da4;
}

.simple-modal-body .form-group {
    margin-bottom: 15px;
}

.simple-modal-body .form-group label {
    display: inline-block;
    max-width: 100%;
    margin-bottom: 5px;
    font-weight: bold;
}

.simple-modal-body .form-control {
    display: block;
    width: 100%;
    height: 34px;
    padding: 6px 12px;
    font-size: 14px;
    line-height: 1.42857143;
    color: #555;
    background: #fff;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}

.simple-modal-body select.form-control {
    height: 34px;
}

.simple-modal-body .input-group {
    display: table;
    border-collapse: separate;
}

.simple-modal-body .input-group-addon {
    display: table-cell;
    width: 1%;
    white-space: nowrap;
    vertical-align: middle;
    padding: 6px 12px;
    font-size: 14px;
    background: #eee;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.simple-modal-body .input-group .form-control {
    display: table-cell;
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
}

.simple-modal-body .help-block {
    display: block;
    margin-top: 5px;
    color: #737373;
    font-size: 12px;
}

.simple-modal-body .alert {
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid transparent;
    border-radius: 4px;
}

.simple-modal-body .alert-danger {
    color: #a94442;
    background: #f2dede;
    border-color: #ebccd1;
}
</style>

<div id="simpleModalOverlay"></div>
<div id="simpleModal">
    <div class="simple-modal-header">
        <button type="button" class="close" id="simpleModalClose">&times;</button>
        <h4>Редактирование типа события</h4>
    </div>
    <form action="<?php echo URL::site('eventConfig/save'); ?>" method="post" id="simpleEditForm">
        <div class="simple-modal-body">
            <input type="hidden" name="ID_EVENTTYPE" id="simple_edit_id">
            
            <div class="form-group">
                <label for="simple_edit_name">Название:</label>
                <input type="text" id="simple_edit_name" name="NAME" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="simple_edit_flag">Флаг:</label>
                <input type="number" id="simple_edit_flag" name="FLAG" min="0" class="form-control">
            </div>
            
            <div class="form-group">
                <label for="simple_edit_color">Цвет (десятичный):</label>
                <div class="input-group">
                    <input type="number" id="simple_edit_color" name="COLOR" min="0" max="16777215" class="form-control" oninput="updateSimpleModalColorPreview()">
                    <span class="input-group-addon">
                        <div id="simpleModalColorPreview" style="display: inline-block; width: 30px; height: 30px; border: 1px solid #000;"></div>
                    </span>
                </div>
                <small class="help-block">Диапазон 0-16777215 (0xFFFFFF)</small>
            </div>
            
            <div class="form-group">
                <label for="simple_edit_sound">Звук (путь к файлу):</label>
                <input type="text" id="simple_edit_sound" name="SOUND" placeholder="например, sound/alert.mp3" class="form-control">
            </div>
            
            <div class="form-group">
                <label for="simple_edit_active">Активен:</label>
                <select id="simple_edit_active" name="ACTIVE" class="form-control">
                    <option value="1">Да</option>
                    <option value="0">Нет</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="simple_edit_id_parent">Родительский тип:</label>
                <select id="simple_edit_id_parent" name="ID_PARENT" class="form-control">
                    <option value="">-- Без родителя --</option>
                    <?php foreach ($parents as $parent): ?>
                    <option value="<?php echo $parent['ID_EVENTTYPE']; ?>">
                        <?php echo HTML::chars($parent['NAME']); ?> (ID: <?php echo $parent['ID_EVENTTYPE']; ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <?php if (!empty($validation_errors) && isset($old_input['ID_EVENTTYPE'])): ?>
            <div class="alert alert-danger">
                <strong>Ошибки валидации:</strong>
                <ul>
                    <?php foreach ($validation_errors as $field => $error): ?>
                    <li><?php echo HTML::chars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
        </div>
        <div class="simple-modal-footer">
            <button type="button" class="btn btn-default" id="simpleModalCancel">Отмена</button>
            <button type="submit" class="btn btn-primary">Сохранить изменения</button>
        </div>
    </form>
</div>

<script>
// Функция обновления предпросмотра цвета
function updateSimpleModalColorPreview() {
    var colorVal = document.getElementById('simple_edit_color').value;
    var hex = '#' + ('000000' + parseInt(colorVal || 16777215).toString(16)).slice(-6);
    document.getElementById('simpleModalColorPreview').style.backgroundColor = hex;
}

// Функции открытия/закрытия модального окна
function openSimpleModal() {
    document.getElementById('simpleModalOverlay').style.display = 'block';
    document.getElementById('simpleModal').style.display = 'block';
}

function closeSimpleModal() {
    document.getElementById('simpleModalOverlay').style.display = 'none';
    document.getElementById('simpleModal').style.display = 'none';
}

// Обработчики событий
$(document).ready(function() {
    // Кнопки редактирования
    $('.edit-event-btn').on('click', function(e) {
        e.preventDefault();
        
        var btn = $(this);
        $('#simple_edit_id').val(btn.data('id'));
        $('#simple_edit_name').val(btn.data('name'));
        $('#simple_edit_flag').val(btn.data('flag'));
        $('#simple_edit_color').val(btn.data('color'));
        $('#simple_edit_sound').val(btn.data('sound'));
        $('#simple_edit_active').val(btn.data('active'));
        $('#simple_edit_id_parent').val(btn.data('id_parent') ? btn.data('id_parent') : '');
        
        updateSimpleModalColorPreview();
        openSimpleModal();
    });
    
    // Закрытие по крестику
    $('#simpleModalClose').on('click', closeSimpleModal);
    
    // Закрытие по кнопке Отмена
    $('#simpleModalCancel').on('click', closeSimpleModal);
    
    // Закрытие по клику на overlay
    $('#simpleModalOverlay').on('click', closeSimpleModal);
    
    // Закрытие по ESC
    $(document).on('keydown', function(e) {
        if (e.keyCode === 27) {
            closeSimpleModal();
        }
    });
    
    <?php if (!empty($validation_errors) && isset($old_input['ID_EVENTTYPE'])): ?>
    // Если есть ошибки валидации, открываем модальное окно
    $('#simple_edit_id').val('<?php echo isset($old_input['ID_EVENTTYPE']) ? $old_input['ID_EVENTTYPE'] : ''; ?>');
    $('#simple_edit_name').val('<?php echo isset($old_input['NAME']) ? addslashes($old_input['NAME']) : ''; ?>');
    $('#simple_edit_flag').val('<?php echo isset($old_input['FLAG']) ? $old_input['FLAG'] : ''; ?>');
    $('#simple_edit_color').val('<?php echo isset($old_input['COLOR']) ? $old_input['COLOR'] : ''; ?>');
    $('#simple_edit_sound').val('<?php echo isset($old_input['SOUND']) ? addslashes($old_input['SOUND']) : ''; ?>');
    $('#simple_edit_active').val('<?php echo isset($old_input['ACTIVE']) ? $old_input['ACTIVE'] : ''; ?>');
    $('#simple_edit_id_parent').val('<?php echo isset($old_input['ID_PARENT']) ? $old_input['ID_PARENT'] : ''; ?>');
    updateSimpleModalColorPreview();
    openSimpleModal();
    <?php endif; ?>
});
</script>
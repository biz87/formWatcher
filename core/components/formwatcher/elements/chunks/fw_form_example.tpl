<form class="formWatcher">
    <div class="form-group">
        <label for="name">Как вас зовут</label>
        <input type="text" class="form-control" id="name" name="name" placeholder="Ваше имя">
        <br><br>
        <label for="phone">Телефон</label>
        <input type="text" class="form-control" id="phone" name="phone" placeholder="+7 777 777-77-77">
    </div>
    <button type="submit" class="btn btn-primary">Отправить</button>
    <input type="hidden" name="fw_form_id" value="fw_form_example">
    <input type="hidden" name="fw_client_id" value="{$_modx->user.id > 0?:$.cookie.PHPSESSID}">
</form>
<form class="formWatcher">
    <div class="form-group">
        <label for="name">Как вас зовут</label>
        <input type="text" class="form-control" id="name" name="name" placeholder="Ваше имя">
        <br><br>
        <label for="phone">Телефон</label>
        <input type="text" class="form-control" required id="phone" name="phone" placeholder="+7 777 777-77-77">
    </div>
    <button type="submit" class="btn btn-primary">Отправить</button>

    <input type="hidden" name="client_id" value="{$.cookie.PHPSESSID}">
</form>
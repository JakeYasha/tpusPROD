<div style="background-color: #ffffff;" bgcolor="#ffffff">
	<p>На сайте TovaryPlus.ru была изменена информация об организации <strong>"<?=$item['company_name']?>"</strong>.</p>
	<p></p>
	<p>Изменения:</p>
        <table border="1" style="border-collapse:collapse;width:600px;" cellspacing="0" cellpadding="3">
            <tr>
                <th style="background-color:#eee;">Поле</th>
                <th style="background-color:#eee;">Старое значение</th>
                <th style="background-color:#eee;">Новое значение</th>
            </tr>
        <?if (count($params['changed_data']) > 0) { foreach($params['changed_data'] as $changed_data){?>
            <tr>
                <td><strong><?=isset($changed_data['name']['label'])?$changed_data['name']['label'] : $changed_data['name'] ?></strong></td>
            <td style="color:#aa0012;"><strike><?=$changed_data['old_value']?></strike></td>
            <td><?=$changed_data['new_value']?></td>
            </tr>
        <?}} else {?>
           <tr><td colspan="3">Данные об организации не изменялись</td></tr>
        <?}?>
        </table>
	<br/>
	<br/>
	<p>-------------------------------------------------------</p>
	<p><strong>На это письмо отвечать не надо, т.к. оно сформировано и отправлено автоматически с сайта tovaryplus.ru.</strong></p>
	<p>Для перехода в кабинет администратора пройдите по ссылке <a href="http://tovaryplus.ru/cms/">http://tovaryplus.ru/cms/</a></p>
</div>


<!-- <p>Доступные переменные:
	<br/>$params['changed_data']
	<br/>$item['company_name']
	<br/>$item['company_phone']
	<br/>$item['company_cell_phone']
	<br/>$item['company_fax']
	<br/>$item['company_map_address']
	<br/>$item['company_email']
	<br/>$item['company_web_site_url']
	<br/>$item['activity']
</p> -->
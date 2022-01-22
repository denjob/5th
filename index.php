<?php
//CONFIG
require __DIR__ . '/conf.php'; 

$ins_upd = function($insert = true, int $id = 0) use($db) {
	//check post data
	$_check_fields = true;
	$_ar_values = [];
	$_ar_cols = [];
	$_ar_sets = [];
	foreach($db->query('show columns from cars') as $col) {
		if($col['Field'] === 'id') continue;
		if(empty($_POST[$col['Field']])){
			$_check_fields = false;
			break;
		}
		$_ar_cols[] = '`'.$col['Field'].'`';
		$_ar_values[] = $db->quote($_POST[$col['Field']]);
		if(!$insert) //for update
			$_ar_sets[] = '`'.$col['Field'].'`='.$db->quote($_POST[$col['Field']]); 
	}
	if($_check_fields && !empty($_ar_cols) && !empty($_ar_values) && count($_ar_cols) == count($_ar_values)){
		if(!$insert){
			if(!empty($_ar_sets) && $id){
				$_sets = implode(',', $_ar_sets);
				$_q_add = "update cars set ".$_sets." WHERE id=$id";
			}else{
				$_error_upd = true;
				return;
			}
		}else{
			$_cols_add = implode(',', $_ar_cols);
			$_vals_add = implode(',', $_ar_values);
			$_q_add = "insert into cars (".$_cols_add.") VALUES (".$_vals_add.")";
		}
		$add = $db->prepare($_q_add);
		if($add->execute()){
			header("Location: /");
		}else{
			if($insert)
				$_error_add = true;
			else
				$_error_upd = true;
		}
	}else{
		if($insert)
			$_error_add = true;
		else
			$_error_upd = true;
	}
};

//CRUD
if(isset($_POST['submit_add'])){ //ADD
	$ins_upd();
}elseif(isset($_POST['submit_edit']) && !empty($_POST['id'])){ //UPD
	$ins_upd(false, (int)$_POST['id']);
}elseif(isset($_POST['submit_del_q']) && !empty($_POST['id'])){ //DEL CONFIRM
	$del_confirm = true;
	$del_confirm_id = (int)$_POST['id'];
}elseif(isset($_POST['submit_del']) && !empty($_POST['id'])){ //DEL
	$_q_del = "delete from cars WHERE id=".(int)$_POST['id'];
	$del = $db->prepare($_q_del);
	if($del->execute()){
		header("Location: /");
	}else{
		$_error_del = true;
	}
}

//VAR
//query string
$ar_sort_sc = ['asc','desc'];
$qs_for_sort = $_GET;
unset($qs_for_sort['sort']);
unset($qs_for_sort['sc']);
$qs_for_page = $_GET;
unset($qs_for_page['page']);
//all columns name
$ar_cols_names = [];
$ar_curr_sort = [];
foreach($db->query('show full columns from cars') as $col) {
	if($col['Field'] === 'id') continue;
	$ar_cols_names[$col['Field']] = $col['Comment'];
	if(isset($_GET['sort']) && isset($_GET['sc']) && $col['Field'] === $_GET['sort']){
		$ar_curr_sort = array(
			$_GET['sort'] => (($_GET['sc'] === 'asc')?'desc':'asc')
		);
	}
}

?>
<!DOCTYPE html>
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>5th</title>
		<link rel="stylesheet" href="/css/site.css" />
	</head>
	<body>
		<div class="container">
			<?php if(isset($del_confirm) && $del_confirm && isset($del_confirm_id)):?>
				<div class="popup_del">
					<div class="popup_del_cont">
						<div class="popup_del_text">Вы действительно хотите удалть данную запись?</div>
						<form id="row_del" method="POST" style="margin-top:5px;">
							<input name="id" type="hidden" value="<?=$del_confirm_id?>" form="row_del"></input>
						</form>
						<input name="submit_del" type="submit" value="ДА" form="row_del"></input>
						<form style="display:inline-block;"><input type="submit" value="НЕТ"></input></form>
					</div>
				</div>
			<?php endif;?>
			<div class="form_add">
				<div class="form_add title">Добавление</div>
				<?php if(isset($_error_add)):?>
					<div class="form_add error">Ошибка добавления записи.</div>
				<?php endif;?>
				<form method="POST">
					<table class="form_add_table">
						<?foreach($ar_cols_names as $col=>$name):?>
						<tr>
							<td><label for="form_add_input_<?=$col;?>"><?=$name;?></label></td>
							<td><input type="text" name="<?=$col;?>" placeholder="<?=$name;?>" id="form_add_input_<?=$col;?>" required></input></td>
						</tr>
						<?endforeach;?>
						<tr>
							<td><input type="submit" name="submit_add" value="Добавить"></input></td>
						</tr>
					</table>
				</form>
			</div>
			<div class="form_add">
				<div class="form_add title">Поиск</div>
				<form method="GET">
					<table class="form_add_table">
						<tr>
							<td><input type="text" name="search" placeholder="Что ищем?" value="<?=!empty($_GET['search'])?$_GET['search']:'';?>"></input></td>
							<td><input type="submit" value="Поиск"></input></td>
						</tr>
					</table>
				</form>
			</div>
			<?php if(isset($_error_upd)):?>
				<div class="form_add error">Ошибка Обновления записи.</div>
			<?php elseif(isset($_error_del)):?>
				<div class="form_add error">Ошибка Удаления записи.</div>
			<?php endif;?>
			<table border="1" class="main_table">
				<tr>
					<th>№</th>
					<?foreach($ar_cols_names as $col=>$name):
						$_sc = 'asc';
						$_class = '';
						if(array_key_exists($col, $ar_curr_sort)){
							$_sc = $ar_curr_sort[$col];
							$_class = 'main_table_sort_active '.$_sc;
						}
					?>
						<th><a href="?sort=<?=$col.'&sc='.$_sc.'&'.http_build_query($qs_for_sort);?>" class="<?=$_class;?>"><?=$name;?></a></th>
					<?endforeach;?>
				</tr>
			<?php
				$cnt = 1;
				$page = ' limit '.PAGE_LIMIT.' offset 0'; //first page
				$sort = '';
				$where = '';
				//sort
				if(!empty($_GET['sort']) && !empty($_GET['sc']) && array_key_exists($_GET['sort'], $ar_cols_names) && in_array($_GET['sc'], $ar_sort_sc)){
					$sort = ' order by '.$_GET['sort'].' '.$_GET['sc'];
				}
				//per page
				if(!empty($_GET['page'])){
					$offset = (intval($_GET['page']) - 1) * PAGE_LIMIT;
					$cnt = $offset + 1;
					$page = ' limit '.PAGE_LIMIT.' offset '.$offset;
				}
				//search
				if(!empty($_GET['search'])){
					$ar_where = [];
					$_search = mb_ereg_replace('[\'\"]','', $_GET['search']);
					foreach($ar_cols_names as $col=>$name) {
						$ar_where[] = '(`'.$col.'` like \'%'.$_search.'%\')';
					}
					$where = ' where '.implode(' OR ', $ar_where).' ';
				}
				//count all rows
				$q_cnt_all = $db->query('select count(*) as cnt from cars'.$where.$sort)->fetch();
				$cnt_all = (int)$q_cnt_all['cnt'];
				//show data
				foreach($db->query('select * from cars'.$where.$sort.$page) as $row) {
					$_id = $row['id'];
					echo '<tr><td>'.$cnt++.'</td>';
					foreach($ar_cols_names as $col=>$name){
						echo '<td><input name="'.$col.'" type="text" value="'.$row[$col].'" form="row_edit_'.$_id.'"></input></td>';
					}
					echo '<td>
							<form id="row_edit_'.$_id.'" method="POST">
								<input name="id" type="hidden" value="'.$_id.'" form="row_edit_'.$_id.'"></input>
								<input name="submit_edit" type="submit" value="Сохранить" form="row_edit_'.$_id.'"></input>
							</form>
							<form id="row_del_'.$_id.'" method="POST" style="margin-top:5px;">
								<input name="id" type="hidden" value="'.$_id.'" form="row_del_'.$_id.'"></input>
								<input name="submit_del_q" type="submit" value="Удалить" form="row_del_'.$_id.'"></input>
							</form>
						</td>
					</tr>';
				}
			?>
			</table>
			<?php
				//pagination
				if($cnt_all >= PAGE_LIMIT){
					$pag_btn_cnt = intval(ceil($cnt_all/PAGE_LIMIT));
					$_cnt_pag = 1;
					if($pag_btn_cnt > 1){
						echo '<div class="main_table_pag"><ul>';
						while($pag_btn_cnt){
							$_class= '';
							if((isset($_GET['page'])&&$_GET['page']==$_cnt_pag) || (!isset($_GET['page'])&&$_cnt_pag==1)){
								$_class= 'pag_active';
							}
							echo '<li><a href="?page='.$_cnt_pag.'&'.http_build_query($qs_for_page).'" class="'.$_class.'">'.$_cnt_pag++.'</a></li>';
							$pag_btn_cnt--;
						}
						echo '</ul></div>';
					}
				}
			?>
		</div>
	</body>
</html>	

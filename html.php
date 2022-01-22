<!DOCTYPE html>
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>5th</title>
		<link rel="stylesheet" href="/css/site.css" />
		<script type="text/javascript" src="/js/script.js"></script>
	</head>
	<body>
		<div class="container">
			<div class="form_add">
				<div class="form_add title">Поиск</div>
				<table class="form_add_table">
					<tr>
						<td><input type="text" name="search" placeholder="Что ищем?" id="input_search"></input></td>
						<td><button onClick="e_click_search(this);">Поиск</button></td>
					</tr>
				</table>
			</div>
			<table border="1" class="main_table" id="main_table">
			</table>
			<div class="main_table_pag" id="main_table_pag"></div>
		</div>
	</body>
</html>	
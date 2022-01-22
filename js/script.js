console.log('5th');
//CONFIG
var page_limit = 3;
//GLOBAL VAR
window._page_ = 1;
window._sort_ = false;
window._sc_ = 'asc';
window._search_ = false;
//DATA
var ar_data = [];
ar_data[1] = [];
ar_data[1]['marka'] = 'Lada';
ar_data[1]['model'] = 'Granta';
ar_data[1]['color'] = 'Синий';
ar_data[1]['count'] = '1';
ar_data[1]['price'] = '600000р.';
ar_data[2] = [];
ar_data[2]['marka'] = 'BMW';
ar_data[2]['model'] = 'X3';
ar_data[2]['color'] = 'Красный';
ar_data[2]['count'] = '2';
ar_data[2]['price'] = '4600000р.';
ar_data[3] = [];
ar_data[3]['marka'] = 'BMW';
ar_data[3]['model'] = 'X5';
ar_data[3]['color'] = 'Белый';
ar_data[3]['count'] = '7';
ar_data[3]['price'] = '6200000р.';
ar_data[4] = [];
ar_data[4]['marka'] = 'Lada';
ar_data[4]['model'] = 'Vesta';
ar_data[4]['color'] = 'Розовый';
ar_data[4]['count'] = '1';
ar_data[4]['price'] = '800000р.';
//columns
var ar_cols = [];
ar_cols['marka'] = 'Марка';
ar_cols['model'] = 'Модель';
ar_cols['color'] = 'Цвет';
ar_cols['count'] = 'Количество';
ar_cols['price'] = 'Цена';
//function get data by id
function getDataById(id){
	ar_data.forEach(function(data, _id, _ar_data){
		if(id == _id) return data;
	});
	return false;
}
//function show data
function show_data(page = 1){
	let _cnt = 1,
		main_table = document.getElementById('main_table'),
		data_rows = document.getElementsByClassName('data_row'),
		offset = 0,
		__cnt = 0;
	//clear data before set new
	while(data_rows[0]){
		data_rows[0].parentNode.removeChild(data_rows[0]);
	}
	//per page
	if(page > 1){
		offset = (page - 1) * page_limit;
		_cnt = offset + 1;
	}
	//sort
	data_sort(window._sort_, window._sc_);
	//search
	let _ar_data = data_search(window._search_);
	//show data
	for(let id in _ar_data){
		__cnt++;
		if('search' in _ar_data[id]){
			if(!_ar_data[id]['search']) continue;
		}
		if((_cnt - offset) > page_limit) break;
		if(__cnt <= offset) continue;
		let d_row = document.createElement('tr');
		d_row.setAttribute('class','data_row');
		let d_row_col = document.createElement('td');
		d_row_col.innerHTML = _cnt++;
		d_row.appendChild(d_row_col);
		for(let col in ar_cols){
			let d_row_col2 = document.createElement('td');
			d_row_col2.innerHTML = '<input type="text" name="'+col+'" value="'+_ar_data[id][col]+'" data-id="'+id+'" class="data_input_val_'+id+'"></input>';
			d_row.appendChild(d_row_col2);
		}
		//add command buttons
		let d_row_com = document.createElement('td');
		d_row_com.innerHTML = '<button data-id="'+id+'" onClick="e_click_edit(this);">Сохранить</button> <button data-id="'+id+'" onClick="e_click_del(this);">Удалить</button>';
		d_row.appendChild(d_row_com);
		main_table.appendChild(d_row);
	}
	show_pag(_ar_data);
}
//function show pagination
function show_pag(_ar_data){
	let cnt_all = 0; //count all
	_ar_data.forEach(function(data, id, _ar_data){
		cnt_all++;
		if('search' in data){
			if(!data['search']) cnt_all--;
		}
	});
	let pag_btn_cnt = Math.ceil(cnt_all/page_limit),
		_cnt_pag = 1;
	//clear pagination before set new
	let pag_rows = document.getElementsByClassName('main_table_pag_ul');
	while(pag_rows[0]){
		pag_rows[0].parentNode.removeChild(pag_rows[0]);
	}
	if(pag_btn_cnt > 1){
		let main_table_pag = document.getElementById('main_table_pag'),
			_str_pag = '';
		while(pag_btn_cnt){
			let _class = '';
			if(_cnt_pag == window._page_) _class = 'pag_active';
			_str_pag += '<li><a href="#" class="'+_class+'" data-page="'+_cnt_pag+'" onClick="e_click_pag(this);">'+(_cnt_pag++)+'</a></li>';
			pag_btn_cnt--;
		}
		main_table_pag.innerHTML = '<ul class="main_table_pag_ul">'+_str_pag+'</ul>';
	}
}
//function click pagination
function e_click_pag(_this){
	let _page = Number(_this.getAttribute('data-page'));
	window._page_ = _page;
	show_data(_page);
	return false;
}
//function click add
function e_click_add(_this){
	let inputs = document.getElementsByClassName('data_add_input_val'),
		ar_new_data = [],
		_check_data = true;
	Array.prototype.forEach.call(inputs, function(el) {
		let val = el.value,
			name = el.getAttribute('name');
		if(!val) _check_data = false;
		ar_new_data[name] = val;
		el.value = null;
	});
	if(_check_data){
		ar_data.push(ar_new_data);
	}
	show_data(window._page_);
	return false;
}
//function click edit
function e_click_edit(_this){
	let _id = Number(_this.getAttribute('data-id')),
		inputs = document.getElementsByClassName('data_input_val_'+_id),
		ar_new_data = [],
		_check_data = true;
	Array.prototype.forEach.call(inputs, function(el) {
		let val = el.value,
			name = el.getAttribute('name');
		if(!val) _check_data = false;
		ar_new_data[name] = val;
	});
	if(_check_data){
		ar_data[_id] = ar_new_data;
	}
	show_data(window._page_);
	return false;
}
//function click delete
function e_click_del(_this){
	if(confirm('Вы действительно хотите удалить эту запись?')){
		let _id = Number(_this.getAttribute('data-id'));
		ar_data.forEach(function(data, id, _ar_data){
			if(id == _id){
				ar_data.splice(id, 1);
				show_data();
			}
		});
	}
	return false;
}
//function click sort
function data_sort(col, sc){
	if(!col) return false;
	//sort
	if(sc == 'asc'){
		ar_data.sort(function (a, b) {
			if (a[col] > b[col]) {
				return 1;
			}
			if (a[col] < b[col]) {
				return -1;
			}
			return 0;
		});
	}else{
		ar_data.sort(function (a, b) {
			if (a[col] > b[col]) {
				return -1;
			}
			if (a[col] < b[col]) {
				return 1;
			}
			return 0;
		});
	}
	return true;
}
function e_click_sort(_this){
	let col = _this.getAttribute('data-col'),
		sc = _this.getAttribute('data-sc');
	//clear prev class sort
	let _sorts = document.getElementsByClassName('main_table_sort_active');
	Array.prototype.forEach.call(_sorts, function(el) {
		el.setAttribute('class','');
	});
	//sort
	if(sc == 'asc'){
		_this.setAttribute('data-sc','desc');
		_this.setAttribute('class','main_table_sort_active desc');
	}else{
		_this.setAttribute('data-sc','asc');
		_this.setAttribute('class','main_table_sort_active asc');
	}
	window._sort_ = col;
	window._sc_ = sc;
	show_data(window._page_);
	return false;
}
//function click search
function data_search(_text = false){
	if(!_text) return ar_data;
	return ar_data.filter(function(data, id) {
		for(let col in ar_cols){
			let regexp = new RegExp(_text,'i');
			if(data[col].search(regexp) != -1){
				return true;
			}
		}
	});
}
function e_click_search(_this){
	let _text = document.getElementById('input_search').value;
	window._search_ = _text;
	window._page_ = 1;
	show_data(window._page_);
	return false;
}
//ready
document.addEventListener("DOMContentLoaded", function(event) { 
	console.log('ready');
	//create table
	let main_table = document.getElementById('main_table'),
		h_row = document.createElement('tr'),
		h_row_num = document.createElement('th');
	h_row_num.innerHTML = '№';
	h_row.appendChild(h_row_num);
	for(let col in ar_cols){
		let h_row_col = document.createElement('th');
		h_row_col.setAttribute('data_colname',col);
		h_row_col.innerHTML = '<a href="#" data-col="'+col+'" data-sc="asc" class="" onClick="e_click_sort(this);">'+ar_cols[col]+'</a>';
		h_row.appendChild(h_row_col);
	}
	let h_row_com = document.createElement('th');
	h_row.appendChild(h_row_com);
	main_table.appendChild(h_row);
	//row add
	let s_row = document.createElement('tr');
	let s_row_col = document.createElement('td');
	s_row.appendChild(s_row_col);
	for(let col in ar_cols){
		let s_row_col = document.createElement('td');
		s_row_col.innerHTML = '<input type="text" name="'+col+'" class="data_add_input_val"></input>';
		s_row.appendChild(s_row_col);
	}
	let s_row_com = document.createElement('td');
	s_row_com.innerHTML = '<button onClick="e_click_add(this);">Добавить</button>';
	s_row.appendChild(s_row_com);
	main_table.appendChild(s_row);
	//rows data
	show_data();
});


































